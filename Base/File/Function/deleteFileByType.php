<?php declare(strict_types = 1);
namespace Core\Base\File;
    /**
     * 通过文件名称删除文件
     * @func_name     deleteFileByType
     * @param  string $path  目录路径
     * @param  string $type  后缀名
     * @param  string $pr    前缀名
     * @return bool   是否成功
     */
return function (string $dir,string $type,string $pre = '') : bool {
    return array_walk($this->getAllFileByType($dir,$type,$pre),function($file){
        $this->deleteFile($file,true);
    });    
};