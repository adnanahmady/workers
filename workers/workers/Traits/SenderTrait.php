<?php

namespace Workers\Traits;

use \Morilog\Jalali\Jalalian;
use GuzzleHttp\Client as Guzzle;

/**
 * Sender Trait
 */
trait SenderTrait
{
    public function getSamanTransactionOptions($data, $token) {
        return [
            'json' => [
                "additionalDocumentDesc" => $data["additionalDocumentDesc"],
                "amount" => $data["amount"],
                "channel" => $data["channel"],
                "cif" => $data["cif"],
                "clientIp" => $data["clientIp"],
                "destinationComment" => $data["destinationComment"],
                "destinationDeposit" => $data["destinationDeposit"],
                "referenceNumber" => $data["referenceNumber"],
                "sourceComment" => $data["sourceComment"],
                "sourceDeposit" => $data["sourceDeposit"],
                "token" => $token,
            ]
        ];
    }

    /**
     * Returns mixed array for send to RecPay Endpoint
     *
     * @param $RecPay
     * @return array
     */
    public function getRecPay($RecPay, $callback) {
        $data = $callback($RecPay);

        $recpay = [
            "RequestId" => (string) $data['RequestId'],
            "Description" =>  '',
            "DetailCode2" => (int) 101701,
            "Desc" => $data['name'],
            "RPCode" => 2,
            "DevId" => "200",
            "strDate" => Jalalian::forge('now')->format('%Y/%m/%d'),
            "CheqNo" => $data['CheqNo'],
            "RecPayItems" => [[
                "RPID" => (string) preg_match('/^60/', $data['tafzil']) ? 101102 : 101101,
                "DetailCode1" =>  (int) $data['tafzil'],
                "Price" => (int) isset($data['amount']) ? $data['amount'] : $data['Amount'],
                "RowDesc" => 'پرداخت آنلاین توسط سایت'
            ]]
        ];

        return $recpay;
    }

    /**
     * Sends a POST request to Url every half-second
     * until gets response or time of $wait gets off
     *
     * @param $url
     * @param $data
     * @param null $status
     * @param int $wait
     * @return bool|string
     */
    public function curlRequest($url, $data, $status = null, $wait = 3)
    {
        $time = microtime(true);
        $expire = $time + $wait;

        while(microtime(true) < $expire)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            try {
                list($header, $body) = explode("\r\n\r\n", $response, 2);
            } catch (\Throwable $e) {
                return FALSE;
            }

            $bodyContent = trim($body, '"');

            if(!$response)
            {
                return FALSE;
            }
            if($status === null)
            {
                if($httpCode < 400)
                {
                    return $bodyContent;
                }
                else
                {
                    return FALSE;
                }
            }
            elseif($status == $httpCode)
            {
                return $bodyContent;
            }

            sleep(0.5);
        }

        return FALSE;
    }

    /**
     * Sends a POST request to Url every half-second
     * until gets response or time of $wait gets off
     *
     * @param $url
     * @param $data
     * @param null $status
     * @param int $wait
     * @return bool|string
     */
    public function GuzzleRequest(
        $url,
        $data,
        $exceptionCallback,
        $additionalDataCallback,
        $method = 'POST',
        $status = null,
        $wait = 3
    )
    {
        $time = microtime(true);
        $expire = $time + $wait;

        while(microtime(true) < $expire)
        {
            try {
                $data = $additionalDataCallback($data);
                $response = (new Guzzle())->request(
                    $method,
                    $url,
                    $data
                );

                return $response->getBody()->getContents();
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                    $exceptionCallback($e);
            }

            sleep(1);
        }

        return FALSE;
    }

    /**
     * Sends a POST request to Url every half-second
     * until gets response or time of $wait gets off
     *
     * @param $url
     * @param $data
     * @param null $status
     * @param int $wait
     * @return bool|string
     */
    public function kohanaRequest($url, $data, $status = null, $wait = 20)
    {
        $time = microtime(true);
        $expire = $time + $wait;
        $method = explode('/', $url);
        $data = isset($data['type']) ?
            $this->{'get' . end($method)}($data) :
            $data;

        while(microtime(true) < $expire)
        {
            $request = \Request::factory($url)
                ->method('POST')
                ->post($data);

            $response = $request->execute();
            $httpCode = $response->status();
            $bodyContent = trim($response->body(), '"');

            if(!$response)
            {
                return FALSE;
            }
            if($status === null)
            {
                if($httpCode < 400)
                {
                    return $bodyContent;
                }
                else
                {
                    return FALSE;
                }
            }
            elseif($status == $httpCode)
            {
                return $bodyContent;
            }

            sleep(1);
        }

        return FALSE;
    }

    /**
     * Sends a GET request to Url every half-second
     * until gets response or time of $wait gets off
     *
     * @param $url
     * @param $requestID
     * @param int $wait
     * @return bool|string
     */
    public function senderGetResponse($url, $requestID, $wait = 3) {
        $query = http_build_query([
            'requestId' => $requestID
        ]);

        $time = microtime(true);
        $expire = $time + $wait;

        // we are the child
        while(microtime(true) < $expire)
        {
            try {
                $content = file_get_contents($url . '?' . $query);
                $error = false;
            } catch (\Throwable $e) {
                $error = true;
            }

            $bodyContent = trim($content, '"');

            if($error == false)
            {
                return $bodyContent;
            }

            sleep(0.5);
        }

        return FALSE;
    }
}