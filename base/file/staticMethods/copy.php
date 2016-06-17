<?php declare(strict_types = 1);
namespace msqphp\base\file;

    /**
     * 复制文件
     * @param  string $from       文件路径
     * @param  string $to         文件路径
     * @param  string $force      不存在则创建, 存在则替换
     * @throws FileException
     * @return void
     */
return function (string $from, string $to, bool $force = false) {
    //是否存在
    if (!is_file($from)) {
        throw new FileException($from.static::ERROR['NotExists'].',无法赋值');
    }

    //是否可操作
    if (!is_readable($from)) {
        throw new FileException($from.static::ERROR['CanNotOperable'].',无法赋值');
    }

    //对应文件是否存在
    if (is_file($to)) {
        if ($force) {
            static::delete($to, true);
        } else {
            throw new FileException($to.static::ERROR['AlreadyExists'].',无法赋值');
        }
    }

    //对应文件父目录是否可操作

    if (!is_writable(dirname($to))) {
        throw new FileException($to.static::ERROR['ParentDir'].static::ERROR['CanNotOperable'].',无法赋值');
    }

    //复制
    if (!copy($from, $to)) {
        throw new FileException($from.static::ERROR['UnknownErro'].',无法赋值');
    }
};