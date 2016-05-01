<?php
	/**
	 * 随机绘制点,直线,矩形
	 * @func_name      drawRandom
	 * @param  int     $num   数量
	 * @param  int     $type  绘制类型(1点，2直线，3矩形)
	 * @param  int     $min_x min_x
	 * @param  int     $min_y min_y
	 * @param  int     $max_x max_x
	 * @param  int     $max_y max_y
	 * @param          $image 图片资源
	 * @return $this
	 */
return function (int $num = 1,$type = 1,int $min_x=0,int $min_y=0,int $max_x=0,int $max_y=0)
{
		
	//绘制范围获取及绘制对象获取
	if($max_x === 0 && $max_y === 0) {
		//最大位置坐标获取
		$image_info = self::$driver->getImageInfo();
		$max_x = $image_info['width'] ?? 0;$max_y = $image_info['height'] ?? 0;
	}
	if($type >3 || $type < 1) {
		throw new \Exception($type.'应为1-3，1点，2直线，3矩形', 500);
	}
	if($min_x<0||$min_y<0||$max_x<$min_x||$max_y<$min_y) {
		throw new \Exception($min_x.$min_y.$max_x.$max_y.'随机位置错误', 500);
	}
	static $color_str = 'AAAAAABBBBBBCCCCCCDDDDDDEEEEEEFFFFFFF012345678901234567890123456789012345678901234567890123456789';
	//循环
	for($i=$num;$i>0;--$i) {
		
		//获取颜色
		$color = array('r'=>rand(0,255),'g'=>rand(0,255),'b'=>rand(0,255));
		//获得随机大小
		$x=rand($min_x,$max_x);
		$y=rand($min_y,$max_y);
		if($type !== 1) {
			$end_x=rand($min_x,$max_x);
			$end_y=rand($min_y,$max_y);
		}
		//绘制
		$type === 1 && self::$driver->drawPoint($x,$y,$color);
		$type === 2 && self::$driver->drawLine( $x,$y,$end_x,$end_y,$color);
		$type === 3 && self::$driver->drawRect( $x,$y,$end_x,$end_y,$color);
	}
};