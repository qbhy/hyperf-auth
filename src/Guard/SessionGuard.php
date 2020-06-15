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

use Hyperf\Contract\SessionInterface;
use Hyperf\Utils\Context;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;
use Qbhy\HyperfAuth\UserProvider;

class SessionGuard extends AbstractAuthGuard
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * JwtGuardAbstract constructor.
     */
    public function __construct(array $config, string $name, UserProvider $userProvider, SessionInterface $session)
    {
        parent::__construct($config, $name, $userProvider);
        $this->session = $session;
    }

    public function login(Authenticatable $user)
    {
        $this->session->put($this->sessionKey(), $user->getId());
        return true;
    }

    public function resultKey()
    {
        return $this->name . 'auth.result:' . $this->session->getId();
    }

    public function user(): ?Authenticatable
    {
        if (Context::has($key = $this->resultKey())) {
            $result = Context::get($key);
            if ($result instanceof \Throwable) {
                throw $result;
            }
            return $result ?: null;
        }

        try {
            if ($credentials = $this->session->get($this->sessionKey())) {
                $user = $this->userProvider->retrieveByCredentials($credentials);
                Context::set($key, $user ?? 0);
                return $user;
            }
            throw new UnauthorizedException('Unauthorized.');
        } catch (\Throwable $exception) {
            Context::set($key, $exception);
            throw $exception;
        }
    }

    public function logout()
    {
        if ($this->session->has($this->sessionKey())) {
            Context::destroy($this->resultKey());
            $this->session->remove($this->sessionKey());
            return true;
        }

        return false;
    }

    protected function sessionKey(): string
    {
        return 'auth.' . $this->name;
    }
}
