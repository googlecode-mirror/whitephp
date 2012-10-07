<?php defined('INDEX_PAGE') or die('no entrance'); ?>
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
		// echo __FILE__;
		echo "<br>\r\n";
		echo_code();
		echo "<br>\r\n";
		//echo "<a href=" . href('/hello/index') . ">首页</a>";
		echo "<br>\r\n";
		echo "<a href=" . href('/folder/inner/index', array('param1'=>'value1')) . ">inner 文件夹内</a>";
		echo "<br>\r\n";
		echo "<a href=" . href('/folder/f2/inner/index', array('param1'=>'value1')) . ">folder/f2 文件夹内</a>";

	}
}