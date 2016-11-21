<?php declare(strict_types = 1);
namespace msqphp\main\view;

use msqphp\base;
use msqphp\core;
use msqphp\main;

abstract class View
{
    // 当前配置
    protected $config      = [];
    // 当前组件
    protected $component   = [];
    protected $data        = null;
    protected $theme       = null;
    protected $group       = null;
    protected $static_html = null;
    protected $language    = null;

    // 抛出异常
    private function exception(string $message) : void
    {
        throw new ViewException($message);
    }

    // 构造函数
    public function __construct()
    {
        // 初始化配置
        $this->initConfig();
        // 获得一个视图数据对象
        $this->data = new component\Data();
        // 获得一个视图分组对象
        $this->group = new component\Group();
        // 如果支持多主题,获得一个视图主题对象
        $this->config['multiple_theme'] && $this->theme = new component\Theme($this->config['theme_config']);
        // 如果支持多语言,获得一个视图语言对象
        $this->config['multilingual']   && $this->language = new component\Language($this->config['language_config']);
    }

    // 配置初始化
    private function initConfig() : void
    {
        // 载入配置
        $config = app()->config->get('view');

        // 目录检测
        is_dir($config['tpl_material_path']) || $this->exception('模版原料路径不存在');
        is_dir($config['tpl_part_path']) || $this->exception('模版零件缓存路径不存在');
        is_dir($config['tpl_package_path']) || $this->exception('模版组件缓存路径不存在');

        // 重新赋值
        $config['tpl_material_path'] = realpath($config['tpl_material_path']) . DIRECTORY_SEPARATOR;
        $config['tpl_part_path']     = realpath($config['tpl_part_path']) . DIRECTORY_SEPARATOR;
        $config['tpl_package_path']  = realpath($config['tpl_package_path']) . DIRECTORY_SEPARATOR;

        // 赋值
        $this->config = $config;
    }

    /**
     * 得到视图文件路径
     * 基础路径/[route分组][语言][主题]文件名[后缀]
     *
     * @param   string  $name  名称
     * @param   string  $type  类型
     *
     * @return  string
     */
    protected function getTplFilePath(string $name, string $type) : string
    {
        $group = $name[0] === '/' || $name[0] === '\\' ? '' : $this->group->get();
        $theme    = $this->theme === null ? '' : $this->theme->get() . DIRECTORY_SEPARATOR;
        $language = $this->language === null ? '' : $this->language->get() . DIRECTORY_SEPARATOR;

        $middle = $language . $theme . $group . $name;

        switch ($type) {
            case 'material' :
                $file = $this->config['tpl_material_path'] . $middle . $this->config['tpl_material_ext'];
                return is_file($file) ? $file : $this->config['tpl_material_path'] . $theme . $group . $name . $this->config['tpl_material_ext'];
            case 'part' :
                return $this->config['tpl_part_path']      . $middle . $this->config['tpl_part_ext'];
            case 'package' :
                return $this->config['tpl_package_path']   . $middle . $this->config['tpl_package_ext'];
            default :
                $this->exception('获取模版路劲错误,未知的模版类型:'.$type);
        }
    }


    /**
     * @param  string $material       原料名
     * @param  string $part
     * @param  string $part_name      零件名称
     * @param  string $package
     * @param  string $package_name   组件名
     * @param  int    $expire         过期时间
     */

    // 当前页面为静态页面
    public function static(int $expire = 3600) : self
    {
        // 允许静态则生成一个视图静态页面对象
        HAS_STATIC && $this->static_html = new component\StaticHtml(['expire'=>$expire]);
        return $this;
    }

    // 添加一个原料
    public function material(string $material) : self
    {
        $this->component[] = ['type'=>'material', 'name' => $material];
        return $this;
    }

    // 添加一个零件
    public function part(string $part) : self
    {
        $this->component[] = ['type'=>'part', 'name' => $part];
        return $this;
    }
    // 添加一个组件
    public function package(string $package) : self
    {
        $this->component[] = ['type'=>'package', 'name' => $package];
        return $this;
    }

    // 加工一个原件 material->part
    public function process(string $part_name, int $expire = 7200) : void
    {
        $material_info = array_pop($this->component);

        if ('material' !== $material_info['type']) {
            $this->exception('模版文件无法加工,原因:当前视图组件中最后一个不为原料视图');
        }

        $material_file = $this->getTplFilePath($material_info['name'], 'material');

        is_file($material_file) || $this->exception('模版文件无法加工,原因:'.$material_info['name'].'模版不存在,模版文件位置应为'.$material_file);

        $part_file = $this->getTplFilePath($part_name, 'part');

        base\file\File::write(
            $part_file,
            main\template\Template::commpile(
                base\file\File::get($material_file),
                $this->data->getAll(),
                $this->language === null ? $this->language->getData($part_name, $this->group->get()) : []
            ),
            true
        );

        0 !== $expire && core\cron\Cron::add($part_file.'视图缓存定时删除', 'deleteFile', $part_file, HAS_VIEW ? time()+$expire : 0);
    }

    // 原材料是否加工过,也可以理解为零件是否存在
    public function processed(string $part_name) : bool
    {
        return HAS_VIEW && is_file($this->getTplFilePath($part_name, 'part'));
    }

    // 拼装 part->package
    public function assemble(string $package_name, int $expire = 3600) : void
    {
        $material = $this->getAllComponnt();

        $package_file = $this->getTplFilePath($package_name, 'package');

        $result = '';

        foreach ($material as $file) {
            $result .= base\file\File::get($file);
        }

        base\file\File::write($package_file, $result, true);

        unset($result);

        0 !== $expire && core\cron\Cron::add($package_file.'视图缓存定时删除', 'deleteFile', $package_file, HAS_VIEW ? time()+$expire : 0);

        $this->component = [];
    }

    // 是否拼装过,也可以理解为组件是否存在
    public function assembled(string $package_name) : bool
    {
        return HAS_VIEW && is_file($this->getTplFilePath($package_name, 'package'));
    }

    // 展示
    public function show() : void
    {

        $______tpl_show_files = $this->getAllComponnt();

        $tpl_arr = [];

        // 遍历赋值
        foreach ($this->data->getAll() as $tpl_key => $tpl_value) {
            $tpl_arr[$tpl_key] = $tpl_value['value'];
        }
        // 打散
        extract($tpl_arr, EXTR_OVERWRITE);

        // 静态则ob, 否则直接require
        if ($this->static_html === null) {
            foreach ($______tpl_show_files as $______tpl_show_file) {
                require $______tpl_show_file;
            }
        } else {
            ob_start();
            ob_implicit_flush(0);
            foreach ($______tpl_show_files as $______tpl_show_file) {
                require $______tpl_show_file;
            }
            $this->static_html->addContent(ob_get_flush());
        }
    }

    private function getAllComponnt() : array
    {
        $result = [];
        foreach ($this->component as ['type'=>$type, 'name'=>$name]) {
            $file = $this->getTplFilePath($name, $type);
            if ('part' === $type && (!is_file($file) || !HAS_VIEW)) {
                $this->component[] = ['type'=>'material', 'name'=>$name];
                $this->process($name);
            }
            if (!is_file($file)) {
                $this->exception('视图组装失败,原因组件'.(string)$name.'不存在');
            }
            $result[] = $file;
        }
        return $result;
    }
}