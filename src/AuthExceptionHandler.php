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

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Qbhy\HyperfAuth\Exception\AuthException;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;
use Qbhy\SimpleJwt\Exceptions\JWTException;
use Throwable;

class AuthExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if ($throwable instanceof JWTException) {
            $this->stopPropagation();
            return $response->withStatus(401)->withBody(new SwooleStream($throwable->getMessage()));
        }
        if ($throwable instanceof UnauthorizedException) {
            $this->stopPropagation();
            return $response->withStatus(401)->withBody(new SwooleStream('Unauthorized.'));
        }

        if ($throwable instanceof AuthException) {
            $this->stopPropagation();
            return $response->withStatus(500)->withBody(new SwooleStream($throwable->getMessage()));
        }

        // 交给下一个异常处理器
        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof AuthException or $throwable instanceof JWTException;
    }
}
