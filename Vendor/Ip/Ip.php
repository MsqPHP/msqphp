<?php declare(strict_types = 1);
namespace Msqphp\Vendor\Ip;

class Ip
{
    /**
     * Ip检测,要求为合法的IPv4/v6 IP
     * @param   string  $ip 待检测IP
     * @return  boolen
     */
    static public function check(string $ip) : bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP);
    }
    static public function get() : string
    {
        $ip = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown',$arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if(false !== ($pos =strpos($ip,','))) {
            $ip = substr($ip, 0,$pos);
        }
        // IP地址合法验证
        if(self::check($ip)) {
           return $ip;
        }
        throw new IpException('无法获得ip地址');
    }
    static public function getInt() : int
    {
        return self::toInt(self::get());
    }
    static public function toInt(string $ip) : int
    {
        return ip2long($ip);
    }
}