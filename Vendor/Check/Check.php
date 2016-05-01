<?php declare(strict_types = 1);
namespace Msqphp\Base\Check;

class Check
{

	/**
	 * Ip检测,要求为合法的IPv4/v6 IP
	 * @param 	string 	$ip 待检测IP
	 * @return  bool
	 */
	static public function ipCheck(string $ip) : bool
	{
		//1.判断是不是正确格式
		if(false === filter_var($ip, FILTER_VALIDATE_IP)) {
 			return false;
 		} else {
 			return true;
 		}
	}
	/**
	 * 检查是否是一个合法json
	 * @param  string $json [description]
	 * @return bool
	 */
	public static function jsonCheck(string $json) : bool
	{
		json_decode($string);
		return json_last_error() === JSON_ERROE_NONE;
	}
	public static function xmlCheck(string $string) : bool
	{
		if (!define('LIBXML_VERSION'))  {
			throw new \Exception('libxml is required', 500);
		}
		$internal_errors = libxml_use_internal_errors();
		libxml_use_internal_errors(true);
		$result = simplexml_load_string($string) !== false;
		libxml_use_internal_errors($internal_errors);

		return $result;
	}
	public static function serializedCheck(string $string) : bool
	{
		$array = unserialize($string);
		return !($array === false && $string !== 'b:0;');
	}
	public static function htmlCheck($string)
	{
		return strlen(strip_tags($string)) < strlen($string);
	}
	/**
	 * 邮箱检测
	 * @param 	string 	$email 待检测邮箱
	 * @return  bool
	 */
	static public function emaileCheck(string $email) : bool
	{
		//1.判断是不是正确格式
		if(false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
 			return false;
 		} else {
 			return true;
 		}
	}
	/**
	 * 手机号码检测
	 * @param  string  $str 手机号码
	 * @return boolen       是否合格
	 */
	static public function mobileCheck($phone) : bool
	{
		//1.正则
        static $exp = '/^13[0-9]{1}[0-9]{8}$|15[012356789]{1}[0-9]{8}$|18[012356789]{1}[0-9]{8}$|14[57]{1}[0-9]{8}$/';  
        //2.判断是否合格
        return true && preg_match($exp,$phone);
	}
    /**
	 * 特殊字符检测
	 * @param  string  $str   字符串
	 * @return boolen         是否合格
	 */
	static public function specialStringCheck(string $string) : bool {
		//1.正则,匹配`~!@#$%^&*()_+-={}[]:;"'|\<>?,./以及～·！＠＃＃￥％……＆×（）——＋｛｝【】：；“‘｜＼《》
        static $exp = '/[\\\\\s\`\~\!\@\#\$\%\^\&\*\(\)\-\_\=\+\[\]\{\}\|\;\:\'\"\<\,\>\.\?\/～·！＠＃＃￥％……＆×（）——＋｛｝【】：；“‘｜＼《》]{1}/';
        //2.判断是否合格
        return preg_match($exp,$string);
	}
	/**
	 * 字符串长度检测
	 * @param  string $str    字符串
	 * @param  int    $min    最小长度
	 * @param  int    $max    最大长度
	 * @return boolen         是否合格
	 */
	static public function lengthCheck(string $str,int $min,int $max) : bool {
		$len = strlen(trim($str));
		return $len > $min ? ($len < $max ) : false;
	}
	/**
	 * qq号检测
	 * @param  string $str 字符串
	 * @return bool
	 */
	static public function qqCheck(string $str) : bool {
		return true && preg_match('/^[1-9]\d{4,12}$/', trim($str));
	}
	/**
	 * 邮政编码检测
	 * @param string $zip  字符串
	 * @return bool
	 */
	public static function zipCheck(string $str) : bool
	{
		return true && preg_match('/^[1-9]\d{5}$/');
	}
	/**
	 * 身份证检测
	 * @param  string $str $str
	 * @return bool
	 */
	public function cardCheck(string $str) : bool
	{
		return true;
	}
}