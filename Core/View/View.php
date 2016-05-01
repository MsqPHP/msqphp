<?php declare(strict_types = 1);
namespace Msqphp\Core\View;

use Msqphp\Core;
use Msqphp\Base;

abstract class View
{
    //二维数组，一维存放模版变量名字，二维存放对应值，缓存，类型
    protected $tpl_vars       = [];
    private $cont = '';
    //模版位置,后缀
    private $tpl_path         = '';//仅仅到主题，不涉及语言D:\guao\web\guao\Application\Module\Home\Templates\Default\
    private $tpl_ext          = '';
    //缓存文件路径，前缀，后缀
    private $tpl_c_path       = '';//到根目录,例D:\guao\web\guao\Application\Module\Home\Templates_c\Default\zh-cn\Index
    private $tpl_c_pre        = '';
    private $tpl_c_ext        = '';
    //最终缓存文件路径，前缀，后缀
    private $tpl_l_path       = '';//到根目录,例D:\guao\web\guao\Application\Module\Home\Templates_l\Default\zh-cn\Index
    private $tpl_l_pre        = '';
    private $tpl_l_ext        = '';
    //当前语言及语言文件路径
    private $lang             = '';//加斜杠版例 zh-cn/
    private $lang_path        = '';
    //是否为静态页面
    private $static           = false;
    //后缀
    private $static_ext       = '.html';
    //内容
    private $static_content   = '';
    //文件路径
    private $static_path      = '';
    //
    private $display_arr      = [];
    //
    private $layout           = false;
    private $layout_begin     = '';
    private $layout_end       = '';

