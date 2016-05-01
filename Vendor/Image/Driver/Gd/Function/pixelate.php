<?php
/**
 * 像素化图片
 * @func_name  pixelate
 * @param  int $px 像素大小
 * @return vodi
 */
return function ($px) {
	imagefilter($this->resource,IMG_FILTER_PIXELATE,$px);
};