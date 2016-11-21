<?php declare(strict_types = 1);
namespace msqphp\main\view\component;

use msqphp\base;
use msqphp\core;

final class StaticHtml
{
    private $path;
    private $content;
    public function __construct(array $config)
    {

        $this->path = core\route\Route::getStaticPath();

        $expire = $config['expire'];

        $this->content = 0 === $expire ? '' : '<?php if (time() >' . (string) (time() + $expire) .') {require \''.\msqphp\Environment::getPath('public').'server.php\';exit;}?>';
    }
    public function addContent(string $content) : void
    {
        $this->content .= $content;
    }
    // 静态页面写入
    public function writeHtml() : void
    {
        base\file\File::write($this->path, $this->content, true);
    }
    public function __destruct()
    {
        $this->writeHtml();
    }
}