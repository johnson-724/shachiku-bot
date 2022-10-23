<?php

namespace App\Services\BotVendor;

use App\Services\ReceiveMessages\ButtonCbMessage;
use App\Services\ReceiveMessages\CommandMessage;
use App\Services\ReceiveMessages\LocationMessage;
use App\Services\ReceiveMessages\Message;
use App\Services\RegisterMessage;
use App\Services\SendMessages\ButtonMessage;
use App\Services\SendMessages\Message as SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram as FacadesTelegram;

class Telegram extends Bot implements RegisterMessage
{
    protected Message $receiveMessage;

    protected SendMessage $sendMessage;

    protected $rawMessage;

    protected $from;

    protected $botName = 'telegram';

    public function parseMessage(Request $request): Message|null
    {
        $this->rawMessage = $request->all();

        if (
            !empty($this->rawMessage['message']['entities'])
            && $this->rawMessage['message']['entities'][0]['type'] === 'bot_command'
        ) {
            $this->from = $this->rawMessage['message']['from'];
            $text = explode('/', $this->rawMessage['message']['text']);
            $this->receiveMessage = new CommandMessage($this, $this->rawMessage, $text[1]);

            return $this->receiveMessage;
        } elseif (isset($this->rawMessage['message']['location'])) {
            $this->from = $this->rawMessage['message']['from'];
            $this->receiveMessage = new LocationMessage($this, $this->rawMessage);

            $this->receiveMessage->setLatAndLon($this->rawMessage['message']['location']['latitude'], $this->rawMessage['message']['location']['longitude']);

            return $this->receiveMessage;
        } else if (isset($this->rawMessage['message']['text'])) {
            $this->from = $this->rawMessage['message']['from'];
            $this->receiveMessage = new Message($this, $this->rawMessage, $this->rawMessage['message']['text']);

            return $this->receiveMessage;
        }else if (isset($this->rawMessage['callback_query'])){
            $this->from = $this->rawMessage['callback_query']['from'];

            $this->receiveMessage = new ButtonCbMessage($this, $this->rawMessage, $this->rawMessage['callback_query']['data']);

            return $this->receiveMessage;
        }

        return null;
    }

    public function prepareSendMessage(SendMessage $message): SendMessage
    {
        $this->sendMessage = $message;

        return $message;
    }

    public function launchRegister()
    {
        $identity = $this->getUserIdentity();

        $key = $this->getBotName() . '-register-' . $identity;

        Cache::forget($key);

        Cache::put(
            $key,
            [
                'lauch' => true,
                'step' => 0
            ],
            180
        );
    }

    /**
     * 
     * @return string|int
     */
    public function getUserIdentity(): string|int
    {
        return $this->from['id'];
    }

    public function send()
    {
        if (!empty($this->sendMessage)) {
            $message = [
                'chat_id' => $this->getUserIdentity(),
                'text' => $this->sendMessage->getText(),
            ];

            if ($this->sendMessage instanceof ButtonMessage) {
                $buttons = [];
                foreach ($this->sendMessage->getButtons() as $key => $value) {
                    $buttons[] = Keyboard::inlineButton([
                        'text' => $key, 'callback_data' => $value,
                    ]);
                }

                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(...$buttons);

                $message['reply_markup'] = $keyboard;
            }

            FacadesTelegram::setAsyncRequest(false)
                ->sendMessage($message);
        }
    }
}
