<?php defined('INDEX_PAGE') or die('no entrance'); ?>
<?php
/**
 * 默认控制器
 *
 * 该控制器仅作为例子供用户参考
 * 请及时删除该类中的方法，以免被利用
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
		$title   = 'WPHP 配置成功了';
		$content = '
		<p>WPHP 是 WhitePHP 的缩写，它是一个简单高效的 PHP 框架。WhitePHP 极其简单，就像一张任你书写的白纸一样。</p>
		<p>您可以打开控制器目录，按照例子一步步来，相信很容易就会使用WPHP的。</p>
		';

		render('vhello', array(
			'title' => $title,
			'content' => $content
		));
	}
	
//
//	//测试数据库
//	//请注意及时隐藏方法
//	//使用 Model 的子类
//	public function testdb() {
//		/* 见 static/testdata.sql */
//
//		$ret = array();
//
// 		//User::$show_sql = true;
//		$model = User::singleton();
//
//		// 1 使用原始方法
//		$sql = 'select id, username from user limit 10';
//		$q = $model->db->query($sql);
//// 		$q   = $model->query($sql);
//
//		if ($q) {
//			while (null != ($r = $q->fetch_assoc())) {
//				$ret[] = $r;
//			}
//		}
//		var_dump($ret);
//		// var_export($ret);
//
//		// 2 使用系统提供的方法
//		// var_dump($model->select('*', '1 limit 10'));
//	}
//
//	//测试数据库1
//	// 请注意及时隐藏方法
//	//使用 Model 类
//	public function testdb1() {
//		/* 见 static/testdata.sql */
//
//		$ret = array();
//		// Model::$show_sql = true;
//		$model = Model::singleton('user');//表名
//
//		// 1 使用原始方法
//		// $sql = 'select id, username from user wherelimit 10';
//		// $q =  $model->db->query($sql);
//		// $q   = $model->query($sql);
//		// if ($q) {
//		// 	while (null != ($r = $q->fetch_assoc())) {
//		// 		$ret[] = $r;
//		// 	}
//		// }
//		// var_dump($ret);
//
//		// 2 使用系统提供的方法
//		// 若使用系统函数（如insert,delete,update,select,select_line）
//		// 必须保证正确设置了表名即 $model = Model::singleton('user');
//		// $model->insert(array('username'=>'hhh', 'password'=>'sss'));
//		// $model->update("username='google', password='abc'", 'id=1');
//		// $model->delete('id=2');
//		var_dump($model->select('*', '1 limit 10'));
//	}
//
//	//测试数据库2
//	// 请注意及时隐藏方法
//	//使用原始 mysql 资源链接
//	public function testdb2() {
//		/* 见 static/testdata.sql */
//
//		$ret = array();
//
//		//直接获取数据库链接资源
//		$model = db_init();
//		$sql = 'select id, username from user limit 10';
//		$q   = $model->query($sql);
//
//		if ($q) {
//			while (null != ($r = $q->fetch_assoc())) {
//				$ret[] = $r;
//			}
//		}
//		var_dump($ret);
//	}
//
//	// 请注意及时隐藏方法
//	public function hi() {
//		$data['data'] = 'hello world';
//		render('vhi.php', $data);
//	}
//
//	//及时隐藏方法
//	public function testchart() {
//		$data['data'] = array ( 0 => array ( 'id' => '1', 'username' => '赵彬言', ), 1 => array ( 'id' => '2', 'username' => '胡锦涛', ), 2 => array ( 'id' => '3', 'username' => 'Xi Jinping', ), 3 => array ( 'id' => '4', 'username' => 'Yetian', ), 4 => array ( 'id' => '5', 'username' => 'Shiyuan', ), 5 => array ( 'id' => '6', 'username' => '\'单引号', ), );
//		render('vtestchart.php', $data);
//	}
//
}