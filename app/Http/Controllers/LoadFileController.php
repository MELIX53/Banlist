<?php

namespace App\Http\Controllers;

use App\Auth\AuthSocket;
use App\Services\TelegramLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoadFileController extends Controller
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

    public function process(Request $request){
        $response = self::DEFAULT_RESPONSE;

        /** @var AuthSocket $guard */
        $guard = Auth::guard('auth-socket');

        if(!$guard->isLogin()){
            $response[self::ERROR_MESSAGE] = 'Вы не авторизированы!';
            return response()->json($response);
        }

        $typeLock = $request->post('typeLock');
        $id = $request->post('id');

        if($typeLock == null or $id == null){
            $response[self::ERROR_MESSAGE] = 'Не переданы все нужные данные!';
            return response()->json($response);
        }

        if(!isset(config('settings.typeLocks')[$typeLock])){
            $response[self::ERROR_MESSAGE] = 'Неизвестный тип блокировки!';
            return response()->json($response);
        }

        $accountData = $guard->getData();

        $params = [
            'id' => $id,
            'tables' => [
                $typeLock => true,
                $accountData['port'] => true
            ],
        ];

        /** @var DataController $dataController */
        $dataController = app(DataController::class);
        $requestData = $dataController->getAllPunishments(new Request($params))->getData(true);

        if(!$requestData['success']){
            $response[self::ERROR_MESSAGE] = 'Неудалось найти запись!';
            return response()->json($response);
        }

        $lockData = $requestData['response']['data'][0];

        if(
            mb_strtolower($lockData['punishedName']) !== mb_strtolower($accountData['nick']) or
            $lockData['port'] != $accountData['port'] or
            $lockData['url'] != null or
            $lockData['confirmed']
        ){
            $response[self::ERROR_MESSAGE] = 'Неудалось определелись запись для изменений!';
            return response()->json($response);
        }


        $allowTypes = [
            'image/png',
            'image/jpeg',
            'video/mp4',
            'image/gif',
        ];

        $error = [];
        $files = [];
        $size = 0;
        foreach ($request->allFiles() as $fileName => $fileData){
            $file = $request->file($fileName);
            if($file == null) continue;
            Log::notice($file->getClientOriginalName());
            $fileType = $file->getMimeType();

            if(!in_array($fileType, $allowTypes)){
                $error[] = $fileType;
                continue;
            }

            $size += $file->getSize();
            $files[] = $file;
        }

        if(count($error) > 0){
            $response[self::ERROR_MESSAGE] = 'В ваших файлах присутствует файл с запрещенным типом для загрузки<br>' . implode("<br>", $error);
            return response()->json($response);
        }

        if(count($files) > 8){
            $response[self::ERROR_MESSAGE] = 'Вы не можете загружать больше 8 файлов!';
            return response()->json($response);
        }

        if($size >= 209715200){
            $response[self::ERROR_MESSAGE] = 'Максимальный размер файлов 200МБ!';
            return response()->json($response);
        }

        $urls = [];
        foreach ($files as $file){
            $cloud = Http::attach('file', file_get_contents($file->path()), mt_rand(1, 100000) . $file->getClientOriginalName())->post(config('settings.upload'))->json();
            Log::notice($cloud['error_message']);
            if($cloud['success']){
                foreach ($cloud['response']['url'] as $url){
                    $urls[] = ['url' => $url, 'type' => $file->getMimeType()];
                }
            }
        }

        DB::table(config('settings.typeLocks')[$typeLock])->where('id', '=', $id)->update(['url' => json_encode($urls)]);

        $urlPost = config('settings.domain') . '?' . http_build_query(['postId' => $id, 'typePost' => $typeLock]);

        $logMessage = "⚠️ Нужно проверить.. ⚠️\n";
        $logMessage .= "Игрок <b>{$lockData['punishedName']}</b> загрузил доказательства, нужно проверить\n\n";
        $logMessage .= "Ссылка на пост: <u>{$urlPost}</u>";
        TelegramLog::sendMessage($logMessage);

        $response[self::SUCCESS] = true;
        $response[self::RESPONSE]['message'] = 'Вы успешно загрузили доказательства на сайт!';
        return response()->json($response);
    }
}
