<?php declare(strict_types = 1);
namespace Msqphp\Core\Session\Driver;

use Msqphp\Base;

class File  implements \SessionHandlerInterface {
    private $path = '';
    private $extension = '';
    private $prefixion = '';
    private $driver = null;

    public function __construct(array $config) {
        $this->path      = realpath($config['path']).DIRECTORY_SEPARATOR;
        $this->extension = $config['extension'];
        $this->prefixion = $config['prefixion'];
        $this->driver    = Base\File\File::getInstance();
    }
    public function close ()
    {
        return true;
    }

    public function destroy ($session_id)
    {
        return $this->driver->delete($this->getPath($session_id));
    }

    public function gc ($maxlifetime)
    {
        return $this->driver->deleteFileByTime($this->path,'m',$maxlifetime,$this->extension,$this->prefixion);
    }

    public function open ($save_path, $name)
    {
        return true;
    }

    public function read ($session_id)
    {
        $path = $this->getPath($session_id);
        if (!is_file($path)) {
            return '';
        }
        return $this->driver->get($path);        
    }

    public function write ($session_id , $session_data)
    {
        return $this->driver->write($this->getPath($session_id),$session_data);
    }
    public function getPath($session_id)
    {
        return $this->path.$this->prefixion.$session_id.$this->extension;
    }
}