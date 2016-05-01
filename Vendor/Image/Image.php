<?php declare(strict_types = 1);
namespace Msqphp\Vendor\Image;
/**
 *  image类(图片类)，基于GD库;
 *  用法:
 *      $image = new \Msqphp\Image();
 *      if($image->load('image_file_path')) {
 *      $bool = $image->resize(100)
 *                    ->water()
 *                    ->save('s');
 *      if($bool === false) {
 *          echo $image->getErrorInfo();
 *      }
 *      } else {
 *          echo $image->getErrorInfo();
 *      }
 *      $image->close();
 *      $image = null;
 *  用法：
 *      $image = new \Msqphp\Image();
 *      if($image->create(100,100,'white') === true) {
 *          1.$bool = $image->fillRandom(10,1)->save('new.jpg','jpg');
 *          2.$bool = $image->fillPoint(x,y,'#888888')
 *                          ->water();
 *                          ->fillLine(10,10,20,20,'#222222')
 *                          ->fillRect(20,30,40,20,'#783943')
 *                          ->save($path);
 *          if($bool === false) {
 *              echo $image->getErrorInfo();
 *          }
 *      } else {
 *          echo $image->getErrorInfo();
 *      }
 *  属性:
 *      $error_info 错误信息
 *  函数：
 *      load        打开图片
 *      save        保存图片
 *      dump        html输出
 *      resize      尺寸调整
 *      crop        裁剪图片
 *      scale       缩放图片
 *      wate        水印
 *      fillRect    填充矩形
 *      fillPoint   填充点
 *      fillLine    填充线
 *      fillRandom  批量填充点，线，矩形
 *  
 */
class Image
{

    const IMAGE_WATER_LEFT_TOP      = 1;//左上
    const IMAGE_WATER_LEFT_CENTER   = 2;//左中
    const IMAGE_WATER_LEFT_BOTTOM   = 3;//左下
    const IMAGE_WATER_CENTER_TOP    = 4;//中上
    const IMAGE_WATER_CENTER_CENTER = 5;//中中
    const IMAGE_WATER_CENTER_BOTTOM = 6;//中下
    const IMAGE_WATER_RIGHT_TOP     = 7;//右上
    const IMAGE_WATER_RIGHT_CENTER  = 8;//右中
    const IMAGE_WATER_RIGHT_BOTTOM  = 9;//右下

    const IMAGE_FILL_POINT = 1;//点
    const IMAGE_FILL_LINE  = 2;//直线
    const IMAGE_FILL_RECT  = 3;//矩形
    //默认保存图片格式
    private $default_type  = 'jpeg';
    //允许图片格式
    private $allowed       = array('jpg','jpeg','png','gif','bmp');
    
    private $info          = [];
    
    //水印路径
    private $water_path    = 'C:\Users\Administrator\Desktop\images\mark.png';
    public $error_info     = '';
    //当前驱动
    static private $driver = null;
    static private $driver_type = 'Gd';


    public function __construct($driver = '') {
        if (static::$driver_type !== $driver) {
            //获得默认的驱动
            $driver_type = $driver ?: 'Gd';
            //载入文件
            require __DIR__.DIRECTORY_SEPARATOR.'DriverInterface.php';
            require __DIR__.DIRECTORY_SEPARATOR.'Driver'.DIRECTORY_SEPARATOR.$driver_type.DIRECTORY_SEPARATOR.$driver_type.'.php';
            //建立实例
            $driver_class_name = __NAMESPACE__.'\\Driver\\'.$driver_type.'\\'.$driver_type;
            static::$driver = $driver = new $driver_class_name();
            if($driver->isInstalled() === false) {
                throw new ImageException($driver_type.'库未安装', 1);
            }
        }
    }
    /**
     * 载入图片
     * @param  string $image_path 图片路径
     * @return bool
     */
    public function load(string $path) : bool
    {
        //检查是否为合法的image文件
        if($this->checkFile($path) === true) {
            $this->info['path'] = $path;
            return static::$driver->load($path);
        } else {
            return false;
        }
    }
    /**
     * 创建一个新的图片
     * @param  int    $w        宽
     * @param  int    $h        高
     * @param  array  $bg_color 背景色('r'=>??,'g'=>??,'b'=>??[,'a'=>0.1 - `1])
     * @return bool
     */
    public function create(int $w,int $h,$bg_color = '#FFFFFF') : bool
    {
        return static::$driver->create($w,$h,$color);
    }
    /**
     * 设置背景颜色
     * @param array $bg_color 背景颜色
     * @param void
     */
    public function setBgColor($color)
    {
        static::$driver->setBgColor($color);
        return $this;
    }
    /**
     * 输出 html 图片
     * @param  string $type 输出类型
     * @return bool
     */
    public function output($type = '') : bool
    {
        return static::$driver->output($type);
    }
    /**
     * 保存
     * @param  string       $save_path 路径
     * @param  string       $save_type 类型
     * @return bool         是否成功
     */
    public function save(string $save_path,string $type = '') : bool
    {
        switch ($save_path) {
            case 's':case 'n':case 'b':case 'w':case 't':case 'c':
                $path = $this->info['path'] ?: static::$driver->getImageInfo('path');
                if($path === '' || $path === false) { throw new \Exception('未定义的文件路径', 500);return false; }
                $file_info = pathinfo($path);
                $type = $type ?: $file_info['extension'];
                $save_path = $file_info['dirname'].'/'.$file_info['filename'].'_'.$save_path.'.'.$type;
                break;
            default:
                $save_path = $save_path;
                break;
        }
        return static::$driver->save($save_path,$type);
    }
    /**
     * 关闭并清除所有资源
     * @return bool
     */
    public function close()
    {
        if(static::$driver->close() === true) {
            static::$driver = null;
        } else {
            return false;
        }
    }
    /**
     * 获得错误信息
     * @return string
     */
    public function getErrorInfo() : string
    {
        if($this->error_info !== '') {
            return $this->error_info;
        } else {
            return static::$driver->getErrorInfo();
        }
    }


/**********************
私有函数
**********************/
    /**
     * 检测是否为合法图片
     * @param  string $path 路径
     * @return bool       是否合法
     */
    private function checkFile(string $path) : bool
    {
        //不是文件或者不在允许范围内
        $bool = (is_file($path) && in_array(pathinfo($path,PATHINFO_EXTENSION),$this->allowed) );
        $bool || $this->error_info = $path . '图片文件非法';
        return $bool;
    }
    /**
     * 得到图片信息
     * @param  string $path 路径
     * @return 信息       
     */
    private function getInfo(string $key = '')
    {
        return static::$driver->getInfo($key);
    }
    /**
     * 得到图片路径
     * @param  string $type water|
     * @return 路径
     */
    private function getImagePath(string $type,string $path) : string
    {
        if(is_file($path)) {
            return $path;
        } else {
            if($type === 'water') {
                return $this->water_path;
            }
        }
    }
    
    public function __call($method,$args)
    {
        static $func = [];
        if (isset($func[$method])) {
            return call_user_func_array($func[$method],$args);
        } elseif (is_file(__DIR__.'/Function/'.$method.'.php')) {
            $func[$method] = include __DIR__.'/Function/'.$method.'.php';
            return call_user_func_array($func[$method],$args);
        } else {
            call_user_func_array(array(static::$driver,$method),$args);
            return $this;
        }
    }

}