<?php



namespace Symfony\Component\Cache\Tests\Adapter;

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\ProxyAdapter;

/**
 * @group time-sensitive
 */
class NamespacedProxyAdapterTest extends ProxyAdapterTest
{
    public function createCachePool($defaultLifetime = 0)
    {
        return new ProxyAdapter(new ArrayAdapter($defaultLifetime), 'foo', $defaultLifetime);
    }
}
