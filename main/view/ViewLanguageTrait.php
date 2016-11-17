<?php declare(strict_types = 1);
namespace msqphp\main\view;

trait ViewLanguageTrait
{
    // 多语支持
    protected $multilingual = false;

    protected function initLanguage()
    {
        $this->multilingual = true;

        $config         = $this->config;

        // 获取当前语言
        is_dir($config['language_path']) || $this->exception('语言存放目录不存在,无法开启视图多语支持');

        $this->config['language_path'] = realpath($config['language_path']) . DIRECTORY_SEPARATOR;

        $this->options['language']     = defined('__LANGUAGE__') ? __LANGUAGE__ : $config['default_language'];
    }

    /**
     * 获取或设置语言
     * @param   string|null  $language  为空获取,有值设置
     * @return  string|self
     */
    public function language(?string $language = null)
    {
        if (null === $language) {
            return $this->getLanguage();
        } else {
            $this->setSanguage($language);
            return $this;
        }
    }

    // 获取语言
    public function getLanguage() : string
    {
        $this->multilingual || $this->exception('视图配置中未开启,无法开启视图多语支持');

        return $this->options['language'];
    }
    // 设置语言
    public function setLanguage(string $language) : void
    {
        $this->multilingual || $this->exception('视图配置中未开启,无法开启视图多语支持');

        $this->options['language'] = $language;
    }
    /**
     * 获取对应的语言数据
     * @param   string  $file_name  文件名称
     * @return  array
     */
    protected function getLanguageData(string $file_name) : array
    {
        $this->multilingual || $this->exception('视图配置中未开启,无法开启视图多语支持');

        $file = $this->getLanguageFile($file_name);

        return is_file($file) ? require $file : [];
    }

    /**
     * 获取对应的语言文件路径
     * @param   string  $file_name  文件名称
     * @return  string
     */
    protected function getLanguageFile(string $file_name) : string
    {
        $file = $this->config['language_path'] . $this->options['language'] . DIRECTORY_SEPARATOR . $this->getGroup() . DIRECTORY_SEPARATOR . $file_name . '.php';

        is_file($file) || $file = $this->config['language_path'] . $this->config['default_language'] . DIRECTORY_SEPARATOR . $this->getGroup() . DIRECTORY_SEPARATOR . $file_name . '.php';

        return $file;
    }
}