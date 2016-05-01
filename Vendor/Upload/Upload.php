<?php declare(strict_types = 1);
namespace Msqphp\Base\Upload;

class Upload
{
    private static $instance = null;
    private function __construct()
    {

    }
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new Upload();
        }
        return static::$instance;
    }
    public function check($key)
    {
        if (!isset($_FILES[$key])) {
            return false;
        }
        if (!is_uploaded_file($_FILES[$key]['name'])) {
            return false;
        }
    }
    public function getFilePath($key)
    {
    }
}