    public function __construct(string $theme = '') {
        $view = $this;
        //载入配置
        $config = require \Msqphp\Environment::$config_path.'view.php';
        //是否支持多主题
        $config['multi_theme'] === true && (in_array($theme,$config['theme_sport_list']) || $theme = $config['default_theme']);
        //获得app_info
        $app_info                       = \Msqphp\Core\App\App::$info;

        $module                         = isset($app_info['module']) ? $app_info['module'] . DIRECTORY_SEPARATOR : '';
        //控制器名，加斜杠，例
        $view->cont = $controller       = $app_info['controller'] . DIRECTORY_SEPARATOR;
        //当前方法名称
        $method                         = $app_info['method'];
        //获得当前语言
        $view->lang                     = $language = $app_info['language'] ?? '';
        !empty($language) && ($language = $language.DIRECTORY_SEPARATOR);
        $view->lang_path                = realpath($config['language_path']).DIRECTORY_SEPARATOR.$module.$language.$controller;
        //路径化 如果不为空加 '/',否则至空
        !empty($theme) && ($theme       = $theme.DIRECTORY_SEPARATOR) || ($theme = '');
        
        //模版路径 例:
        $view->tpl_path  = $tpl_path    = realpath($config['templates_path']).DIRECTORY_SEPARATOR.$module.$theme;
        //模版后缀
        $view->tpl_ext                  = $config['tpl_ext'] ?? '';
        //缓存路径，前缀，后缀等拼接
        $view->tpl_c_pre                = $config['tpl_c_pre'] ?? '';
        $view->tpl_c_ext                = $config['tpl_c_ext'] ?? '';

        $view->tpl_c_path               = realpath($config['templates_cache_path']).DIRECTORY_SEPARATOR.$theme.$language.$controller;
        //最终缓存
        $view->tpl_l_pre                = $config['tpl_l_pre'] ?? '';
        $view->tpl_l_ext                = $config['tpl_l_ext'] ?? '';
        $view->tpl_l_path               = realpath($config['templates_last_path']).DIRECTORY_SEPARATOR.$theme.$language.$controller;
        //layout
        $view->layout                   = $config['layout_on'];
        $view->layout_begin             = $tpl_path.$controller.$config['latout_begin_file'];
        $view->layout_end               = $tpl_path.$controller.$config['latout_end_file'];
        //文件驱动
        $view->file                     = Base\File\File::getInstance();
    }
    /**
     * 模版变量赋值
     * @param  string|array  $tpl_var  变量名称或对应值
     * @param  string  $value 变量值
     * @param  boolen  $cache 是否缓存
     * @param  boolen  $type  是否仅仅为html文本
     * @return boolen  是否成功
     */
    public function assign($tpl_var, $value='', bool $cache=false, bool $type=false) {
        //获得当前视图对象
        $view = $this;
        //定义一个数组，存放变量数据
        $tpl_arr = [];
        //数组
        if(is_array($tpl_var)) {
            $tpl_key = $tpl_var['key'] ?? $tpl_var[0] ?? '';
            $value = $tpl_var['value'] ?? $tpl_var[1] ?? '';
            $cache = $tpl_var['cache'] ?? $tpl_var[2] ?? false;
            $type  = $tpl_var['type']  ?? $tpl_var[3] ?? false;
        } elseif(is_string($tpl_var)) {//字符串
            $tpl_key = $tpl_var;
        } else {
            throw new ViewException("{$tpl_var}数据有误", 500);
        }
        //转义
        $type === true && ($value = \Msqphp\Vendor\Filter\Filter::html($value));
        //赋值
        $tpl_arr['value'] = $value;
        $tpl_arr['cache'] = $cache;
        //赋值
        $view->tpl_vars[$tpl_key] = $tpl_arr;

        return $this;
    }
    /**
     * 取得模版变量的值
     * @param  string $name 变量名称
     * @return mix 未传值返回整个模版变量，存在返回对应值，否则false
     */
    public function get(string $name = '')
    {
        //获得当前视图对象
        $view = $this;
        if($name === '') {
            return $view->tpl_vars;
        }
        return $view->tpl_vars[$name] ?? false;
    }
    /**
     * 加载模板和页面输出
     * @param  string       $file_name 模版名
     * @param  int|integer  $expire  缓存时间
     * @return $this
     */
    public function display(string $file_name,int $expire=86400)
    {
        //缓存路径
        $this->display_arr[] = $tpl_c_file = $this->tpl_c_path.$this->tpl_c_pre.$file_name.$this->tpl_c_ext;
        //判断是否有专门的语言模版文件，否则
        if (defined('NO_VIEW') || !is_file($tpl_c_file) || (filemtime($tpl_c_file) + $expire < time())) {
            //模版路径
            //是否有 某语言专用模版
            $tpl_file = $this->tpl_path.$this->lang.$this->cont.$file_name.$this->tpl_ext;
            is_file($tpl_file) || $tpl_file = $this->tpl_path.$this->cont.$file_name.$this->tpl_ext;

            if (!is_file($tpl_file)) {
                throw new ViewException($tpl_file.'模版文件不存在');
                return '';
            }
            //获得当前的设置
            $config = [];
            $config['language_path'] = $this->lang_path.$file_name.'.php';
            $config['tpl_vars'] = $this->tpl_vars;
            $config['tpl_file_path'] = $tpl_file;
            $config['tpl_file_c_path'] = $tpl_c_file;
            Core\Template\Template::cache($config);
        }
        return $this;
    }
    public function displayed(string $file_name,int $expire=86400)
    {
        $tpl_c_file = $this->tpl_c_path.$this->tpl_c_pre.$file_name.$this->tpl_c_ext;
        return !defined('NO_VIEW') && is_file($tpl_c_file) && (filemtime($tpl_c_file) + $expire < time());
    }
    public function layout(bool $layout = true)
    {
        $this->layout = $layout;
        return $this;
    }
    /**
     * 拼装并生成最终的tpl文件
     * @param  string $file_name 储存名称
     * @return $this
     */
    public function assemble(string $file_name)
    {

        $file = $this->tpl_l_path.$this->tpl_l_pre.$file_name.$this->tpl_l_ext;
        $content = '';

        $this->layout && $content .= $this->file->get($this->layout_begin);
        foreach ($this->display_arr as $show_file) {
            $content .= $this->file->get($show_file);
        }
        $this->layout && $content .= $this->file->get($this->layout_end);

        $content = $this->file->formatHtml($content);
        $this->file->write($file,$content,true);
        unset($content);
        return $this;
    }
    /**
     * 静态页面
     * @param  int|integer  $deadtime    过期时间
     * @return $this
     */
    public function static(int $deadtime = 3600)
    {
        if (defined('NO_STATIC')) {
            $this->static = false;
            return $this;
        }
        //静态
        $this->static = true;
        //拼接静态文件路径
        $public_path   = \Msqphp\Environment::$public_path;
        $param = strtr(trim($_SERVER['QUERY_STRING'],'/'),'/',DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->static_path = $public_path.$param.'index.php';
        
        //设置开头内容
        $deadtime = time() + $deadtime;
        $root_path = \Msqphp\Environment::$root_path;
        //过期则引入入口文件,重新处理
        $this->static_content = '<?php if(time() >' . $deadtime.') {require \''.$root_path.'bootstrap/app.php\';exit;} ?>';
        return $this;
    }
    /**
     * 展示页面
     * @param  string      $file_name 文件名(路径为 最终缓存)
     * @param  int|integer $expire    过期时间
     * @return bool
     */
    public function show(string $file_name,int $expire = 86400) : bool
    {
        //拼装文件路径
        $file_path = $this->tpl_l_path.$this->tpl_l_pre.$file_name.$this->tpl_l_ext;
        
        $tpl_arr = [];
        //遍历赋值
        foreach ($this->tpl_vars as $tpl_key => $tpl_value) {
            $tpl_arr[$tpl_key] = $tpl_value['value'];
        }
        //打散
        extract($tpl_arr,EXTR_OVERWRITE);
        //静态则ob,否则直接Include
        if($this->static === true) {
            ob_start();
            require $file_path;
            $static_content = ob_get_flush();
            $this->static_content .= $static_content;
        } else {
            require $file_path;
        }
        return true;
    }
    public function showed(string $file_name,int $expire = 86400) : bool
    {
        //拼装文件路径
        $file_path = $this->tpl_l_path.$this->tpl_l_pre.$file_name.$this->tpl_l_ext;
        //文件不存在或者已过期
        if (defined('NO_VIEW') || !is_file($file_path) || (filemtime($file_path) + $expire < time())) {
            return false;
        }
        return true;
    }
    public function __destruct() {
        $this->static === true && $this->file->write($this->static_path,$this->static_content,true);
    }
}