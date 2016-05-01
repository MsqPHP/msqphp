<?php
/**
 * 图片类型转换
 * @func_name     convert
 * @param  string $type 目标类型
 * @return void
 */
return function (string $type) {
	$img = $this;
	if($type === 'gif') {
		imageinterlace($img->resource,0);
		$img->info['type'] = $type;
		$img->info['mimew'] = 'image/gif';
	} else {
		$type === 'jpg' && $type = 'jpeg';
		$img->info['type'] = $type;
		$img->info['mime'] = 'image/'.$type;
	}
}