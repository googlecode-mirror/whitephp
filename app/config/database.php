<?php defined('INDEX_PAGE') or die('no entrance'); ?>
<?php
/**
 * 数据库配置文件
 * 目前仅支持 mysql 数据库
 * 支持数据库主从分离，支持多数据库连接
 * 支持一主多从，从库加 Slave 区分，可在此动态确定具体的从库
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

/*---------------- default 数据库 -----------------*/
$db_conf['default'] = array(
	'db_username' => 'root',
	'db_passwd' => '123456',
	'db_database' => 'testdata',
	'db_host' => 'localhost',	//默认 localhost
	'db_port' => '3306',		//默认 3306
);

// 从数据库
// $db_conf['default_slave'] = array(
// 	'db_username' => 'root',
// 	'db_passwd' => '123456',
// 	'db_database' => 'testdata',
// 	'db_host' => 'localhost',
// 	'db_port' => '3306',
// );

//如果是 SAE 环境
if (defined('SAE_MYSQL_USER')) {
	/*---------------- SAE 数据库 -----------------*/
	$db_conf['default'] = array(
		'db_username' => SAE_MYSQL_USER,
		'db_passwd' => SAE_MYSQL_PASS,
		'db_database' => SAE_MYSQL_DB,
		'db_host' => SAE_MYSQL_HOST_M,
		'db_port' => SAE_MYSQL_PORT
	);

	$db_conf['default_slave'] = array(
		'db_username' => SAE_MYSQL_USER,
		'db_passwd' => SAE_MYSQL_PASS,
		'db_database' => SAE_MYSQL_DB,
		'db_host' => SAE_MYSQL_HOST_S,
		'db_port' => SAE_MYSQL_PORT
	);
}


/*---------------- test 数据库 -----------------*/
/*
$db_conf['test'] = array(
	'db_username' => 'root',
	'db_passwd' => '123456',
	'db_database' => 'testdata'
	'db_host' => 'localhost',	//默认 localhost
	'db_port' => '3306',		//默认 3306
);

多个从数据库实现示例
if (time()%2==0) {
	$db_conf['test_slave'] = array(
		'db_username' => 'root',
		'db_passwd' => '123456',
		'db_database' => 'testdata',
		'db_host' => 'localhost',
		'db_port' => '3306',
	);
} else {
	$db_conf['test_slave'] = array(
		'db_username' => 'root',
		'db_passwd' => '123456',
		'db_database' => 'testdata',
		'db_host' => 'localhost',
		'db_port' => '3306',
	);
}

*/
