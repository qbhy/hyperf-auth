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

namespace Qbhy\HyperfAuth\Exception;

use Qbhy\HyperfAuth\AuthGuard;

class UnauthorizedException extends AuthException
{
    protected ?AuthGuard $guard;

    protected int $statusCode = 401;

    public function __construct(string $message, AuthGuard $guard = null, \Throwable $previous = null)
    {
        parent::__construct($message, 401, $previous);
        $this->guard = $guard;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): static
    {
        $this->statusCode = $statusCode;
        return $this;
    }
}
