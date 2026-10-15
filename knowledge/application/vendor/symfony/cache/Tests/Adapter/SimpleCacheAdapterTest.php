<?php



namespace Symfony\Component\Cache\Tests\Adapter;

use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Cache\Adapter\SimpleCacheAdapter;

/**
 * @group time-sensitive
 */
class SimpleCacheAdapterTest extends AdapterTestCase
{
    public function createCachePool($defaultLifetime = 0)
    {
        return new SimpleCacheAdapter(new FilesystemCache(), '', $defaultLifetime);
    }
}
