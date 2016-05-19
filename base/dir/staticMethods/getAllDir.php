<?php declare(strict_types = 1);
namespace msqphp\base\dir;

/**
 * 根据类型获得指定目录下所有对应文件
 * @func_name     getAllDir
 * @param  string $dir 目录
 * @param  string $type 类型
 * @param  string $type 前缀
 * @throws DirException
 * @return 一维索引数组，值为文件绝对路径
 */
return function (string $dir) : array
{

    if (!is_readable($dir)) {
        throw new DirException($dir.' 文件夹不存在或无法操作');
    }

    $dir_list = Dir::getDirList($dir, true);

    foreach (Dir::getList($dir, 'dir', true) as $children_dir) {
        $dir_list = array_merge($dir_list, Dir::getAllDir($children_dir));
    }

    return $dir_list;
};