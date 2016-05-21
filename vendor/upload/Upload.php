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

        if($file['error'] !== 0 ) {
            //文件上传错误
            switch ($file['error']) {
                case 1:
                    throw new UploadException('文件过大，超出php.ini设置');
                case 2:
                    throw new UploadException('文件过大，超出表单最大设置');
                case 3:
                    throw new UploadException('文件没有上传完成');
                case 4:
                    throw new UploadException('没有上传文件');
                case 6:
                case 7:
                    throw new UploadException('临时文件错误');
                default :
                    throw new UploadException('未知错误');
            }
        }

        //判断类型
        if(!in_array($file['type'], $this->allow_types)) {
            throw new UploadException('类型不对');
        }

        //判断大小
        if($file['size'] > $this->max_size) {
            throw new UploadException('文件过大');
        }

        //移动
        if(!is_uploaded_file($file['tmp_name'])) {
            throw new UploadException('上传文件可疑');
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