<?php
/**
 * Gd 扩展函数 锐化
 * @func_name pencil
 * @return void
 */
return function() {
	imagefilter($this->resource,IMG_FILTER_MEAN_REMOVAL);
};