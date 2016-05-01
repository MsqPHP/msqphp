<?php declare(strict_types = 1);
namespace Msqphp\Base\File;

    /**
     * 移动目录,文件
     * @func_name                  moveDir
     * @param  string $from       目录路径
     * @param  string $to         目标路径
     * @throws Exception
     * @return bool
     */
return function (string $from, string $to) : bool {
    return $this->copyDir($from,$to) && $this->deleteDir($from);
};