<?php declare(strict_types = 1);
namespace Msqphp\Base\File;
/**
 * 通过文件相关时间删除文件
 * @func_name     deleteFileByTime
 * @param  string $dir        目录路径
 * @param  string $type       a(fileatime.访问时间),c(filectim.文件信息改变时间),m(filectime.修改时间)
 * @param  string $expire   过期时间
 * @param  string &ext        后缀缀名
 * @param  string $pre        前缀名
 * @return bool   是否成功
 */
return function (string $dir,string $type,int $expire = 3600,string $ext = '',string $pre = '') : bool {
    switch ($type) {
        case 'a':
        case 'c':
        case 'm':
            $func = 'file'.$type.'time';
            break;
        default:
            throw new FileException($type.'应为 a(fileatime.访问时间),c(filectim.文件信息改变时间),m(filectime.修改时间)');
            return;
            break;
    }
    $expire = time() - $expire;
    foreach($this->getAllFileByType($dir,$ext,$pre) as $file) {
        if ($func($file) > $expire && !$this->deleteFile($file)) {
            return false;
        }
    }
    return true;
};