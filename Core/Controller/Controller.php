<?php declare(strict_types = 1);
namespace Msqphp\Core\Controller;

abstract class Controller
{
    public $get = [];
    public $post = [];
    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
    }
    /**
     * 判断post提交
     * @return boolean
     */
    public function isPost() : bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * 判断get提交
     * @return boolean
     */
    public function isGet() : bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * 判断ajax提交
     * @return boolean
     */
    public function isAjax() : bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    /**
     * 页面重定向
     * @param  string  $url  跳转地址
     * @param  integer $code 跳转代码
     * @return void
     */
    public function redirect(string $url, int $code = 302)
    {
        if(headers_sent() === false) {
            header('location:'.$url,true,$code);
        } else {
            echo '<meta http-equiv="Refresh" content="0;url='.$url.'">';
        }
        exit();
    }
    /**
     * 页面跳转
     * @param  string   $url  跳转地址
     * @param  ing      $time 等待时间
     * @param  string  $msg  信息
     * @return void
     */
    public function jump(string $url, int $time = 0,string $msg = '')
    {
        $msg = $msg ?: '系统将在'.$time.'秒之后自动跳转到<a href="'.$url.'">'.$url.'</a>！';
        if(headers_sent() === false) {
            if($time > 0) {
                header('refresh:'.$time.';url='.$url);
                echo($msg);
            } else {
                header('location:'.$url,true);
            }
        } else {
            $str = '<meta http-equiv="Refresh" content="'.$time.'URL='.$url.'">';
            $time > 0 || ($str .=$msg);
            echo $str;
        }
        exit();
    }
    /**
     * 得到设置token
     */
    public function getToken()
    {
        // return $_COOKIE['token'];
    }
    public function setToken()
    {

    }
    /**
     * JS窗口提示并跳转
     * @param  string $msg     提示消息
     * @param  string $url     跳转URL
     * @param  string $charset 页面编码
     * @return void
     */
    public function alert(string $msg, string $url = '', $charset='utf-8')
    {
        header('Content-type: text/html; charset='.$charset);
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
     * 错误信息显示
     */
    public function error(string $msg,int $time = 3,string $url = '')
    {
        echo $msg;exit;
        $tpl_path = APP_PATH.$_config['TPL_PATH'].'error.html';
        include $tpl_path;exit();
    }

    public function __get(string $name)
    {
        static $extension = [];
        if (!isset($extension[$name])) {
            $extension[$name] = require __DIR__.'/Get/'.$name.'.php';
        }
        return $extension[$name];
    }
    public function __call(string $method,array $args)
    {
        static $func = [];
        if (!isset($func[$method])) {
            $func[$method] = require __DIR__.DIRECTORY_SEPARATOR.'Function'.DIRECTORY_SEPARATOR.$method.'.php';
        }
        return call_user_func_array($func[$method], $args);
    }
}