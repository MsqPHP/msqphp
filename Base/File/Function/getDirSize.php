<?php declare(strict_types = 1);
namespace Core\Base\File;
/**
 * 得到目录文件大小
 * @func_name     getDirSize
 * @param   string $path 路径
 * @param   bool    $round      是否保留整数  default     true
 * @param   bool    $unit       是否带单位      default     true
 * @return  strging|int
 */
return function (string $dir,bool $round = true,bool $unit = true) {
    if (!is_writable($dir)) {
        throw new \Exception($dir.' 文件夹不存在或无法操作', 1);
        return 0;
    }
    $dir = realpath($dir).DIRECTORY_SEPARATOR;
    
    $size = 0;
    
    foreach ($this->getList($dir,'dir') as $dir) {
        $size += $this->getDirSize($dir.$dir,false,false);
    }
    foreach ($this->getList($dir,'file') as $file) {
        $size += $this->getFileSize($dir.$file,false,false);
    }
    
    $round === true && $size = round($size);
    
    $unit === true && $size = \Core\Base\Str\Str::getSize($size);
    
    return $size;
};