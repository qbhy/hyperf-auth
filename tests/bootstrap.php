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
use Doctrine\Common\Cache\FilesystemCache;
use Hyperf\Context\ApplicationContext;
use Hyperf\Di\Container;
use Hyperf\Di\Definition\DefinitionSourceFactory;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Request;
use HyperfTest\DemoUser;
use Qbhy\HyperfAuth\Guard\JwtGuard;
use Qbhy\HyperfAuth\Guard\SessionGuard;
use Qbhy\HyperfAuth\Provider\EloquentProvider;

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';
define('BASE_PATH', $dir = dirname(__DIR__, 1));

$container = new Container((new DefinitionSourceFactory(false))());
ApplicationContext::setContainer($container);

$container->define(RequestInterface::class, function () {
    return new Request();
});

$container->define(\Psr\SimpleCache\CacheInterface::class, function () use ($container) {
    return new \Hyperf\Cache\Driver\FileSystemDriver($container, []);
});

$container->define(\Hyperf\Contract\SessionInterface::class, function () {
    return new Hyperf\Session\Session('testing', new \Hyperf\Session\Handler\FileHandler(
        new \Hyperf\Utils\Filesystem\Filesystem(),
        BASE_PATH . '/runtime/testing',
        10
    ));
});

$container->define(\Qbhy\HyperfAuth\AuthManager::class, function () {
    $jwtConfig = [
        'driver' => JwtGuard::class, // guard 类名
        'secret' => 'test.secret',
        'provider' => 'test-provider', // 不设置的话用上面的 default.provider 或者用 'default'
        'encoder' => null,
        /*
         * 可选配置
         * 默认使用的加密类
         */
        'default' => Qbhy\SimpleJwt\EncryptAdapters\CryptEncrypter::class,
        'cache' => new FilesystemCache(sys_get_temp_dir()), // 如果需要分布式部署，请选择 redis 或者其他支持分布式的缓存驱动
    ];

    return new \Qbhy\HyperfAuth\AuthManager(new \Hyperf\Config\Config([
        'auth' => [
            'default' => [
                'guard' => 'jwt',
                'provider' => 'test-provider',
            ],

            'guards' => [
                'sso' => array_merge($jwtConfig, [
                    'driver' => \Qbhy\HyperfAuth\Guard\SsoGuard::class,

                    // 支持的设备，用英文逗号隔开
                    'clients' => ['pc', 'weapp'],

                    // hyperf/redis 实例
                    'redis' => function () {
                        return make(\Hyperf\Redis\Redis::class);
                    },

                    // 自定义 redis key，必须包含 {uid}，{uid} 会被替换成用户ID
                    'redis_key' => 'u:token:{uid}',
                    //                    'cache' => function () {
                    //                        return make(\Qbhy\HyperfAuth\HyperfRedisCache::class);
                    //                    },
                ]),
                'jwt' => $jwtConfig,
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
    ]));
});
