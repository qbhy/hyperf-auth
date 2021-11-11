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

interface AuthGuard
{
    public function id();

    public function login(Authenticatable $user);

    public function user(): ?Authenticatable;

    public function check(): bool;

    public function guest(): bool;

    public function logout();

    public function getProvider(): UserProvider;

    public function getName(): string;
}
