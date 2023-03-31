<?php

namespace App\Services;

use App\Models\JWTEntity;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\Validator;

class JWTService extends Service
{
    /**
     * @param JWTEntity $entity
     * @param int $ttl
     * @param array $claims
     * @return Token|null
     * @throws \Exception
     */
    public static function issue(JWTEntity $entity, int $ttl = 0, array $claims = []): ?Token
    {
        $ttl = $ttl ?: config('auth.jwt.ttl', 1440);
        $builder = new Token\Builder(new JoseEncoder(), ChainedFormatter::default());
        $now = new \DateTimeImmutable('now', new \DateTimeZone('PRC'));
        $secret = InMemory::plainText(config('app.key', str_repeat('0', 32)));
        $signer = new Sha256();

        return $builder
            ->issuedBy(config('app.name'))
            ->issuedAt($now)
            ->permittedFor(config('app.url'))
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify("+{$ttl} minute"))
            ->withClaim('uid', $entity->getKey())
            ->withClaim('ext', $claims)
            ->getToken($signer, $secret);
    }

    /**
     * @param string $jwt
     * @return \stdClass
     */
    public static function validate(string $jwt): object
    {
        $bag = new class {
            public $message = 'Fail';
            public $status = false;
            /** @var Token $token */
            public $token = null;
        };


        try {
            $bag->token = $token = (new Token\Parser(new JoseEncoder))->parse($jwt);
            $validator = new Validator();
            if (!$validator->validate($token, new IssuedBy(config('app.name', '')))) {
                throw new \Exception('[I] Invalid token');
            }

            if (!$validator->validate($token, new PermittedFor(config('app.url', '')))) {
                throw new \Exception('[P] Invalid token');
            }

            $clock = new FrozenClock(new \DateTimeImmutable('now', new \DateTimeZone('PRC')));
            if (!$validator->validate($token, new StrictValidAt($clock))) {
                throw new \Exception('Token expired.');
            }

            $bag->status = true;
            $bag->message = 'OK';
        } catch (\Exception $except) {
            $bag->message = $except->getMessage();

            // 纯工具包，不记录日志了。
            // Log::warning('Parse JWT string fail:' . $except->getMessage(), compact('jwt'));
        }

        return $bag;
    }
}
