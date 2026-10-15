<?php



namespace Symfony\Component\Cache\Tests\Simple;

use Symfony\Component\Cache\Simple\PhpFilesCache;

/**
 * @group time-sensitive
 */
class PhpFilesCacheTest extends CacheTestCase
{
    protected $skippedTests = array(
        'testDefaultLifeTime' => 'PhpFilesCache does not allow configuring a default lifetime.',
    );

    public function createSimpleCache()
    {
        if (!PhpFilesCache::isSupported()) {
            $this->markTestSkipped('OPcache extension is not enabled.');
        }

        return new PhpFilesCache('sf-cache');
    }
}
