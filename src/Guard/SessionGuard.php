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
use Qbhy\HyperfAuth\Exception\AuthException;
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

        Context::set($this->resultKey(), $user);

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
            throw new UnauthorizedException('Unauthorized.', $this);
        } catch (\Throwable $exception) {
            $newException = $exception instanceof AuthException ? $exception : new UnauthorizedException(
                $exception->getMessage(),
                $this,
                $exception
            );
            Context::set($key, $newException);
            throw $newException;
        }
    }

    public function id()
    {
        return $this->session->get($this->sessionKey());
    }

    public function check(): bool
    {
        try {
            return $this->user() instanceof Authenticatable;
        } catch (AuthException $exception) {
            return false;
        }
    }

    public function logout()
    {
        Context::set($this->resultKey(), null);
        return (bool) $this->session->remove($this->sessionKey());
    }

    protected function sessionKey(): string
    {
        return 'auth_' . $this->name;
    }
}
