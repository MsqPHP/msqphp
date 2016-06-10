<?php declare(strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base;

    /**
     * 清空目录内容
     * @func_name               empty
     * @param  string $dir      目录路径
     * @param  bool   $force    为空创建
     * @throws DirException
     * @return void
     */
return function (string $dir, bool $force = false) {
    //目录检测
    if (!is_dir($dir)) {
        if ($force) {
            Dir::make($dir, $force);
        } else {
            throw new DirException($dir.'目录不存在, 无法清空');
        }
    } else {
        //权限判断
        if (!is_writable($dir)) {
            throw new DirException($dir.'目录不可操作, 无法清空');
        }
        //清空目录
        foreach (Dir::getDirList($dir, true) as $children_dir) {
            Dir::delete($children_dir, true);
        }
        foreach (Dir::getFileList($dir, true) as $children_file) {
            base\file\File::delete($children_file, true);
        }
    }
};