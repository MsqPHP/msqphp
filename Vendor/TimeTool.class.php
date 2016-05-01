<?php declare(strict_types = 1);
namespace Tool;
class TimeTool {
	private $timeZone;
	static public function setTimeZone($timeZone) {
		$this->timeZone = $timeZone;
		date_default_timezone_set($timeZone);
	}
	static public function getTimeZone() {
		$this->timeZone = date_timezone_get();
	}
	static public function getTime() {
		empty($timeZone) && self::getTimeZone();
		$timeZone = self::$timeZone;
		return date(self::getTimeFormat());
	}
	static public function getTimeFormat() {
		empty($timeZone) && self::getTimeZone();
		$timeZone = self::$timeZone;
		return 'Y-m-d H:i:s';
	}
}