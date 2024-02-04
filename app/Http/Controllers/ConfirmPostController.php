<?php

namespace App\Http\Controllers;

use App\Auth\AuthSocket;
use App\Services\TelegramLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConfirmPostController extends Controller
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

        $id = $request->post('postId') ?? null;
        $typeLock = $request->post('typeLock');

        if($id == null or $typeLock == null){
            $response[self::ERROR_MESSAGE] = 'Не переданы обязательные параметры!';
            return response()->json($response);
        }

        if(!isset(config('settings.typeLocks')[$typeLock])){
            $response[self::ERROR_MESSAGE] = 'Неизвестный тип блокировки!';
            return response()->json($response);
        }

        /** @var AuthSocket $guard */
        $guard = Auth::guard('auth-socket');

        if(!$guard->isLogin() or !$guard->isAdmin()){
            $response[self::ERROR_MESSAGE] = 'У вас нет прав подтвержать доказательства!';
            return response()->json($response);
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

        if(!$requestData['success']){
            $response[self::ERROR_MESSAGE] = 'Пост не найден!';
            return response()->json($response);
        }

        $lockData = $requestData['response']['data'][0];

        if((bool)$lockData['confirmed']){
            $response[self::ERROR_MESSAGE] = 'Доказательства к данному посту уже подтверждены!';
            return response()->json($response);
        }

        if($lockData['url'] == null){
            $response[self::ERROR_MESSAGE] = 'К данному видео не прикреплены доказательства!';
            return response()->json($response);
        }

        DB::table(config('settings.typeLocks')[$typeLock])->where('id', '=', $id)->update(['confirmed' => 1]);

        $response[self::SUCCESS] = true;
        $response[self::RESPONSE]['message'] = 'Вы успешно подтвердили данный пост!';

        $urlPost = config('settings.domain') . '?' . http_build_query(['postId' => $id, 'typePost' => $typeLock]);

        $logMessage = "✅ Подтверждение поста✅\n";
        $logMessage .= "Админ <b>{$guard->getData()['nick']}</b> подтвердил доказательсвта на посте\n\n";
        $logMessage .= "✏️ Ссылка на пост: <u>{$urlPost}</u>";

        TelegramLog::sendMessage($logMessage);

        return response()->json($response);
    }
}
