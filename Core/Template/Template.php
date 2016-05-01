<?php declare(strict_types = 1);
namespace Msqphp\Core\Template;

class Template
{
	static public function cache(array $config)
	{
		//变量
		$tpl_vars = $config['tpl_vars'];
		//缓存文件路径
		$tpl_file_c_path = $config['tpl_file_c_path'];
		//源文件内容
		$tpl_content = file_get_contents($config['tpl_file_path']);
		//语言
		$language = is_file($config['language_path']) ? include $config['language_path'] : [];
		//内容转换
		$tpl_content = Parse::commpile($tpl_content,$tpl_vars,$language,$config);
		//获得目录并创建
		\Msqphp\Base\File\File::getInstance()->write($tpl_file_c_path,$tpl_content,true);
		unset($tpl_content);
		//返回
		return true;
	}
}