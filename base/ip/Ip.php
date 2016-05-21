<?php declare(strict_types = 1);
namespace msqphp\base\ip;

class Ip
{
    use base\Base;
    private static $ip = '';
    private static $intip = 0;
    /**
     * Ip检测, 要求为合法的IPv4/v6 IP
     * @param   string  $ip 待检测IP
     * @return  boolen
     */
    public static function check(string $ip) : bool
    {
        return false !== filter_var($ip, FILTER_VALIDATE_IP);
    }
    public static function get() : string
    {
        if (empty(static::$ip)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim($arr[0]);
                if (false !== ($pos =strpos($ip, ','))) {
                    $ip = substr($ip, 0, $pos);
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            } else {
                throw new IpException('无法获得ip地址');
            }

            // IP地址合法验证
            if(static::check($ip)) {
                static::$ip = $ip;
            } else {
                throw new IpException('无法获得合法ip地址');
            }
        }
        return static::$ip;
    }
    public static function getInt() : int
    {
        if (empty(static::$intip)) {
            static::$intip = static::toInt(static::get());
        }
        return static::$intip;
    }
    public static function toInt(string $ip) : int
    {
        return ip2long($ip);
    }

    public static function getHostByName(string $host) : string
    {
        return gethostbyname($host);
    }
    public static function getHostListByName(string $host) : array
    {
        return gethostbynamel($host);
    }
}