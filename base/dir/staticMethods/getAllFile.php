<?php declare(strict_types = 1);
namespace msqphp\base\dir;

/**
 * 根据类型获得指定目录下所有对应文件
 * @func_name     getAllFile
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

    $file_list = Dir::getFileList($dir, true);

    foreach (Dir::getDirList($dir, true) as $children_dir) {
        $file_list = array_merge($file_list, Dir::getAllFile($children_dir));
    }

    return $file_list;
};