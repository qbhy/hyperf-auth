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

abstract class DriverManager
{
    /**
     * @var string
     */
    protected $defaultDriver = 'default';

    /**
     * @var array
     */
    protected $drivers;

    public function defaultDriver(): string
    {
        return $this->defaultDriver;
    }

    abstract public function driver(?string $name = null): Driver;
}
