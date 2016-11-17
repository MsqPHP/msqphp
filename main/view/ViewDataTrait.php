<?php declare(strict_types = 1);
namespace msqphp\main\view;

use msqphp\base;

trait ViewDataTrait
{
    // 二维数组，一维存放模版变量键，二维存放对应值，缓存，类型
    protected $data     = [];

    /**
     * @param  string $key 键
     * @param  string|array  $tpl_var  变量名称或对应值
     * @param  miexd   $value 变量值
     * @param  boolen  $cache 是否缓存
     * @param  boolen  $html  是否仅仅为html文本
     * @throws ViewException
     */

    // 模版变量是否存在
    public function exists(string $name) : bool
    {
        return isset($this->data[$name]);
    }
    // 取得模版变量的值
    public function get(?string $name = null)
    {
        if (null === $name) {
            return $this->data;
        }
        return $this->data[$name];
    }
    // 模版变量赋值
    public function assign($tpl_var, $value = null, bool $cache = false, bool $html = false) : void
    {
        // 数组
        if (is_array($tpl_var)) {
            $key     = $tpl_var['key']   ?? $tpl_var[0];
            $value   = $tpl_var['value'] ?? $tpl_var[1];
            $cache   = $tpl_var['cache'] ?? $tpl_var[2] ?? false;
            $html    = $tpl_var['html']  ?? $tpl_var[3] ?? false;
        } elseif (is_string($tpl_var)) {// 字符串
            $key = $tpl_var;
        } else {
            $this->exception('视图数据添加错误,添加数据类型错误');
        }
        // 转义
        $html && $value = base\filter\Filter::html($value);

        // 赋值
        $this->data[$key] = ['value'=>$value,'cache'=>$cache];
    }

    // 模版变量赋值
    public function set($tpl_var, $value = null, bool $cache = false, bool $html = false) : void
    {
        $this->assign($tpl_var, $value, $cache, $html);
    }
    // 删除模版变量
    public function delete(string $key) : void
    {
        unset($this->data[$key]);
    }
}