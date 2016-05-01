<?php declare(strict_types = 1);
namespace Msqphp\Vender\Image\Driver\Gd;
class Gd implements Msqphp\Vender\Image\DriverInterface
{
	//图片资源
	private $resource     = null;
	//图片信息
	private $info = [];
	//默认格式
	private $default_type = 'jpeg';
	//错误信息
	public  $error_info   = '';

	/**
	 * 是否安装GD扩展
	 * @return bool
	 */
	public function isInstalled() : bool
	{
		return function_exists('gd_info');
	}
	public function __call($method,$args)
	{
		static $func = [];
		if (!isset($func[$method])) {
			$func[$method] = require __DIR__.DIRECTORY_SEPARATOR.'function'.DIRECTORY_SEPARATOR.$method.'.php';
		}
		return call_user_func_array($func[$method],$args);
	}
	/**
	 * 打开图片
	 * @param  string $path 路径
	 * @return bool         是否成功
	 */
	public function load(string $path) : bool
	{
		//获得当前img对象
		$img                    = $this;
		//获得图片信息
		$img->info              = $info = $img->getInfoByPath($path);
		$img->resource          = $img->createResource($path,$info['type']);
		//错误信息是否为空
		return $img->error_info === '';
	}
	/**
	 * 创建一个新的图片资源
	 * @param  int      $w      宽度
	 * @param  int      $h      高度
	 * @param  string   $color  颜色
	 * @return bool
	 */
	public function create(int $w,int $h,$bg_color) : bool
	{
		//获得当前img对象
		$img           = $this;
		$img->resource = $resource = imagecreatetruecolor($w,$h);
		$img->info     = array('width' => $w, 'height'=> $h,'bg_color'=>$bg_color);
		imagefill($resource,0,0,$this->getColor($bg_color));
		$img->bg_color = $bg_color;
		return true;
	}
	/**
	 * 设置背景颜色
	 * @param string $bg_color 背景颜色
	 * @return void
	 */
	public function setBgColor($bg_color)
	{
		$this->info['bg_color']= $bg_color;
	}
	/**
	 * 获得图片信息
	 * @param  string $key key
	 * @return string|array
	 */
	public function getImageInfo(string $key = '')
	{
		if($key === '') {
			return $this->info;
		} else {
			return $this->info[$key] ?? '';
		}
	}
	/**
	 * html图片输出
	 * @param  string $type 输出
	 * @return bool
	 */
	public function output(string $type = '') : bool
	{
		//获得当前img对象
		$img = $this;
		//如果有错误，直接返回
		if($img->error_info !== ''){return false;}
		//获得类型
		$type === '' && ($type = $img->info['type'] ?? $img->default_type);
		if(!headers_sent()) {
			header('HTTP/1.1 200 OK');
			header('Content-Type:image/'.$type);
		}		
		$func = 'image'.$type;
		return $func($img->resource,NULL);
	}
	/**
	 * 保存
	 * @param  string       $save_path 路径
	 * @param  string       $save_type 类型
	 * @return bool         是否成功
	 */
	public function save(string $save_path,string $type = '') : bool
	{
		//获得当前img对象
		$img                = $this;
		//如果有错误，直接返回
		if($img->error_info !== ''){return false;}
		//得到图片
		$image              = $img->resource;
		//获得保存图片文件类型
		$type               === '' &&  ( $type = ($img->info['type'] ?? $img->default_type) );
		$type               === 'jpg' && $type = 'jpeg';
		//导出
		$implode_func       = 'image'.$type;
		return $implode_func($img->resource,$save_path);
	}
	/**
	 * 关闭并清除所有资源
	 * @return bool
	 */
	public function close() : bool
	{
		//获得当前img对象
		$img = $this;
		//如果错误信息不为空，返回false
		if($img->error_info !== '') {
			return false;
		} else {
			//清空
			$img->info = [];
			clearstatcache();
			//销毁资源
			return imagedestroy($img->resource);
		}
	}
	/**
	 * 得到错误信息
	 * @return string
	 */
	public function getErrorInfo() : string
	{
		return $this->error_info;
	}

/*******************************************************
 * 内部私有函数
 /*****************************************************/
	/**
	 * 通过路径得到图片信息
	 * @param  string $path 路径
	 * @return 信息       
	 */
	private function getInfoByPath(string $path) : array
	{
		//得到图片信息
		$info = getimagesize($path);
		// if($info === false || ($info[2] === IMAGETYPE_GIF && empty($info['bits']))){
		if($info === false || ($info[2] === IMAGETYPE_GIF && empty($info['bits']))){
			$this->error_info = '无法获得图片信息';
			return [];
		} else {
			$info['mime']   = $info['mime'];
			$info['type']   = str_replace('image/', '', $info['mime']);
			$info['width']  = (int) $info[0];
			$info['height'] = (int) $info[1];
			$info['path']   = $path;
			return $info;
		}
	}
	/**
	 * 创建图片资源，
	 * @param  string $type 图片类型
	 * @return 资源
	 */
	private function createResource(string $path,string $type)
	{
		$create_func = 'imagecreatefrom' . $type;
		$image       = $create_func($path);
		//是否成功
		$image       === null && $this->error_info = '无法创建图片资源';
		return $image;
	}
	/**
	 * 获得颜色
	 * @param  resource $image 图片资源
	 * @param  string   $rgb 颜色(#000000-#FFFFFF)
	 * @return resource  颜色资源
	 */
	private function getColor($color)
	{
		if(is_array($color)) {
			$color['r'] = $color['r'] ?? $color[0] ?? 0;
			$color['g'] = $color['g'] ?? $color[1] ?? 0;
			$color['b'] = $color['b'] ?? $color[2] ?? 0;
		} elseif(is_string($color) && strlen($color) === 7) {
			$color = ltrim($color,'#');
			$r = hexdec($color[0].$color[1]);
			$g = hexdec($color[2].$color[3]);
			$b = hexdec($color[4].$color[5]);
			$color = array('r'=>$r,'g'=>$g,'b'=>$b);
		} else {
			throw new \Exception($color.'不是正确的颜色', 500);
		}
		return $this->createColor($this->resource,$color);
	}
	private function createColor($resource,array $color) {
        return isset($color['a]']) ?
        imagecolorallocatealpha($resource, (int)$color['r'], (int)$color['g'], (int)$color['b'], (int)$color['a']) :
        imagecolorallocate($resource, (int)$color['r'], (int)$color['g'], (int)$color['b']);
	}
}