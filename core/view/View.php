<?php declare(strict_types = 1);
namespace msqphp\core\view;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

abstract class View
{
    //二维数组，一维存放模版变量键，二维存放对应值，缓存，类型
    protected $data     = [];

    //主题支持
    protected $theme    = false;

    //多语支持
    protected $language = false;

    //布局是否开启
    protected $layout   = false;

    //是否为静态页
    protected $static   = false;

    //当前视图选项
    protected $options  = [];
    //当前配置
    protected $config   = [];

    //构造函数
    public function __construct() {
        //载入配置
        $config = core\config\Config::get('view');
        //目录赋值
        if (!is_dir($config['tpl_path']) || !is_dir($config['tpl_path']) || !is_dir($config['tpl_last_path'])) {
            throw new ViwException('模版路径或模版缓存路径不存在');
        }
        //模版路径
        $config['tpl_path']       = realpath($config['tpl_path']) . DIRECTORY_SEPARATOR;
        //缓存路径
        $config['tpl_cache_path'] = realpath($config['tpl_cache_path']) . DIRECTORY_SEPARATOR;
        //最终路径
        $config['tpl_last_path']  = realpath($config['tpl_last_path']) . DIRECTORY_SEPARATOR;

        //选项
        $options = [];

        //是否支持多主题
        if ($config['theme']) {
            $this->theme = true;
            $options['theme'] = core\route\Route::$info['themt'] ?? $config['default_theme'];
        }

        //是否支持多语
        if ($config['language']) {
            $this->language = true;
            //获得当前语言
            $config['language_path'] = realpath($config['language_path']) . DIRECTORY_SEPARATOR;
            $options['language'] = core\route\Route::$info['language'] ?? $config['default_language'];
        }

        //是否支持布局
        if ($config['layout']) {
            $this->layout = true;
            $options['layout_begin'] = (array) $config['layout_begin'];
            $options['layout_end']   = (array) $config['layout_end'];
        }


        //获得分组信息
        $group_info = core\route\Route::$group;

        $group = '';
        for ($i = 0, $l = count($group_info) / 2; $i < $l; ++$i) {
            $group .=  $group_info[$i] . DIRECTORY_SEPARATOR;
        }

        $options['group'] = $group;

        //赋值
        $this->config = $config;

        $this->options = $options;
    }

