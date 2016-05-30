<?php declare(strict_types = 1);
namespace msqphp\base\file;
/**
 * 目录|文件重命名
 * @func_name     rename
 * @param  string $old_path    目录|文件 路径
 * @param  string $new_path    重命名后路径
 * @param  bool   $force       忽略重名后路径重复, 忽略重名后父目录不存在
 * @throws FileException
 * @return void
 */
return function (string $old_path, string $new_path)
{
    //原目录是否存在
    if (!is_file($old_path)) {
        throw new FileException($old_path.'文件不存在, 无法重命名');
    }
    //是否可操作
    if (!is_writable($old_path) || !is_executable($old_path)) {
        throw new FileException($old_path.'文件不可操作, 无法重命名');
    }
    //目标目录是否存在
    if (is_file($new_path)) {
        if ($force) {
            static::delete($to_dir, false);
        } else {
            throw new FileException($new_path.'对应文件已存在, 无法重命名');
        }

    }
    //目标父目录是否存在
    $new_parent_path = dirname($new_path);
    if (!is_dir($new_parent_path)) {
        if ($force) {
            base\dir\Dir::make($new_parent_path, true);
        } else {
            throw new FileException($new_path.'上级目录不存在, 无法重命名');
        }
    }
    //目标父目录是否可操作
    if (!is_writable($new_parent_path) || !is_executable($new_parent_path)) {
        throw new FileException($new_path.'上级目录无法操作, 无法重命名');
    }
    //重命名
    if (!rename($old_path, $new_path)) {
        throw new FileException('未知错误, 无法重命名');
    }
};