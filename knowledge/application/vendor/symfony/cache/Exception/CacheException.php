<?php



namespace Symfony\Component\Cache\Exception;

use Psr\Cache\CacheException as Psr6CacheInterface;
use Psr\SimpleCache\CacheException as SimpleCacheInterface;

class CacheException extends \Exception implements Psr6CacheInterface, SimpleCacheInterface
{
}
