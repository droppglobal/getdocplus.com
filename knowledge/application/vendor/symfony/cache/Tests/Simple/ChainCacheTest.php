<?php



namespace Symfony\Component\Cache\Tests\Simple;

use Symfony\Component\Cache\Simple\ArrayCache;
use Symfony\Component\Cache\Simple\ChainCache;
use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * @group time-sensitive
 */
class ChainCacheTest extends CacheTestCase
{
    public function createSimpleCache($defaultLifetime = 0)
    {
        return new ChainCache(array(new ArrayCache($defaultLifetime), new FilesystemCache('', $defaultLifetime)), $defaultLifetime);
    }

    /**
     * @expectedException \Symfony\Component\Cache\Exception\InvalidArgumentException
     * @expectedExceptionMessage At least one cache must be specified.
     */
    public function testEmptyCachesException()
    {
        new ChainCache(array());
    }

    /**
     * @expectedException \Symfony\Component\Cache\Exception\InvalidArgumentException
     * @expectedExceptionMessage The class "stdClass" does not implement
     */
    public function testInvalidCacheException()
    {
        new Chaincache(array(new \stdClass()));
    }
}
