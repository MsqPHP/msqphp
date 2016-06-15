<?php declare(strict_types = 1);
namespace msqphp\core\view;

use msqphp\base;
use msqphp\core;

trait ViewLanguageTrait
{
    //多语支持
    protected $language = false;

    protected function initLanguage()
    {
        $this->language = true;
        $config         = $this->config;
        //获得当前语言
        if (!is_dir($config['language_path'])) {
            throw new ViewException('语言目录不存在');
        }
        $this->config['language_path'] = realpath($config['language_path']) . DIRECTORY_SEPARATOR;
        $this->options['language']     = defined('__LANGUAGE__') ? __LANGUAGE__ : $config['default_language'];
    }

    public function language(string $language = '')
    {
        if (!$this->language) {
            throw new ViwException('未设置多语支持');
        }

        if ('' === $language) {
            return $this->getLanguage();
        } else {
            return $this->setSanguage($language);
        }
    }
    protected function getLanguage() : string
    {
        return $this->options['language'];
    }
    protected function setLanguage(string $language) : self
    {
        $this->options['language'] = $language;
        return $this;
    }
    protected function getLanguageData(string $file_name) : array
    {
        if (!$this->language) {
            throw new ViwException('未设置多语支持');
        }
        $file = $this->config['language_path'].$this->options['language'].DIRECTORY_SEPARATOR.$this->options['group'].DIRECTORY_SEPARATOR.$file_name.'.php';
        is_file($file) || $file = $this->config['language_path'].$this->config['default_language'].DIRECTORY_SEPARATOR.$this->options['group'].DIRECTORY_SEPARATOR.$file_name.'.php';
        return is_file($file) ? require $file : [];
    }
}