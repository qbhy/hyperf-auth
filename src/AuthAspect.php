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

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Qbhy\HyperfAuth\Annotation\Auth;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;

/**
 * @Aspect
 */
class AuthAspect extends AbstractAspect
{
    public $annotations = [
        Auth::class,
    ];

    /**
     * @Inject
     * @var AuthManager
     */
    protected $auth;

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /** @var Auth $authAnnotation */
        $authAnnotation = $proceedingJoinPoint->getAnnotationMetadata()->class[Auth::class];

        $guards = is_array($authAnnotation->value) ? $authAnnotation->value : [$authAnnotation->value];

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->guest()) {
                throw new UnauthorizedException("Without authorization from {$guard} guard");
            }
        }

        return $proceedingJoinPoint->process();
    }
}
