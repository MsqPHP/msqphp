<?php declare(strict_types = 1);
namespace msqphp\vendor\image\handlers;

Interface ImageHandlerInterface
{
    /**
     * 是否安装对应扩展
     * @throws  ImageHandlersException
     * @return  void
     */
    public function isInstalled();
    /**
     * 载入图片
     * @param  string $image_path 图片路径
     * @return void
     */
    public function load(string $image_path);
    /**
     * 创建一个新的图片
     * @param  int    $w        宽
     * @param  int    $h        高
     * @param  array  $bg_color 背景色('r'=>??, 'g'=>??, 'b'=>??[, 'a'=>0.1 - `1])
     * @return void
     */
    public function create(int $w, int $h, $bg_color = '#FFFFFF');
    /**
     * 得到图片信息
     * @param  string $key  为空时返回全部
     * @return string 单一信息
     * @return ['width'=>??, 'height'=>??, 'type'=>??('jpeg'), mime=>??);
     */
    public function getImageInfo(string $key = '');
    /**
     * 设置背景颜色
     * @param array $bg_color 背景颜色
     * @param void
     */
    public function setBgColor($bg_color);
    /**
     * 输出 html 图片
     * @param  string $type 输出类型
     * @return void
     */
    public function output(string $type='');
    /**
     * 保存图片
     * @param  string $save_path 保存路径
     * @param  string $type      保存类型
     * @return void
     */
    public function save(string $save_path, string $type);
    /**
     * 关闭并清除所有资源
     * @return void
     */
    public function close();
}