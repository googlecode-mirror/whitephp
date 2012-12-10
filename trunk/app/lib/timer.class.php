<?php
/**
 * Timer
 * 
 * filename:	timer.php
 * charset:		UTF-8
 * create date: 2012-9-25
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

/**
 * Timer
 */
class Timer {
	private $stime = 0;
	private $etime = 0;
	private $data = array();

	public function start() {
		$this->stime = microtime(true);
	}

	public function end() {
		$this->etime = microtime(true);
	}

	public function time($dot = 4) {
		return sprintf("%.${dot}f", (float)($this->etime - $this->stime));
	}

	public function show($dot = 4) {
		return printf("%.${dot}f", (float)($this->etime - $this->stime));
	}
	
	public function set($key) {
		if (!in_array($key, $this->data)) {
			$this->data[$key] = microtime(true);
		}
	}

	public function get($key, $dot = 4) {
		if (array_key_exists($key, $this->data)) {
			return sprintf("%.${dot}f", (float)($this->data[$key]));
		}
	}

	public function get_all() {
		return $this->data;
	}
}