<?php declare(strict_types = 1);
namespace Core\Base\File;
/**
 * 目录|文件重命名
 * @func_name     renameDir
 * @param  string $old_path    目录|文件 路径
 * @param  string $new_path    重命名后路径
 * @param  bool   $force       忽略重名后路径重复,忽略重名后父目录不存在
 * @throws Exception
 * @return bool
 */
return function (string $old_path,string $new_path,bool $force = false) : bool
{
    return $this->rename($old_path,$new_path,$force);
};