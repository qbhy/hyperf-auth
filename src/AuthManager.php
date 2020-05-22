<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Qbhy\HyperfAuth;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ContainerInterface;

/**
 * Class AuthManager.
 * @method login(Authenticatable $user)
 * @method null|Authenticatable user()
 * @method bool check(Authenticatable $user)
 * @method logout()
 * @mixin GuardInterface
 */
class AuthManager extends DriverManager
{
    protected $config;

    public function __construct(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $this->config = $config->get('auth');
    }

    /**
     * @return AuthGuard
     */
    public function driver(?string $name = null): Driver
    {
        $name = $name ?? $this->defaultDriver();
        $guardConfig = $this->config['guards'][$name] ?? [];
        return $this->drivers[$name] ?: $this->drivers[$name] = make($guardConfig['driver'], [$guardConfig]);
    }

    public function defaultDriver(): string
    {
        return $this->config['default'] ?? $this->defaultDriver;
    }
}
