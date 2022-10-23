<?php

namespace App\Services\Conversation;

use App\Models\User;
use App\Services\BotVendor\Bot;
use App\Services\EipService;
use App\Services\EipVendor\Nueip;
use App\Services\ReceiveMessages\LocationMessage;
use App\Services\SendMessages\ButtonMessage;
use App\Services\SendMessages\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RegisterFlow
{
    protected $bot;

    const FLOW = [
        'username',
        'system',
        'account',
        'password',
        'companyCode',
        'time',
        'location'
    ];

    protected $alreadyToNext = false;

    // TODO: bot message
    protected $replayMessage;

    protected $step;

    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
    }

    public function registerKey()
    {
        return $this->bot->getBotName() . '-register-' . $this->bot->getUserIdentity();
    }

    public function handle()
    {
        $flowData = Cache::get($this->registerKey());

        $this->step = $flowData['step'];

        $method = self::FLOW[$flowData['step']];

        return $this->$method();
    }

    public function launch()
    {
        Cache::forget($this->registerKey());

        Cache::put(
            $this->registerKey(),
            [
                'lauch' => true,
                'step' => -1
            ]
        );

        $this->alreadyToNext = true;

        return;
    }

    public function next()
    {
        $flowData = Cache::get($this->registerKey());
        if ($this->alreadyToNext) {
            $flowData['step']++;
            $this->step = $flowData['step'];

            if (count(self::FLOW) <= $this->step) {
                $this->done();

                return;
            }

            $method = self::FLOW[$this->step];

            Cache::put($this->registerKey(), $flowData);

            return $this->$method(false);
        }
    }

    public function username($commit = true)
    {
        if (!$commit) {
            $this->bot->prepareSendMessage(
                new Message($this->bot, 'step ' . ($this->step + 1) . ' : 輸入名稱')
            );

            return;
        }

        if (empty($this->bot->getReceiveMessageText())) {
            $this->bot->prepareSendMessage(
                new Message($this->bot, 'step ' . ($this->step + 1) . ' : 輸入名稱錯誤')
            );

            return;
        }

        $cahce = Cache::get($this->registerKey());
        $cahce['username'] = $this->bot->getReceiveMessageText();

        Cache::put($this->registerKey(), $cahce);

        $this->alreadyToNext = true;
    }

    public function system($commit = true)
    {
        \Log::info('system', [$this->bot->getReceiveMessage()]);
        if (!$commit) {
            $message = new ButtonMessage($this->bot, 'step ' . ($this->step + 1) . ' : 選擇 eip');

            $message->setButtons([
                Nueip::VENDOR_NAME => Nueip::VENDOR_NAME
            ]);

            $this->bot->prepareSendMessage($message);

            return;
        }

        if (empty($this->bot->getReceiveMessageText())) {
            $this->bot->prepareSendMessage(
                new Message($this->bot, 'step ' . ($this->step + 1) . ' : 輸入 eip 名稱錯誤')
            );

            return;
        }

        if (empty(EipService::getFromName($this->bot->getReceiveMessageText()))) {
            $this->bot->prepareSendMessage(
                new Message($this->bot, 'step ' . ($this->step + 1) . ' : 輸入 eip 名稱不支援')
            );

            return;
        }

        $cahce = Cache::get($this->registerKey());
        $cahce['systemName'] = $this->bot->getReceiveMessageText();

        Cache::put($this->registerKey(), $cahce);

        $this->alreadyToNext = true;
    }

    public function account($commit = true)
    {
        if (!$commit) {
            $this->bot->prepareSendMessage(
                new Message($this->bot, 'step ' . ($this->step + 1) . ' : 輸入 account')
            );

            return;
        }

        if (empty($this->bot->getReceiveMessageText())) {
            $this->bot->prepareSendMessage(
                new Message($this->bot, 'step ' . ($this->step + 1) . ' : 輸入 account 錯誤')
            );

            return;
        }

        $cahce = Cache::get($this->registerKey());
        $cahce['account'] = $this->bot->getReceiveMessageText();

        Cache::put($this->registerKey(), $cahce);

        $this->alreadyToNext = true;
    }


    public function password($commit = true)
    {
        if (!$commit) {
            $this->bot->prepareSendMessage(
                new Message($this->bot, 'step ' . ($this->step + 1) . ' : 輸入 password')
            );

            return;
        }

        if (empty($this->bot->getReceiveMessageText())) {
            $this->bot->prepareSendMessage(
                new Message($this->bot, 'step ' . ($this->step + 1) . ' : 輸入 password 錯誤')
            );

            return;
        }


        $cahce = Cache::get($this->registerKey());
        $cahce['password'] = $this->bot->getReceiveMessageText();

        Cache::put($this->registerKey(), $cahce);

        $this->alreadyToNext = true;
    }


    public function companyCode($commit = true)
    {
        if (!$commit) {
            $this->bot->prepareSendMessage(
                new Message($this->bot, 'step ' . ($this->step + 1) . ' : 輸入 companyCode')
            );

            return;
        }

        if (empty($this->bot->getReceiveMessageText())) {
            $this->bot->prepareSendMessage(
                new Message($this->bot, 'step ' . ($this->step + 1) . ' : 輸入 companyCode 錯誤')
            );

            return;
        }


        $cahce = Cache::get($this->registerKey());
        $cahce['companyCode'] = $this->bot->getReceiveMessageText();

        Cache::put($this->registerKey(), $cahce);

        $this->alreadyToNext = true;
    }

    public function time($commit = true)
    {
        if (!$commit) {
            $this->bot->prepareSendMessage(
                new Message($this->bot, 'step ' . ($this->step + 1) . ' : 輸入 time')
            );

            return;
        }

        if (empty($this->bot->getReceiveMessageText())) {
            $this->bot->prepareSendMessage(
                new Message($this->bot, 'step ' . ($this->step + 1) . ' : 輸入 time 錯誤')
            );

            return;
        }

        try {
            $time = Carbon::parse($this->bot->getReceiveMessageText())->format('H:i');
        } catch (\Throwable $th) {
            $this->bot->prepareSendMessage(
                new Message($this->bot, 'step ' . ($this->step + 1) . ' : 輸入 time 格式錯誤 (H:i)')
            );

            return;
        }

        $cahce = Cache::get($this->registerKey());
        $cahce['time'] = $time;

        Cache::put($this->registerKey(), $cahce);

        $this->alreadyToNext = true;
    }

    public function location($commit = true)
    {
        if (!$commit) {
            $this->bot->prepareSendMessage(
                new Message($this->bot, 'step ' . ($this->step + 1) . ' : 定位 location (輸入隨意文字跳過)')
            );

            return;
        }

        $cahce = Cache::get($this->registerKey());
        $cahce['location'] = [
            'lat' => $this->bot->getReceiveMessage()->getLat(),
            'lon' => $this->bot->getReceiveMessage()->getLon()
        ];

        Cache::put($this->registerKey(), $cahce);

        $this->alreadyToNext = true;
    }

    public function done()
    {
        $data = Cache::get($this->registerKey());

        User::create([
            'name' => $data['username'],
            'account' => $data['account'],
            'password' => $data['password'],
            'location' => json_encode($data['location']),
            'time_to_work' => $data['time'],
            'company_code' => $data['companyCode'],
            'eip_vendor' => $data['systemName'],
            'bot_chat_id' => $this->bot->getUserIdentity(),
            'bot_vendor_id' => $this->bot->getUserIdentity(),
            'bot_vendor' => $this->bot->getBotName(),
        ]);

        $this->bot->prepareSendMessage(
            new Message($this->bot, '完成 !')
        );

        Cache::forget($this->registerKey());
    }

    public function isDone()
    {
        return !Cache::has($this->registerKey());
    }
}
