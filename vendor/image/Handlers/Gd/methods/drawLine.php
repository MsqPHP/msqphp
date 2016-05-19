<?php
/**
 * 绘制直线
 * @func_name     drawLine
 * @param  int    $x       x坐标
 * @param  int    $y       y坐标
 * @param  int    $end_x   结束x坐标
 * @param  int    $end_y   结束y坐标
 * @param  array  $color 颜色
 * @return void
 */
return function (int $x, int $y, int $end_x, int $end_y, array $color) {
    imageline($this->resource, $x, $y, $end_x, $end_y, $this->getColor($color));
};