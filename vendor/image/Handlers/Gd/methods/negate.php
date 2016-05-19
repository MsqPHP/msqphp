<?php
/**
 * 反色
 * @func_name negate
 * @return void
 */
return function () {
    imagefilter($this->resource, IMG_FILTER_NEGATE);
};