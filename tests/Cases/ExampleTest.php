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

use Hyperf\Utils\ApplicationContext;
use HyperfTest\DemoUser;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\AuthGuard;
use Qbhy\HyperfAuth\AuthManager;
use Qbhy\HyperfAuth\Guard\JwtGuard;
use Qbhy\HyperfAuth\Guard\SessionGuard;
use Qbhy\HyperfAuth\Guard\SsoGuard;
use Qbhy\HyperfAuth\Provider\EloquentProvider;
use Qbhy\SimpleJwt\Exceptions\TokenBlacklistException;
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

    public function testAuthFunc()
    {
        $this->assertTrue(auth() instanceof AuthManager);
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

        $this->assertTrue(is_string($newToken = $guard->refresh($token))); // 测试刷新 token

        try {
            $this->assertTrue($guard->guest($token)); // 试试新 token 是否生效
        } catch (TokenBlacklistException $exception) {
            $this->assertTrue(true); // 试试旧 token 是否失效
        }

        $this->assertTrue($guard->check($newToken)); // 试试新 token 是否生效

        $token = $newToken;

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

    public function testSsoGuard()
    {
        /** @var SsoGuard $guard */
        $guard = $this->auth()->guard('sso');
        $this->assertTrue($guard instanceof SsoGuard);
        $this->assertTrue($guard->getProvider() instanceof EloquentProvider);
        $user = $this->user(10);
        $token = $guard->login($user, 'pc');
        $this->assertTrue(is_string($token));
        $this->assertTrue($guard->check($token));

//        // 抢线登录
//        $newToken = $guard->login($user, 'pc');
//        var_dump('抢线的token', $newToken);
//        $this->assertTrue($newToken != $token);
//        $this->assertTrue($guard->check($newToken));
//
//        // 测试旧 token 还能不能用
//        $this->assertTrue($guard->guest($token));
//
//        // 第二个设备登录
//        $weappToken = $guard->login($user, 'weapp');
//        $this->assertTrue($guard->check($weappToken));
    }

    protected function auth()
    {
        return ApplicationContext::getContainer()->get(AuthManager::class);
    }

    protected function user($id = 1)
    {
        return new DemoUser($id);
    }
}
