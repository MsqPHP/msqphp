<?php declare(strict_types = 1);
namespace msqphp\core\response;

use msqphp\base;

trait ResponseJumpTrait
{
    /**
     * @param  string      $msg  错误信息
     * @param  int|integer $time 等待时间
     * @param  string      $url  跳转url
     * @param  int         $code 跳转代码
     * @param  string      $charset 页面编码
     */

    // 页面重定向
    public static function redirect(string $url, int $code = 301) : void
    {
        header('location:'.$url, true, $code);
    }
    // 页面跳转
    public static function jump(string $url, int $time = 0, string $message = '') : void
    {
        $message = $message ?: '系统将在'.$time.'秒之后自动跳转到<a href="'.$url.'">'.$url.'</a>！';
        if($time > 0) {
            header('refresh:'.$time.';url='.$url);
            include static::getViewPath('jump');
        } else {
            static::redirect($url);
        }
    }
    // 错误信息显示
    public static function error(string $msg, int $time = 3, string $url = '') : void
    {
        include static::getViewPath('error');
    }
    // 成功信息显示
    public static function success(string $msg, int $time = 3, string $url = '') : void
    {
        include static::getViewPath('success');
    }
    // 不可用页面(维护)
    public static function unavailable() : void
    {
        include static::getViewPath('unavailable');
    }
    // JS窗口提示并跳转
    public static function alert(string $msg, string $url = '', string $charset = 'utf-8') : void
    {
        // header头为html
        base\header\Header::type('html', $charset);
        // 弹出信息
        $alert_msg = 'alert("'.addslashes($msg).'");';
        // 跳转页面
        $go_url = empty($url) ? 'history.go(-1);' : 'window.location.href = "'.$url.'";';
        // 输出
        echo '<meta charset="',$charset,'"><script type="text/javascript">',$alert_msg,$go_url,'</script>';
    }

    /**
     * 得到视图文件对应路径
     * @param   string  $filename  文件名
     * @return  string
     */
    private static function getViewPath(string $filename) : string
    {
        $view = \msqphp\Environment::getPath('resources').'views'.DIRECTORY_SEPARATOR.$filename.'.html';
        is_file($view) || $view = \msqphp\Environment::getPath('framework').'resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$filename.'.html';
        if (!is_file($view)) {
            throw new ResponseException($view.'视图文件不存在,无法进行相关操作');
        }
        return $view;
    }
}