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
namespace Qbhy\HyperfAuth;

use Hyperf\Contract\ConfigInterface;
use Qbhy\HyperfAuth\Exception\GuardException;
use Qbhy\HyperfAuth\Exception\UserProviderException;

/**
 * Class AuthManager.
 * @method login(Authenticatable $user)
 * @method null|Authenticatable user($token = null)
 * @method bool check($token = null)
 * @method logout()
 * @method string getName()
 * @method bool guest()
 * @method getProvider()
 * @method id($token = null)
 * @mixin AuthGuard
 */
class AuthManager
{
    /**
     * @var string
     */
    protected $defaultDriver = 'default';

    /**
     * @var array
     */
    protected $guards = [];

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @var array
     */
    protected $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config->get('auth');
    }

    public function __call($name, $arguments)
    {
        $guard = $this->guard();

        if (method_exists($guard, $name)) {
            return call_user_func_array([$guard, $name], $arguments);
        }

        throw new GuardException('Method not defined. method:' . $name);
    }

    /**
     * @throws GuardException
     * @throws UserProviderException
     */
    public function guard(?string $name = null): AuthGuard
    {
        $name = $name ?? $this->defaultGuard();

        if (empty($this->config['guards'][$name])) {
            throw new GuardException("Does not support this driver: {$name}");
        }

        $config = $this->config['guards'][$name];
        $userProvider = $this->provider($config['provider'] ?? $this->defaultDriver);

        return $this->guards[$name] ?? $this->guards[$name] = make(
            $config['driver'],
            compact('name', 'config', 'userProvider')
        );
    }

    /**
     * @throws UserProviderException
     */
    public function provider(?string $name = null): UserProvider
    {
        $name = $name ?? $this->defaultProvider();

        if (empty($this->config['providers'][$name])) {
            throw new UserProviderException("Does not support this provider: {$name}");
        }

        $config = $this->config['providers'][$name];

        return $this->providers[$name] ?? $this->providers[$name] = make(
            $config['driver'],
            [
                'config' => $config,
                'name' => $name,
            ]
        );
    }

    public function defaultGuard(): string
    {
        return $this->config['default']['guard'] ?? $this->defaultDriver;
    }

    public function defaultProvider(): string
    {
        return $this->config['default']['provider'] ?? $this->defaultDriver;
    }

    public function getGuards(): array
    {
        return $this->guards;
    }
}
