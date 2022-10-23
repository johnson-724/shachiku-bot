<?php

namespace App\Services\BotVendor;

use App\Services\ReceiveMessages\Message;
use App\Services\SendMessages\Message as SendMessaage;
use Illuminate\Http\Request;

abstract class Bot
{
    protected Message $receiveMessage;

    protected SendMessaage $sendMessage;

    protected $rawMessage;

    protected $from;

    protected $botName;

    public static function generate()
    {
        return new static();
    }

    public function getBotName()
    {
        return $this->botName;
    }

    public function getReceiveMessageText()
    {
        return $this->receiveMessage->getRawText();
    }

    /**
     * prepare bot message to send
     * 
     * @param SendMessaage $message 
     * @return SendMessaage 
     */
    abstract public function prepareSendMessage(SendMessaage $message): SendMessaage;

    /**
     * parse bot message
     * 
     * @param Request $request 
     * @return Message|null 
     */
    abstract public function parseMessage(Request $request): Message|null;

    public function getReceiveMessage(): Message
    {
        return $this->receiveMessage;
    }

    public function getSendMessage(): SendMessaage
    {
        return $this->sendMessage;
    }

    abstract public function launchRegister();

    /**
     * 
     * @return mixed 
     */
    abstract public function getUserIdentity();

    /**
     * 發送執行結果回覆
     * 
     * @return mixed 
     */
    abstract public function send();
}
