<?php declare(strict_types = 1);

class PhpFile {
    /**
     * 过滤注释代码
     * @param  string $from 文件路径
     * @param  string $to   过滤后文件路经
     * @throws Exception
     * @return bool
     */
    public static function fifter(string $from, string $to) : bool
    {
        if(!is_file($from) || !is_readable($from)) {
            throw new \msqphp\core\exception\Exception($file.'文件不存在或不可读', 500);
        } elseif (!is_dir(dirname($to)) || !is_writable(dirname($to))) {
            throw new \msqphp\core\exception\Exception($to.'文件所在目录不可写', 500);
        } elseif (substr($file, -4) !== '.php') {
            throw new \msqphp\core\exception\Exception($from.'不是一个以.php结尾的php文件', 1);
        } else {
            $value = php_strip_whitespace($from);
            return false !== file_put_contents($to, $value);
        }
    }
    /**
     * 检测文件语法(未实现)
     * @param  string $file 文件路径
     * @throws Exception
     * @return bool
     */
    public static function check(string $file) : bool
    {
        if (is_file($file) || !is_writable($file)) {
            throw new \msqphp\core\exception\Exception($file.'文件不存在或不可读', 500);
        } else {
            return php_check_syntax($file);
        }
    }
    /**
     * 返回所有加载的文件
     * @return array
     */
    public static function requires() : array
    {
        return get_required_files();
    }
    public static function debugPrintBacktrace()
    {
        return debug_print_backtrace();
    }
}