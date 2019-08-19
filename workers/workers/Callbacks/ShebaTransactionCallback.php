<?php

namespace Worker\Callbacks;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;
use Worker\Abstracts\AbstractCallback;
use Worker\Exceptions\InvalidRequestException;
use Worker\Extras\Job;
use Worker\Extras\Logger;
use Worker\Extras\Sheba;
use Worker\Extras\Token;
use Worker\Models\ShebaTransactionDocument;

class ShebaTransactionCallback extends AbstractCallback {
    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $data = Job::getJobData($msg);
        $token = ((new Token())->make());
        $expire = time() + parent::EXPIRE_TIME;
        while(time() < $expire)
        {
            try {
                $options = new Sheba($data, $token);
                $options->setTrackerId(Uuid::uuid4()->toString());
                $options = $options->toArray();
                $res = (new Client())->request(
                    'POST',
                    config('endpoints.saman.sheba'),
                    $options
                );
                $response = json_decode($res->getBody()->getContents(), true);

                ShebaTransactionDocument::insertBankResult(
                    $data['_id'],
                    $response,
                    $options['json']['trackerId']
                );
                $data = array_merge($data, $response);
                $date = (strtotime(config('time.start')) - strtotime( 'now') > -1) ?
                    date('Y-m-d', strtotime('now')) . ' ' . config('time.start') :
                    date('Y-m-d', strtotime('tomorrow')) . ' ' . config('time.start')
                ;
                sendTask(
                    config('rabbit.queue.block'),
                    'check_sheba_transaction_result',
                    $data,
                    [],
                    [],
                    $date
                );
                Logger::info('transaction passed for check result later',
                    ['transaction id' => $data['_id']]);
                break;
            } catch (ClientException $e) {
                if ($e->getCode() == '403'):
                    sendTask(
                        config('rabbit.queue.priority'),
                        'login'
                    );
                    sleep(parent::WAIT_FOR_LOGIN);
                else:
                    ShebaTransactionDocument::updateOne(
                        ['_id' => $data['_id']],
                        ['$set' => ['exception' => $e->getResponse()->getBody()->getContents()]]
                    );
                    throw new InvalidRequestException(
                        $e->getMessage() ?
                            $e->getResponse()->getBody()->getContents() :
                            'Gazzle Http faced an unknown problem'
                    );
                    break;
                endif;
            } catch (\Throwable $e) {
                ShebaTransactionDocument::updateOne(
                    ['_id' => $data['_id']],
                    ['$set' => ['exception' => $e->getMessage()]]
                );

                throw new InvalidRequestException(
                    $e->getMessage() ?
                        $e->getMessage() :
                        'Gazzle Http faced an unknown problem'
                );
            }

            sleep(parent::REQUEST_WAIT);
        }
        $this->ack($msg);

        return $msg;
    }
}