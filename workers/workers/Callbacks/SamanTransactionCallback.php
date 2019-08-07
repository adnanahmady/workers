<?php

namespace Worker\Callbacks;

use GuzzleHttp\Exception\ClientException;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;
use Worker\Abstracts\AbstractCallback;
use Worker\Exceptions\InvalidFieldException;
use Worker\Exceptions\InvalidRequestException;
use Worker\Extras\Job;
use Worker\Extras\Logger;
use GuzzleHttp\Client as Guzzle;
use Worker\Extras\Timer;
use Worker\Extras\Token;
use Worker\Models\Driver;
use Worker\Models\Login;
use Worker\Models\Passenger;
use Worker\Models\SamanTransactionDocument;
use Worker\Traits\SenderTrait;

class SamanTransactionCallback extends AbstractCallback {
    use SenderTrait;

    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $data = Job::getJobData($msg);

        if (preg_match('/^70/', $data['tafzil'])) {
            $accountCheck = Passenger::updateWallet(['detail_code' => $data['tafzil']], $data['amount']);
        } else {
            $accountCheck = Driver::updateWallet(['detail_code' => $data['tafzil']], $data['amount']);
        }

        if (! $accountCheck) {
            SamanTransactionDocument::updateOne(
                ['_id' => $data['_id']],
                ['$set' => ['exception' => (
                    preg_match('/^70/', $data['tafzil']) ?
                        'passengers' :
                        'drivers'
                    ) . " amount with tafzil \"{$data['tafzil']}\" is not enough"
                ]],
                ['upsert' => false]
            );

            throw new InvalidFieldException(
                (preg_match('/^70/', $data['tafzil']) ?
                    'passengers' :
                    'drivers') . " amount with tafzil \"{$data['tafzil']}\" is not enough"
            );
        }

        $token = (new Token())->make();
        $options = $this->getSamanTransactionOptions($data, $token);

        $expire = time() + parent::EXPIRE_TIME;
        while(time() < $expire)
        {
            try {
                $options['json']['trackerId'] = Uuid::uuid4()->toString();
                $res = (new Guzzle())->request(
                    'POST',
                    config('endpoints.saman.normal_transfer'),
                    $options
                );
                $response = json_decode($res->getBody()->getContents(), true);
                SamanTransactionDocument::insertBankResult(
                    $data['_id'],
                    $response,
                    $options['json']['trackerId']
                );
                $data[] = $response;
                sendTask(
                    config('rabbit.queue.priority'),
                    'insert_to_accounting_plan',
                    $data
                );
                Logger::info('transaction passed to accounting plan', ['transaction id' => $data['_id']]);
                break;
            } catch (ClientException $e) {
                if ($e->getCode() == '403'):
                    sendTask(
                        config('rabbit.queue.priority'),
                        'login'
                    );
                    sleep(parent::WAIT_FOR_LOGIN);
                else:
                    if (preg_match('/^70/', $data['tafzil'])) {
                        Passenger::updateWallet(
                            ['detail_code' => $data['tafzil']],
                            $data['amount'],
                            true
                        );
                    } else {
                        Driver::updateWallet(
                            ['detail_code' => $data['tafzil']],
                            $data['amount'],
                            true
                        );
                    }
                    SamanTransactionDocument::updateOne(
                        ['_id' => $data['_id']],
                        ['$set' => ['exception' => $e->getResponse()->getBody()->getContents()]],
                        ['upsert' => false]
                    );

                    throw new InvalidRequestException(
                        $e->getMessage() ?
                            $e->getResponse()->getBody()->getContents() :
                            'Gazzle Http faced an unknown problem'
                    );
                    break;
                endif;
            } catch (\Throwable $e) {
                SamanTransactionDocument::updateOne(
                    ['_id' => $data['_id']],
                    ['$set' => ['exception' => $e->getMessage()]],
                    ['upsert' => false]
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