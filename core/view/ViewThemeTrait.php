<?php declare(strict_types = 1);
namespace msqphp\core\view;

use msqphp\base;
use msqphp\core;

trait ViewThemeTrait
{
    //主题支持
    protected $theme    = false;

    protected function initTheme()
    {
        $this->theme      = true;
        $this->options['theme'] = defined('__THEME__') ? __THEME__ : $this->config['default_theme'];
    }


    public function theme(string $theme = '')
    {
        if (!$this->theme) {
            throw new ViewException('不支持多主题');
        }

        if ('' === $theme) {
            return $this->getTheme();
        } else {
            return $this->setTheme($theme);
        }
    }
    protected function getTheme() : string
    {
        return $this->options['theme'];
    }
    protected function setTheme(string $theme) : self
    {
        if (in_array($this->config['theme_sport_list'])) {
            $this->options['theme'] = $theme;
            return $this;
        } else {
            throw new ViewException('不支持的主题:'.$theme);
        }
    }
}