<?php declare(strict_types = 1);
namespace Core\Base\File;
    //复制文件别名
return function (string $from,string $to,bool $force = false) : bool {
    return $this->copyFile($from,$to,$force);
};