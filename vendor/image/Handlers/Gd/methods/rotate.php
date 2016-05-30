<?php
    /**
     * 旋转
     * @func_name     rotate
     * @param  int    $degress 旋转图数
     * @return void
     */
return function (int $degress) {
    $img = $this;
    $resource = $img->resource;
    $bg_color = $img->info['bg_color'] ?? '#FFFFFF';
    $color = $img->getColor($bg_color);
    $target_image = imagerotate ($resource, $degress, $color);
    //销毁当前资源
    imagedestroy($img->resource);
    //修改当前图片信息
    $img->resource = $target_image;
    $img->info['width'] = imagesx($target_image);
    $img->info['height'] = imagesy($target_image);
};