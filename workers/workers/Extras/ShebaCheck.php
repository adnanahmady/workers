<?php

namespace Worker\Extras;

/**
 * Class ShebaCheck
 *
 * @package Worker\Extras
 * @method string getToken()
 * @method setToken(string $value)
 * @method string getChannel()
 * @method setChannel(string $value)
 * @method string getCif()
 * @method setCif(string $value)
 * @method string getReferenceId()
 * @method setReferenceId(string $value)
 */
class ShebaCheck
{
    protected $referenceId;
    protected $channel;
    protected $cif;
    protected $token;

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
        $body ['json'] = array_diff_key((array) get_object_vars($this), ['headers' => '', 'synchronous' => '']);
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