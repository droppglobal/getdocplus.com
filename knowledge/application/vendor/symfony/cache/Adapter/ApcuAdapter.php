<?php



namespace Symfony\Component\Cache\Adapter;

use Symfony\Component\Cache\Traits\ApcuTrait;

class ApcuAdapter extends AbstractAdapter
{
    use ApcuTrait;

    /**
     * @param string      $namespace
     * @param int         $defaultLifetime
     * @param string|null $version
     *
     * @throws CacheException if APCu is not enabled
     */
    public function __construct($namespace = '', $defaultLifetime = 0, $version = null)
    {
        $this->init($namespace, $defaultLifetime, $version);
    }
}
