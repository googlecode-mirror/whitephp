<?php defined('INDEX_PAGE') or die('no entrance'); ?>
<?php
/**
 * 系统函数
 * 若要使用系统提供的验证码功能 echo_code() ，请勿删除此文件
 * verify_code() 函数不受此限制
 * 
 * 用来测试各项功能是否可用以及是否完整
 * 请【不要删除此文件】，否则一些功能可能无法使用，如可视化的验证码
 * 
 * filename:	syscommon.php
 * charset:		UTF-8
 * create date: 2012-5-25
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

class Syscommon {
	public function index() {
		$title   = '测试';
		$content = '测试页面是否可用';
		render('vhello', get_defined_vars());
	}
	
	/**
	 * 测试验证码
	 */
	public function vcode() {
		if (isset($_SESSION['verify_code'])) {
			echo '<br />Pre Code: ', $_SESSION['verify_code'];
		}
		
		echo_code();
	}
	
	/**
	 * 和系统函数有关，勿删除
	 */
	public function verify_code() {
		//验证码函数参数
		$array = array(
			'width' => 100,
			'height' => 30,
			'num' => 5,
			'verify_code' => 'verify_code',
			'ads' => array(),
			'teight' => 9 / 10,
			'angle' => 30
		);
		verify_code($array);
	}
}