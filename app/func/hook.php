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

function wphp_hook_change_query_string($query_string) {
	return $query_string;
}

function wphp_hook_before_instance() {
	
}

function wphp_hook_after_instance() {
	echo '<br>';
	var_dump($_GET);
}