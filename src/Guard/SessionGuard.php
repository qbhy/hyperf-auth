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
use Qbhy\HyperfAuth\Authenticatable;
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
        $this->session->put($this->sessionKey(), $user->getKey());
        return true;
    }

    public function user(): ?Authenticatable
    {
        if ($credentials = $this->session->get($this->sessionKey())) {
            return $this->userProvider->retrieveByCredentials($credentials);
        }
        return null;
    }

    public function logout()
    {
        if ($this->session->has($this->sessionKey())) {
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
