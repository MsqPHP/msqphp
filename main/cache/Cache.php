<?php declare(strict_types = 1);
namespace msqphp\main\cache;

use msqphp\core\traits;

final class Cache
{
    use CachePointerTrait, CacheOperateTrait, CacheStaticTrait;

    // 抛出异常
    private static function exception(string $message) : void
    {
        throw new CacheException($message);
    }
}