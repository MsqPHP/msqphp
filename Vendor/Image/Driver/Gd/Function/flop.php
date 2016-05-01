<?php
/**
 * 垂直翻转
 * @func_name  flop
 * @return void
 */
return function () {
	imageflip($this->resource,IMG_FLIP_VERTICAL);
};