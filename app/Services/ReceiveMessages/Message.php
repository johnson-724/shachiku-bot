<?php

namespace App\Services\ReceiveMessages;

use App\Services\BotVendor\Bot;

class Message
{
    private $message;

    private $vendor;

    private $text;

    private $lat;

    private $lon;

    public function __construct(Bot $vendor, array $message, string $text = '')
    {
        $this->vendor = $vendor;
        $this->message = $message;
        $this->text = $text;
    }

    public function getRawText()
    {
        return $this->text;
    }

    public function getFrom()
    {
        return $this->vendor->getUserIdentity();
    }


    /**
     * 緯度
     * 
     * @return mixed 
     */
    public function getLat(): float|null
    {
        return $this->lat;
    }

    /**
     * 經度
     * 
     * @return mixed 
     */
    public function getLon(): float|null
    {
        return $this->lon;
    }

    public function setLatAndLon($lat, $lon)
    {
        $this->lat = $lat;
        $this->lon = $lon;
    }
}
