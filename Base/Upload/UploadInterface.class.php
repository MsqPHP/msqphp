<?php declare(strict_types = 1);
namespace Core\Upload;
Interface UploadInterface
{
	//错误信息
	// abstract public $error_info;
	//构造函数
	abstract public function __construct(array $config);
	//获得错误信息
	abstract public function getErrorInfo() : string;
	/**
	 * @param array  $config 	 驱动配置
	 * @param string $root_path  根目录
	 * @param string $path  	 目录
	 * @param array  $file_info  文件信息目录
	 */
	abstract public function rootPath(string $root_path) : bool;
	abstract public function checkPath(string $path) : bool;
	abstract public function saveFile(array $file_info) : bool;
}