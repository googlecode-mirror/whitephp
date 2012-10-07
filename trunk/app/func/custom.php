<?php defined('INDEX_PAGE') or die('no entrance'); ?>
<?php
/**
 * 无需更改框架即可改变系统
 * 
 * filename:	hook.php
 * charset:		UTF-8
 * create date: 2012-9-30
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

function wphp_custom_change_query_string($query_string) {
	//echo $query_string;
	return $query_string;
}

function wphp_custom_before_instance() {
	
}

function wphp_custom_after_instance() {
	return;
	
	echo '<div style="display:none"><pre>';
	
	echo '<br><br>$_GET = ';
	var_export($_GET);

	echo '<br><br>$_POST = ';
	var_export($_POST);

	echo '<br><br>$_COOKIE = ';
	var_export($_COOKIE);

	echo '<br><br>$_SERVER = ';
	var_export($_SERVER);
	
	echo '</pre></div>';
}