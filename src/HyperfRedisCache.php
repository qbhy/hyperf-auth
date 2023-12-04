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

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\RedisCache;
use Hyperf\Redis\Redis;
use Redis as RedisExt;

/**
 * Class HyperfRedisCache.
 */
class HyperfRedisCache extends RedisCache
{
    protected ?Redis $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Sets the redis instance to use.
     *
     * @param mixed $redis
     */
    public function setRedis($redis)
    {
        //        $redis->setOption(RedisExt::OPT_SERIALIZER, $this->getSerializerValue());
        $this->redis = $redis;
    }

    /**
     * Gets the redis instance used by the cache.
     */
    public function getRedis(): ?Redis
    {
        return $this->redis;
    }

    protected function doFetch($id)
    {
        return $this->redis->get($id);
    }

    protected function doFetchMultiple(array $keys): array|bool
    {
        $fetchedItems = array_combine($keys, $this->redis->mget($keys));

        // Redis mget returns false for keys that do not exist. So we need to filter those out unless it's the real data.
        $keysToFilter = array_keys(array_filter($fetchedItems, static function ($item): bool {
            return $item === false;
        }));

        if ($keysToFilter) {
            $multi = $this->redis->multi(RedisExt::PIPELINE);
            foreach ($keysToFilter as $key) {
                $multi->exists($key);
            }
            $existItems = array_filter($multi->exec());
            $missedItemKeys = array_diff_key($keysToFilter, $existItems);
            $fetchedItems = array_diff_key($fetchedItems, array_fill_keys($missedItemKeys, true));
        }

        return $fetchedItems;
    }

    protected function doSaveMultiple(array $keysAndValues, $lifetime = 0): bool
    {
        if ($lifetime) {
            // Keys have lifetime, use SETEX for each of them
            $multi = $this->redis->multi(RedisExt::PIPELINE);
            foreach ($keysAndValues as $key => $value) {
                $multi->setex($key, $lifetime, $value);
            }
            $succeeded = array_filter($multi->exec());

            return count($succeeded) == count($keysAndValues);
        }

        // No lifetime, use MSET
        return (bool) $this->redis->mset($keysAndValues);
    }

    protected function doContains($id): bool
    {
        $exists = $this->redis->exists($id);

        if (is_bool($exists)) {
            return $exists;
        }

        return $exists > 0;
    }

    protected function doSave($id, $data, $lifeTime = 0): bool
    {
        if ($lifeTime > 0) {
            return $this->redis->setex($id, $lifeTime, $data);
        }

        return $this->redis->set($id, $data);
    }

    protected function doDelete($id): bool
    {
        return $this->redis->del($id) >= 0;
    }

    protected function doDeleteMultiple(array $keys): bool
    {
        return $this->redis->del($keys) >= 0;
    }

    protected function doFlush(): bool
    {
        return $this->redis->flushDB();
    }

    protected function doGetStats(): ?array
    {
        $info = $this->redis->info();

        return [
            Cache::STATS_HITS => $info['keyspace_hits'],
            Cache::STATS_MISSES => $info['keyspace_misses'],
            Cache::STATS_UPTIME => $info['uptime_in_seconds'],
            Cache::STATS_MEMORY_USAGE => $info['used_memory'],
            Cache::STATS_MEMORY_AVAILABLE => false,
        ];
    }

    /**
     * Returns the serializer constant to use. If Redis is compiled with
     * igbinary support, that is used. Otherwise the default PHP serializer is
     * used.
     *
     * @return int One of the Redis::SERIALIZER_* constants
     */
    protected function getSerializerValue(): int
    {
        if (defined('Redis::SERIALIZER_IGBINARY') && extension_loaded('igbinary')) {
            return RedisExt::SERIALIZER_IGBINARY;
        }

        return RedisExt::SERIALIZER_PHP;
    }
}
