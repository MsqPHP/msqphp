<?php declare(strict_types = 1);
namespace msqphp\vendor\upload;

class Upload
{
    private static $instance = null;
    private $pointer = [];
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
    public function init() : self
    {
        $this->pointer = [];
        return $this;
    }
    public function file(string $file) : self
    {
        $this->pointer['file'] = $file;
        return $this;
    }
    public function name(string $name) : self
    {
        return $this->rename($name);
    }
    public function rename(string $name) : self
    {
        $this->pointer['name'] = $name;
        return $this;
    }
    public function ext(string $extension) : self
    {
        return $this->ext($extension);
    }
    public function extension(string $extension) : self
    {
        $this->pointer['extension'] = '.'.ltrim($extension, '.');
        return $this;
    }
    public function allowed() : self
    {
        $this->pointer['allowed'] = func_get_args();
        return $this;
    }
    public function maxSize(int $size) : self
    {
        $this->pointer['size'] = $size;
        return $this;
    }
    public function size(int $size) : self
    {
        return $this->maxSize($size);
    }
    public function to(string $to) : self
    {
        $this->pointer['to'] = $to;
        return $this;
    }
    public function check() : self
    {
        $pointer = $this->pointer;
        if (!isset($pointer['file'])) {
            throw new UploadException('未设置上传文件键');
        }

        if (!isset($_FILES[$pointer['file']])) {
            throw new UploadException('上传文件不存在');
        }
        $file = $_FILES[$pointer['file']];

        $file_path = $file['tmp_name'];
        if (!is_uploaded_file($file_path)) {
            throw new UploadException('不合理的上传文件');
        }

        //文件上传错误
        switch ($file['error']) {
            case 0:
                break;
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

        //判断类型
        if (isset($pointer['allower']) && !in_array($file['type'], $pointer['allower'])) {
            throw new UploadException('上传文件不符合类型');
        }

        //判断大小
        if(isset($pointer['size']) && $file['size'] > $pointer['size']) {
            throw new UploadException('上传文件过大');
        }

        return $this;
    }
    public function move() : self
    {
        $pointer = $this->pointer;

        if (!isset($pointer['to'])) {
            throw new UploadException('未设置上传文件存放路径');
        }
        $to   = realpath($pointer['to']) . DIRECTORY_SEPARATOR;
        if (!is_dir($to)) {
            throw new UploadException('存放路径不存在');
        }
        $this->pointer['name'] = $name = $pointer['name'] ?? uniqid();

        $file = $_FILES[$pointer['file']];

        $ext = $pointer['extension'] ?? strrchr($file['name'], '.');

        $this->pointer['path'] = $file_path = $to.$name.$ext;
        if (false === move_uploaded_file($file['tmp_name'], $file_path)) {
            throw new UploadException('移动上传文件失败');
        }
        return $this;
    }
    public function getName()
    {
        if (!isset($this->pointer['name'])) {
            throw new UploadException('文件未移动');
        }
        return $this->pointer['name'];
    }
    public function getPath()
    {
        if (!isset($this->pointer['path'])) {
            throw new UploadException('文件未移动');
        }
        return $this->pointer['path'];
    }
}