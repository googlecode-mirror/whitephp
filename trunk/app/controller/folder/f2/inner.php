<?php
/**
 * 测试二级目录下的控制器是否好用
 * 
 * filename:	inner.php
 * charset:		UTF-8
 * create date: 2012-6-26
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

class Inner extends Controller {
	
	public function index() {
		echo 'hello';
		echo_code();
	}
}