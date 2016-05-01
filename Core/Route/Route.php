<?php declare(strict_types = 1);
namespace Msqphp\Core\Route;

use Msqphp\Core;

class Route {
    /**
     * 返回路由信息 $info = array('app'=[]           APP信息       'language','module','controller','method'
     *                            'get'=[]         GET信息
     *                           );
     * @return array
     */
    static public function getRouteInfo(array $config = []) : array
    {
        $config = $config ?: require \Msqphp\Environment::$config_path.'route.php';
        
        //获得参数字符串
        $params_str = trim($_SERVER['REQUEST_URI'] ?? $_SERVER['QUERY_STRING'],'/');
        //后缀去除
        if (in_array(substr($params_str,-4),array('.php','.asp','.jsp','.jsf'))) {
            $params_str = substr($params_str, 0,strlen($params_str)-4);
        }
        if (in_array(substr($params_str,-5),array('.html','.aspx'))) {
            $params_str = substr($params_str, 0,strlen($params_str)-5);
        }
        //url缓存键
        $cache_key = md5($params_str);
        
        //载入配置
        $config = require \Msqphp\Environment::$config_path.'route.php';
        //缓存类
        $cache = Core\Cache\Cache::getInstance()->getDriver($config['cache_handler']);
        //缓存存在直接返回
        if ($cache->available($cache_key)) {
            return $cache->get($cache_key);
        }
        //获得信息
        $info = static::getInfo($params_str,$config);
        //是否缓存
        count($info['get']) < 3 && $cache->set($cache_key,$info);
        //清空配置和缓存类
        unset($config);unset($cache);
        //返回信息
        return $info;
    }
    /**
     * 得到路由信息
     * @param  $url url
     * @return array  信息
     */
    static private function getInfo(string $params_str,array $config) : array
    {
        $type = $config['type'] ?? 1;
        //得到参数数组 一维索引数组
        $params = static::getParams($params_str,$type);
        //通过参数获得信息
        return static::getInfoByParams($params,$config);
    }
    static private function getParams(string $params_str,int $type) : array
    {
        //为空直接返回
        if ($params_str === '') {
            return [];
        }
        //判断类型
        $params = [];
        switch ($type) {
            case 0:
                foreach ($_GET as $key => $value) {
                    $params[] = $key;
                    $params[] = $value;
                }
                break;
            case 1:
                $params_str = str_replace(array('=','&','?'),'/',$params_str);
            case 2:
                $params = explode('/', trim($params_str, '/'));
                break;
            default:
                throw new RouteException('未定义的路由解析类型');
                break;
        }           
        return $params;
    }
    /**
     * 得到路由信息
     * @return array
     */
    static private function getInfoByParams(array $params,array $config) : array
    {        
        $info = [];

        
        //当前url 加语言,模块
        $url = ($_SERVER['REQUEST_SCHEME'] ?? 'http').'://'.($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']).'/';
        //app信息
        $app_info      = [];
        //判断下一个参数是否为语言参数，是则设置，并删除该参数
        if ($config['multi_lang']) {
            if (isset($params[0]) && in_array($params[0],$config['lang_list'])) {
                $app_info['language'] = $params[0];
                $url .= $params[0].'/';
                array_shift($params);
            } else {
                $app_info['language']      = $config['default_lang'];
            }
        }
        //判断下一个参数是否为模块参数，是则设置，并删除该参数
        if ($config['multi_module']) {
            if (isset($params[0]) && in_array(ucfirst($params[0]),$config['module_list'])) {
                $app_info['module'] = ucfirst($params[0]);
                $url .= $params[0].'/';
                array_shift($params);
            } else {
                $app_info['module']      = $config['default_module'];
            }
        }
        
        $info['url'] = $url;
        //参数判断
        if (empty($params)) {
            //没有参数调用默认语言默认模块默认控制器的默认方法
            $app_info['controller']      = $config['default_controller'];
            $app_info['method']          = $config['default_method'];
        } else {
            //控制器，模块处理,如果有参数
            if (count($params) % 2 === 1) {
                //如果奇数个，则认为省略了控制器，取默认控制器
                $app_info['controller']      = $config['default_controller'];
                $app_info['method']     = $params[0];
                array_shift($params);
            } else {
                //如果偶数个,取控制器,方法
                $app_info['controller'] = ucfirst($params[0]);
                $app_info['method']     = $params[1];
                array_shift($params);array_shift($params);
            }
        }

        $info['app'] = $app_info;
        

        //参数组合，变为get参数
        $get = [];
        //遍历，赋值
        for($i=count($params) - 1;$i>=1;$i-=2) {
            //4.1例[id][1][name][zhangsan];
            //count=4;
            //val=zhangsan;key=name
            $val = $params[$i];
            $key = $params[$i - 1];
            $get[$key] = $val;
        }
        $info['get'] = $get;

        return $info;
    }
}