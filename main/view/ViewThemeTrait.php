<?php declare(strict_types = 1);
namespace msqphp\main\view;

trait ViewThemeTrait
{
    // 主题支持
    protected $multiple_theme    = false;

    // 初始化主题支持
    protected function initTheme()
    {
        $this->multiple_theme      = true;
        $this->options['theme'] = defined('__THEME__') ? __THEME__ : $this->config['default_theme'];
    }

    /**
     * 获取或设置主题
     * @param   string|null  $theme  为空获取,有值设置
     * @return  string|self
     */
    public function theme(?string $theme = null)
    {
        if (null === $theme) {
            return $this->getTheme();
        } else {
            $this->setTheme($theme);
            return $this;
        }
    }
    // 获取主题
    public function getTheme() : string
    {
        $this->multiple_theme || $this->exception('视图配置中未开启,无法开启视图多主题支持');

        return $this->options['theme'];
    }

    // 设置主题
    public function setTheme(string $theme) : void
    {
        $this->multiple_theme || $this->exception('视图配置中未开启,无法开启视图多主题支持');

        in_array($this->config['theme_sport_list']) || $this->exception('视图多主题允许列表中不包括该主题:'.$theme);

        $this->options['theme'] = $theme;
    }
}