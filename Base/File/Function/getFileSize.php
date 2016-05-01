<?php declare(strict_types = 1);
namespace Core\Base\File;
/**
 * 得到文件大小
 * @func_name     getFileSize
 * @param   string $path 路径
 * @param   bool    $round      是否保留整数  default     true
 * @param   bool    $unit       是否带单位      default     true
 * @return  strging|int
 */
return function (string $file,bool $round = true,bool $unit = true) {
    if (!is_writable($file)) {
        throw new \Exception($dir.' 文件不存在或无法操作', 1);
        return false;
    }
    //获得字节大小
    $size = filesize($path);
    
    $round === true && $size = round($size);
    $unit === true && $size = \Core\StringClass::getSize($size);
    return $size;
};