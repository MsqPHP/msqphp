<?php declare(strict_types = 1);
namespace Msqphp\Base\File;
    /**
     * 目录是否为空
     * @param  string $dir      目录路径
     * @return bool
     */
return function (string $dir) : bool {
    //scandir 获得当前目录列表, 如果为空,则只有 . 和 ..
    return count(scandir($dir)) === 2;
};