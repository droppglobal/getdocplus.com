<?php



namespace Symfony\Component\Cache\Tests\Adapter;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @group time-sensitive
 */
class FilesystemAdapterTest extends AdapterTestCase
{
    public function createCachePool($defaultLifetime = 0)
    {
        return new FilesystemAdapter('', $defaultLifetime);
    }

    public static function tearDownAfterClass()
    {
        self::rmdir(sys_get_temp_dir().'/symfony-cache');
    }

    public static function rmdir($dir)
    {
        if (!file_exists($dir)) {
            return;
        }
        if (!$dir || 0 !== strpos(dirname($dir), sys_get_temp_dir())) {
            throw new \Exception(__METHOD__."() operates only on subdirs of system's temp dir");
        }
        $children = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($children as $child) {
            if ($child->isDir()) {
                rmdir($child);
            } else {
                unlink($child);
            }
        }
        rmdir($dir);
    }
}
