<?php
/**
 * 模糊
 * @func_name  blur
 * @param  int  $amount 模糊次数
 * @param  int  $type   类型
 * @return void
 */
return function (int $amount, int $type = IMG_FILTER_GAUSSIAN_BLUR) {
    $i = $amount;
    while ($i>0) {
        imagefilter($this->resource, $type);
        --$i;
    }
};