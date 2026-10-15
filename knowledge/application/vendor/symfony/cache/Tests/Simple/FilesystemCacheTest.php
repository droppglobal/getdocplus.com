<?php



namespace Symfony\Component\Cache\Tests\Simple;

use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * @group time-sensitive
 */
class FilesystemCacheTest extends CacheTestCase
{
    public function createSimpleCache($defaultLifetime = 0)
    {
        return new FilesystemCache('', $defaultLifetime);
    }
}
