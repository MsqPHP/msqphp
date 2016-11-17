<?php declare(strict_types = 1);
namespace msqphp\core\loader;

use msqphp\base;

/**
 * 实现原理:
 * 修改代码 composer/ClassLoader.php函数为
 * function includeFile($file)
 * {
 *   \msqphp\core\loader\Loader::addClasses($file);
 *   include $file;
 * }
 * 或者使用框架本身加载类
 * 处理过程: 未知->收集->整理->完成
 * +. 判断是否有缓存信息,有则判断对应状态,跳至对应步骤
 * +. 未知:没有对应信息,在结束时获得一次加载文件列表,进入收集模式
 * +. 收集:判断收集记录个数->不足继续收集
 *                         ->整理所有记录,当出现概率超过70%时,将该文件放入待整理文件列表中.
 * +. 整理:整理文件列表,直至依赖关系解决,排序完成
 * +. 完成:直接载入
 * [
 *     'type'    => 'unknown|collect|miexd|last',
 *     'collect' => [] // 收集记录,
 *     'needful' => [] // 需要文件
 *     'tidied'  => [] // 整理后文件
 *     'last'    => [] // 最终列表
 * ]
 *
 * 例:
 * 需要加载123456789九个文件, 1需要23,2需要468,3需要57,45678无依赖
 * 1. 收集12345678
 * 2. 收集123456789
 * .....
 * 10. 收集结果12345678十次,9五次,需要加载文件列表为12345678
 * 11. 加载文件1,加载前未加载.放入tided,但依赖导致加载2345678, 当加载2-8时,时光荏加载前已经加载,放至needful中
 * 12. 将2-8反序加载,依次加载文件8765432,加载前均未加载,放入tidied中,此时,needful为空获得最终加载文件列表
 * 13. 直接加载87654321.
 */
trait AiloadTrait
{
    use AiLoadPointerTrait, AiLoadOperateTrait;

    // 初始化静态类
    private static function initStatic() : void
    {
        static::emptyClasses();
    }
}

trait AiLoadPointerTrait
{
    // 指针
    private $pointer = [];

    // 构造函数
    public function __construct()
    {
        // 清空,避免对其他地方自动加载造成污染;
        $this->init();
    }

    // 初始化
    public function init() : self
    {
        // 静态类初始化
        static::initStatic();

        // 指针清空
        $this->pointer = [];

        return $this;
    }

    // 赋值键
    public function key(string $key) : self
    {
        $this->pointer['key'] = $key;
        return $this;
    }
}

trait AiLoadOperateTrait
{
    // 加载对应键缓存
    public function load() : void
    {
        $info = $this->getInfo();
        switch ($info['type']) {
            case 'unknown':
                // 赋值
                $this->pointer['info'] = ['type'=>'unknown'];
                break;
            case 'last':
                // 不为空则,全部载入. 注:如果为空,不判断,array_map报错
                !empty($info['last']) && array_map(function (string $file) {
                    require $file;
                }, $info['last']);
                // 赋值
                $this->pointer['info'] = ['type'=>'last', 'last'=>$info['last']];
                break;
            case 'collect':
                // 赋值
                $this->pointer['info'] = ['type'=>'collect', 'collect'=>$info['collect']];
                break;
            case 'mixed':
                // 需要加载 整理后的列表
                $needful = $tidied = [];
                // 加载需要加载的文件
                foreach (array_reverse($info['needful']) as $file) {
                    // 如果已经加载过,再次放入needful
                    if (in_array($file, static::$classes)) {
                        $needful[] = $file;
                    } else {
                    // 添加至整理过的
                        $tidied[] = $file;
                        require $file;
                    }
                }
                // 加载所有整理过的文件
                if (isset($info['tidied'])) {
                    foreach ($info['tidied'] as $file) {
                        require $file;
                        $tidied[] = $file;
                    }
                }
                // 如果needful为空,则表示得到最终加载顺序
                $this->pointer['info'] = empty($needful) ? ['type'=>'last', 'last'=>$tidied] : ['type'=>'mixed', 'needful'=>$needful,'tidied'=>$tidied];
                break;
            default:
                static::exception('错误的aiload缓存,文件位置:' . (string) $file);
        }
    }

