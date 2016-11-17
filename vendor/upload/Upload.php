<?php declare(strict_types = 1);
namespace msqphp\vendor\upload;

final class Upload
{
    use UploadPointerTrait;
    use UploadOperateTrait;

    private function exception(string $message) : void
    {
        throw new UploadException($message);
    }
}

trait UploadPointerTrait
{
    private $pointer = [];

    private function __construct()
    {
        $this->init();
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
    public function rename(string $name) : self
    {
        $this->pointer['name'] = $name;
        return $this;
    }
    public function extension(string $extension) : self
    {
        $this->pointer['extension'] = '.'.ltrim($extension, '.');
        return $this;
    }
    public function allowed(array $allowed) : self
    {
        $this->pointer['allowed'] = $allowed;
        return $this;
    }
    public function disallowed(array $disallowed) : self
    {
        $this->pointer['disallowed'] = $disallowed;
        return $this;
    }
    public function type(string $type) : self
    {
        switch ($type) {
            case 'img':
            case 'image':
                $this->allowed('jpg','jpeg','png','bmp','gif');
                break;
            case 'video':
                $this->allowed('mp4','avi','rmvb','rm','mpg','flv','mov','wmv','3gp','mkv');
                break;
            case 'music':
            case 'audio':
                $this->allowed('mp3','wma','aiff','au','midi','aac','ape');
                break;
            default:
                $this->exception('not supported type:'.$type);
        }
    }
    public function maxSize(int $size) : self
    {
        $this->pointer['size'] = $size;
        return $this;
    }
    public function to(string $to) : self
    {
        $this->pointer['to'] = $to;
        return $this;
    }
}
trait UploadOperateTrait
{
    public function check() : void
    {
        $pointer = $this->pointer;

        isset($pointer['file']) || $this->exception('未设置上传文件键');

        isset($_FILES[$pointer['file']]) || $this->exception('上传文件不存在');

        $file = $_FILES[$pointer['file']];

        is_uploaded_file($file['tmp_name']) || $this->exception('上传文件上传方式不合法');

        //文件上传错误
        switch ($file['error']) {
            case 0:
                break;
            case 1:
                $this->exception('文件过大，超出php.ini设置');
            case 2:
                $this->exception('文件过大，超出表单最大设置');
            case 3:
                $this->exception('文件没有上传完成');
            case 4:
                $this->exception('没有上传文件');
            case 6:
            case 7:
                $this->exception('临时文件错误');
            default :
                $this->exception('未知错误');
        }

        //判断类型
        (isset($pointer['allowed']) && !in_array($file['type'], $pointer['allowed'])) && $this->exception('上传文件类型不符合');

        (isset($pointer['disallowed']) && in_array($file['type'], $pointer['disallowed'])) && $this->exception('上传文件类型不符合');

        //判断大小
        (isset($pointer['size']) && $file['size'] > $pointer['size']) && $this->exception('上传文件过大');
    }
    public function moveTo(string $to) : void
    {
        $this->to($to)->move();
    }
    public function move() : void
    {
        $pointer = $this->pointer;

        isset($pointer['to']) || $this->exception('未设置上传文件存放路径');

        $to   = realpath($pointer['to']) . DIRECTORY_SEPARATOR;

        is_dir($to) || $this->exception('存放路径不存在');

        $this->pointer['name'] = $name = $pointer['name'] ?? uniqid();

        $file = $_FILES[$pointer['file']];

        $this->pointer['extension'] = $ext = $pointer['extension'] ?? strrchr($file['name'], '.');

        $this->pointer['path'] = $file_path = $to.$name.$ext;

        false === move_uploaded_file($file['tmp_name'], $file_path) || $this->exception('移动上传文件失败');
    }
    public function getName() : string
    {
        $this->moved() || $this->exception('文件未移动');
        return $this->pointer['name'];
    }
    public function getExtension() : string
    {
        $this->moved() || $this->exception('文件未移动');
        return $this->pointer['extension'];
    }
    public function getPath() : string
    {
        $this->moved() || $this->exception('文件未移动');
        return $this->pointer['path'];
    }
    private function moved() : bool
    {
        return isset($this->pointer['path']);
    }
}