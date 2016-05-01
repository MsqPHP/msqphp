<?php declare(strict_types = 1);
namespace Core\Base\File;
/**
 * empty 清空文件别名
 * @func_name     empty
 */
return function (string $path,bool $force = false) : bool {
    return $this->emptyFile($path,$force);
};