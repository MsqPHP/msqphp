<?php
/**
 * 调整对比度
 * @func_name  contrast
 * @param  int $amount
 * @return void
 */
return function (int $amount) {
    imagefilter($this->resource, IMG_FILTER_CONTRAST, $amount);
};