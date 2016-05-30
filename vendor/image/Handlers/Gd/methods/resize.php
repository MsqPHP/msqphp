<?php
/**
 * 尺寸调整
 * @func_name     resize
 * @param  int    $w 目标尺寸
 * @param  int    $h 目标宽带
 * @return void
 */
return function (int $w, int $h) {
    //获得当前img对象
    $img = $this;
    $target_image = imagecreatetruecolor($w, $h);
    //采样到新图片
    imagecopyresampled($target_image, $img->resource, 0, 0, 0, 0, $w, $h, $img->info['width'], $img->info['height']);
    imagedestroy($img->resource);
    $img->resource = $target_image;
    //修改当前图片信息
    $img->info['width'] = $w;
    $img->info['height'] = $h;
};