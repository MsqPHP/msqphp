<?php declare(strict_types = 1);
namespace msqphp\core\view;

use msqphp\base;
use msqphp\core;

trait ViewDataTrait
{
    //二维数组，一维存放模版变量键，二维存放对应值，缓存，类型
    protected $data     = [];
    /**
     * 模版变量是否存在
     * @param  string $name 键
     * @return bool
     */
    public function exists(string $name) : bool
    {
        return isset($this->data[$name]);
    }
    /**
     * 取得模版变量的值
     * @param  string $name 变量名称
     * @return mix
     */
    public function get(string $name = '')
    {
        if ('' === $name) {
            return $this->data;
        }
        return $this->data[$name];
    }
    /**
     * 模版变量赋值
     * @param  string|array  $tpl_var  变量名称或对应值
     * @param  miexd   $value 变量值
     * @param  boolen  $cache 是否缓存
     * @param  boolen  $html  是否仅仅为html文本
     * @throws ViewException
     * @return self
     */
    public function assign($tpl_var, $value = null, bool $cache = false, bool $html = false) : self
    {
        //数组
        if (is_array($tpl_var)) {
            $key = $tpl_var['key'] ?? $tpl_var[0] ?? '';
            $value   = $tpl_var['value'] ?? $tpl_var[1] ?? '';
            $cache   = $tpl_var['cache'] ?? $tpl_var[2] ?? false;
            $html    = $tpl_var['html']  ?? $tpl_var[3] ?? false;
        } elseif (is_string($tpl_var)) {//字符串
            $key = $tpl_var;
        } else {
            throw new ViewException('View数据设置错误,原因:数据格式为'.gettype($tpl_var));
        }

        //转义
        $html && $value = base\filter\Filter::html($value);

        //赋值
        $this->data[$key] = ['value'=>$value,'cache'=>$cache];

        return $this;
    }
    public function set($tpl_var, $value = null, bool $cache = false, bool $html = false) : self
    {
        return $this->assign($tpl_var, $value, $cache, $html);
    }
    /**
     * 删除模版变量
     * @param  string $key [description]
     * @return self
     */
    public function delete(string $key) : self
    {
        unset($this->data[$key]);
        return $this;
    }
}