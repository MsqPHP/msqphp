<?php
/**
 * 绘制文本
 * @func_name            drawText
 * @param  string        $text      文本
 * @param  int           $x         x坐标
 * @param  int           $y         y坐标
 * @param  int           $size      尺寸
 * @param  string|array  $color     颜色
 * @param  string        $font_file 字体文件
 * @return void
 */
return function (string $text,int $x,int $y,float $size,$color,int $rotate,string $font_file) {
	imagettftext($this->resource, $size, $rotate, $x, $y, $this->getColor($color), $font, $text);
};