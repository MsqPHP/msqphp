<?php declare(strict_types = 1);
namespace Msqphp\Base\File;
/**
 * 创建目录
 * @func_name             makeDir
 * @param  string $dir    路径
 * @param  bool   $force  是否强制创建父目录 或 忽略目录是否已经创建
 * @param  int    $code   读写执行代码
 * @throws Exception
 * @return bool
 */
return function (string $dir,bool $force = false,int $code = 0777) : bool {
    //是否目录已存在
    if (is_dir($dir)) {
        if ($force) {
            return true;
        } else {
            //目录已存在
            throw new FileException($dir.'目录已存在');
            return false;
        }
    }
    //判断父目录是否存在
    $parent_dir = dirname($dir);
    if (!is_dir($parent_dir)) {
        if (!$force) {
            //父目录不存在
            throw new FileException($dir.'上级目录不存在');
            return false;
        }
        $this->makeDir($parent_dir,true,$code);
    }

    //父目录是否可写
    if (!is_writable($parent_dir)) {
        throw new FileException($dir.'上级目录不可操作,无法创建');
        return false;
    }

    //创建目录
    if (!mkdir($dir,$code)) {
        throw new FileException($dir.'未知错误,无法创建');
        return false;
    }
    return true;
};