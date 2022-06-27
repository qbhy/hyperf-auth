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

interface UserProvider
{
    /**
     * Retrieve a user by the given credentials.
     *
     * @param $credentials
     */
    public function retrieveByCredentials($credentials): ?Authenticatable;

    /**
     * Validate a user against the given credentials.
     *
     * @param $credentials
     */
    public function validateCredentials(Authenticatable $user, $credentials): bool;
}
