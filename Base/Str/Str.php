<?php declare(strict_types = 1);
namespace Core\Base\Str;
class Str
{
    /**
     * 得到指定类型随机字符
     * @param  int $length 字符长度
     * @param  int $type   字符类型
     * @return string       生成的字符
     */
    public static function randomString(int $length = 4,int $type = 3) : string
    {
        $random = '';
        switch ($type) {
            case 7:
                $random .= '~!@#$%^&*()_+`-=[]{};\'"\\|:<>?,./';
                break;
            case 6:
                $random .= '_-';
            case 5:
                $random .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case 4:
                $random .= '~!@#$%^&*()_+`-=[]{};\'"\\|:<>?,./';
            case 3:
                $random .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            case 2:
                $random .= 'abcdefghijklmnopqrstuvwxyz';
            case 1:
                $random .= '0123456789';
                break;
            default:
                $random .= '0123456789';
                break;
        }
        $len = (int) (($length/10) + 1);
        return substr(str_shuffle(str_repeat($random,$len)),0,$length);
        //打乱字符串后截取4个长度
    }
    /**
     * 得到随机的加密字符
     * @param  int|integer $length 长度
     * @return string
     */
    public static  function randomBytes(int $length = 16) : string 
    {
        $len = $length - rand(0,$length);
        return random_bytes($len).random_bytes($length-$len);
    }
    /**
     * 得到随机字符(高安全)
     * @param  int|integer $length 长度
     * @return string
     */
    public static  function random(int $length = 16) : string
    {
        $string = '';
        while ($length > 0) {
            $size = rand(1,$length);

            $bytes = random_bytes($size);

            $string .= substr(str_shuffle(str_replace(['/','+','='], '', base64_encode($bytes))) , 0, $size);
            $length -= $size;
        }

        return $string;
    }
    /**
     * 快速得到一个字符串
     * @param  integer $length 长度
     * @return string
     */
    public static  function quickRandom($length = 16) : string
    {
        //0-9A-Za-z
        $pool = '7O56JpRkTjPvKNn1S849zuXIYgCFaZ3GrmeUds02yWqcwAhQHfLMDiVlboxtEB';

        return substr(str_shuffle((str_repeat($pool, $length))), 0, $length);
    }
    /**
     * 是否包含某字符串
     * @param  string       $string  字符串
     * @param  string|array $targets 目标
     * @return bool
     */
    public static  function contains(string $string, $targets) : bool
    {
        foreach ((array) $targets as $target) {
            if ($target !== '' && strpos($string, $target) !== false) {
                return true;
            }
        }
        return false;
    }
    /**
     * 是否以某些字符开始
     * @param  string       $string   字符串
     * @param  string|array $targets  查找字符
     * @return bool
     */
    public static  function startsWith(string $string, $targets) : bool
    {
        foreach ((array) $targets as $target) {
            if ($target !== '' && strpos($string, $target) !== false) {
                return true;
            }
        }

        return false;   
    }
    /**
     * 是否以某些字符结束
     * @param  string       $string   字符串
     * @param  string|array $targets  查找字符
     * @return bool
     */
    public static  function endsWith(string $string, $targets) : bool
    {
        foreach ((array) $targets as $target) {
            if ((string) $target === substr($string, -strlen($target))) {
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
        preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches);

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
    public static function levenshtein(string $a,string $b) : int {
        return levenshtein($a,$b);
    }
    public static function strcmp(string $a,string $b) : int
    {
        // strcmp($str1,$str2):     $str1>=<$str2分别为正1,0,-1（字符串比较）
        // strcasecmp()         同上（不分大小写）
        // strnatcmp("4","14")     按自然排序比较字符串
        // strnatcasecmp()       同上，（区分大小写）
    }
    /**
     * 数字转换文件大小格式
     * @param  int     $size  数字
     * @param  boolean $round 是否取整
     * @return string  对应大小
     */
    public static function getSize($size, $round = true) : string {
        if (!is_numeric($size)) {
            throw new StrException($size.'不是一个有效数字',500);
        }
        //单位进制
        static $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB"); 
        $pos = 0;
        while ($size >= 1024) {
            $size /= 1024;
            ++$pos;
        }
        //是否取整
        $round && $size = round($size);
        //返回结果
        return $size . $sizes[$pos];
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
            $value = preg_replace('/\s+/', '', $value);

            $value = strtolower(preg_replace('/(.)(?=[A-Z])/', '$1'.'_', $value));
        }

        return $value;
    }
    /**
     * 转换一个字符串为studly命名法 ....   ->IndexAction
     * @param  string $string 转换后字符
     * @return string
     */
    public static  function studly(string $string) : string
    {
        $string = ucwords(str_replace(['-', '_'], ' ', $string));
        return str_replace(' ', '', $string);
    }
    

    public static function __callStatic(string $method, array $args)
    {
        static $func = [];
        if(!isset($func[$method])) {
            $func[$method] = require __DIR__.DIRECTORY_SEPARATOR.'Function'.DIRECTORY_SEPARATOR.$method.'.php';
        }
        return call_user_func_array($func[$method],$args);
    }


    public static function gzcompress(string $string) : string
    {
        return gzcompress($string);
    }
    public static function gzuncompress(string $string) : string
    {
        return gzuncompress($string);
    }
    public static function gzencode(string $string) : string
    {
        return gzcompress($string);
    }
    public static function gzdecode(string $string) : string
    {
        return gzuncompress($string);
    }
    public static function highlight(string $string) : string
    {
        
    }
    public static function highlightFile(string $file) : string
    {
        if (!is_writable($file)) {
            throw new StrException($file.'不可读');
            return '';          
        }
        return highlight_file($file);
    }
    public static function wordwrap(string $string) : string
    {
        return wordwrap($string);
    }
}
