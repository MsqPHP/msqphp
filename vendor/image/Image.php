<?php declare(strict_types = 1);
namespace msqphp\vendor\image;
/**
 *  image类(图片类)，基于GD库;
 *  用法:
 *      $image = new \msqphp\Image();
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
 *      $image = new \msqphp\Image();
 *      if($image->create(100, 100, 'white') === true) {
 *          1.$bool = $image->fillRandom(10, 1)->save('new.jpg', 'jpg');
 *          2.$bool = $image->fillPoint(x, y, '#888888')
 *                          ->water();
 *                          ->fillLine(10, 10, 20, 20, '#222222')
 *                          ->fillRect(20, 30, 40, 20, '#783943')
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
    //允许图片格式
    private $allowed       = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];

    //当前驱动
    static private $handler = null;
    static private $handler_type = 'Gd';


    public function __construct($handler = '') {
        if (static::$handler_type !== $handler) {
            //获得默认的驱动
            $handler = $handler ?: 'Gd';
            //载入文件
            require __DIR__.DIRECTORY_SEPARATOR.'ImageHandlerInterface.php';
            require __DIR__.DIRECTORY_SEPARATOR.'Handelers'.DIRECTORY_SEPARATOR.$handler_type.DIRECTORY_SEPARATOR.$handler_type.'.php';
            //建立实例
            $handler_class_name = __NAMESPACE__.'\\Handelers\\'.$handler_type.'\\'.$handler_type;
            static::$handler = $handler = new $handler_class_name();
            if($handler->isInstalled() === false) {
                throw new ImageException($handler_type.'库未安装', 1);
            }
        }
    }
    /**
     * 载入图片
     * @param  string $image_path 图片路径
     * @return self
     */
    public function load(string $path) : self
    {
        //检查是否为合法的image文件
        $this->checkFile($path);
        static::$handler->load($path);
        return $this;
    }
    /**
     * 图片类型转换
     * @func_name  convert
     * @param  string $type 目标类型
     * @return self
     */
    public function convert (string $type) : self
    {
        if (!in_array($type,$this->allowed)) {
            throw new \msqphp\core\exception\Exception($type,'不被允许', 500);
        }
        static::$driver->convert($type);
        return $this;
    }

/**********************
私有函数
**********************/
    /**
     * 检测是否为合法图片
     * @param  string $path 路径
     * @return void
     */
    private function checkFile(string $path)
    {
        //不是文件或者不在允许范围内
        if (!is_file($path) || !in_array(pathinfo($path, PATHINFO_EXTENSION), $this->allowed) ) {
            throw new ImageException($path. '为不允许的图片');
        }
    }
    public function __call($method, $args)
    {
        try {
            call_user_func_array([static::$handler, $method], $args);
        } catch(ImageException $e) {
            throw new ImageException($e->getMessage());
        }
        return $this;
    }
}