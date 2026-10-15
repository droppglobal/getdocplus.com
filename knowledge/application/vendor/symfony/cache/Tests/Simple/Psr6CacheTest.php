<?php



namespace Symfony\Component\Cache\Tests\Simple;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Simple\Psr6Cache;

/**
 * @group time-sensitive
 */
class Psr6CacheTest extends CacheTestCase
{
    public function createSimpleCache($defaultLifetime = 0)
    {
        return new Psr6Cache(new FilesystemAdapter('', $defaultLifetime));
    }
}
