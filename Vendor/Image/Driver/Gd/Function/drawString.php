<?php
/**
 * 绘制字符串
 * @func_name     drawText
 * @param  string $string    字符串
 * @param  int    $x         x坐标
 * @param  int    $y         y坐标
 * @param  array  $color     颜色
 * @param  bool  $horizontal 是否水平绘制
 * @return void
 */
return function (string $string,int $x,int $y,array $color,bool $horizontal = true) {
	if($horizontal === true) {
		imagestring($this->resource,5,$x,$y,$string,$this->getColor($color));
	} else {
		imagestringup($this->resource,5,$x,$y,$string,$this->getColor($color));
	}
};