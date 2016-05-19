<?php declare(strict_types = 1);
namespace Tool;
class Timer
{
    //开始时间
    static private $start_time = 0; 
    //经过时间
    static private $total_time = 0; 
    //调用次数
    static private $calls = 0;
    //当前定时器
    static private $timer = '';
    //所用定时器
    static private $timers = [];
    //开始
    public static function startTimer($name = '') {
        if(isset(static::$timers[$name])) {
            throw new \Exception('定时器已经被定义', 1);
        }
        static::$timers[$name]['start_time'] = microtime(true);
    }
    //增加时间
    public static function addTime() {
        $timer = & static::getTimer($name);
        $spend = microtime(true) - $timer['start_time'];
        $timer['total_time'] += $spend;
        ++ $timer['calls'];
        return $spend;
    }
    //得到调用次数
    public static function getCalls() : int{
        return static::$timers['calls'];
    }
    //得到经过时间
    public static function getElapsedTime() {
        $timer = & static::getTimer($name);
        if($timer['total_time'] === 0) {
            return microtime(true) - $timer['start_time'];
        }
        return $timer['total_time'];
    }

    public static function createTimer($name = '') {
        if($name === '') {
            if(static::$timer !== '') {
                throw new \Exception('定时器已经被定义', 1);
            }
            $name = 'default_timer';
            static::$timer = $name;           
        }
        static::startTimer($name);    
    }
    public static function getTimers() {
        return static::$timers;
    }
    public static function clearTimers() {
        static::$timers = [];
    }
    static private function & getTimer(string $name) {
        $timers = static::$timers;
        if($name === '') {
            return $timers[static::$timer];
        } elseif(isset($timers[$name])) {
            return $timers[$name];
        } else {
            throw new \Exception('定时器不存在', 500);            
        }
    }
}