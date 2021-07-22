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
namespace Qbhy\HyperfAuth\Events;

use Qbhy\HyperfAuth\Authenticatable;

/**
 * 被迫下线事件
 * Class ForcedOfflineEvent.
 */
class ForcedOfflineEvent
{
    /**
     * 用户实例.
     * @var Authenticatable
     */
    public $user;

    /**
     * 客户端标识.
     * @var string
     */
    public $client;

    /**
     * ForcedOfflineEvent constructor.
     */
    public function __construct(Authenticatable $user, string $client)
    {
        $this->user = $user;
        $this->client = $client;
    }
}
