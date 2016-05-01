<?php
/**
 * 着色
 * @func_name     colorize
 * @param  int    $r r
 * @param  int    $g g
 * @param  int    $b b
 * @return void
 */
return function (int $r,int $g,int $b) {
	imagefilter($this->resource, IMG_FILTER_COLORIZE,$r,$g,$b);
};