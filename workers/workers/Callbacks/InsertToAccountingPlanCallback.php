<?php
/**
 * contains InsertToAccountingPlanCallback Class
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
namespace Worker\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;
use Worker\Abstracts\AbstractCallback;
use Worker\Exceptions\InvalidFieldException;
use Worker\Exceptions\InvalidRequestException;
use Worker\Extras\Job;
use Worker\Extras\Logger;
use Worker\Models\Flag;
use Worker\Models\SamanTransactionDocument;
use Worker\Models\Worker;
use Worker\Reflector\Reflector;
use Worker\Traits\SenderTrait;
use GuzzleHttp\Client as Guzzle;

class InsertToAccountingPlanCallback extends AbstractCallback {
    use SenderTrait;

    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $data = Job::getJobData($msg);

        $options = [];
        $options['synchronous'] = true;
        $options['headers'] = $this->getGuzzleHeaders();
        $options['json'] = $this->getRecPay($data, function ($data)
        {
            $data['CheqNo'] = substr(str_replace('-', '', Uuid::uuid4()->toString()), 15);

            return $data;
        });

        $expire = time(true) + parent::EXPIRE_TIME;
        while(time(true) < $expire)
        {
            try {
                $options['json']['RequestId'] = Uuid::uuid4()->toString();
                $res = (new Guzzle())->request(
                    'POST',
                    config('endpoints.accounting_plan.recpay'),
                    $options
                );
                $content = [];
                $response = json_decode($res->getBody()->getContents(), true);
                $content['sabt_dar_sanadpardaz_(ID)'] = $response['Id'];
                $content['sabt_dar_sanadpardaz_response'] = $response;
                $result = SamanTransactionDocument::updateOne(
                    ['_id' => $data['_id']],
                    ['$set' => $content],
                    ['upsert' => false]
                );
//                Logger::info('data upserted', $result->getUpsertedCount());
                break;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                SamanTransactionDocument::updateOne(
                    ['_id' => $data['_id']],
                    ['$set' => ['exception_sanadpardaz' => [
                        'message' => $e->getResponse()->getBody()->getContents(),
                        'trace' => $e->getTraceAsString()
                    ]]],
                    ['upsert' => false]
                );

                throw new InvalidRequestException(
                    $e->getMessage() ?
                        $e->getResponse()->getBody()->getContents() :
                        'Gazzle Http faced an unknown problem'
                );
            } catch (\Throwable $e) {
                SamanTransactionDocument::updateOne(
                    ['_id' => $data['_id']],
                    ['$set' => ['exception_sanadpardaz' => [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]]],
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