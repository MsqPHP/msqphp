<?php declare(strict_types = 1);
namespace msqphp\core\view;

use msqphp\base;
use msqphp\core;

trait ViewLanguageTrait
{
    //多语支持
    protected $language = false;

    public function language(string $language = '')
    {
        if ($this->language) {
            if ('' === $language) {
                return $this->getLanguage();
            } else {
                return $this->setSanguage($language);
            }
        } else {
            throw new ViwException('未设置多语支持');
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
        if ($this->language) {
            $file = $this->config['language_path'].$this->options['language'].DIRECTORY_SEPARATOR.$this->options['group'].DIRECTORY_SEPARATOR.$file_name.'.php';
            if (!is_file($file)) {
                $file = $this->config['language_path'].$this->config['default_language'].DIRECTORY_SEPARATOR.$this->options['group'].DIRECTORY_SEPARATOR.$file_name.'.php';
            }
            return is_file($file) ? require $file : [];
        } else {
            return [];
        }
    }
}