<?php declare(strict_types = 1);
namespace msqphp\test\core\cache;

use msqphp\base;

class CacheTest extends \msqphp\test\Test
{
    public function testStart() : void
    {
        $this->init();
        app()->config->set('cache', [
                // 是否允许多缓存处理器
                'multi'            =>  true,
                // 缓存处理器支持列表
                'sports'           =>  ['File','Memcached'],
                // 默认处理器
                'default_handler'  =>  'File',
                // 缓存前缀(影响全部)
                'prefix'           =>  'msq_',
                // 默认过期时间(影响全部)
                'expire'           =>  3600,
                // 处理器配置
                'handlers_config'  =>  [
                    /*
                        通用配置
                        'length'   =>  最多储存多少个缓存.即启用缓存队列,0则无限
                     */
                    'File'         =>  [
                        // 路径
                        'path'       => __DIR__.'/storage/cache',
                        // 后缀
                        'extension'  => '.cache',
                        // 深度
                        'deep'       => 0,
                        // 最大文件缓存数
                        'length'     => 0,
                        // 数据是否压缩
                        'compress'   => false,
                    ],
                ],
        ]);
        base\dir\Dir::empty(__DIR__.'/storage/cache');
        $this->object(app()->cache);
        $this->testThis($this);
    }
    public function testFile() : void
    {
        $this->clear();
        $this->chain([
            ['init', 'File'],
            ['key', 'test'],
            ['exists']
        ])->result(false)->test();
        $this->chain([
            ['value', 1],
            ['set'],
        ])->result(null)->test();
        $this->method('exists')->args(bull)->result(true)->test();
        $this->method('get')->args()->result(1)->test();
        $this->method('increment')->args()->result(2)->test();
        $this->method('get')->args()->result(2)->test();
        $this->method('decrement')->args()->result(1)->test();
        $this->method('get')->args()->result(1)->test();
        $this->method('inc')->args()->result(2)->test();
        $this->method('get')->args()->result(2)->test();
        $this->method('dec')->args()->result(1)->test();
        $this->method('exists')->args()->result(true)->test();
        $this->method('delete')->args()->result(null)->test();
        $this->method('exists')->args()->result(false)->test();
    }
}