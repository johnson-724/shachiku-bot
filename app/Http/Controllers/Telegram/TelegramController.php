<?php

namespace App\Http\Controllers\Telegram;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\BotVendor\Telegram;
use App\Services\MessageService;

class TelegramController extends Controller
{
    public function __invoke(Request $request)
    {
        $bot = Telegram::generate();

        try {
            $messageService = new MessageService($bot, $request);
    
            $messageService->dispatchMessage();
    
            $messageService->sendMessage();
        } catch (\Throwable $th) {
            var_dump($th->getMessage(), $th->getTraceAsString());   
        }

        return response()->json('success');
    }
}
