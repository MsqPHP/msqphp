<?php declare(strict_types = 1);
namespace msqphp\base\str;

use msqphp\base;
use msqphp\traits;

final class Str
{
    use traits\CallStatic;

    /**
     * 得到指定类型随机字符
     *
     * @param  int $length 字符长度
     * @param  int $type   字符类型
     *       //type1:0-9
     *       //type2:0-9a-z
     *       //type3:0-9a-zA-Z
     *       //type4:0-9a-zA-Z~!@#$%^&*()_+`-=[]{};'"\|:<>?, ./
     *
     * @return string       生成的字符
     */
    public static function randomString(int $length = 4, int $type = 3) : string
    {
        if ($length < 1) {
            throw new StrException($length.'必须大于0');
        }
        $random = '';
        switch ($type) {
            case 4:
                $random .= '~!@#$%^&*()_+`-=[]{};\'"\\|:<>?, ./';
            case 3:
                $random .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            case 2:
                $random .= 'abcdefghijklmnopqrstuvwxyz';
            case 1:
            default:
                $random .= '0123456789';
                break;
        }
        return substr(str_shuffle(str_repeat($random, $length)), 0, $length);
        //打乱字符串后截取4个长度
    }
    /**
     * 得到随机的加密字符
     *
     * @param  int|integer $length 长度
     *
     * @return string
     */
    public static  function randomBytes(int $length = 16) : string
    {
        if ($length < 1) {
            throw new StrException($length.'必须大于0');
        }
        return str_shuffle(random_bytes($length));
    }
    /**
     * 得到随机字符(高安全)
     *
     * @param  int|integer $length 长度
     *
     * @return string
     */
    public static  function random(int $length = 16) : string
    {
        if ($length < 1) {
            throw new StrException($length.'必须大于0');
        }
        $string = '';
        while ($length > 0) {
            $size = rand(1, $length);

            $bytes = random_bytes($size);

            $string .= substr(str_shuffle(str_replace(['/', '+', '='], '', base64_encode($bytes))) , 0, $size);
            $length -= $size;
        }

        return $string;
    }
    /**
     * 快速得到一个字符串
     *
     * @param  integer $length 长度
     *
     * @return string
     */
    public static  function quickRandom($length = 16) : string
    {
        if ($length <= 0) {
            throw new StrException($length.'必须大于0');
        }
        return substr(str_shuffle((str_repeat('7O56JpRkTjPvKNn1S849zuXIYgCFaZ3GrmeUds02yWqcwAhQHfLMDiVlboxtEB', $length))), 0, $length);
    }

    /**
     * 反转字符
     *
     * @param  string $string 字符串
     *
     * @return string
     */
    public static function reverse(string $string) : string
    {
        $result = '';
        for ($i = mb_strlen($string); $i >0 ; --$i) {
            $result .= mb_substr($string, $i - 1, 1);
        }
        return $result;
    }

    /**
     * 是否包含某字符串
     * @param  string       $haystack  字符串
     * @param  string|array $needle    查找字符
     * @return bool
     */
    public static  function contains(string $haystack, $needle) : bool
    {
        foreach ((array) $needle as $target) {
            if ('' !== $target && false !== strpos($haystack, $target)) {
                return true;
            }
        }
        return false;
    }
    /**
     * 是否以某些字符开始
     * @param  string       $haystack   字符串
     * @param  string|array $needle     查找字符
     * @return bool
     */
    public static  function startsWith(string $haystack, $needle) : bool
    {
        foreach ((array) $needle as $target) {
            if ('' !== $target && 0 === strpos($haystack, $target)) {
                return true;
            }
        }
        return false;
    }
    /**
     * 是否以某些字符结束
     * @param  string       $haystack   字符串
     * @param  string|array $needle     查找字符
     * @return bool
     */
    public static  function endsWith(string $haystack, $needle) : bool
    {
        foreach ((array) $needle as $target) {
            if ((string) $target === substr($haystack, -strlen($target))) {
                return true;
            }
        }
        return false;
    }
    /**
     * 限定多少个的单词
     * @param  string      $string 字符串
     * @param  int|integer $limit  限制长度
     * @param  string      $end    结尾字符
     * @return string
     */
    public static  function words(string $value, int $words = 100, string $end = '...') : string
    {
        preg_match('/^\s*+(?:\S++\s*+){1, '.$words.'}/u', $value, $matches);

        if (! isset($matches[0]) || strlen($value) === strlen($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]).$end;
    }


    /**
     * 得到字符串间的差异值(最少几个字符可以替换)
     * @param  string $a 字符a
     * @param  string $b 字符b
     * @return int    值
     */
    public static function levenshtein(string $a, string $b) : int
    {
        return levenshtein($a, $b);
    }
    public static function countWords(string $string) : int {
       return str_word_count($string);
    }
    public static function escapeshellcmd(string $string) : string {
       return escapeshellcmd($string);
    }
    /**
     * 转换一个字符串为snake命名法 ....   ->index_action
     * @param  string $string 转换后字符
     * @return string
     */
    public static  function snake(string $value) : string
    {
        if (! ctype_lower($value)) {
            return strtolower(preg_replace('/(.)(?=[A-Z])/', '$1'.'_', preg_replace('/\s+/', '', $value)));
        } else {
            return $value;
        }
    }
    /**
     * 转换一个字符串为studly命名法 ....   ->IndexAction
     * @param  string $string 转换后字符
     * @return string
     */
    public static  function studly(string $string) : string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string)));
    }
}
