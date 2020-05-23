<?php

declare(strict_types=1);
/**
 * This file is part of qbhy/hyperf-auth.
 *
 * @link     https://github.com/qbhy/hyperf-auth
 * @document https://github.com/qbhy/hyperf-auth/blob/master/README.md
 * @contact  qbhy0715@qq.com
 * @license  https://github.com/qbhy/hyperf-auth/blob/master/LICENSE
 */
namespace Qbhy\HyperfAuth\Guard;

use Doctrine\Common\Cache\Cache;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Str;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\UserProvider;
use Qbhy\SimpleJwt\Exceptions\JWTException;
use Qbhy\SimpleJwt\JWTManager;

class JwtGuard extends AbstractAuthGuard
{
    /**
     * @var JWTManager
     */
    protected $jwtManager;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * JwtGuardAbstract constructor.
     */
    public function __construct(
        array $config,
        string $name,
        UserProvider $userProvider,
        RequestInterface $request
    ) {
        parent::__construct($config, $name, $userProvider);
        $secret = $config['secret'] ?? 'secret';
        $encoder = $config['encoder'] ?? null;
        $cache = $config['cache'] ?? null;
        $this->jwtManager = new JWTManager($secret, $encoder, $cache instanceof Cache ? $cache : null);
        $this->request = $request;
    }

    public function parseToken()
    {
        $header = $this->request->header('Authorization', '');
        if (Str::startsWith($header, 'Bearer ')) {
            return Str::substr($header, 7);
        }

        if ($this->request->has('token')) {
            return $this->request->input('token');
        }

        return null;
    }

    public function check($token = null): bool
    {
        return $this->user($token) instanceof Authenticatable;
    }

    public function guest($token = null): bool
    {
        return ! $this->check($token);
    }

    public function login(Authenticatable $user)
    {
        return $this->jwtManager->make(['uid' => $user->getKey()])->token();
    }

    public function user(?string $token = null): ?Authenticatable
    {
        try {
            $token = $token ?? $this->parseToken();

            if ($token) {
                $jwt = $this->jwtManager->parse($token);
                $uid = $jwt->getPayload()['uid'] ?? null;
                return $uid ? $this->userProvider->retrieveByCredentials($uid) : null;
            }
            return null;
        } catch (JWTException $exception) {
            return null;
        }
    }

    /**
     * 刷新 token，旧 token 会失效.
     * @throws \Qbhy\SimpleJwt\Exceptions\InvalidTokenException
     * @throws \Qbhy\SimpleJwt\Exceptions\JWTException
     * @throws \Qbhy\SimpleJwt\Exceptions\SignatureException
     * @throws \Qbhy\SimpleJwt\Exceptions\TokenExpiredException
     */
    public function refresh(?string $token = null): ?string
    {
        $token = $token ?? $this->parseToken();

        if ($token) {
            $jwt = $this->jwtManager->parse($token);
            $this->logout($token);
            return $this->jwtManager->refresh($jwt)->token();
        }

        return null;
    }

    public function logout($token = null)
    {
        if ($token = $token ?? $this->parseToken()) {
            $jti = $this->jwtManager->parse($token)->getPayload()['jti'] ?? $token;
            $this->jwtManager->addBlacklist($jti);
            return true;
        }
        return false;
    }

    public function getJwtManager(): JWTManager
    {
        return $this->jwtManager;
    }
}
