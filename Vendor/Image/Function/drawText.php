<?php
/**
 * 绘制文本 (用指定文件) Image
 * @func_name     drawText
 * @param  string $text      文本
 * @param  int    $x         x坐标
 * @param  int    $y         y坐标
 * @param  int    $size      尺寸
 * @param  array  $color     颜色
 * @param  int    $rotate    旋转度数
 * @param  string $font_file 字体文件
 * @return void
 */
return function (string $text,int $x,int $y,float $size,$color,int $rotate,string $font_file)
{
	if(is_file($font_file) === false) {
		throw new \Exception($ont_file.'文件不存在', 500);
	}
	$color = $this->getColor($color);
	self::$driver->drawText($text,$x,$y,$size,$color,$rotate,$font_file);
};