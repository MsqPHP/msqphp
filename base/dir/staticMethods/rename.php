<?php declare(strict_types = 1);
namespace msqphp\base\dir;

/**
 * 目录|文件重命名
 *
 * @func_name     rename
 *
 * @param  string $old_path    目录|文件 路径
 * @param  string $new_path    重命名后路径
 * @param  bool   $force       忽略重名后路径重复, 忽略重名后父目录不存在
 *
 * @throws DirException
 * @return void
 */
return function (string $old_path, string $new_path, bool $force = true) : void {
    // 原目录是否存在
    is_dir($old_path) || static::exception($old_path.'不存在, 无法重命名');

    // 是否可操作
    if (!is_writable($old_path) || !is_executable($old_path)) {
        static::exception($old_path.'不可操作, 无法重命名');
    }

    // 目标目录是否存在
    if (is_dir($new_path)) {
        if ($force) {
            static::deleteDir($to_dir, true);
        } else {
            static::exception($new_path.'已存在, 无法重命名');
        }

    }
    // 目标父目录是否存在
    $new_parent_path = dirname($new_path);

    if (!is_dir($new_parent_path)) {
        if ($force) {
            static::make($new_parent_path, true);
        } else {
            static::exception($new_path.'上级目录不存在, 无法重命名');
        }
    }

    // 目标父目录是否可操作
    if (!is_writable($new_parent_path) || !is_executable($new_parent_path)) {
        static::exception($new_path.'上级目录无法操作, 无法重命名');
    }

    // 重命名
    rename($old_path, $new_path) || static::exception('未知错误, 无法重命名');
};