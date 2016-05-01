<?php
/**
 * 填充矩形
 * @func_nam      fillRect    
 * @param  int    $x       x坐标
 * @param  int    $y       y坐标
 * @param  int    $width   宽
 * @param  int    $height  高
 * @param  array  $color 颜色
 * @return void
 */
return function (int $x,int $y,int $w,int $h,array $color) {
	imagefilledrectangle($this->resource,$x,$y,$w,$h,$this->getColor($color));
};