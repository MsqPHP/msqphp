<?php
	/**
	 * 水印 位置(1-9) 左上左中左下 中上中中中下 右上右中右下,水印路径
	 * @func_name     water
	 * @param  int    $position 水印位置
	 * @param  string $path     水印路径
	 * @return $this
	 */
return function (int $position = 9,string $path = 'n') {
	$path = $this->getImagePath('water',$path);
	self::$driver->water($position,$path);
	return $this;
};