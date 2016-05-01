<?php
namespace Core;
class Error
{
	static private $errno = 0;
	static private $errmsg ='';
	static private $filename = '';
	static private $line = 0;
	static private $vars = [];
	static public function deal(int $errno,string $errmsg,string $filename,int $line,array $vars)
	{
		self::$errno = $errno;
		self::$errmsg = $errmsg;
		self::$filename = $filename;
		self::$line = $line;
		self::$vars = $vars;
		switch($errno) {
			case E_USER_ERROR:
				return self::dealError();
				break;
			case E_USER_ERROR:
			case E_WARNING:
				return self::dealWarning();
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				return self::dealNotice();
				break;
			default:
				return false;
				break;
		}
	}
	static public function dealError()
	{
		ob_start();
		debug_print_backtrace();
		$back_trace=ob_get_flush();
		$error_msg =<<<EOF
		出现了致命错误，如下：
产生错误的文件：{self::filename}
产生错误的信息：{self::message}
产生错误的行号：{self::line}
追踪信息：{$back_trace}
EOF;
		error_log($error_msg,1);
		exit(1);
	}
	static public function dealWarning()
	{
		$error_msg =<<<EOF
		出现了警告错误，如下：
产生错误的文件：{self::filename}
产生错误的信息：{self::message}
产生错误的行号：{self::line}
EOF;
		return error_log($error_msg,1,'^^');
	}
	static public function dealNotice()
	{
		$datename = date('Y-m-d H:i:s',time());
		$error_msg =<<<EOF
		出现了通知错误，如下：
产生通知的文件：{self::filename}
产生通知的信息：{self::message}
产生通知的行号：{self::line}
产生通知的时间：{$datename}
EOF;
		return error_log($error_msg,3);
	}
}
