<?php declare(strict_types = 1);
namespace msqphp\core\loader;

final class Loader
{
    use BaseTrait, AutoloadTrait, AiLoadTrait;

    // 抛出异常
    private static function exception(string $message) : void
    {
        throw new LoaderException($message);
    }
}