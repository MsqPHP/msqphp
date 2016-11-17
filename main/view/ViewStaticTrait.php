<?php declare(strict_types = 1);
namespace msqphp\main\view;

use msqphp\base;
use msqphp\core;

trait ViewStaticTrait
{

    // 是否为静态页
    protected $static   = false;

    /**
     * 静态页面
     *
     * @param  int|integer  $expire    过期时间
     *
     * @return self
     */
    public function static(int $expire = 3600) : self
    {
        // 静态
        $this->options['static'] = HAS_STATIC;

        $this->options['static_path'] = $path = core\route\Route::getStaticPath();

        $this->options['static_content'] = 0 === $expire ? '' : '<?php if (time() >' . (string) (time() + $expire) .') {require \''.\msqphp\Environment::getPath('public').'server.php\';exit;}?>';

        return $this;
    }

    // 静态页面写入
    public function writeStaticHtml() : void
    {
        base\file\File::write($this->options['static_path'], $this->options['static_content'], true);
    }
}