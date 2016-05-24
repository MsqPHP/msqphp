<?php declare(strict_types = 1);
namespace msqphp\core\cache\handlers;

use msqphp\base;use msqphp\traits;
use msqphp\core;

class File implements CacheHandlerInterface
{
    //配置参数
    private $config = [
        //路径
        'path'       => '',
        //后缀
        'extension'  => '',
        //深度
        'deep'       => 1,
        //最大文件缓存数
        'length'     => 0,
        //数据是否压缩
        'compress'   => false,
    ];

    public function __construct(array $config)
    {
        $this->config = array_merge($this->config, $config);

        $path = $this->config['path'];
        if (!is_dir($path)) {
            base\dir\Dir::make($path , true);
        }
        $this->config['path'] = realpath($path) . DIRECTORY_SEPARATOR;
    }
    /**
     * cache是否存在
     * @param  string $key cache键
     * @return boolen      是否存在
     */
    public function available(string $key) : bool
    {
        $now  = time();
        //获得文件路径
        $file = $this->filename($key);
        clearstatcache(true, $file);
        //文件不存在返回false
        if (is_file($file)) {
            //读取前十个字符, 如果大于现在时间, 则过期
            try {
                //是否为空
                if ((int)base\file\File::read($file, 10) < $now) {
                    base\file\File::delete($file);
                    return false;
                } else {
                    return true;
                }
            } catch(base\file\FileException $e) {
                return false;
            }
        } else {
            return false;
        }
    }
    /**
     * 得到cache
     * @param  string $key 键
     * @return string      值
     */
    public function get(string $key)
    {
        //获得文件路径
        $file = $this->filename($key);
        //得到内容
        try {
            $value = base\file\File::get($file);
        } catch(base\file\FileException $e) {
            throw new CacheHandlerException($e->getMessage());
        }
        //是否解压
        if ($this->config['compress'] && function_exists('gzuncompress')) {
            $value = gzuncompress($value);
        }
        //去除前十个字符（过期时间）
        return unserialize(substr($value, 10));
    }
    /**
     * 设置缓存
     * @param string      $key    键
     * @param string      $value  值
     * @param int         $expire 存在时间
     */
    public function set(string $key, $value, int $expire)
    {
        //获得文件路径
        $file     = $this->filename($key);
        //值:过期时间 . 转义后的值

        $value = (string)(time() + $expire) . serialize($value);
        //是否压缩
        if ($this->config['compress'] && function_exists('gzcompress')) {
            $value = gzcompress($value, 3);
        }
        //存储
        try {

            base\file\File::write($file, $value, true);

            //如果限制了最大储存数, 调用队列
            $this->config['length'] > 0 && $this->queue($key);

        } catch(base\file\FileException $e) {
            throw new CacheHandlerException($e->getMessage());
        }
    }
    /**
     * 递增
     * @param  string $key  键
     * @param  int    $step 偏移量
     * @return [type]
     */
    public function increment(string $key, int $offset)
    {
        $file = $this->filename($key);
        try {
            clearstatcache(true, $file);
            if (is_file($file)) {
                $expire = (int) base\file\File::read($file, 10);
                $now = time();
                if ($expire < $now) {
                    throw new CacheHandlerException($key.'已过期,无法自增');
                } else {
                    $num = $this->get($key);
                    $num += $offset;
                    $this->set($key, $num, $expire - $now);
                }
            } else {
                throw new CacheHandlerException($key.'不存在,无法自增');
            }
        } catch(base\file\FileException $e) {
            throw new CacheHandlerException($e->getMessage());
        }
    }
    public function decrement(string $key, int $offset)
    {
        $file = $this->filename($key);
        try {
            clearstatcache(true, $file);
            if (is_file($file)) {
                $expire = (int) base\file\File::read($file, 10);
                $now = time();
                if ($expire < $now) {
                    throw new CacheHandlerException($key.'已过期,无法自增');
                } else {
                    $num = $this->get($key);
                    $num -= $offset;
                    $this->set($key, $num, $expire - $now);
                }
            } else {
                throw new CacheHandlerException($key.'不存在,无法自增');
            }
        } catch(base\file\FileException $e) {
            throw new CacheHandlerException($e->getMessage());
        }
    }
    /**
     * 删除指定缓存
     * @param  string $key 键
     * @return boolen      是否成功
     */
    public function delete(string $key)
    {
        //获得文件路径
        try {
            base\file\File::delete($this->filename($key), true);
        } catch(base\file\FileException $e) {
            throw new CacheHandlerException($e->getMessage());
        }
    }
    /**
     * 清空所有缓存
     * @throws FileException
     * @return void
     */
    public function clear()
    {
        try {
            base\dir\Dir::deleteAllFileByType($this->config['path'], $this->config['extension'], $this->config['prefix']);
        } catch(base\file\FileException $e) {
            throw new CacheHandlerException($e->getMessage());
        }
    }
    /**
     * 缓存队列整理
     * @throws FileException
     * @return void
     */
    private function queue($key)
    {

        //获得缓存队列文件名
        $queue_file = $this->config['path'].'cacheQueue.php';

        //如果不存在
        $queue = is_file($queue_file) ? require $queue_file : [];

        //如果未找到则添加
        false === array_search($key, $queue) && array_push($queue, $key);

        //如果队列长度大于配置长度
        if (count($queue) > $this->config['length']) {
            //移除第一个
            $old_key = array_shift($queue);
            //删除对应文件
            base\file\File::delete($this->filename($this->filename($old_key)), true);
        }
        //重新写入
        base\file\File::write($queue_file, '<?php return '.var_export($queue, true).';', true);
    }
    /**
     * 得到缓存文件名
     * @param  string $key 键
     * @return string
     */
    private function filename(string $key) : string
    {
        $name = md5($key);

        $dir  = $this->config['path'];
        //深度
        $deep = $this->config['deep'];

        for ($i = 0; $i < $deep; ++$i) {

            $dir .= $name[$i].DIRECTORY_SEPARATOR;
            //目录不存在则创建
            if (!is_dir($dir)) {
                base\dir\Dir::make($dir, true, 0755);
            }
        }

        //目录.md5后键.扩展名
        return $dir.$name.$this->config['extension'];
    }
}