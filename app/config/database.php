<?php defined('INDEX_PAGE') or die('no entrance'); ?>
<?php
/**
 * 数据库配置文件
 * 目前仅支持 mysql 数据库
 * 支持数据库主从分离，支持多数据库连接
 * 
 * filename:	database.php
 * charset:		UTF-8
 * create date: 2012-5-25
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

//改善机制，支持多数据库
$db_conf = array();

//是否启用数据库
define('IS_DB_ACTIVE', TRUE);

/*---------------- 主数据库 -----------------*/
//每增加一个数据库，增加一条配置信息，索引为数据库分组，db.php 会用到,extract
$db_conf['default'] = array(
	'db_username' => 'root',
	'db_passwd' => '123456',
	'db_database' => 'testdata'
	// 		'db_host' => 'localhost',	//默认 localhost
	// 		'db_port' => '3306',		//默认 3306
);

//从数据库,此数据库仅作为 default 的从数据库
// $db_conf['slave'] = array(
// 		'db_username' => 'root',
// 		'db_passwd' => '123456',
// 		'db_database' => 'testdata',
// 		'db_host' => 'localhost',
// 		'db_port' => '3306',
// );

//如果是 SAE 环境
if (defined('SAE_MYSQL_USER')) {
	/*---------------- 主数据库 -----------------*/
	//每增加一个数据库，增加一条配置信息，索引为数据库分组，db.php 会用到,extract
	$db_conf['default'] = array(
		'db_username' => SAE_MYSQL_USER,
		'db_passwd' => SAE_MYSQL_PASS,
		'db_database' => SAE_MYSQL_DB,
		'db_host' => SAE_MYSQL_HOST_M,
		'db_port' => SAE_MYSQL_PORT
	);
	
	//从数据库,此数据库仅作为 default 的从数据库
	$db_conf['slave'] = array(
		'db_username' => SAE_MYSQL_USER,
		'db_passwd' => SAE_MYSQL_PASS,
		'db_database' => SAE_MYSQL_DB,
		'db_host' => SAE_MYSQL_HOST_S,
		'db_port' => SAE_MYSQL_PORT
	);
}

/*
//初始配置例子
$db_conf['default'] = array(
'db_username' => 'root',
'db_passwd' => '123456',
'db_database' => 'testdata',
'db_host' => 'localhost',	//默认 localhost
'db_port' => '3306',		//默认 3306
);

//从数据库,此数据库仅作为 default 的从数据库
$db_conf['slave'] = array(
'db_username' => 'root',
'db_passwd' => '123456',
'db_database' => 'testdata',
'db_host' => 'localhost',
);

//其它数据库
$db_conf['yourownname'] = array(
'db_username' => 'root',
'db_passwd' => '123456',
'db_database' => 'testdata',
'db_host' => 'localhost',
);

*/
