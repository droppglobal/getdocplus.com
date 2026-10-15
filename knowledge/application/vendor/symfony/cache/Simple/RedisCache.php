<?php



namespace Symfony\Component\Cache\Simple;

use Symfony\Component\Cache\Traits\RedisTrait;

class RedisCache extends AbstractCache
{
    use RedisTrait;

    /**
     * @param \Redis|\RedisArray|\RedisCluster|\Predis\Client $redisClient
     * @param string                                          $namespace
     * @param int                                             $defaultLifetime
     */
    public function __construct($redisClient, $namespace = '', $defaultLifetime = 0)
    {
        $this->init($redisClient, $namespace, $defaultLifetime);
    }
}
