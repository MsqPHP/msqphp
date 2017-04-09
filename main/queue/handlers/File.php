<?php declare(strict_types = 1);
namespace msqphp\main\queue\handlers;

use msqphp\base;

final class File implements  QueueHandlerInterface
{
    private $path = '';

    public function __construct(array $config)
    {
        is_dir($config['path']) || base\dir\Dir::make($config['path']);
        $this->path = realpath($config['path']) . DIRECTORY_SEPARATOR;
    }

    public function in(string $info)
    {
        $info = $this->getInfo();
        $info['queue'][] = $info;
        ++$info['length'];
        $this->setInfo($info);
    }

    public function out() : ?string
    {
        $info = $this->getInfo();
        $result = $info['length'] > 0 ? array_shift($info['queue']) : null;
        --$info['length'];
        $this->setInfo($info);
        return $result;
    }

    public function length() : int
    {
        $info = $this->getInfo();
        return $info['length'];
    }
    public function getInfo() : array
    {
        $file = $this->path . 'queue.php';
        $info = is_file($file) ? require $file : ['queue'=>[],'length'=>0];
    }
    public function setInfo(array $info) : void
    {
        base\file\File::write($file, '<?php reutrn '.var_export($info, true).';');
    }
}