<?php
/**
 * 水平反转
 * @func_name  flip
 * @return void
 */
return function () {
    imageflip($this->resource, IMG_FLIP_HORIZONTAL);
};