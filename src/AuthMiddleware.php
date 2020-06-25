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

use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;

/**
 * Class AuthMiddleware.
 */
class AuthMiddleware implements MiddlewareInterface
{
    protected $guards = [null];

    /**
     * @Inject
     * @var AuthManager
     */
    protected $auth;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        foreach ($this->guards as $name) {
            $guard = $this->auth->guard($name);

            if (! $guard->user() instanceof Authenticatable) {
                throw new UnauthorizedException("Without authorization from {$guard->getName()} guard", $guard);
            }
        }

        return $handler->handle($request);
    }
}
