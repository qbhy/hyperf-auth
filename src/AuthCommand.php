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

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;

/**
 * @Command
 * Class AuthCommand
 */
class AuthCommand extends HyperfCommand
{
    /**
     * 执行的命令行.
     *
     * @var string
     */
    protected $name = 'gen:auth-env';

    public function handle()
    {
        $this->gen('AUTH_SSO_CLIENTS', 'h5,weapp');
        $this->gen('SSO_JWT_SECRET');
        $this->gen('SIMPLE_JWT_SECRET');
    }

    public function gen($key, string $value = null)
    {
        if (empty(env($key))) {
            file_put_contents(BASE_PATH . '/.env', sprintf(PHP_EOL . '%s=%s', $key, $value ?? str_random(16)), FILE_APPEND);
            $this->info($key . ' 已生成!');
        } else {
            $this->info($key . ' 已存在!');
        }
    }
}
