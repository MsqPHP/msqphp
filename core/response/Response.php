<?php declare(strict_types = 1);
namespace msqphp\core\response;

use msqphp\base;
use msqphp\traits;

final class Response
{
    use traits\CallStatic;
    /**
     * 页面重定向
     *
     * @param  string  $url  跳转地址
     * @param  integer $code 跳转代码
     *
     * @return void
     */
    public static function redirect(string $url, int $code = 301)
    {
        header('location:'.$url, true, $code);
        exit;
    }
    /**
     * 页面跳转
     *
     * @param  string   $url  跳转地址
     * @param  int      $time 等待时间
     * @param  string   $msg  信息
     *
     * @return void
     */
    public static function jump(string $url, int $time = 0, string $msg = '')
    {
        $msg = $msg ?: '系统将在'.$time.'秒之后自动跳转到<a href="'.$url.'">'.$url.'</a>！';
        if($time > 0) {
            header('refresh:'.$time.';url='.$url);
            include static::getViewPath('jump');
        } else {
            header('location:'.$url, true, 301);
        }
        exit;
    }
    /**
     * 错误信息显示
     *
     * @param  string      $msg  错误信息
     * @param  int|integer $time 等待时间
     * @param  string      $url  跳转url
     *
     * @return void
     */
    public static function error(string $msg, int $time = 3, string $url = '')
    {
        include static::getViewPath('error');
        exit;
    }
    /**
     * 成功信息显示
     *
     * @param  string      $msg  成功信息
     * @param  int|integer $time 等待时间
     * @param  string      $url  跳转url
     *
     * @return void
     */
    public static function success(string $msg, int $time = 3, string $url = '')
    {
        include static::getViewPath('success');
        exit;
    }
    /**
     * 不可用页面(维护)
     *
     * @return void
     */
    public static function unavailable()
    {
        include static::getViewPath('unavailable');
        exit;
    }
    private static function getViewPath(string $filename) : string
    {
        $view = \msqphp\Environment::getPath('resources').'views'.DIRECTORY_SEPARATOR.$filename.'.html';
        if (!is_file($view)) {
            $view = \msqphp\Environment::getPath('framework').'resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$filename.'.html';
            if (!is_file($view)) {
                throw new ResponseException($view.'文件不存在');
            }
        }
        return $view;
    }
    /**
     * JS窗口提示并跳转
     * @param  string $msg     提示消息
     * @param  string $url     跳转URL
     * @param  string $charset 页面编码
     * @return void
     */
    public static function alert(string $msg, string $url = '', string $charset='utf-8')
    {
        //header头为html
        base\header\Header::type('html');
        //弹出信息
        $alert_msg = 'alert("'.addslashes($msg).'");';
        //跳转页面
        $go_url = empty($url) ? 'history.go(-1);' : 'window.location.href = "'.$url.'";';
        //输出
        echo '<meta charset="',$charset,'"><script type="text/javascript">',$alert_msg,$go_url,'</script>';

        exit;
    }
    /**
     * xml格式返回
     *
     * @param  miexd        $data 数据
     * @param  string       $root 根节点
     * @param  bool|boolean $end  是否退出
     *
     * @return void
     */
    public static function xml($data, string $root = 'root', bool $end = true)
    {
        base\header\Header::type('xml');

        $xml = '<?xml version="1.0" encoding="utf-8"?>';

        $xml = '<'.$root.'>'.base\xml\Xml::encode($data).'</'.$root.'>';

        echo $xml;

        $end && exit;
    }
    /**
     * json格式返回
     *
     * @param  miexd        $data 数据
     * @param  bool|boolean $end  是否退出
     *
     * @return void
     */
    public static function json($data, $end = true)
    {
        base\header\Header::type('json');

        echo base\json\Json::encode($data);

        $end && exit;
    }

    public static function dump()
    {
        if (\msqphp\Environment::getSapi() === 'cli') {
            array_map(function ($v) {
                var_export($v);
            }, func_get_args());
            echo PHP_EOL;
        } else {
            echo '<pre>';

            array_map(function ($v) {
                var_export(base\filter\Filter::html($v));
            }, func_get_args());

            echo '</pre><hr/>';
        }
    }
    public static function dumpArray(array $array)
    {
        foreach ($array as $value) {
            static::dump($value);
        }
    }
}