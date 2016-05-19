<?php declare(strict_types = 1);
namespace msqphp\core\session\handlers;

use msqphp\base;

class File  implements \SessionHandlerInterface {
    private $path = '';
    private $extension = '';

    public function __construct(array $config)
    {
        if (!is_dir($config['path'])) {
            throw new FileException($config['path'].' session储存路径不存在');
        }
        $this->path      = realpath($config['path']).DIRECTORY_SEPARATOR;
        $this->extension = $config['extension'];
    }
    public function close ()
    {
        return true;
    }

    public function destroy ($session_id)
    {
        try {
            base\file\File::delete($this->getFilePath($session_id));
            return true;
        } catch (base\file\FileException $e) {
            throw new SessionHandlerException($e->getMessage());
        }
    }

    public function gc ($maxlifetime)
    {
        try {
            base\file\File::deleteFileByTime($this->path, 'm', $maxlifetime, $this->extension);
            return true;
        } catch (base\file\FileException $e) {
            throw new SessionHandlerException($e->getMessage());
        }
    }

    public function open ($save_path, $name)
    {
        return true;
    }

    public function read ($session_id)
    {
        $file_path = $this->getFilePath($session_id);
        if (!is_file($file_path)) {
            return '';
        }
        try {
            return base\file\File::get($file_path);
        } catch (base\file\FileException $e) {
            throw new SessionHandlerException($e->getMessage());
        }
    }

    public function write ($session_id , $session_data)
    {
        try {
            base\file\File::write($this->getFilePath($session_id), $session_data);   
            return true;
        } catch (base\file\FileException $e) {
            throw new SessionHandlerException($e->getMessage());
        }
    }
    public function getFilePath($session_id)
    {
        return $this->path.$session_id.$this->extension;
    }
}