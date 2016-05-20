<?php declare(strict_types = 1);
namespace msqphp\vender\header;

class Header
{
    private static $instance = null;

    public static function noCache()
    {
        header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
        header( 'Pragma: no-cache' );
    }
    public static function status(int $code)
    {
        static $status_info = [
            // Informational 1xx
            100 => 'Continue', 
            101 => 'Switching Protocols', 
            // Success 2xx
            200 => 'OK', 
            201 => 'Created', 
            202 => 'Accepted', 
            203 => 'Non-Authoritative Information', 
            204 => 'No Content', 
            205 => 'Reset Content', 
            206 => 'Partial Content', 
            // Redirection 3xx
            300 => 'Multiple Choices', 
            301 => 'Moved Permanently', 
            302 => 'Moved Temporarily ',  // 1.1
            303 => 'See Other', 
            304 => 'Not Modified', 
            305 => 'Use Proxy', 
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect', 
            // Client Error 4xx
            400 => 'Bad Request', 
            401 => 'Unauthorized', 
            402 => 'Payment required', 
            403 => 'Forbidden', 
            404 => 'Not Found', 
            405 => 'Method Not Allowed', 
            406 => 'Not Acceptable', 
            407 => 'Proxy Authentication required', 
            408 => 'Request Timeout', 
            409 => 'Conflict', 
            410 => 'Gone', 
            411 => 'Length required', 
            412 => 'Precondition Failed', 
            413 => 'Request Entity Too Large', 
            414 => 'Request-URI Too Long', 
            415 => 'Unsupported Media Type', 
            416 => 'Requested Range Not Satisfiable', 
            417 => 'Expectation Failed', 
            // Server Error 5xx
            500 => 'Internal Server Error', 
            501 => 'Not Implemented', 
            502 => 'Bad Gateway', 
            503 => 'Service Unavailable', 
            504 => 'Gateway Timeout', 
            505 => 'HTTP Version Not Supported', 
            509 => 'Bandwidth Limit Exceeded'
        ];
        if (isset($status_info[$code])) {
            header('HTTP/1.1 '.$code.' '.$status_info[$code]);
        }
    }
    public static function type(string $type)
    {
        static $headers = [
            'json'   => 'application/json', 
            'xml'    => 'text/xml', 
            'html'   => 'text/html', 
            'jsonp'  => 'application/javascript', 
            'script' => 'application/javascript', 
            'text'   => 'text/plain', 
        ];
        if (isset($headers[$type])) {
            header('Content-Type:' . $headers[$type] . '; charset=utf-8');
        } else {
            throw new HeaderException($type.'暂未支持');
        }
    }
    public static function download(string $type, string $filepath, string $filename = '')
    {
        $this->type($type);
        header('Accept-Ranges:bytes');
        header('Accept-Length:'.filesize($filepath));
        header('Content-Disposition:attachment;filename='.($filename ?: pathinfo($filepath, PATHINFO_FILENAME)));
    }

}