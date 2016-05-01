<?php
/**
 * 水印 位置(1-9) 左上左中左下 中上中中中下 右上右中右下,水印路径
 * @func_name     water
 * @param  int    $position 水印位置
 * @param  string $path     水印路径
 * @return void
 */
return function (int $position,string $path) {
	//获得当前img对象
	$img              = $this;
	$image_info       = $img->info;
	//得到water图片信息
	$water_info       = $img->getInfoByPath($path);
	//水印计算位置
	$water_position_x = (int) ceil ($position / 3);//1左2中3右
	$water_position_y = (int) (ceil($position % 3) ?: 3);//1上2中3下
	$water_position_x === 1 && $src_x = 0;
	$water_position_y === 1 && $src_y = 0;
	$water_position_x === 2 && $src_x = ceil(($image_info['width'] - $water_info['width']) / 2);
	$water_position_y === 2 && $src_y = ceil(($image_info['height'] - $water_info['height']) / 2);
	$water_position_x === 3 && $src_x = $image_info['width'] - $water_info['width'];
	$water_position_y === 3 && $src_y = $image_info['height'] - $water_info['height'];
	//得到water图片资源
	$water_image      = $img->createResource($path,$water_info['type']);
	//按坐标位置拷贝水印图片到真彩图像上   
	imagecopy($img->resource, $water_image, $src_x, $src_y, 0, 0, $water_info['width'], $water_info['height']);
	//销毁图片资源等
	unset($water_info);
	imagedestroy($water_image);
};