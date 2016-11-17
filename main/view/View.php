<?php declare(strict_types = 1);
namespace msqphp\main\view;

use msqphp\base;

abstract class View
{
    // 数据操作trait
    use ViewDataTrait;

    // 多主题trait
    use ViewThemeTrait, ViewLanguageTrait, ViewStaticTrait, ViewGroupTrait;

    // 组件trait
    use ViewComponentTrait;

    // 当前视图选项
    protected $options  = [];

    // 当前配置
    protected $config   = [];

    // 当前组件
    protected $component = [];

    // 构造函数
    public function __construct()
    {
        // 载入配置
        $config = app()->config->get('view');

        // 目录赋值
        if (!is_dir($config['tpl_material_path']) || !is_dir($config['tpl_part_path']) || !is_dir($config['tpl_package_path'])) {
            $this->exception('模版路径或模版缓存路径不存在');
        }

        // 模版路径
        $config['tpl_material_path'] = realpath($config['tpl_material_path']) . DIRECTORY_SEPARATOR;
        // 缓存路径
        $config['tpl_part_path']     = realpath($config['tpl_part_path']) . DIRECTORY_SEPARATOR;
        // 最终路径
        $config['tpl_package_path']  = realpath($config['tpl_package_path']) . DIRECTORY_SEPARATOR;

        // 赋值
        $this->config = $config;

        // 是否支持多主题
        $config['multiple_theme']    && $this->initTheme();

        // 是否支持多语
        $config['multilingual'] && $this->initLanguage();
    }

    // 抛出异常
    private function exception(string $message) : void
    {
        throw new ViewException($message);
    }

    public function __destruct()
    {
        isset($this->options['static']) && $this->options['static'] && $this->writeStaticHtml();
    }
}