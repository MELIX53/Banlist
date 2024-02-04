<?php

namespace App\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthSocket implements Guard
{
    use GuardHelpers;

    public Request $request;
    public string $token;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function isLogin(): bool
    {
        return session('auth', false);
    }

    public function isActiveTwoFactor(): bool
    {
        return !($this->getCodeTwoFactor() == 0);
    }

    public function isAdmin()
    {
        $data = $this->getData();
        return (DB::table('table_admins')->whereRaw('LOWER(nick) = LOWER(?)', [$data['nick']])->first()) !== null;
    }

    public function getCodeTwoFactor(): ?int
    {
        return session()->get('two-factor');
    }

    public function getData(): array
    {
        return session()->all();
    }

    public function setTwoFactorValue(int $value = 0): void
    {
        session()->put('two-factor', $value);
    }

    public function setAuthValue(bool $value = true): void
    {
        session()->put('auth', $value);
    }

    public function destroy(): void
    {
        session()->flush();
    }

    public function login(string $nick, int $port, int $code = 0): void
    {
        session([
            'auth' => ($code == 0),
            'nick' => $nick,
            'port' => $port,
            'two-factor' => $code
        ]);
    }

    public function user(): ?Authenticatable
    {
        return null;
    }

    public function validate(array $credentials = []): bool
    {
        return true;
    }
}