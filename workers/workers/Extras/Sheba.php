<?php

namespace Worker\Extras;

use Worker\Exceptions\PropertyNotExistException;

/**
 * Class Sheba
 *
 * @package Worker\Extras
 *
 * @method string getToken()
 * @method setToken(string $value)
 * @method string getChannel()
 * @method setChannel(string $value)
 * @method string getCif()
 * @method setCif(string $value)
 * @method string getClientIp()
 * @method setClientIp(string $value)
 * @method string getDescription()
 * @method setDescription(string $value)
 * @method string getFactorNumber()
 * @method setFactorNumber(string $value)
 * @method string getIbanNumber()
 * @method setIbanNumber(string $value)
 * @method string getOwnerName()
 * @method setOwnerName(string $value)
 * @method string getSourceDepositNumber()
 * @method setSourceDepositNumber(string $value)
 * @method string getTrackerId()
 * @method setTrackerId(string $value)
 * @method string getTransferDescription()
 * @method setTransferDescription(string $value)
 * @method bool getSynchronous()
 * @method setSynchronous(bool $value)
 * @method array getHeaders()
 * @method setHeaders(array $value)
 */
class Sheba
{
    protected $amount;
    protected $channel;
    protected $cif;
    protected $clientIp;
    protected $description;
    protected $factorNumber;
    protected $ibanNumber;
    protected $ownerName;
    protected $sourceDepositNumber;
    protected $token;
    protected $trackerId;
    protected $transferDescription;

    protected $synchronous = TRUE;
    protected $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];

    public function __construct($data, $token = NULL)
    {
        foreach($data as $key => $value)
        {
            if (property_exists($this, $key))
            {
                if (in_array($key, ['ibanNumber']))
                {
                    $value = strtoupper($value);
                }

                $this->$key = $value;
            }
        }

        if ($token !== NULL)
        {
            $this->setToken($token);
        }
    }

    public function __call($name, $arguments)
    {
        $isGet = preg_match('/^get/i', $name);
        $isSet = preg_match('/^set/i', $name);

        if (! ($isGet || $isSet))
        {
            return FALSE;
        }
        $name = preg_replace('/^' . ($isGet ? 'get' : 'set') . '/i', '', $name);

        if (! property_exists($this, $name))
        {
            $name = lcfirst($name);
        }

        if (! property_exists($this, $name))
        {
            return FALSE;
        }

        if ($isSet)
        {
            $this->set($name, current($arguments));
        }
        else
        {
            return $this->get($name);
        }
    }

    protected function get($property)
    {
        return @$this->$property;
    }

    protected function set($property, $value)
    {
        $this->$property = $value;
    }

    protected function getBody()
    {
        $body ['json'] = array_diff_key(
            (array) get_object_vars($this),
            ['headers' => '', 'synchronous' => '']
        );
        $body ['headers'] = (array) $this->getHeaders();
        $body ['synchronous'] = (bool) $this->getSynchronous();

        return $body;
    }

    public function addHeaders($headers)
    {
        $this->headers += $headers;
    }

    public function toArray()
    {
        return $this->getBody();
    }

    public function __debugInfo()
    {
        return get_object_vars($this);
    }
}