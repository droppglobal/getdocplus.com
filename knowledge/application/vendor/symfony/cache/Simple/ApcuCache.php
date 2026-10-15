<?php



namespace Symfony\Component\Cache\Simple;

use Symfony\Component\Cache\Traits\ApcuTrait;

class ApcuCache extends AbstractCache
{
    use ApcuTrait;

    /**
     * @param string      $namespace
     * @param int         $defaultLifetime
     * @param string|null $version
     */
    public function __construct($namespace = '', $defaultLifetime = 0, $version = null)
    {
        $this->init($namespace, $defaultLifetime, $version);
    }
}
