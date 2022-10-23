<?php

namespace App\Services\SendMessages;

use App\Services\BotVendor\Bot;
use App\Services\ReceiveMessages\Message as ReceiveMessagesMessage;

class Message
{
    protected $buttons;

    protected Bot $vendor;

    protected $text;

    public function __construct(Bot $vendor, $text)
    {
        $this->vendor = $vendor;
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setButtons($buttons)
    {
    }
}
