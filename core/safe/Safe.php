<?php declare(strict_types = 1);
namespace msqphp\core\Safe;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

class Safe
{
    public static function maliciousRefresh()
    {
        $session = Core\Session\Session::getInstance();
        //设置session键
        $session->init()->key('refresh');
        if ($session->exists() && ($session->get() > microtime(true)-0.7)) {
            throw new SafeException('刷新过快');
        } else {
            $session->value(microtime(true))->set();
            unset($session);
        }
    }
}