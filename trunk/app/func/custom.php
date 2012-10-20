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
	// echo $query_string;
	return $query_string;
}

function wphp_controller_unexists($c) {
	echo 'controller not found';
	log_error('controller ' . $c . ' not found - ' . get_conf('query_string'));
}

function wphp_action_unexists($a) {
	echo 'action not found';
	log_error('action ' . $a . ' not found - ' . get_conf('query_string'));
}

function wphp_page_unexists($file) {
	echo 'file not found';
	log_error('file ' . $file . ' not found - ' . get_conf('query_string'));
}

function wphp_custom_before_instance() {
	$timer = new Timer();
	$timer->start();
	set_conf('timer', $timer);
}

function wphp_custom_after_instance() {
	$timer = get_conf('timer');
	$timer->end();
	echo "<br>\r\n";
	echo $timer->time(), ' s';
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