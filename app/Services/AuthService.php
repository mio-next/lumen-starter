<?php

namespace App\Services;

use App\Models\User;
use App\Models\JWTEntity;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\UnencryptedToken;

class AuthService extends Service
{
    /**
     * @param string $jwt
     * @return JWTEntity|null
     */
    public static function getAuthenticateEntity(string $jwt): ?JWTEntity
    {
        $status = JWTService::validate($jwt);
        if (($status->status ?? false) === false) {
            Log::warning(join(':', [__METHOD__, $status->message]), compact('jwt'));

            return null;
        }

        /** @var UnencryptedToken $token */
        $token = $status->token;

        return User::find($token->claims()->get('uid', 0));
    }
}
