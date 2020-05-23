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
namespace HyperfTest\Cases;

use Doctrine\Common\Cache\FilesystemCache;
use HyperfTest\DemoUser;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\AuthGuard;
use Qbhy\HyperfAuth\AuthManager;
use Qbhy\HyperfAuth\Guard\JwtGuard;
use Qbhy\HyperfAuth\Guard\SessionGuard;
use Qbhy\HyperfAuth\Provider\EloquentProvider;
use Qbhy\SimpleJwt\JWT;

/**
 * @internal
 * @coversNothing
 */
class ExampleTest extends AbstractTestCase
{
    public function testExample()
    {
        $this->assertTrue(true);

        $this->assertTrue(extension_loaded('swoole'));
    }

    public function testJwtGuard()
    {
        /** @var AuthManager|JwtGuard $auth */
        $auth = $this->auth();
        /** @var JwtGuard $guard */
        $guard = $auth->guard();
        $token = $auth->login($this->user());
        $this->assertTrue($auth->check($token));
        $this->assertTrue(! $auth->guest($token));

        // 测试默认 guard
        $this->assertTrue($guard instanceof AuthGuard);
        $this->assertTrue($guard instanceof JwtGuard);
        $this->assertTrue($guard->getProvider() instanceof EloquentProvider);

        $jwtManager = $guard->getJwtManager();
        $token = $guard->login($this->user());

        $this->assertTrue(is_string($token));
        $jwt = $jwtManager->parse($token);
        $this->assertTrue($jwt instanceof JWT);
        $this->assertTrue($guard->user($token) instanceof Authenticatable);
        $this->assertTrue($guard->getProvider()->retrieveByCredentials($jwt->getPayload()['uid']) instanceof Authenticatable);
    }

    public function testSessionGuard()
    {
        /** @var SessionGuard $guard */
        $guard = $this->auth()->guard('session');
        $this->assertTrue($guard instanceof SessionGuard);
        $this->assertTrue($guard->getProvider() instanceof EloquentProvider);
        $user = $this->user();

        $this->assertTrue($guard->login($user));
        $this->assertTrue($guard->user() instanceof Authenticatable);
    }

    protected function config()
    {
        return new \Hyperf\Config\Config([
            'auth' => [
                'default' => [
                    'guard' => 'jwt',
                    'provider' => 'test-provider',
                ],

                'guards' => [
                    'jwt' => [
                        'driver' => JwtGuard::class, // guard 类名
                        'secret' => 'test.secret',
                        'provider' => 'test-provider', // 不设置的话用上面的 default.provider 或者用 'default'
                        'encoder' => null,
                        'cache' => new FilesystemCache(sys_get_temp_dir()), // 如果需要分布式部署，请选择 redis 或者其他支持分布式的缓存驱动
                    ],
                    'session' => [
                        'driver' => SessionGuard::class, // guard 类名
                        'provider' => 'test-provider', // 不设置的话用上面的 default.provider 或者用 'default'
                    ],
                ],

                'providers' => [
                    'test-provider' => [
                        'driver' => EloquentProvider::class, // user provider name
                        'model' => DemoUser::class,
                        // ... others config
                    ],
                ],
            ],
        ]);
    }

    protected function auth()
    {
        return new AuthManager($this->config());
    }

    protected function user($id = 1)
    {
        return new DemoUser($id);
    }
}
