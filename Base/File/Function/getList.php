<?php declare(strict_types = 1);
namespace Core\Base\File;
/**
 * 得到当前目录列表
 * @func_name       getList
 * @param  string   $dir 路径
 * @param  string   $type 类型(all|file|dir)
 * @throws Exception
 * @return array
 */
return function (string $dir,string $type = 'all') : array
{
    if (!is_writable($dir)) {
        //无法操作
        throw new FileException($dir.$this->errors[15]);
    }
    //根据类型进一步过滤
    switch ($type) {
        case 'all':
            return array_filter(scandir($dir,0), function($path) {
                return $path !== '.' && $path !== '..';
            },0);
            break;
        case 'file':
            return array_filter(scandir($dir,0), function($path) {
                return is_file($path);
            },0);
            break;
        case 'dir':
            return array_filter(scandir($dir,0), function($path) {
                return $path !== '.' && $path !== '..' && is_dir($path);
            },0);
            break;
        default:
            throw new FileException($type.'应为all|file|dir', 500);
            return [];
            break;
    }
};