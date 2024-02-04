<?php

namespace App\Http\Controllers;

use App\Auth\AuthSocket;
use App\Services\MinecraftRcon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    const DEFAULT_RESPONSE = [
        'success' => false,
        'error_message' => null,
        'response' => [
            'message' => null,
            'two-factor' => false
        ]
    ];

    const SUCCESS = 'success';
    const ERROR_MESSAGE = 'error_message';
    const RESPONSE = 'response';

    public function logout(): ?JsonResponse
    {
        $response = self::DEFAULT_RESPONSE;
        $response[self::RESPONSE] = [];
        /** @var AuthSocket $guard */
        $guard = Auth::guard('auth-socket');

        if (!$guard->isLogin()) {
            $response[self::ERROR_MESSAGE] = 'Вы не авторизированы!';
            return response()->json($response);
        }

        $guard->destroy();
        $response[self::SUCCESS] = true;
        $response[self::RESPONSE]['message'] = 'Вы успешно вышли с аккаунта!';
        return response()->json($response);
    }

    public function getAccountData(Request $_): ?JsonResponse
    {
        $response = self::DEFAULT_RESPONSE;
        $response[self::RESPONSE] = [];
        /** @var AuthSocket $guard */
        $guard = Auth::guard('auth-socket');

        if (!$guard->isLogin()) {
            $response[self::ERROR_MESSAGE] = "Вы не авторизированы!";
            return response()->json($response);
        }

        $userData = $guard->getData();
        $response[self::SUCCESS] = true;
        $response[self::RESPONSE]['data'] = [
            'port' => $userData['port'],
            'portPrefix' => config('settings.ports')[$userData['port']]['prefix'],
            'nick' => $userData['nick'],
            'is_admin' => $guard->isAdmin()
        ];
        return response()->json($response);
    }

    public function login(Request $request): ?JsonResponse
    {
        $response = self::DEFAULT_RESPONSE;
        $credentials = $request->all();
        $code = $credentials['code'] ?? 0;

        /** @var AuthSocket $guard */
        $guard = Auth::guard('auth-socket');

        if ($guard->isActiveTwoFactor()) {
            if ($code == 0) {
                $response[self::RESPONSE]['two-factor'] = true;
            } else {
                if (!is_numeric($code) or (int)$code !== (int)$guard->getCodeTwoFactor()) {
                    $response[self::ERROR_MESSAGE] = "Вы ввели не верный код! ";
                    $guard->destroy();
                } else {
                    $guard->setTwoFactorValue();
                    $guard->setAuthValue();
                    $response[self::SUCCESS] = true;
                    $response[self::RESPONSE]['message'] = 'Вы успешно авторизировались!';
                }
            }
            return response()->json($response);
        }

        $password = $credentials['password'] ?? "";
        $nick = $credentials['nick'] ?? "";
        $port = $credentials['port'] ?? 0;

        if (!isset(config('settings.ports')[$port])) {
            $response[self::ERROR_MESSAGE] = "Неудалось определить порт сервера!";
            return response()->json($response);
        }

        $portData = config('settings.ports')[$port];

        if (mb_strlen($password) == 0 or mb_strlen($nick) == 0) {
            $response[self::ERROR_MESSAGE] = "Введите ник и пароль!";
            return response()->json($response);
        }

        $rcon = new MinecraftRcon();
        if (!$guard->isLogin()) {
            if (!$rcon->Connect($portData['ip'], $port, $portData['rcon'])) {
                $response[self::ERROR_MESSAGE] = "Неудалось соединиться с сервером, попробуйте позже";
            } else {
                $data = [
                    'type_request' => 'auth',
                    'data' => [
                        'type' => 'login',
                        'nick' => mb_strtolower($nick),
                        'password' => $password
                    ],
                ];

                $sendData = $rcon->Data($data);

                if ($sendData) {
                    if (!$sendData["success"]) {
                        $response[self::ERROR_MESSAGE] = $sendData["error_message"];
                    } else {
                        $code = $sendData['reply']['code_two_factor'] ?? 0;
                        $guard->login($nick, $port, $code);
                        if ($code !== null) return $this->login($request);

                        $response[self::SUCCESS] = true;
                        $response[self::RESPONSE]['message'] = 'Вы успешно авторизировались!' . json_encode(session()->all());
                    }
                } else {
                    $response[self::ERROR_MESSAGE] = "Неудалось отправить команду";
                }
            }
            return response()->json($response);
        }
        $response[self::ERROR_MESSAGE] = "Вы уже авторизированы!";
        return response()->json($response);
    }

}