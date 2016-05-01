<?php
/**
 * 绘制点
 * @func_name     drawPoint
 * @param  int    $x       x坐标
 * @param  int    $y       y坐标
 * @param  array $rgb      颜色(#000000-#FFFFFF)
 * @return void
 */
return function (int $x,int $y,array $color) {
	imagesetpixel($this->resource,$x,$y,$this->getColor($color));
};