<?php declare(strict_types = 1);
namespace Msqphp\Base\File;
    /**
     * 复制目录,文件
     * @func_name                 copyDir 
     * @param  string $from       目录路径
     * @param  string $to         目标路径
     * @param  string $force      如果目标路径不存在是否强制创建
     * @throws Exception
     * @return bool
     */
return function (string $from, string $to,bool $force = false) : bool {
    
    //原目录是否存在
    if (false === is_dir($from)) {
        throw new FileException($from.' 目录不存在');
        return false;
    }
    //是否可操作
    if (!is_writable($from) || !is_readable($from)) {
        throw new FileException($from.' 无法操作');
        return false;
    }
    //目标目录是否存在
    if (!is_dir($to)) {
        if ($force) {
            $this->makeDir($to,true);
        } else {
            throw new FileException($to.' 目录已存在');
            return false;
        }
    }
    //目标父目录是否可操作
    if (!is_writable(dirname($to)) || !is_readable(dirname($to))) {
        throw new FileException($to.' 父目录无法操作');
        return false;
    }
    foreach ($this->getList($path,'dir') as $dir) {
        $this->copyDir($from.$dir,$to.$dir,true);
    }
    foreach ($this->getList($path,'file') as $file) {
        $this->copyFile($from.$file,$to.$file,true);
    }
    return true;
};