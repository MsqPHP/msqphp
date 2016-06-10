<?php declare(strict_types = 1);
namespace msqphp;

final class Cli
{
    private static $args = [];
    public static function run()
    {
        $args = static::$args = $GLOBALS['argv'];
        switch ($args[1]) {
            case 'install':
                \msqphp\Framework::install(dirname($args[0]));
                break;
            case 'update':
                \msqphp\Framework::update(dirname($args[0]));
                break;
            case 'cron':
                core\cron\Cron::getInstance();
                core\cron\Cron::unsetInstance();
                break;
            case 'test':
                require __DIR__.'/test.php';
                break;
            default:
                throw new CliException('未知的cli命令');
        }
    }
}