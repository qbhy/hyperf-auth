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

abstract class AuthGuard extends Driver implements GuardInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * AuthGuard constructor.
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }
}
