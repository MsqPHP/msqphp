<?php declare(strict_types = 1);
namespace Core\Base\File;
/**
 * 删除目录
 * @func_name     deleteDir
 * @param  string $dir 路径
 * @param  bool   $force    忽略是否存在,强制删除
 * @throws Exception
 * @return bool
 */
return function (string $dir,bool $force = false) : bool {
    //目录是否存在
    if(!is_dir($dir)) {
        if (!$force) {
            throw new FileException($dir.'目录不存在,无法删除');
            return false;
        }
        return true;
    }
    //是否可操作
    if (!is_writable($dir) || !is_executable($dir)) {
        throw new FileException($dir.'目录不可操作,无法删除');
        return false;
    }
    //如果强制，先清空目录
    $force === true && $this->emptyDir($dir);
    //检测是否为空
    if (!$this->isEmptyDir($dir)) {
        throw new FileException($dir.'目录不为空,无法删除');
        return false;
    }
    //删除目录
    if (!rmdir($dir)) {
        throw new FileException($dir.'未知错误,无法删除');
        return false;
    }
    return true;
};