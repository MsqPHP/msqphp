<?php
/**
 * 裁剪 从 (x,y)到(x+width,y+height)
 * @func_name          crop
 * @param  int         $w 宽度
 * @param  int         $h 高度
 * @param  int|integer $x 开始X坐标
 * @param  int|integer $y 开始y坐标
 * @return void
 */
return function (int $w,int $h,int $x = 0,int $y = 0) {
	$img = $this;
	$resource = imagecrop($img->resource,array('x'=>$x,'y'=>$y,'width'=>$w,'height'=>$h));
	imagedestroy($img->resource);
	$img->resource = $resource;
	$img->info['width'] = $w - $x;
	$img->info['height'] = $h - $y;
};