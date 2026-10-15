<?php



namespace Symfony\Component\Cache\Tests\Adapter;

use Doctrine\Common\Cache\ArrayCache;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;

/**
 * @group time-sensitive
 */
class DoctrineAdapterTest extends AdapterTestCase
{
    protected $skippedTests = array(
        'testDeferredSaveWithoutCommit' => 'Assumes a shared cache which ArrayCache is not.',
        'testSaveWithoutExpire' => 'Assumes a shared cache which ArrayCache is not.',
        'testNotUnserializable' => 'ArrayCache does not use serialize/unserialize',
    );

    public function createCachePool($defaultLifetime = 0)
    {
        return new DoctrineAdapter(new ArrayCache($defaultLifetime), '', $defaultLifetime);
    }
}
