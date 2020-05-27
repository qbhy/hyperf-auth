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
namespace Qbhy\HyperfAuth\Provider;

use Qbhy\HyperfAuth\Authenticatable;

class EloquentProvider extends AbstractUserProvider
{
    public function retrieveByCredentials($credentials)
    {
        return call_user_func_array([$this->config['model'], 'retrieveById'], [$credentials]);
    }

    public function validateCredentials(Authenticatable $user, $credentials): bool
    {
        return $user->getId() === $credentials;
    }
}
