<?php declare(strict_types = 1);
namespace Msqphp\Core\Safe;

use Msqphp\Core;

class Safe
{
    /**
     * 安全检测
     * @throws SafeException
     * @return void
     */
    static public function check()
    {
        static::maliciousRefresh();
    }

    static public function maliciousRefresh() : bool
    {
        $session = Core\Session\Session::getInstance();
        //设置session键
        $session->init()->key('refresh');
        if ($session->exists() && ($session->get() > microtime(true)-0.7)) {
            throw new SafeException('刷新过快');
            unset($session);return false;
        } else {
            $bool = $session->value(microtime(true))->set();
            unset($session);return $bool;
        }
    }
}