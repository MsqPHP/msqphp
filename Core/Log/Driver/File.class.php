<?php
namespace Msqphp\Log\Driver;
class File {
	protected $_config = array(
		'_log_time_format' => ' c ',
		'_log_file_size' => 2097152,
		'_log_path' => '',
	);
	/**
	 * 实例化并传参
	 * @param array $config 配置数组
	 */
	public function __construct(array $_config = []) {
		$this->_config = array_merge($this->_config, $_config);
	}
	/**
	 * 日志写入接口
	 * @param  string $_log         日志信息
	 * @param  string $_destination 写入目标
	 * @return void
	 */
	public function write(string $_log, string $_destination = '') {
		//
		$now = date($this->_config['_log_time_format']);
		//得到写入目标文件路径
		$_destination = $_destination ?? $this->_config['_log_path'] . date('y_m_d') . '.log';
		//得到目录路径
		$_log_dir = dirname(($_destination));
		//判断目录是否存在
		if(!is_dir($_log_dir)) {
			mkdir($_log_dir, 0755, true);
		}
		//检测日志大小，超过则备份，重新生成
		if(is_file($_destination) && floor($this->_config['_log_file_size']) <= filesize($_destination)) {
			rename($_destination, $_log_dir . '/' . time() . '-' . basename($_destination));
		}
		//写入
		error_log('[' . $now .']' . $_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['REQUEST_URI'] . PHP_EOL . $_log . PHP_EOL, 3, $_destination);
	}
}