<?php

declare(strict_types=1);
/**
 * This file is part of qbhy/hyperf-auth.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Qbhy\HyperfAuth\Guard;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Str;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\AuthGuard;
use Qbhy\SimpleJwt\JWTManager;

class JwtGuard extends AuthGuard
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
     * JwtGuard constructor.
     */
    public function __construct(array $config, RequestInterface $request)
    {
        parent::__construct($config);
        $this->jwtManager = new JWTManager($config['secret'] ?? 'secret');
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

    public function login(Authenticatable $user)
    {
        return $this->jwtManager->make(['uid' => $user->getKey()])->token();
    }

    public function user(): ?Authenticatable
    {
        if ($token = $this->parseToken()) {
            $jwt = $this->jwtManager->parse($token);
            $uid = $jwt->getPayload()['uid'] ?? null;
            return $uid ? call_user_func_array([$this->config['model'], 'findFromKey'], [$uid]) : null;
        }

        return null;
    }

    public function check(): bool
    {
        return $this->user() instanceof Authenticatable;
    }

    public function logout()
    {
    }
}
