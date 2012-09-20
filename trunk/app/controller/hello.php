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
		$model = User::singleton();
// 		User::$tb_name = '';
// 		User::$show_sql = '';
		
		// 使用原始方法
		$sql = 'select id, username from user limit 10';
		// $q = User::$db->query($sql);
		$q   = $model->query($sql);
		
		if ($q) {
			while (null != ($r = $q->fetch_assoc())) {
				$ret[] = $r;
			}
		}
		var_dump($ret);
		
		// 使用系统提供的方法
		//var_dump($model->select('*', '1 limit 10'));
	}
	
	//测试数据库2
	public function testdb2() {
		/* 见 static/testdata.sql */
	
		$ret = array();
		$model = Model::singleton();//可写上表名和数据库配置名
// 		Model::$tb_name = '';
// 		Model::$show_sql = '';
	
		// 使用原始方法
		$sql = 'select id, username from user limit 10';
		// $q = Model::$db->query($sql);
		$q   = $model->query($sql);
	
		if ($q) {
			while (null != ($r = $q->fetch_assoc())) {
				$ret[] = $r;
			}
		}
		var_dump($ret);
	
		//使用系统提供的方法，若使用系统函数（如insert,delete,update,select,select_line）
		//必须保证正确设置了表名即 $model = Model::singleton('user');
		//var_dump($model->select('*', '1 limit 10'));
	}
	
	//测试数据库3
	public function testdb3() {
		/* 见 static/testdata.sql */
	
		$ret = array();
		
		//直接获取数据库链接资源
		$model = db_init();
		$sql = 'select id, username from user limit 10';
		$q   = $model->query($sql);
	
		if ($q) {
			while (null != ($r = $q->fetch_assoc())) {
				$ret[] = $r;
			}
		}
		var_dump($ret);
	}
}