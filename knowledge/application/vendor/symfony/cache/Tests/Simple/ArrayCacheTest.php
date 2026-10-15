<?php



namespace Symfony\Component\Cache\Tests\Simple;

use Symfony\Component\Cache\Simple\ArrayCache;

/**
 * @group time-sensitive
 */
class ArrayCacheTest extends CacheTestCase
{
    public function createSimpleCache($defaultLifetime = 0)
    {
        return new ArrayCache($defaultLifetime);
    }
}
