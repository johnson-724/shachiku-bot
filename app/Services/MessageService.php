<?php

namespace App\Services;

use App\Services\BotVendor\Bot;
use App\Services\Conversation\Conversation;
use App\Services\Conversation\RegisterFlow;
use App\Services\ReceiveMessages\CommandMessage;
use App\Services\ReceiveMessages\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MessageService
{
    protected $bot;

    protected $waitSendMessage;

    protected $conversation;

    protected $message;

    /**
     * 
     * @var string
     */
    const CONVERSATIONS = [
        'register' => RegisterFlow::class
    ];

    public function __construct(Bot $bot, Request $request)
    {
        $this->bot = $bot;

        $this->message = $this->bot->parseMessage($request);
    }

    public function dispatchMessage()
    {
        if ($this->message instanceof CommandMessage) {
            $this->lanuchCommand();

            return;
        }

        if ($this->message instanceof Message) {
            $this->handleMessage();

            return;
        }
    }


    public function sendMessage()
    {
        $this->bot->send();
    }

    public function lanuchCommand()
    {
        switch ($this->message->getRawText()) {
            case 'register':
                $this->conversation = 'register';
                Cache::put(
                    $this->conversationKey(),
                    [
                        'type' => $this->conversation
                    ]
                );

                $this->launchRegister();

                break;
            case 'break':
                Cache::forget($this->conversationKey());

                break;
            default:
                break;
        }
    }

    public function handleMessage()
    {
        if ($conversation = Cache::get($this->conversationKey())) {
            $flow = self::CONVERSATIONS[$conversation['type']];

            $flow = new $flow($this->bot);

            $flow->handle($this->message);

            $flow->next();

            if ($flow->isDone()) {
                Cache::forget($this->conversationKey());
            }

            return;
        }

        // TODO: 其他非指令 message 
        // switch ($this->message->getRawText()) {
        //     default:
        //         # code...
        //         break;
        // }
    }

    public function conversationKey()
    {
        return $this->bot->getBotName() . "-conversation-" . $this->bot->getUserIdentity();
    }

    /**
     * 
     * @param Request $request 
     * @return void 
     */
    public function launchRegister()
    {
        $flow = new RegisterFlow($this->bot);

        $flow->launch();

        $flow->next();

        return;
    }
}
