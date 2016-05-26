<?php declare(strict_types = 1);
namespace msqphp\base\validator;

use msqphp\base;
use msqphp\traits;

final class Validator
{
    use traits\CallStatic;
    /**
     * Ip验证, 要求为合法的IPv4/v6 IP
     * @param   string  $ip 待验证IP
     * @return  bool
     */
    public static function ip(string $ip) : bool
    {
        return false !== filter_var($ip, FILTER_VALIDATE_IP);
    }
    /**
     * 手机号码验证
     * @param  string  $str 手机号码
     * @return boolen       是否合格
     */
    public static function mobile($phone) : bool
    {
        return false !== preg_match('/^13[0-9]{1}[0-9]{8}$|15[012356789]{1}[0-9]{8}$|18[012356789]{1}[0-9]{8}$|14[57]{1}[0-9]{8}$/', $phone);
    }
    /**
     * 邮箱验证
     * @param   string  $email 待验证邮箱
     * @return  bool
     */
    public static function emaile(string $email) : bool
    {
        return false !== filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    /**
     * qq号验证
     * @param  string $str 字符串
     * @return bool
     */
    public static function qq(string $qq) : bool {
        return false !== preg_match('/^[1-9]\d{4, 12}$/', trim($qq));
    }
    /**
     * 邮政编码验证
     * @param string $zip  邮政编码
     * @return bool
     */
    public static function zip(string $zip) : bool
    {
        return false !== preg_match('/^[1-9]\d{5}$/', trim($zip));
    }
    /**
     * url验证
     * @param string $zip  url
     * @return bool
     */
    public static function url(string $url) : bool
    {
        return false !== filter_var($url, FILTER_VALIDATE_URL);
    }
    /**
     * 身份证验证
     * @param  string $idCard 身份证
     * @return bool
     */
    public static function idCard(string $idCard) : bool
    {
        throw new ValidatorException('未实现');
    }
    /**
     * 检查是否是一个合法json
     * @param  string $json json字符串
     * @return bool
     */
    public static function json(string $json) : bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROE_NONE;
    }
    /**
     * 是否是一个xml文本
     * @param  string $xml xml文本
     * @return bool
     */
    public static function xml(string $xml) : bool
    {
        if (!define('LIBXML_VERSION'))  {
            throw new ValidatorException('libxml is required', 500);
        }
        $internal_errors = libxml_use_internal_errors();
        libxml_use_internal_errors(true);
        $result = simplexml_load_string($xml) !== false;
        libxml_use_internal_errors($internal_errors);

        return $result;
    }
    /**
     * 是否是一个html文本
     * @param  string $html html文本
     * @return bool
     */
    public static function html(string $html) : bool
    {
        return strlen(strip_tags($html)) < strlen($html);
    }
    /**
     * 是否全部为字母和(或)数字字符。
     * @param  string $alnum 字符串
     * @return bool
     */
    public static function alnum(string $alnum) : bool
    {
        return ctype_alnum($alnum);
    }
    /**
     * 纯字母检测,字母仅仅是指 [A-Za-z]
     * @param  string $alpha 字符串
     * @return bool
     */
    public static function alpha(string $alpha) : bool
    {
        return ctype_alpha($alpha);
    }
    /**
     * 查提供的 string 里面的字符是不是都是控制字符。 控制字符就是例如：换行、缩进、空格
     * @param  string $cntrl 字符串
     * @return bool
     */
    public static function cntrl(string $cntrl) : bool
    {
        return ctype_cntrl($cntrl);
    }
    /**
     * 是不是都是数字。 (允许小数)
     * @param  string $digit 字符串
     * @return bool
     */
    public static function digit(string $digit) : bool
    {
        return ctype_digit($digit);
    }
    /**
     * 没有空白
     * @param  string $graph  字符串
     * @return bool
     */
    public static function graph(string $graph) : bool
    {
        return ctype_graph($graph);
    }
    /**
     * 检查提供的 string 和 text 里面的字符是不是都是可以打印出来。
     * @param  string $print 字符串
     * @return bool
     */
    public static function print(string $print) : bool
    {
        return ctype_print($print);
    }
    /**
     * 小写字母
     * @param  string $lower 字符串
     * @return bool
     */
    public static function lower(string $lower) : bool
    {
        return ctype_lower($lower);
    }
    /**
     * 大写字母
     * @param  string $upper 字符串
     * @return bool
     */
    public static function upper(string $upper) : bool
    {
        return ctype_upper($upper);
    }
    /**
     * 标点符号
     * @param  string $punct 字符串
     * @return bool
     */
    public static function punct(string $punct) : bool
    {
        return ctype_punct($punct);
    }
    /**
     * 空白字符
     * @param  string $space 字符串
     * @return bool
     */
    public static function space(string $space) : bool
    {
        return ctype_space($space);
    }
    /**
     * 十六进制检测
     * @param  string $xdigit 字符串
     * @return bool
     */
    public static function xdigit(string $xdigit) : bool
    {
        return ctype_xdigit($xdigit);
    }
}