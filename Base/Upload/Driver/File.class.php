<?php declare(strict_types = 1);
namespace Core\Upload\Driver;
class File implements \Core\Upload\UploadInterface
{
	private $driver = null;
	//错误信息
	public $error_info = '';
	//构造函数
	public function __construct($config)
	{
		$this->driver = \Core\File\File::getDriver();
	}
	public function rootPath($path) : bool
	{
		$driver = $this->driver;
		if($driver->checkDir($path,false) === true) {
			return true;
		} else {
			$this->error_info = $driver->error_info;
			return false;
		}
	}
	public function checkPath($path) : bool
	{
		$driver = $this->driver;
		if($driver->checkDir($path,true) === true) {
			return true;
		} else {
			$this->error_info = $driver->error_info;
			return false;
		}
	}
	public function saveFile($file) : bool
	{
		if(move_uploaded_file($file['tmp_name'],$file['savepath'].$file['savename']) === true) {
			return true;
		} else {
			$this->error_info = '文件无法保存';
			return false;
		}
	}
}