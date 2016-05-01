<?php declare(strict_types = 1);
namespace Msqphp\Base\File;
/**
*类使用说明：
*    例：
*    $driver = \Msqphp\Base\File::getInstance();        ->        获得本对象
*    参数：
*        @param  string $file        =>        文件路径
*        @param  string $dir         =>        目录路径
*        @param  int    $code        =>        目录读写执行权限      @default  0777
*        @param  bool   $force       =>        是否强制              @default  false
*                                                   1.文件不存在时创建
*                                                   2.目录不存在时创建                                            
*                                                   3.忽略文件是否存在 例(deleteFile,appendContent,saveContent,write)
*                                                   4.忽略dir是否为空  例(deleteDir)
*                                                   5.是否强制创建父目录
*                                                   6.忽略目录以创建
*        @return bool   失败,除了is开头以外,全部抛出Exception
*        @return string 对应值，失败为空
*    $driver->
*             makeDir($dir,$force,$code) : bool                ->  创建目录
*             deleteDir($dir,$force) : bool                    ->  删除目录
*                别名:dropDir;           
*             deleteFile($file,$force) : bool                  ->  删除文件
*                别名:delete,drop,dropFile;           
*             emptyDir($dir,$force) : bool                     ->  清空目录
*             emptyFile($file,$force) : bool                   ->  清空文件
*                别名：empty;           
*             rename($old_path,$new_path) : bool               ->  目录|文件重命名
*                别名：renameFile,renameDir;           
*             readFile($file,$len) : string                    ->  读取指定长度的文件内容
*                别名:read;           
*             getContent($file) : string                       ->  读取文件内容
*                别名:get;           
*             appendContent($file,$content) : string           ->  追加文件内容
*                别名:append;
*             copyDir($from_path,$to_path,$force) : bool       ->  复制目录
*             copyFile($from_path,$to_path,$force) : bool      ->  复制文件
*                别名:copy;
*             moveDir($from_path,$to_path,$force) : bool       ->  移动目录(copy+delete)
*             moveFile($from_path,$to_path,$force) : bool      ->  移动文件(copy+delete)
*                别名:move;
*                注：如果在同一盘符什么的可以用rename什么的;
*             writeFile($file,$content,$force) : bool          ->  写入文件
*                别名:write,saveFile,save
*             getErrorInfo() : string                           ->  错误信息
*
*             getList($dir,$type='all'|'file'|'dir') : array   ->  得到目录列表
*                注：错误dir抛出异常
*             getAllFileByType($dir,$type,$pre = '') : array   ->  通过类型获得文件列表
*             deleteFileByType($dir,$type,$pre = '') : bool    ->  通过类型删除文件
*                注：type为后缀名，不加点 例: 'php',            或'class.php'
*                匹配模式为：                  $pre . '*.php'   $pre . '*.class.php'
*             deleteFileByTime($dir,$type,$deadtime = 3600,$ext='',$pre = '') : bool ->通过文件相关时间来删除
*             
*             getDirSize($dir,$round,$unit)                    ->  获得目录大小
*             getFileSize($file,$round,$unit)                  ->  获得文件大小
*                注：路径，是否取整，带单位,依赖于 \Msqphp\StringClass::getSize();
*             getFileExt($file) : string                       ->  获得文件后缀名，失败返回''
*             getFileInfo($file,$type)                         ->  获得文件信息
*
*/
class File
{
    /**
     * 当前实例
     * @var obj
     */
    static private $instance = null;
    /**
     * 私有化构造方法
     */
    private function __construct() {}
    /**
     * 获得实例
     * @return $this
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new File();
        }
        return static::$instance;
    }
    /**
     * 删除文件
     * @param  string $path     路径
     * @param  bool   $force    忽略是否存在,强制删除
     * @throws Exception
     * @return bool
     */
    public function deleteFile(string $file,bool $force = false) : bool
    {
        //是否存在
        if (!is_file($file)) {
            if ($force) {
                return true;
            } else {
                throw new FileException($file.'文件不存在,无法删除');
                return false;
            }
        }
        //权限判断
        if (!is_writable($file) || !is_executable($file)) {
            throw new FileException($file.'文件不可操作,无法删除');
            return false;
        }
        //删除文件
        if (unlink($file)) {
            return true;
        } else {
            throw new FileException($file.'未知错误,无法删除');
            return false;
        }
    }
    //删除文件别名
    public function dropFile(string $file,bool $force = false) : bool {return $this->deleteFile($file,$force);}
    public function drop(string $file,bool $force = false) : bool {return $this->deleteFile($file,$force);}
    public function delete(string $file,bool $force = false) : bool {return $this->deleteFile($file,$force);}
    /**
     * 读取指定长度的文件内容
     * @param  string $file 目标文件路径
     * @param  string $len  长度
     * @throws Exception
     * @return string
     */
    public function readFile(string $file,int $len) : string
    {
        if(!is_writable($file)) {
            throw new FileException($file.'不存在或不可读');
            return '';
        }
        $fp = fopen($file,'r');
        $content = (string) fread($fp, $len);
        fclose($fp);
        unset($fp);
        if(false === $content || strlen($content) < $len) {
            throw new FileException($file.'读取'.$len.'长度，但文件无法读取或者长度不够');
            return '';
        }
        return $content;
    }
    public function read(string $file,int $len) : string
    {
        return $this->readFile($file,$len);
    }
    /**
     * 获得文件内容
     * @param  string $file 目标文件路径
     * @throws Exception
     * @return string
     */
    public function getContent(string $file) : string
    {
        if(!is_writable($file)) {
            throw new FileException($file.'不存在或不可读');
            return '';
        }
        if (false !== ($content = file_get_contents($file))) {
            return $content;
        } else {
            throw new FileException($file.'未知错误,无法读取');
            return '';
        }
        
    }
    public function get(string $file) : string
    {
        return $this->getContent($file);
    }
    /**
     * 追加文件内容
     * @param  string     $file     目标文件路径
     * @param  string|int $content  追加内容
     * @param  string     $force    当文件不存在的时候是否强制创建
     * @throws Exception
     * @return bool
     */
    public function appendContent(string $file,$content,bool $force = false) : bool
    {
        if (false === is_file($file)) {
            if (false === $force) {
                throw new FileException($file.' 文件不存在');
                return false;
            }
            return $this->write($file,$content,$force);
        }

        if (false === $this->writable($file)) {
            throw new FileException($file.' 文件不可写');
            return false;
        }

        if (false === file_put_contents($file,$content,FILE_APPEND)) {
            throw new FileException($file.' 无法追加内容');
            return false;
        }
        return true;
    }
    public function append(string $file,$content,bool $force = false) : bool
    {
        return $this->append($file,$content,$force);
    }
    /**
     * 重写文件|保存文件
     * @param  string     $file     路径
     * @param  string|int $content  写入内容
     * @param  bool       $force    如果父文件夹不存在，强制创建 如果文件不存在，创建
     * @throws Exception
     * @return bool
     */
    public function write(string $file,$content,bool $force = false) : bool
    {
        $parent_dir = dirname($file);
        if (!is_dir($parent_dir)) {
            if (!$force) {
                throw new FileException($file.'父目录不存在');
                return false;
            }
            $this->makeDir($parent_dir,$force);
        }
        if (!is_writable($parent_dir)) {
            throw new FileException($file.'父目录不可写');
            return false;
        }
        if (!file_put_contents($file,(string)$content)) {
            throw new FileException($file.'未知错误,无法写入', 500);
            return false;
        }
        return true;
    }
    public function writeFile(string $file,$content,bool $force = false) : bool {return $this->write($file,$content,$force);}
    public function saveFile(string $file,$content,bool $force = false) : bool {return $this->write($file,$content,$force);}
    public function save(string $file,$content,bool $force = false) : bool {return $this->write($file,$content,$force);}
    
    public function __call(string $method, array $args)
    {
        static $func = [];
        if (! isset($func[$method])) {
            $func[$method] = require __DIR__.DIRECTORY_SEPARATOR.'Function'.DIRECTORY_SEPARATOR.$method.'.php';
        }
        return call_user_func_array($func[$method], $args);
    }
}