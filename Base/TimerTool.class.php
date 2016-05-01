<?php declare(strict_types = 1);
namespace Tool;
class Timer {
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
	static public function startTimer($name = '') {
		if(isset(self::$timers[$name])) {
			throw new \Exception('定时器已经被定义', 1);
		}
		self::$timers[$name]['start_time'] = microtime(true);
	}
	//增加时间
	static public function addTime() {
		$timer = & self::getTimer($name);
		$spend = microtime(true) - $timer['start_time'];
		$timer['total_time'] += $spend;
		++ $timer['calls'];
		return $spend;
	}
	//得到调用次数
	static public function getCalls() : int{
		$timer = & self::getTimer($name);
		return $timers['calls'];
	}
	//得到经过时间
	static public function getElapsedTime() {
		$timer = & self::getTimer($name);
		if($timer['total_time'] === 0) {
			return microtime(true) - $timer['start_time'];
		}
		return $timer['total_time'];
	}

	static public function createTimer($name = '') {
		if($name === '') {
			if(self::$timer !== '') {
				throw new \Exception('定时器已经被定义', 1);
			}
			$name = 'default_timer';
			self::$timer = $name;			
		}
		self::startTimer($name);	
	}
	static public function getTimers() {
		return self::$timers;
	}
	static public function clearTimers() {
		self::$timers = [];
	}
	static private function & getTimer(string $name) {
		$timers = self::$timers;
		if($name === '') {
			return $timers[self::$timer];
		} elseif(isset($timers[$name])) {
			return $timers[$name];
		} else {
			throw new \Exception('定时器不存在', 500);			
		}
	}
}