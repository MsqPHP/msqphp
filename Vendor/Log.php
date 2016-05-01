<?php
namespace Msqphp;
class Log {
    // 日志级别 从上到下，由低到高
    const EMERG     = 'EMERG';  // 严重错误: 导致系统崩溃无法使用
    const ALERT     = 'ALERT';  // 警戒性错误: 必须被立即修改的错误
    const CRIT      = 'CRIT';  // 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
    const ERR       = 'ERR';  // 一般错误: 一般性错误
    const WARN      = 'WARN';  // 警告性错误: 需要发出警告的错误
    const NOTICE    = 'NOTIC';  // 通知: 程序可以运行但是还不够完美的错误
    const INFO      = 'INFO';  // 信息: 程序输出信息
    const DEBUG     = 'DEBUG';  // 调试: 调试信息
    const SQL       = 'SQL';  // SQL：SQL语句 注意只在调试模式开启时有效
    // 日志信息
    static protected $_log       =  [];

    // 日志存储
    static protected $_storage   =   null;

    /**
     * 日志初始化设置
     * @param  array $config 数组
     * @return void 
     */
    static public function init( array $_config = [])
    {
        //得到存储方式
        $_save_type = $_config['type'] ?? $GLOBALS['_config']['LOG_TYPE'] ?? 'File';
        $_save_type = ucfirst($_save_type);
        //得到对应类
        $_driver_class  = strpos($_save_type, '\\') ? $_save_type : 'Msqphp\\Log\Driver\\' . $_save_type;
        //销毁配置
        unset($_config['type']);
        self::$_storage = new $_driver_class($_config);
    }
    /**
     * 记录日志
     * @param  string         $_log_message 日志信息
     * @param  string         $_log_level   错误级别
     * @param  boolen         $_force_record  是否强制记录
     * @return void
     */
    //static public function record(string $_log_message, $_log_level = self::ERR, boolen $_force_record = false) {
    static public function record(string $_log_message, $_log_level = self::ERR, $_force_record = false )
    {
        if($_force_record === true || strpos($GLOBALS['_config']['LOG_LEVEL'], $_log_level) !== false) {
            self::$_log[] = $_log_level . ':' . $_log_message . PHP_EOL;
        }
    }
    /**
     * 保存日志
     * @param  string $_save_type        记录方式
     * @param  string $_destination 写入目标
     * @return void
     */
    static public function save(string $_save_type = '', string $_destination)
    {
        //1.如果日志为空则返回
        if(empty(self::$_log)) {return false;}
        //2.如果未指定缓存目录，则取配置
        $_destination = $_destination ?? $GLOBALS['_config']['LOG_PATH'] . date('y_m_d') . '.log';
        //3.???
        if(!self::$_storage) {
            //3.1得到存储方式
            $_save_type = $_save_type ?: $GLOBALS['_config']['LOG_TYPE'];
            $_save_type = ucfirst($_save_type);
            //3.2载入对应驱动
            $_driver_class = 'Msqphp\\Log\\Driver\\' . $_save_type;
            //3.3实例化            
            self::$_storage = new $_driver_class();
        }
        //4.处理信息
        $_log_message = implode('', self::$_log);
        //5.写入
        self::$_storage->write($_log_message, $_destination);
        //6.至空
        self::$_log = [];
    }
    /**
     * 写入日志
     * @param  string $_log_message 日志信息
     * @param  string $_log_level   错误级别
     * @param  string $_save_type   保存类型
     * @param  string $_destination 写入目标
     * @return void
     */
    static public function write(string $_log_message, string $_log_level = self::ERR, string $_save_type = '', string $_destination = '')
    {
        //1.??
        if(!self::$_storage) {
            //1.1得到存储方式
            $_save_type = $_save_type ?: $GLOBALS['_config']['LOG_TYPE'];
            //1.2得到存储路径
            $_config['path'] = $GLOBALS['_config']['LOG_PATH'];
            //1.3载入对应驱动
            $_driver_class = 'Msqphp\\Log\\Driver\\' . $_save_type;
            //1.4实例化
            self::$_storage = new $_driver_class($_config);
        }
        //2.得到写入目标文件
        $_destination = $_destination ?: $GLOBALS['_config']['LOG_PATH'] . date('y_m_d') . '.log';
        //3.写入
        self::$_storage->write($_log_level . ':' . $_log_message, $_destination);
    }
}