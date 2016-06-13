<?php declare(strict_types = 1);
namespace msqphp\base\arr;

use msqphp\base;
use msqphp\traits;

final class Arr
{
    //万能静态call
    use traits\CallStatic;
    /**
     * 设置数组值,以点为分隔符,若键为空,则用值替换数组;
     *
     * @example
     *         $arr = ['liming'=>['username'=>'test','password'=>'123456']];
     *         Arr::set($arr, 'liming.username', 'liming');
     *         $arr ----> ['liming'=>['username'=>'liming','password'=>'123456']];
     *
     * @param  array  $array     待设置数组
     * @param  string $arr_key   数组键
     * @param  miexd  $arr_value 对应值
     *
     * @throws ArrException
     */
    public static function set(array & $array, string $arr_key, $arr_value)
    {
        //如果键为空
        if ('' === $arr_key) {
            $array = $arr_value;
            return;
        }

        //以点分割
        $key = explode('.', $arr_key);

        //结果
        $result = & $array;

        //递归
        for ($i = 0, $l = count($key); $i < $l; ++$i) {
            $result = & $result[$key[$i]];
        }

        //赋值
        $result = $arr_value;
    }
    /**
     * 获取数组值,,以点为分隔符,若键为空,则获取整个数组值;
     *
     * @example
     *         $arr = ['liming'=>['username'=>'test','password'=>'123456']];
     *         Arr::get($arr, 'liming.username')  ----> 'test'
     *
     * @param  array  $array     待设置数组
     * @param  string $arr_key   数组键
     *
     * @throws ArrException
     * @return miexd
     */
    public static function get(array & $array, string $arr_key)
    {
        //键为空
        if ('' === $arr_key) {
            return $array;
        }
        //以点分割
        $key = explode('.', $arr_key);

        //结果
        $result = & $array;
        //递归赋值
        for ($i = 0, $l = count($key); $i < $l; ++$i) {
            $result = & $result[$key[$i]];
        }

        //返回
        return $result;
    }
}