<?php declare(strict_types = 1);
namespace msqphp\core\view;

use msqphp\base;
use msqphp\core;

abstract class View
{
    //多主题
    use ViewThemeTrait;
    //多语支持
    use ViewLanguageTrait;
    //数据操作
    use ViewDataTrait;
    //layout支持
    use ViewLayoutTrait;

    //是否为静态页
    protected $static   = false;
    //当前视图选项
    protected $options  = [];
    //当前配置
    protected $config   = [];

    //构造函数
    public function __construct()
    {
        //载入配置
        $config = core\config\Config::get('view');
        //目录赋值
        if (!is_dir($config['tpl_path']) || !is_dir($config['tpl_cache_path']) || !is_dir($config['tpl_last_path'])) {
            throw new ViwException('模版路径或模版缓存路径不存在');
        }
        //模版路径
        $config['tpl_path']       = realpath($config['tpl_path']) . DIRECTORY_SEPARATOR;
        //缓存路径
        $config['tpl_cache_path'] = realpath($config['tpl_cache_path']) . DIRECTORY_SEPARATOR;
        //最终路径
        $config['tpl_last_path']  = realpath($config['tpl_last_path']) . DIRECTORY_SEPARATOR;

        //赋值
        $this->config = $config;

        //获得分组信息
        $group_info = core\route\Route::$group;

        $group = '';
        for ($i = 0, $l = count($group_info) / 2; $i < $l; ++$i) {
            $group .=  $group_info[$i] . DIRECTORY_SEPARATOR;
        }

        $this->options = ['group'=>$group];

        //是否支持多主题
        $config['theme'] && $this->initTheme();

        //是否支持多语
        $config['language'] && $this->initLanguage();

        //是否支持布局
        $config['layout'] && $this->initLayout();

        unset($config);

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
        $options  = $this->options;
        $theme    = $this->theme ? $options['theme'] . DIRECTORY_SEPARATOR : '';
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
        if (!$this->displayed($file_name)) {
            $tpl_file = $this->getTplFilePath($file_name);

            if (!is_file($tpl_file)) {
                throw new ViewException($tpl_file.'模版文件不存在');
            }

            base\file\File::write(
                $tpl_cache_file,
                core\template\Template::commpile(
                    base\file\File::get($tpl_file),
                    $this->data,
                    $this->language ? $this->getLanguageData($file_name) : []
                ),
                true
            );

            0 !== $expire && core\cron\Cron::getInstance()->set($tpl_cache_file, [core\cron\Cron::DELETE_FILE, $tpl_cache_file], time()+$expire);
        }
        return $this;
    }
    public function need(string $file_name, int $expire = 86400) : self
    {
        return $this->display($file_name, $expire);
    }
    public function assembled(string $file_name) : bool
    {
        return $this->displayed($file_name);
    }
    public function displayed(string $file_name) : bool
    {
        return !defined('NO_VIEW') && is_file($this->getTplCacheFilePath($file_name));
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
            array_map(function (string $file_name) {
                return $this->getTplFilePath($file_name);
            }, $this->options['layout_begin']),
            $this->options['display'],
            array_map(function (string $file_name) {
                return $this->getTplFilePath($file_name);
            }, $this->options['layout_end'])
        )
        : $this->options['display'];

        unset($this->options['display']);

        foreach ($display as $file) {
            $content .= base\file\File::get($file);
        }


        if ($last) {
            $content = base\str\Str::formatHtml($content);
            $tpl_cache_file = $this->getTplLastFilePath($file_name);
        } else {
            $tpl_cache_file = $this->getTplCacheFilePath($file_name);
        }

        base\file\File::write($tpl_cache_file, $content, true);

        unset($content);

        0 !== $expire && core\cron\Cron::getInstance()->set($tpl_cache_file, [core\cron\Cron::DELETE_FILE, $tpl_cache_file], time()+$expire);

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
        $request = trim(strtr(core\route\Route::$info['request'], '/', DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
        empty($request) || $request .= DIRECTORY_SEPARATOR;

        $path = \msqphp\Environment::getPath('public') . $request . 'index.php';

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
        $______tpl_last_file = $this->getTplLastFilePath($file_name);

        $tpl_arr = [];
        //遍历赋值
        foreach ($this->data as $tpl_key => $tpl_value) {
            $tpl_arr[$tpl_key] = $tpl_value['value'];
        }
        //打散
        extract($tpl_arr, EXTR_OVERWRITE);
        //静态则ob, 否则直接require
        if ($this->static) {
            ob_start();
            ob_implicit_flush(0);
            require $______tpl_last_file;
            $this->options['static_content'] .= ob_get_flush();
        } else {
            require $______tpl_last_file;
        }
    }
    public function showed(string $file_name) : bool
    {
        return !defined('NO_VIEW') && is_file($this->getTplLastFilePath($file_name));
    }
    public function __destruct() {
        $this->static && base\file\File::write($this->options['static_path'], $this->options['static_content'], true);
    }
}