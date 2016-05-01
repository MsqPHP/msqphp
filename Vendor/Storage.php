<?php declare(strict_types = 1);
namespace Msqphp;
class Storage
{
	//主驱动
	static private $driver = null;
	/**
	 * 初始化缓存
	 * @return void
	 */
	static public function init()
	{
		global $_config;
		//获得默认的驱动
		$driver_type = $_config['STORAGE_DRIVER'];
		//默认驱动配置
		$config = $_config['STORAGE_CONFIG'][$driver_type];
		//载入缓存接口文件
		require Msqphp_PATH.'Storage/StorageInterface'.EXT;
		//加载驱动
		self::$driver = self::initDriver($driver_type,$config);
	}
	/**
	 * 得到对应的驱动
	 * @return 驱动
	 */
	static private function getDriver()
	{
		$driver = self::$driver;
		//如果为空或者等于默认驱动，直接返回
		if($driver === null) {
			self::init();
		}
		return $driver;
	}
	/**
	 * 加载驱动
	 * @param  string $type     驱动名称
	 * @param  array  $config   驱动配置
	 * @return 驱动
	 */
	static private function & initDriver(string $type,array $config = [])
	{
		//驱动路径
		$driver_path = Msqphp_PATH.'Storage/Driver/'.$type.EXT;
		//文件是否存在
		if(is_file($driver_path) === false) {
			throw new \Exception($type . 'Storage driver 文件无法加载', 500);
		}
		//加载
		require Msqphp_PATH.'Storage/Driver/'.$type.EXT;
		//拼接类名
		$class_name = 'Msqphp\\Storage\\Driver\\'.$type;
		//创建类
		$driver = new $class_name($config);
		//返回
		return $driver;
	}
	/**
	 * @param  array   $config   驱动配置
	 * @param  string  $path     文件路径
	 * @param  string  $content  文件内容
	 * @param  string  $from     文件现位置
	 * @param  string  $to       文件移动到
	 * @param  array   $option   相关参数
	 * @return bool 是否成功 |   是否存在
	 */
	//读取
	static public function read(string $path) : string
	{
		return self::getDriver()->read($path);
	}
	//写入
	static public function write(string $path,string $content,array $option) : string
	{
		return self::getDriver()->write($path,$content,$option);
	}
	//追加
	static public function append(string $path,string $content) : string
	{
		return self::getDriver()->append($path,$content);
	}
	//删除
	static public function delete(string $path) : string
	{
		return self::getDriver()->delete($path);
	}
	//是否存在
	static public function exists(string $path) : string
	{
		return self::getDriver()->exists($path);
	}
	//移动
	static public function move(string $from,string $to) : string
	{
		return self::getDriver()->move($from,$to);
	}
}