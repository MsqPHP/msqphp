<?php
/**
 * 扩展函数规范
 */
Interface FunctionInterface {
/*******************************************************
 * 图片处理
 /*****************************************************/
	/**
	 * 尺寸调整
	 * @param  int    $w 目标尺寸
	 * @param  int    $h 目标宽带
	 * @return void
	 */
	public function resize(int $w,int $h);
	/**
	 * 裁剪 从 (x,y)到(x+width,y+height)
	 * @param  int         $w 宽度
	 * @param  int         $h 高度
	 * @param  int|integer $x 开始X坐标
	 * @param  int|integer $y 开始y坐标
	 * @return void
	 */
	public function crop(int $w,int $h,int $x = 0,int $y = 0);
	/**
	 * 缩略图
	 * @param  int          $max_size 尺寸
	 * @param  bool|boolean $white    是否留白
	 * @param  void
	 */
	public function thumb(int $max_size,bool $white = true);
	/**
	 * 水印 位置(1-9) 左上左中左下 中上中中中下 右上右中右下,水印路径
	 * @param  int    $position 水印位置
	 * @param  string $path     水印路径
	 * @return void
	 */
	public function water(int $position,string $path);
	/**
	 * 缩放
	 * @param  float  $scale 缩放尺度 例(1.2 | 0.5)
	 * @return void
	 */
	public function scale(float $scale);
	/**
	 * 图片类型转换
	 * @param  string $type 目标类型
	 * @return void
	 */
	public function convert(string $type);
	/**
	 * 旋转
	 * @param  int    $degress 旋转图数
	 * @return void
	 */
	public function rotate(int $degress);
	/**
	 * 水平翻转
	 * @return void
	 */
	public function flip();
	/**
	 * 垂直翻转
	 * @return void
	 */
	public function flop();


/*******************************************************
 * 绘制处理
 /*****************************************************/
 	
 	/**
	 * 填充点
	 * @param  int    $x     x坐标
	 * @param  int    $y     y坐标
	 * @param  mixed $color 颜色
	 * @return void
	 */
	public function drawPoint(int $x,int $y,$color);
	/**
	 * 填充直线
	 * @param  int    $x     x坐标
	 * @param  int    $y     y坐标
	 * @param  int    $end_x 结束x坐标
	 * @param  int    $end_y 结束y坐标
	 * @param  mixed $color 颜色
	 * @return void
	 */
	public function drawLine(int $x,int $y,int $end_x,int $end_y,$color);
	/**
	 * 填充矩形
	 * @param  int    $x     x坐标
	 * @param  int    $y     y坐标
	 * @param  int    $w     结束x坐标
	 * @param  int    $h     结束y坐标
	 * @param  mixed $color 颜色
	 * @return void
	 */
	public function drawRect(int $x,int $y,int $w,int $h,$color);
	/**
	 * 填充矩形
	 * @param  int    $x     x坐标
	 * @param  int    $y     y坐标
	 * @param  int    $w     结束x坐标
	 * @param  int    $h     结束y坐标
	 * @param  mixed $color 颜色
	 * @return void
	 */
	public function fillRect(int $x,int $y,int $w,int $h,$color);
	/**
	 * 绘制字符串
	 * @param  string $string    字符串
	 * @param  int    $x         x坐标
	 * @param  int    $y         y坐标
	 * @param  mixed  $color     颜色
	 * @param  bool  $horizontal 是否水平绘制
	 * @return void
	 */
	public function drawString(string $string,int $x,int $y,$color,bool $horizontal = true);
	/**
	 * 绘制文本 (用指定文件)
	 * @param  string $text      文本
	 * @param  int    $x         x坐标
	 * @param  int    $y         y坐标
	 * @param  int    $size      尺寸
	 * @param  mixed  $color     颜色
	 * @param  int    $rotate    旋转度数
	 * @param  string $font_file 字体文件
	 * @return void
	 */
	public function drawText(string $text,int $x,int $y,float  $size,$color,int $rotate,string $font_file);
}