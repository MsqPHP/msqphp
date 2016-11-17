<?php declare(strict_types = 1);
namespace msqphp\main\view;

use msqphp\base;
use msqphp\core;
use msqphp\main;

trait ViewComponentTrait
{
    /**
     * @param  string $material 原料名
     * @param  string $part     零件名称
     * @param  string $package  组件名
     */


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

    /**
     * 得到视图文件路径
     * 基础路劲/[route分组][语言][主题]文件名[后缀]
     * @param   string  $name  名称
     * @param   string  $type  类型
     * @return  string
     */
    protected function getTplFilePath(string $name, string $type) : string
    {
        $options  = $this->options;

        $group    = $name[0] === '/' ? $name : $this->getGroup();

        $theme    = $this->multiple_theme    ? $options['theme'] . DIRECTORY_SEPARATOR : '';
        $language = $this->multilingual ? $options['language'] . DIRECTORY_SEPARATOR : '';

        $middle = $group . $language . $theme . $name;

        switch ($type) {
            case 'material' :
                $file = $this->config['tpl_material_path'] . $middle . $this->config['tpl_material_ext'];
                return is_file($file) ? $file : $this->config['tpl_material_path'] . $group . $theme . $name . $this->config['tpl_material_ext'];
            case 'part' :
                return $this->config['tpl_part_path']      . $middle . $this->config['tpl_part_ext'];
            case 'package' :
                return $this->config['tpl_package_path']   . $middle . $this->config['tpl_package_ext'];
            default :
                $this->exception('获取模版路劲错误,未知的模版类型:'.$type);
        }
    }

    /**
     * 加工一个原件 material->part
     *
     * @param  string      $part_name 加工后组件名称
     * @param  int|integer $expire    有效期
     *
     * @return self
     */
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
                $this->data,
                $this->multilingual ? $this->getLanguageData($part_name) : []
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
        foreach ($this->data as $tpl_key => $tpl_value) {
            $tpl_arr[$tpl_key] = $tpl_value['value'];
        }
        // 打散
        extract($tpl_arr, EXTR_OVERWRITE);

        // 静态则ob, 否则直接require
        if ($this->static) {
            ob_start();
            ob_implicit_flush(0);
        }

        foreach ($______tpl_show_files as $______tpl_show_file) {
            require $______tpl_show_file;
        }

        if ($this->static) {
            $this->options['static_content'] .= ob_get_flush();
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