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
return [
    'default' => 'jwt',
    'guards' => [
        'jwt' => [
            'driver' => Qbhy\HyperfAuth\Guard\JwtGuard::class,
            'secret' => env('JWT_SECRET', 'qbhy/hyperf-auth'),
            'model' => App\Model\User::class, // 你的 model 类， 需要实现 Qbhy\HyperfAuth\Authenticatable 接口
        ],
    ],
];
