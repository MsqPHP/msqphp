<?php declare(strict_types = 1);
namespace msqphp\core\Safe;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

final class Safe
{
    public static function maliciousRefresh()
    {
        $session = core\session\Session::getInstance()->init()->key('refresh');
        //设置session键
        if ($session->exists() && ($session->get() > microtime(true)-0.7)) {
            throw new SafeException('刷新过快');
        } else {
            $session->value(microtime(true))->set();
        }
        unset($session);
    }
}