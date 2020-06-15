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

use Doctrine\Common\Cache\RedisCache;
use Hyperf\Redis\Redis;

/**
 * Class HyperfRedisCache.
 */
class HyperfRedisCache extends RedisCache
{
    /**
     * @var Redis
     */
    private $redis;

    /**
     * RedisCache constructor.
     */
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function __call($name, $arguments)
    {
        return $this->redis->__call($name, $arguments);
    }

    public function setRedis($redis)
    {
        $this->redis = $redis;
    }
}
