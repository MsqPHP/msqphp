<?php declare(strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base;

/**
 * 通过文件相关时间删除文件
 * @func_name     deleteAllFileByTime
 * @param  string $dir        目录路径
 * @param  string $type       a(fileatime.访问时间), c(filectim.文件信息改变时间), m(filectime.修改时间)
 * @param  string $expire     过期时间
 * @param  string &ext        后缀缀名
 * @param  string $pre        前缀名
 * @throws DirException
 * @return void
 */
return function (string $dir, string $type, int $expire = 3600, string $ext = '', string $pre = '')
{
    //获得func 名
    switch ($type) {
        case 'a':
        case 'c':
        case 'm':
            $func = 'file'.$type.'time';
            break;
        default:
            throw new DirException($type.'应为 a(fileatime.访问时间), c(filectim.文件信息改变时间), m(filectime.修改时间)');
    }
    //过期时间
    $expire = time() - $expire;
    //遍历获得所有文件
    foreach(Dir::getAllFileByType($dir, $ext, $pre) as $file) {
        //过期删除
        $func($file) < $expire && base\file\File::delete($file);
    }
};