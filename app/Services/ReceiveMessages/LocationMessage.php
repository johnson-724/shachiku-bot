<?php

namespace App\Services\ReceiveMessages;

use App\Services\BotVendor\Bot;

class LocationMessage extends Message
{

    private $lat;

    private $lon;

    /**
     * 緯度
     * 
     * @return mixed 
     */
    public function getLat(): float
    {
        return $this->lat;
    }

    /**
     * 經度
     * 
     * @return mixed 
     */
    public function getLon(): float
    {
        return $this->lon;
    }

    public function setLatAndLon($lat, $lon)
    {
        $this->lat = $lat;
        $this->lon = $lon;
    }
}
