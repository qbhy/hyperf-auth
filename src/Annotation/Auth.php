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
namespace Qbhy\HyperfAuth\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class Auth extends AbstractAnnotation
{
    public function __construct(public ?string $value = null)
    {
    }
}
