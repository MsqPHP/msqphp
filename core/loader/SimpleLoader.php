<?php declare(strict_types = 1);
namespace msqphp\core\loader;

use msqphp\base;

final class SimpleLoader
{
    use BaseTrait;
    use AiloadTrait;

    // 抛出异常
    private static function exception(string $message) : void
    {
        static::exception($message);
    }
}