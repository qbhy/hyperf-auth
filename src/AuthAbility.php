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

use Hyperf\Database\Model\Model;

/**
 * Trait AuthAbility.
 * @mixin Authenticatable|Model
 */
trait AuthAbility
{
    public function getId()
    {
        return $this->getKey();
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return self::query()->find($key);
    }
}
