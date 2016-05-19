<?php declare(strict_types = 1);
namespace msqphp\base\Upload;

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
        $file = $_FILES[$key];
        if (!isset($_FILES[$key])) {
            return false;
        }
        if (!is_uploaded_file($_FILES[$key]['name'])) {
            return false;
        }

        if($file['error'] != 0 ) {
            //文件上传错误
            switch ($file['error']) {
                case 1:
                    $this->error_info = '文件过大，超出php.ini设置';
                    break;
                case 2:
                    $this->error_info = '文件过大，超出表单最大设置';
                    break;
                case 3:
                    $this->error_info = '文件没有上传完成';
                    break;
                case 4:
                    $this->error_info = '没有上传文件';
                    break;
                case 6:
                case 7:
                    $this->error_info = '临时文件错误';
                    break;
            }
            return false;
        }
        
        //判断类型
        if(!in_array($file['type'], $this->allow_types)) {
            $this->error_info = '类型不对';
            return false;
        }

        //判断大小
        if($file['size'] > $this->max_size) {
            $this->error_info = '文件过大';
            return false;
        }

        //移动
        if(!is_uploaded_file($file['tmp_name'])) {
            $this->error_info = '上传文件可疑';
            return false;
        }

    }
    public function move($key, $to)
    {
        $dst_file = uniqid($prefix) .strrchr($file['name'], '.');
        if(move_uploaded_file($file['tmp_name'], $to . $dst_file)) {
            //成功
            return $dst_file;
        }
    }
    public function getFilePath($key)
    {
    }
}