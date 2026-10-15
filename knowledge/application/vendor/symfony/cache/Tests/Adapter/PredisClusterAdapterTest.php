<?php



namespace Symfony\Component\Cache\Tests\Adapter;

class PredisClusterAdapterTest extends AbstractRedisAdapterTest
{
    public static function setupBeforeClass()
    {
        parent::setupBeforeClass();
        self::$redis = new \Predis\Client(array(array('host' => getenv('REDIS_HOST'))));
    }

    public static function tearDownAfterClass()
    {
        self::$redis = null;
    }
}
