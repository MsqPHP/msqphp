<?php
/**
 * 缩略图
 * @func_name           thumb
 * @param  int          $max_size 最大尺寸
 * @param  bool|boolean $white    是否留白
 * @return void
 */
return function (int $max_size,bool $white = true) {
		$img = $this;
		$w = $img->info['width'];
		$h = $img->info['height'];
		if($w>$h) {
			$target_w = $max_size;
			$target_h = (int) ($max_size / ($w / $h));
		} else {
			$target_h = $max_size;
			$target_w = (int) ($max_size / ($h / $w));
		}
		if($white === false) {
			$target_image = imagecreatetruecolor($target_w,$target_h);
			$target_x = 0;
			$target_y = 0;
		} else {
			$target_image = imagecreatetruecolor($max_size,$max_size);
			$bg_color = $img->info['bg_color'] ?? array('r'=>0,'g'=>0,'b'=>0);
			$color = $img->createColor($target_image,$bg_color);
			imagefill($target_image,0,0,$color);
			$target_x = (int) (($max_size - $target_w) / 2);
			$target_y = (int) (($max_size - $target_h) / 2);
		}
		imagecopyresampled($target_image,$img->resource,$target_x,$target_y,0,0,$target_w,$target_h,$w,$h);
		imagedestroy($img->resource);
		//修改当前图片信息
		$img->resource = $target_image;
		$img->info['width'] = imagesx($target_image);
		$img->info['height'] = imagesy($target_image);
};