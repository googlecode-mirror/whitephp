<?php defined('INDEX_PAGE') or die('no entrance'); ?>
<?php
/**
 * 数据库连接文件
 * 后期可能会支持多种连接方式，暂时仅采用 mysql 
 * 
 * filename:    db.php
 * charset:        UTF-8
 * create date: 2012-5-25
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

/**
 * 数据库初始化，如果直接调用此函数记得关闭。
 * $db = db_init('default');//配置名称
 * $db->query();
 * $db->close();
 */

set_conf('db_conf', $db_conf);

function db_init($db_group = 'default') {
	IS_DB_ACTIVE or show_error('please check your database configration');
	//数据库资源
	$db          = false;
	$db_username = '';
	$db_passwd   = '';
	$db_database = '';
	$db_host     = 'localhost';
	$db_port     = '3306';
	$db_conf     = get_conf('db_conf');
	$charset     = str_replace('-', '', CHARSET); //将 utf-8 切换为 utf8
	
	if (!$db_conf) {
		show_error('none valid database configration!');
	} else if (!array_key_exists($db_group, $db_conf)) {
		show_error("invalid database group '{$db_group}' called!");
	} else {
		extract($db_conf[$db_group]);
		$db = new mysqli($db_host, $db_username, $db_passwd, $db_database, $db_port);
		if ($db->connect_errno) {
			$error_msg = convert_str('database established error - error code: ' . $db->connect_errno . " | " . 'error msg: ' . $db->connect_error);
			show_error($error_msg);
		} else {
			$db->set_charset($charset); //由 set names xxx 升级为该行
		}
	}
	return $db;
}