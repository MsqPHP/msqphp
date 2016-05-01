<?php declare(strict_types = 1);
namespace Core\Base\File;
/**
 * 根据类型获得指定目录下所有对应文件
 * @func_name     getAllFileByType
 * @param  string $dir 目录
 * @param  string $type 类型
 * @param  string $type 前缀
 * @return 一维索引数组，值为文件绝对路径
 */
return function (string $dir,string $type= '*',string $pre = '') : array {
    if (!is_writable($dir)) {
        throw new \Exception($dir.' 文件夹不存在或无法操作');
        return [];
    }
    $dir = realpath($dir).DIRECTORY_SEPARATOR;

    $files = [];

    foreach(glob($dir.$pre.'*.'.$type) as $_file) {
        $files[] = $_file;
    };
    
    foreach ($this->getList($dir,'dir') as $_dir) {
        $files = array_merge($files,$this->getAllFileByType($dir.$_dir,$type,$pre));
    }
    return $files;
};