    /**
     * 得到模版路径
     * tpl_path/[route分组][语言][主题]文件名[后缀]
     *
     * @param  string $file_name $file_name
     *
     * @return string
     */
    protected function getTplFilePath(string $file_name) : string
    {
        $options = $this->options;
        $theme = $this->theme ? $options['theme'] . DIRECTORY_SEPARATOR : '';
        $language = $this->language ? $options['language'] . DIRECTORY_SEPARATOR : '';

        $file = $this->config['tpl_path'].$options['group'].$language.$theme.$file_name.$this->config['tpl_ext'];

        return is_file($file) ? $file : $this->config['tpl_path'].$options['group'].$theme.$file_name.$this->config['tpl_ext'];
    }
    protected function getTplLastFilePath(string $file_name) : string
    {
        $options = $this->options;

        $theme = $this->theme ? $options['theme'] . DIRECTORY_SEPARATOR : '';
        $language = $this->language ? $options['language'] . DIRECTORY_SEPARATOR : '';

        return $this->config['tpl_last_path'].$theme.$language.$options['group'].$file_name.$this->config['tpl_last_ext'];
    }
    protected function getTplCacheFilePath(string $file_name) : string
    {
        $options = $this->options;
        $theme = $this->theme ? $options['theme'] . DIRECTORY_SEPARATOR : '';
        $language = $this->language ? $options['language'] . DIRECTORY_SEPARATOR : '';
        return $this->config['tpl_cache_path'].$theme.$language.$options['group'].$file_name.$this->config['tpl_cache_ext'];
    }

###
#  多语设置
###
    public function language(string $language = '')
    {
        if ($this->language) {
            if ('' === $language) {
                return $this->getLanguage();
            } else {
                return $this->setSanguage($language);
            }
        } else {
            throw new ViewException('不支持多语');
        }
    }
    protected function getLanguage() : string
    {
        if ($this->language) {
            return $this->options['language'];
        } else {
            throw new ViwException('未设置多语支持');
        }
    }
    protected function setLanguage(string $language) : self
    {
        if ($this->language) {
            $this->options['language'] = $language;
            return $this;
        } else {
            throw new ViwException('未设置多语支持');
        }
    }
    protected function getLanguageData(string $file_name) : array
    {
        if ($this->language) {
            $file = $this->config['language_path'].$this->options['language'].DIRECTORY_SEPARATOR.$this->options['group'].DIRECTORY_SEPARATOR.$file_name.'.php';
            if (!is_file($file)) {
                $file = $this->config['language_path'].$this->config['default_language'].DIRECTORY_SEPARATOR.$this->options['group'].DIRECTORY_SEPARATOR.$file_name.'.php';
            }
            return is_file($file) ? require $file : [];
        } else {
            return [];
        }
    }
###
#  主题
###
    public function theme(string $theme = '')
    {
        if ($this->theme) {
            if ('' === $theme) {
                return $this->getTheme();
            } else {
                return $this->setTheme($theme);
            }
        } else {
            throw new ViewException('不支持主题');
        }
    }
    protected function getTheme() : string
    {
        return $this->options['theme'];
    }
    protected function setTheme(string $theme) : self
    {
        if (in_array($this->config['theme_sport_list'])) {
            $this->options['theme'] = $theme;
            return $this;
        } else {
            throw new ViewException('不支持的主题:'.$theme);
        }
    }
###
#  模版变量操作
###
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
     * @param  string  $value 变量值
     * @param  boolen  $cache 是否缓存
     * @param  boolen  $html  是否仅仅为html文本
     * @throws ViewException
     * @return self
     */
    public function assign($tpl_var, $value='', bool $cache = false, bool $html = false) :self
    {
        //定义一个数组，存放变量数据
        $tpl_arr = [];
        //数组
        if (is_array($tpl_var)) {
            $tpl_key = $tpl_var['key'] ?? $tpl_var[0] ?? '';
            $value = $tpl_var['value'] ?? $tpl_var[1] ?? '';
            $cache = $tpl_var['cache'] ?? $tpl_var[2] ?? false;
            $html  = $tpl_var['html']  ?? $tpl_var[3] ?? false;
        } elseif(is_string($tpl_var)) {//字符串
            $tpl_key = $tpl_var;
        } else {
            throw new ViewException($tpl_var.'数据有误');
        }
        //转义
        $html && $value = base\filter\Filter::html($value);
        //赋值
        $tpl_arr['value'] = $value;
        $tpl_arr['cache'] = $cache;
        //赋值
        $this->data[$tpl_key] = $tpl_arr;

        return $this;
    }
    public function set($tpl_var, $value='', bool $cache = false, bool $html = false) :self
    {
        return $this->assign($tpl_var, $value, $cache, $html);
    }
    /**
     * 删除模版变量
     * @param  string $key [description]
     * @return self
     */
    public function delete(string $key) :self
    {
        unset($this->data[$key]);
        return $this;
    }

###
#  模版操作
###
    public function layout(bool $layout = true) :self
    {
        $this->layout = $layout;
        return $this;
    }
    /**
     * 加载模板和页面输出
     * @param  string       $file_name 模版名
     * @param  int|integer  $expire  缓存时间
     * @return self
     */
    public function display(string $file_name, int $expire = 86400) :self
    {
        //缓存路径
        $this->options['display'][] = $tpl_cache_file = $this->getTplCacheFilePath($file_name);
        //判断是否有专门的语言模版文件，否则
        if ($this->displayed($file_name)) {
            return $this;
        } else {
            $tpl_file = $this->getTplFilePath($file_name);
            if (!is_file($tpl_file)) {
                throw new ViewException($tpl_file.'模版文件不存在');
            }

            $result = core\template\Template::commpile(base\file\File::get($tpl_file), $this->data, $this->getLanguageData($file_name));

            base\file\File::write($tpl_cache_file, $result, true);

            if (0 !== $expire) {
                core\cron\Cron::getInstance()->set($tpl_cache_file, [core\cron\Cron::DELETE_FILE, $tpl_cache_file], time()+$expire);
            }

            return $this;
        }
    }
    public function displayed(string $file_name) : bool
    {
        if (defined('NO_VIEW')) {
            return false;
        } else {
            return is_file($this->getTplCacheFilePath($file_name));
        }
    }
    /**
     * 拼装并生成最终的tpl文件
     *
     * @param  string      $file_name 文件名
     * @param  int|integer $expire    过期时间
     * @param  boolean     $last      是否为最终缓存
     *
     * @return self
     */
    public function assemble(string $file_name, int $expire = 3600, $last = true) : self
    {

        $content = '';

        $display = $this->layout
        ? array_merge(
            array_map(function ($value) {
                return $this->getTplFilePath($value);
            }, $this->options['layout_begin']),
            $this->options['display'],
            array_map(function ($value) {
                return $this->getTplFilePath($value);
            }, $this->options['layout_end'])
        ) : $this->options['display'];

        unset($this->options['display']);

        foreach ($display as $file) {
            $content .= base\file\File::get($file);
        }


        if ($last) {
            !defined('NO_CACHE') && $content = base\str\Str::formatHtml($content);
            $tpl_cache_file = $this->getTplLastFilePath($file_name);
        } else {
            $tpl_cache_file = $this->getTplCacheFilePath($file_name);
        }

        base\file\File::write($tpl_cache_file, $content, true);

        unset($content);
        if (0 !== $expire) {
            core\cron\Cron::getInstance()->set($tpl_cache_file, [core\cron\Cron::DELETE_FILE, $tpl_cache_file], time()+$expire);
        }
        return $this;
    }
    /**
     * 静态页面
     * @param  int|integer  $expire    过期时间
     * @throws ViewException
     * @return self
     */
    public function staticHtml(int $expire = 3600) : self
    {
        if (defined('NO_STATIC')) {
            $this->options['static'] = false;
            return $this;
        }
        $this->options['static'] = true;
        //静态
        $this->static = true;
        //拼接静态文件路径
        $param = empty($_SERVER['QUERY_STRING']) ? '' : trim(strtr($_SERVER['QUERY_STRING'], '/', DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);

        $path = \msqphp\Environment::getPath('public') . $param . DIRECTORY_SEPARATOR . 'index.php';

        $this->options['static_path'] = $path;

        if (0 === $expire) {
            $this->options['static_content'] = '';
        } else {
            $this->options['static_content'] = '<?php if (time() >' . (string) (time() + $expire) .') {require \''.\msqphp\Environment::getPath('bootstrap').'app.php\';exit;}?>';
        }
        if (0 !== $expire) {
            core\cron\Cron::getInstance()->set($path, [core\cron\Cron::DELETE_FILE, $path], time()+$expire);
        }
        return $this;
    }
    /**
     * 展示页面
     * @param  string      $file_name 文件名(路径为 最终缓存)
     * @param  int|integer $expire    过期时间
     * @throws ViewException
     * @return void
     */
    public function show(string $file_name)
    {
        //拼装文件路径
        $tpl_last_file = $this->getTplLastFilePath($file_name);

        $tpl_arr = [];
        //遍历赋值
        foreach ($this->data as $tpl_key => $tpl_value) {
            $tpl_arr[$tpl_key] = $tpl_value['value'];
        }
        //打散
        extract($tpl_arr, EXTR_OVERWRITE);
        //静态则ob, 否则直接require
        if($this->static === true) {
            ob_start();
            ob_implicit_flush(0);
            require $tpl_last_file;
            $this->options['static_content'] .= ob_get_flush();
        } else {
            require $tpl_last_file;
        }
    }
    public function showed(string $file_name) : bool
    {
        if (defined('NO_VIEW')) {
            return false;
        } else {
            return is_file($this->getTplLastFilePath($file_name));
        }
    }
    public function __destruct() {
        $this->static && base\file\File::write($this->options['static_path'], $this->options['static_content'], true);
    }
}