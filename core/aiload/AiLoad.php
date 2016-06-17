<?php declare(strict_types = 1);
namespace msqphp\core\aiload;

use msqphp\base;
use msqphp\traits;

/**
 * 实现原理:
 * $info = [
 *     'last'     => [],//最终需要加载的文件
 *     'needful'  => [],//需要加载的文件,但不确定依赖关系(加载顺序),
 *     'tidied'   => [],//整理过的文件列表,直接依次加载即可
 * ];
 * 主要逻辑为对数据的处理
 * 如果$info不存在,取空,返回
 * 如果$info['last']存在并且$info['needful']不存在,直接加载返回
 * 其他情况进行整理
 */

final class AiLoad
{
    use traits\Instance;

    public static $composer = [];

    private $pointer = [];

    public function init() : self
    {
        $this->pointer = [];
        return $this;
    }
    public function key(string $key) : self
    {
        $this->pointer['key'] = $key;
        return $this;
    }
    public function load() : self
    {
        $this->pointer['info'] = [];

        //文件不存在,自动加载信息为空,直接返回
        if (defined('NO_CACHE') || !is_file($file = $this->getFile($this->pointer['key']))) {
            return $this;
        }

        //载入文件
        $info = require $file;

        //如果得到最终结果,直接加载所有文件并返回
        if (isset($info['last']) && !isset($info['needful'])) {

            array_map(function ($file) {
                require $file;
            }, $info['last']);

            $this->pointer['info'] = $info;

            return $this;
        }

        //需要加载的文件
        $needful = [];
        //整理过后的列表
        $tidied  = [];

        //载入整理过文件的函数
        $require_tidied_file = function (string $file) use (& $tidied) {
            require $file;
            $tidied[] = $file;
        };

        //如果有最终缓存的话,重新加载放入整理层中
        isset($info['last']) && array_map($require_tidied_file, $info['last']);

        //颠倒needful,并加载
        array_map(function($file) use(& $tidied, & $needful) {
            //如果已经加载过,再次放入needful
            if (in_array($file, \msqphp\Environment::$autoload_classes)) {
                $needful[] = $file;
            } else {
            //添加到整理过的
                $tidied[] = $file;
                require $file;
            }
        }, array_reverse($info['needful']));

        isset($info['tidied']) && array_map($require_tidied_file, $info['tidied']);

        //如果needful为空,则表示得到最终加载顺序
        $this->pointer['info'] = empty($needful) ? ['last'=>$tidied] : ['needful'=>$needful,'tidied'=>$tidied];

        return $this;
    }

    public function end()
    {
        static::unsetInstance();
    }

    public function last() : bool
    {
        return !empty(\msqphp\Environment::$autoload_classes) || !isset($this->pointer['info']['last']);
    }

    private function getFile(string $key) : string
    {
        return \msqphp\Environment::getPath('storage').'framework'.DIRECTORY_SEPARATOR.'autoload'.DIRECTORY_SEPARATOR.md5($key).'.php';
    }

    public function delete() : self
    {
        base\file\File::delete($this->getFile($this->pointer['key']), true);
        return $this;
    }
    public function deleteAll()
    {
        base\dir\Dir::empty(\msqphp\Environment::getPath('storage').'framework'.DIRECTORY_SEPARATOR.'autoload', true);
    }
    public function update() : self
    {
        $info = $this->pointer['info'];
        //添加至必须
        !empty(\msqphp\Environment::$autoload_classes) && $info['needful'] = array_merge($info['needful'] ?? [], \msqphp\Environment::$autoload_classes);

        //清空,避免对其他地方自动加载造成污染;
        static::$composer = array_merge(static::$composer, \msqphp\Environment::$autoload_classes);

        \msqphp\Environment::$autoload_classes = [];

        //取唯一值,避免重复值
        isset($info['needful']) && $info['needful'] = array_unique($info['needful']);

        isset($info['tidied'])  && $info['tidied']  = array_unique($info['tidied']);

        $this->pointer['info'] = $info;


        base\file\File::write($this->getFile($this->pointer['key']), '<?php'.PHP_EOL.'return '.var_export($this->pointer['info'], true).';', true);

        return $this;

    }
}