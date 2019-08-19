<?php

namespace Worker\Callbacks;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use PhpAmqpLib\Message\AMQPMessage;
use Worker\Abstracts\AbstractCallback;
use Worker\Exceptions\InvalidRequestException;
use Worker\Exceptions\RejectionException;
use Worker\Extras\Job;
use Worker\Extras\Logger;
use Worker\Extras\ShebaCheck;
use Worker\Extras\Timer;
use Worker\Extras\Token;
use Worker\Models\ShebaTransactionDocument;

class CheckShebaTransactionResultCallback extends AbstractCallback {
    public function __invoke(AMQPMessage $msg): AMQPMessage {
        Logger::setPath(config('log.block.path'));
        Logger::setDebug();
        $jobDate = Job::getJobDate($msg);

        if (! ((new Timer())->isBetween($jobDate, config('time.stop'))))
        {
            $this->wait($jobDate);
        }
        $data = Job::getJobData($msg);
        $token = (new Token())->make();
        $expire = time() + parent::EXPIRE_TIME;
        while(time() < $expire)
        {
            try {
                $options = new ShebaCheck($data);
                $options->setToken($token);
                $options = $options->toArray();
                $res = (new Client())->request(
                    'POST',
                    config('endpoints.saman.check_sheba'),
                    $options
                );
                $response = json_decode($res->getBody()->getContents(), true);
                $response['final_transactionStatus'] = $response['transactions'][0]['status'];
                ShebaTransactionDocument::insertBankResult(
                    $data['_id'],
                    $response,
                    $options['json']['trackerId']
                );
                $data = array_merge($data, $response);

                if ($data['transactionStatus'] != 'ACCEPTED')
                {
                    throw new RejectionException('Transfer Rejected [transactionStatus]="' .
                        $data['transactionStatus'] . '"');
                }

                if ($response['final_transactionStatus'] != 'TRANSFERRED')
                {
                    $date = date(
                        'Y-m-d H:i:s',
                        strtotime("$jobDate +" . config('time.restart'))
                    );
                    sendTask(
                        config('rabbit.queue.block'),
                        'check_sheba_transaction_result',
                        $data,
                        [],
                        [],
                        $date
                    );
                    break;
                }
                ShebaTransactionDocument::updateUserWallet($data);
                sendTask(
                    config('rabbit.queue.priority'),
                    'insert_to_accounting_plan',
                    $data
                );
                Logger::info('transaction passed to insert to accounting plan',
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
                        ['$set' => [
                            'exception' => $e->getResponse()->getBody()->getContents(),
                        ]]
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
                    ['$set' => ['exception' => $e->getMessage() . ' file: ' . $e->getFile() . ' line: ' . $e->getFile()]]
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