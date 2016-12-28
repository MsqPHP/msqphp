<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\core\traits;

final class Route
{
    // 万能静态call
    use traits\CallStatic;

    // 解析,规则
    use RouteParseTrait, RouteRouleTrait;

    // 分组,限制
    use RouteCategoryTrait, RouteLimiteTrait;

    // 方法,静态
    use RouteMethodTrait, RouteStaticTrait;

    // 当前处理的url
    private static $url           = '';

    // 待处理路径
    private static $pending_path  = [];

    // 当前命名空间
    private static $namespace     = '\\app\\';

    // 是否匹配成功过
    private static $matched       = false;

    // 异常抛出
    private static function exception(string $message) : void
    {
        throw new RouteException($message);
    }

    // route运行
    public static function run() : void
    {
        // 解析路径和参数
        static::parsePathAndQuery();
        // 路由流程文件
        $file = \msqphp\Environment::getPath('application') . 'route.php';
        is_file($file) || static::exception('路由解析失败,原因:路由流程文件'.$file.'不存在');
        // 载入文件
        require \msqphp\Environment::getPath('application') . 'route_rule.php';
        require $file;
    }

    // 构建并获取url常量
    public static function bulid() : string
    {
        $url = static::getProtocol().'://'.static::getDomain().'/'.static::$url;
        defined('__URL__') || define('__URL__', $url);
        return $url;
    }
}