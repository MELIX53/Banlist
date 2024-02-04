<?php

namespace App\Http\Controllers;

use App\Auth\AuthSocket;
use App\Services\TelegramLog;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RemovePostController extends Controller
{

    const DEFAULT_RESPONSE = [
        'success' => false,
        'error_message' => null,
        'response' => [
            'message' => '',
        ]
    ];

    const SUCCESS = 'success';
    const ERROR_MESSAGE = 'error_message';
    const RESPONSE = 'response';


    public function process(Request $request): JsonResponse
    {
        $response = self::DEFAULT_RESPONSE;

        $id = $request->post('postId');
        $typeLock = $request->post('typeLock');

        if ($id == null or $typeLock == null) {
            $response[self::ERROR_MESSAGE] = 'Не переданы обязательные параметры';
            return response()->json($response);
        }

        if (!isset(config('settings.typeLocks')[$typeLock])) {
            $response[self::ERROR_MESSAGE] = 'Неизвестный тип блокировки!';
            return response()->json($response);
        }

        $removeKey = $request->post('removeKey');

        /** @var AuthSocket $guard */
        $guard = Auth::guard('auth-socket');

        if ($removeKey == null) {
            if (!$guard->isLogin() or !$guard->isAdmin()) {
                $response[self::ERROR_MESSAGE] = 'Вы должны быть Админом чтобы удалять посты!';
                return response()->json($response);
            }
        } else {
            if ($removeKey !== config('settings.key-remove-post')) {
                $response[self::ERROR_MESSAGE] = 'Передан не верный ключ для удаления постов!';
                return response()->json($response);
            }
        }

        $params = [
            'id' => $id,
            'tables' => [
                $typeLock => true,
            ],
        ];
        foreach (config('settings.ports') as $port => $_) {
            $params['tables'][$port] = true;
        }

        /** @var DataController $dataController */
        $dataController = app(DataController::class);
        $requestData = $dataController->getAllPunishments(new Request($params))->getData(true);

        if (!$requestData['success']) {
            $response[self::ERROR_MESSAGE] = 'Неудалось найти запись!';
            return response()->json($response);
        }

        $lockData = $requestData['response']['data'][0];

        if ($lockData['url'] !== null) {
            $urlDatum = json_decode($lockData['url'], true);

            $requestURL = [];
            foreach ($urlDatum as $urlData) {
                $urlImplode = explode("/", $urlData['url']);
                $requestURL[] = $urlImplode[array_key_last($urlImplode)];
            }
            Log::notice(implode(' ', $requestURL));

            $cloudResponse = Http::asForm()->post(config('settings.remove'), $requestURL);

            if (!$cloudResponse->successful()) {
                $response[self::ERROR_MESSAGE] = 'Неудалось соединиться с облаком!';
                return response()->json($response);
            }

            $removeData = $cloudResponse->json();

            if (!$removeData['success']) {
                $response[self::ERROR_MESSAGE] = 'Ошибка на стороне облака: ' . $removeData[self::ERROR_MESSAGE];
                return response()->json($response);
            }
        }

        DB::table(config('settings.typeLocks')[$typeLock])->delete($id);

        $logMessage = "❌ Удаление поста ❌\n\n";
        $logMessage .= "Администратор {$guard->getData()['nick']} удалил пост\n\n";

        $logMessage .= "<u>📜 Информация о посте 📜</u>" . "\n";
        $logMessage .= "Ник нарушителя: <b>{$lockData['opponentName']}</b>\n";
        $logMessage .= "Ник карателя: <b>{$lockData['punishedName']}</b>\n";
        if($lockData['pardoned'] !== null){
            $logMessage .= "Снял наказание: <b>{$lockData['pardoned']}</b>\n";
        }
        $logMessage .= "Причина: <b>{$lockData['reason']}</b>\n";
        $logMessage .= "Доказательства: <b>" . ($lockData['url'] == null ? "Отсуствуют" : "Загружено " . count(json_decode($lockData['url'], true)) . " файлов") . "</b>\n\n";

        $logMessage .= "<code>Время наказания: {$lockData['timeGenerated']}</code>\n";
        if ($lockData['timeLocking'] !== null) {
            $logMessage .= "<code>Блокировка до: {$lockData['timeLocking']}</code>\n";
        }
        $logMessage .= "\n";

        $logMessage .= "<u>[{$lockData['portPrefix']}]</u>";

        TelegramLog::sendMessage($logMessage);

        $response[self::SUCCESS] = true;
        $response[self::RESPONSE]['message'] = 'Вы успешно успешно удалили пост';

        return response()->json($response);
    }
}
