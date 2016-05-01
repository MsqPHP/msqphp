<?php declare(strict_types = 1);
namespace Core\Base\File;
/**
 * 目录|文件重命名
 * @func_name     rename
 * @param  string $old_path    目录|文件 路径
 * @param  string $new_path    重命名后路径
 * @param  bool   $force       忽略重名后路径重复,忽略重名后父目录不存在
 * @throws Exception
 * @return bool
 */
return function (string $old_path,string $new_path) : bool {
    //原目录是否存在
    if (!file_exists($old_path)) {
        throw new FileException($old_path.'不存在,无法重命名');
        return false;
    }
    //是否可操作
    if (!is_writable($old_path) || !is_readable($old_path)) {
        throw new FileException($old_path.'不可操作,无法重命名');
        return false;
    }
    
    //目标目录是否存在
    if (file_exists($new_path)) {
        if ($force === false) {
            throw new FileException($new_path.'已存在,无法重命名');
            return false;
        }
        $this->deleteDir($to_dir,true);
    }        
    //目标父目录是否存在
    $new_parent_path = dirname($new_path);
    if (!is_dir($new_parent_path)) {
        if (!$force) {
            throw new FileException($new_path.'上级目录不存在,无法重命名');
            return false;
        }
        $this->makeDir($new_parent_path,true);
    }
    //目标父目录是否可操作
    if (!is_writable($new_parent_path) || !is_readable($new_parent_path)) {
        throw new FileException($new_path.'上级目录无法操作,无法重命名');
        return false;
    }
    //重命名
    if (!rename($old_path,$new_path)) {
        throw new FileException('未知错误,无法重命名');
        return false;
    }
    return true;
};