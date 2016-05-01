<?php declare(strict_types = 1);
namespace Msqphp\Base\File;
/**
 * 复制文件
 * @func_name                 copyFile 
 * @param  string $from       文件路径
 * @param  string $to         文件路径
 * @param  string $force      目标路径不存在则强制创建,对应文件存在则替换
 * @throws Exception
 * @return bool
 */
return function (string $from, string $to,bool $force = false) : bool {
    //是否存在
    if (!is_file($from)) {
        throw new FileException($from.'不存在,无法复制');
        return false;
    }
    //是否可操作
    if (!is_readable($from) || !is_writable($from)) {
        throw new FileException($from.'无法操作,无法复制');
        return false;
    }
    //对应文件是否存在
    if (is_file($to)) {
        if (!$force) {
            throw new FileException($to.'已存在,无法复制');
            return false;
        }
        $this->deleteFile($file);
    }
    //对应文件父目录是否可操作
    $to_dir = dirname($to);
    if (!is_writable($to_dir) || !is_readable($to_dir)) {
        throw new FileException($to.'父目录无法操作,无法复制');
        return false;
    }
    //复制
    if (!copy($from,$to)) {
        throw new FileException('未知错误.无法复制');
        return fasle;
    }
    return true;
};