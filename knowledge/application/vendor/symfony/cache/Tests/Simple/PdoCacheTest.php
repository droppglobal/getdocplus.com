<?php



namespace Symfony\Component\Cache\Tests\Simple;

use Symfony\Component\Cache\Simple\PdoCache;

/**
 * @group time-sensitive
 */
class PdoCacheTest extends CacheTestCase
{
    protected static $dbFile;

    public static function setupBeforeClass()
    {
        if (!extension_loaded('pdo_sqlite')) {
            self::markTestSkipped('Extension pdo_sqlite required.');
        }

        self::$dbFile = tempnam(sys_get_temp_dir(), 'sf_sqlite_cache');

        $pool = new PdoCache('sqlite:'.self::$dbFile);
        $pool->createTable();
    }

    public static function tearDownAfterClass()
    {
        @unlink(self::$dbFile);
    }

    public function createSimpleCache($defaultLifetime = 0)
    {
        return new PdoCache('sqlite:'.self::$dbFile, 'ns', $defaultLifetime);
    }
}
