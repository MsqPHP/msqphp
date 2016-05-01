<?php
/**
 * 去色
 * @func_name   desaturate
 * @return void
 */
return function () {
	imagefilter($this->resource,IMG_FILTER_GRAYSCALE);
};