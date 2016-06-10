<?php declare(strict_types = 1);
namespace msqphp\core\config;

use msqphp\base;

final class Config
{
    //配置
    private static $config = [];
    /**
     * 初始化配置
     * @return void
     */
    public static function init()
    {
        if (defined('NO_CACHE')) {
            static::loadAllConfig();
        } else {
            $cache_path = \msqphp\Environment::getPath('storage').'framework'.DIRECTORY_SEPARATOR.'Config.php';
            if (is_file($cache_path)) {
                static::$config = require $cache_path;
            } else {
                static::loadAllConfig();
                base\file\File::write($cache_path, '<?php return '.var_export(static::$config, true).';', true);
            }
        }
    }
    /**
     * 得到配置
     * @param  string $key 键
     * @return miexd
     */
    public static function get(string $key = '')
    {
        return base\arr\Arr::get(static::$config, $key);
    }
    /**
     * 设置配置值
     * @param  string $key   键
     * @param  miexd  $value 值
     * @return void
     */
    public static function set(string $key, $value)
    {
        base\arr\Arr::set(static::$config, $key, $value);
    }
    /**
     * 加载所有配置
     * @return void
     */
    private static function loadAllConfig()
    {
        //加载所有
        array_map('static::loadConfig', base\dir\Dir::getFileList(\msqphp\Environment::getPath('config'), true));
    }
    private static function loadConfig(string $file)
    {
        if (!is_readable($file)) {
            throw new ConfigException('配置文件不存在或不可读');
        }

        $file_info = pathinfo($file);

        switch ($file_info['extension']) {
            case 'php':
                static::$config[$file_info['filename']] = require $file;
                break;
            case 'txt':
                static::$config[$file_info['filename']] = unserialize(base\file\File::get($file));
                break;
            case 'xml':
                static::$config[$file_info['filename']] = base\xml\Xml::decode(base\file\File::get($file));
                break;
            case 'ini':
                static::$config[$file_info['filename']] = base\ini\Ini::decode(base\file\File::get($file));
                break;
            default:
                throw new ConfigException($file_info['extension'].'未知类型的config文件');
        }
    }
}