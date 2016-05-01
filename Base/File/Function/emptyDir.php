<?php declare(strict_types = 1);
namespace Msqphp\Base\File;
    /**
     * 清空目录内容
     * @func_name               emptyDir
     * @param  string $dir      目录路径
     * @param  bool   $force    为空创建
     * @throws Exception
     * @return bool
     */
return function (string $dir,bool $force = false) : bool {
    //目录检测
    if (!is_dir($dir)) {
        if (!$force) {
            throw new FileException($dir.'目录不存在,无法清空');
        }
        return $this->makeDir($dir,$force);
    }
    //权限判断
    if (!is_writable($dir) || !is_executable($dir)) {
        throw new FileException($dir.'目录不可操作,无法清空');
        return false;
    }
    //清空目录
    foreach ($this->getList($dir,'dir') as $_dir) {
        $this->deleteDir($_dir,true);
    }
    foreach ($this->getList($dir,'file') as $_file) {
        $this->deleteDir($_file,true);
    }
    return true;
};