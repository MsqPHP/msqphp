<?php declare(strict_types = 1);
namespace Msqphp\Base\File;
//删除目录别名
return function (string $dir,bool $force = false) : bool {
    return $this->deleteDir($dir,$force);
};