<?php
	/**
	 * 图片类型转换
	 * @func_name  convert
	 * @param  string $type 目标类型
	 * @return $this
	 */
return function (string $type) {
	if(in_array($type,$this->allowed) === false) {
		throw new \Exception($type,'不被允许', 500);
	}
	self::$driver->convert($type);
	return $this;
};