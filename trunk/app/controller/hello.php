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
		$title   = 'WPHP 框架官网';
		$content = 'WPHP 是 WhitePHP 的缩写，它是一个简单高效的 PHP 框架。WhitePHP 极其简单，就像一张任你书写的白纸一样。';
		render('vhello', array(
			'title' => $title,
			'content' => $content
		));
	}
	
	//测试数据库
	public function testdb() {
		/* 见 static/testdata.sql */
		
		$ret = array();
		
		// 方法1
		load_model('user');
		$model = User::singleton('user');
		
		// 方法2
		// 		$model = Model::singleton();
		
		// 使用原始方法
		$sql = 'select id, username from user group by username';
		// 		$q = $model::$db->query($sql);
		$q   = $model->query($sql);
		
		if ($q) {
			while (null != ($r = $q->fetch_assoc())) {
				$ret[] = $r;
			}
		}
		var_dump($ret);
		
		// 使用系统提供的方法
		// 		var_dump($model->select());
		
	}
}