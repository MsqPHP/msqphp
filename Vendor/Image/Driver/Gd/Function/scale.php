<?php
/**
 * 缩放
 * @func_name     scale
 * @param  float  $scale 缩放尺度 例(1.2 | 0.5)
 * @return void
 */
return function (float $scale) {
	$img = $this;
	$w = (int) ($img->info['width'] * $scale);
	$h = (int) ($img->info['height'] * $scale);
	$img->resize($w,$h);
};