<?php
/**
 * 设置亮度
 * @func_name  brightness
 * @param  int $amout
 * @return void
 */
return function (int $amount) {
    imagefilter($this->resource, IMG_FILTER_BRIGHTNESS, $amount);
};