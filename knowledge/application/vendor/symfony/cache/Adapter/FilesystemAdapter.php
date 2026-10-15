<?php



namespace Symfony\Component\Cache\Adapter;

use Symfony\Component\Cache\Traits\FilesystemTrait;

class FilesystemAdapter extends AbstractAdapter
{
    use FilesystemTrait;

    /**
     * @param string      $namespace
     * @param int         $defaultLifetime
     * @param string|null $directory
     */
    public function __construct($namespace = '', $defaultLifetime = 0, $directory = null)
    {
        parent::__construct('', $defaultLifetime);
        $this->init($namespace, $directory);
    }
}
