<?php declare(strict_types = 1);
namespace msqphp\vendor\zip;

use msqphp\base;

class Zip
{
    private $zip = [];
    public function file(string $file)
    {
        $this->zip['file'][] = $file;
        return $this;
    }
    public function dir(string $dir)
    {
        $this->zip['dir'][] = $file;
        return $this;
    }
    public function to(string $to)
    {
        $this->zip['to'] = $to;
        return $this;
    }
    public function password(string $password)
    {
        $this->zip['password'] = $password;
        return $this;
    }
    public function zip()
    {
        
    }
    public function unzip()
    {

    }
}