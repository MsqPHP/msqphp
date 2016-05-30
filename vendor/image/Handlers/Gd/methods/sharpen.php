<?php
/**
 * sharpen
 * @func_name   sharpen
 * @param  int  $amount 次数
 * @return void
 */
return function (int $amount) {
        imagefilter($this->resource, IMG_FILTER_SMOOTH, $amount);
};