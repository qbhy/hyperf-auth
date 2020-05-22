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

use Qbhy\HyperfAuth\UserProvider;

abstract class AbstractUserProvider implements UserProvider
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $name;

    /**
     * AbstractUserProvider constructor.
     */
    public function __construct(array $config, string $name)
    {
        $this->config = $config;
        $this->name = $name;
    }
}
