<?php declare(strict_types = 1);
namespace msqphp\base\response;

class Response
{
    private static $type = '';
    /**
     * 页面重定向
     * @param  string  $url  跳转地址
     * @param  integer $code 跳转代码
     * @return void
     */
    public static function redirect(string $url, int $code = 301)
    {
        header('location:'.$url, true, $code);
    }
    /**
     * 页面跳转
     * @param  string   $url  跳转地址
     * @param  ing      $time 等待时间
     * @param  string  $msg  信息
     * @return void
     */
    public static function jump(string $url, int $time = 0, string $msg = '')
    {
        $msg = $msg ?: '系统将在'.$time.'秒之后自动跳转到<a href="'.$url.'">'.$url.'</a>！';
        if($time > 0) {
            header('refresh:'.$time.';url='.$url);
            echo $msg;
        } else {
            header('location:'.$url, true, 301);
        }
    }
    /**
     * 错误信息显示
     */
    public static function error(string $msg, int $time = 3, string $url = '')
    {
        $view = \msqphp\Environment::getPath('resources').'views'.DIRECTORY_SEPARATOR.'500.html';
        if (!is_file($view)) {
            $view = \msqphp\Environment::getPath('framework').'resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'500.html';
            if (!is_file($view)) {
                throw new ResponseException($view.'文件不存在');
            }
        }
        require $view;
        exit;
    }
    public static function success(string $msg, int $time = 3, string $url = '')
    {
        $view = \msqphp\Environment::getPath('resources').'views'.DIRECTORY_SEPARATOR.'200.html';
        if (!is_file($view)) {
            $view = \msqphp\Environment::getPath('framework').'resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'200.html';
            if (!is_file($view)) {
                throw new ResponseException($view.'文件不存在');
            }
        }
        exit;
    }
    /**
     * JS窗口提示并跳转
     * @param  string $msg     提示消息
     * @param  string $url     跳转URL
     * @param  string $charset 页面编码
     * @return void
     */
    public static function alert(string $msg, string $url = '', $charset='utf-8')
    {
        base\header\Header::type('html');
        $alert_msg = 'alert("'.$msg.'");';
        if( empty($url) ) {
            $go_url = 'history.go(-1);';
        }else{
            $go_url = 'window.location.href = "'.$url.'";';
        }
        echo '<script type="text/javascript">'.$alert_msg.$go_url.'</script>';
        exit;
    }
    /**
     * xml
     */
    public static function xml($data, string $root = 'root', bool $end = false)
    {
        base\header\Header::type('xml');

        $xml = '<?xml version="1.0" encoding="utf-8"?>';

        $xml = '<'.$root.'>'.base\xml\Xml::encode($data).'</'.$root.'>';

        echo $xml;

        $end && exit;
    }
}