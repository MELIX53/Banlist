<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramLog
{

    public static function sendMessage(string $message): void
    {
        try{
            $logData = config('settings.telegram-log');
            $apiUrl = "https://api.telegram.org/bot{$logData['token']}/sendMessage";
            Http::asForm()->post($apiUrl, ['chat_id' => $logData['chatId'], 'text' => $message, 'parse_mode' => 'HTML']);
        }catch (\Exception $exception){
            Log::notice("Неудачная попытка отправить сообщение в Телеграм\n" . $exception->getMessage());
        }
    }

}
