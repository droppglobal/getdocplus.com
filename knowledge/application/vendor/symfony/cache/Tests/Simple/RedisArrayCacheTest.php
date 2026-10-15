<?php



namespace Symfony\Component\Cache\Tests\Simple;

class RedisArrayCacheTest extends AbstractRedisCacheTest
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
