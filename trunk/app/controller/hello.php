<?php
/**
 * 默认控制器
 * 
 * filename:	hello.php
 * charset:		UTF-8
 * create date: 2012-5-25
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */
class Hello extends Controller {
	public function index() {
		$db = db_init();
		$title = 'WPHP 框架官网';
		$content = 'WPHP 是 WhitePHP 的缩写，它是一个简单高效的 PHP 框架。WhitePHP 极其简单，就像一张任你书写的白纸一样。';
		
		render('vhello', get_defined_vars());		
	}
}