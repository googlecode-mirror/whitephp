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

	
	// 对 model 层的操作
	public function db() {
		$user = new User();
		//var_dump($user->get_row());
	}
	
	


}
