<?php
/**
 * 创建随机rgb颜色
 * @func_name     randomColor
 * @return string 颜色
 */
return function () : string {
    static $c = '0123456789ABCDEF';
    return '#'.$c[rand(0,15)].$c[rand(0,15)].$c[rand(0,15)].$c[rand(0,15)].$c[rand(0,15)].$c[rand(0,15)];
};