<?php

declare(strict_types=1);
/**
 * This file is part of qbhy/hyperf-auth.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Qbhy\HyperfAuth;

use Hyperf\Database\Model\Model;

/**
 * Trait AuthAbility.
 * @mixin Authenticatable|Model
 */
trait AuthAbility
{
    public static function findFromKey(string $key): ?Authenticatable
    {
        /* @var null|Authenticatable|Model $user */
        return self::query()->find($key);
    }
}