    // 获得缓存信息
    private function getInfo() : array
    {
        // 缓存文件
        $file = $this->getCahceFilePath($this->pointer['key']);
        // 无缓存或者文件不存在,返回未知数据
        if (!HAS_CACHE || !is_file($file)) {
            return ['type' => 'unknown'];
        }
        // 返回载入文件
        return require $file;
    }

    // 是否需要加载文件明确且顺序排列完毕
    public function last() : bool
    {
        return $this->pointer['info']['type'] === 'last';
    }

    // 得到最终需要加载文件列表
    public function getLastNeedfulClasses() : array
    {
        // 没有处理到最后,不可以获得对应文件列表,抛出异常
        $this->last() || static::exception('aiload缓存未处理至最后,无法获取最终需要文件列表');
        // 返回
        return $this->pointer['info']['last'];
    }

    // 删除当前键对应缓存
    public function delete() : void
    {
        try {
            base\file\File::delete($this->getCahceFilePath($this->pointer['key']), true);
        } catch (base\dir\DirException $e) {
            static::exception('无法删除指定的智能加载缓存文件,错误原因:'.(string)$e->getMessage());
        }
    }

    // 删除所有缓存
    public function deleteAll() : void
    {
        try {
            // 清空对应目录下所有文件
            base\dir\Dir::empty($this->getCacheDirPath(), true);
        } catch (base\dir\DirException $e) {
            static::exception('无法删除所有智能加载缓存文件,错误原因:'.(string)$e->getMessage());
        }
    }


    // 更新当前键对应缓存
    public function update() : void
    {
        switch ($this->pointer['info']['type']) {
            case 'unknown':
                // 没有数据,获得第一的的记录,进入收集模式
                $info = ['type'=>'collect', 'collect' => [static::getClasses()]];
                break;
            case 'collect':
                $collect = $this->pointer['info']['collect'];
                // 如果收集结果等于10,开始整理收集记录
                if (10 === count($collect)) {
                    $needful = [];

                    $counts = [];

                    foreach ($collect as $collect_files) {
                        foreach ($collect_files as $collect_file) {
                            $counts[$collect_file] = $counts[$collect_file] ?? 0;
                            ++$counts[$collect_file];
                        }
                    }

                    foreach ($counts as $file => $count) {
                        $count > 7 && $needful[] = $file;
                    }

                    // 得到混合文件列表,排序未知
                    $info = ['type' => 'mixed', 'needful' => $needful];
                } else {
                    // 继续收集
                    $info = ['type' => 'collect', 'collect' => array_merge($collect, [static::getClasses()])];
                }
                break;
            case 'mixed':
            case 'last':
                // 载入时已更新
                $info = $this->pointer['info'];
                break;
            default:
                static::exception('未知错误');
        }
        // 写入缓存
        $this->writeCache($info);
        // 赋值
        $this->pointer['info'] = $info;
    }


    // 得到缓存文件路径
    private function getCahceFilePath(string $key) : string
    {
         return $this->getCacheDirPath() . md5($key) . '.php';
    }

    // 得到缓存目录
    private function getCacheDirPath() : string
    {
        return \msqphp\Environment::getPath('storage') . 'framework/aiload/';
    }

    // 写入缓存
    private function writeCache(array $info) : void
    {
        try {
            base\file\File::write($this->getCahceFilePath($this->pointer['key']), '<?php'.PHP_EOL.'return '.var_export($info, true).';', true);
        } catch (base\file\FileException $e) {
            static::exception('无法写入智能加载缓存,错误原因:'.(string)$e->getMessage());
        }
    }
}