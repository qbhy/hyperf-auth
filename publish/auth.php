<?php
/**
 * This file is part of qbhy/hyperf-auth.
 *
 * @link     https://github.com/qbhy/hyperf-auth
 * @document https://github.com/qbhy/hyperf-auth/blob/master/README.md
 * @contact  qbhy0715@qq.com
 * @license  https://github.com/qbhy/hyperf-auth/blob/master/LICENSE
 */
use Doctrine\Common\Cache\FilesystemCache;
use Qbhy\SimpleJwt\Encoders\Base64UrlSafeEncoder;
use Qbhy\SimpleJwt\EncryptAdapters\PasswordHashEncrypter;

declare(strict_types=1);
/*
 * This file is part of qbhy/hyperf-auth.
 *
 * @link     https://github.com/qbhy/hyperf-auth
 * @document https://github.com/qbhy/hyperf-auth/blob/master/README.md
 * @contact  qbhy0715@qq.com
 * @license  https://github.com/qbhy/hyperf-auth/blob/master/LICENSE
 */
return [
    'default' => [
        'guard' => 'jwt',
        'provider' => 'users',
    ],
    'guards' => [
        'jwt' => [
            'driver' => Qbhy\HyperfAuth\Guard\JwtGuard::class,
            'provider' => 'users',
            'secret' => new PasswordHashEncrypter(env('JWT_SECRET', 'qbhy/hyperf-auth')),
            'encoder' => new Base64UrlSafeEncoder(),
            'cache' => new FilesystemCache(sys_get_temp_dir()), // 如果需要分布式部署，请选择 redis 或者其他支持分布式的缓存驱动
        ],
        'session' => [
            'driver' => Qbhy\HyperfAuth\Guard\SessionGuard::class,
            'provider' => 'users',
        ],
    ],
    'providers' => [
        'users' => [
            'driver' => \Qbhy\HyperfAuth\Provider\EloquentProvider::class,
            'model' => App\Model\User::class, //  需要实现 Qbhy\HyperfAuth\Authenticatable 接口
        ],
    ],
];
