<?php declare(strict_types = 1);
namespace Msqphp\Base\File;
/**
 * 清空文件内容
 * @func_name     emptyFile
 * @param  string $file     文件路径
 * @param  bool   $force    为空创建
 * @throws Exception
 * @return bool
 */
return function (string $path,bool $force = false) : bool {
    //文件不存在
    if (!is_file($path) && !$force) {
        throw new FileException($file.'文件不存在,无法清空');
        return false;
    }
    //创建一个空文件
    return $this->write($path,'',true);
};