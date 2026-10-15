<?php



namespace Symfony\Component\Cache\Tests\Adapter;

class RedisArrayAdapterTest extends AbstractRedisAdapterTest
{
    public static function setupBeforeClass()
    {
        parent::setupBeforeClass();
        if (!class_exists('RedisArray')) {
            self::markTestSkipped('The RedisArray class is required.');
        }
        self::$redis = new \RedisArray(array(getenv('REDIS_HOST')), array('lazy_connect' => true));
    }
}
