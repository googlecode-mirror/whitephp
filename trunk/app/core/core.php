<?php
/**
 * 这个文件是框架的核心
 * 
 * filename:	core.php
 * charset:		UTF-8
 * create date: 2012-5-25
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */
//start

//主要配置文件
require APP_PATH . '/config/system.php';

//设置时区，编码等信息
header('Content-Type:text/html; charset="' . CHARSET . '"');	//可以覆写
date_default_timezone_set(TIME_ZONE);

//工作环境
switch (SYS_MODE) {
	case 'development':
		error_reporting(E_ALL | E_STRICT);
		break;
	case 'testing':
		error_reporting(E_ALL & ~E_DEPRECATED);
		break;
	case 'pruduction':
		error_reporting(0);
		break;
	default:
		error_reporting(0);
		show_error('bad SYS_MODE');
}

//开启 session ，全局无须再次开启 session
session_start();

//核心函数、控制器、模型
require APP_PATH . '/core/func.php';
require APP_PATH . '/core/controller.php';

if (IS_DB_ACTIVE) {
	require APP_PATH . '/core/db.php';
	require APP_PATH . '/core/model.php';	
}

//调用函数的时候可能会用到
set_conf('rewrite_rules', $rewrite_rules);

//路由开始，这里也可以配置成为分段路由模式
$query_string = '';
$query_string = $_SERVER['QUERY_STRING'];

//分段，从零开始，如$segments = get_conf('segments');$segments['0'] 可以取得第一个参数
set_conf('segments', explode('/', $query_string));
define('QUERY_STRING', $query_string);

//如果启用 path 网址
if (IS_PATH_URL) {
	//如果符合匹配规则
	if (key_exists($query_string, $rewrite_rules)) {
		$url = $rewrite_rules[$query_string];
		$url = p2q($url);
		extract($url);
	//如果还是传统url类型的
	} else if (false !== strpos($query_string, 'c=')) {
		// $c controller
		$c = null != v('c') ? v('c') : CONTROLLER;
		// $a action
		$a = null != v('a') ? v('a') : ACTION;
	} else {
		$url = p2q($query_string);
		extract($url);
	}
	unset($url);
} else {
	//虽然没有声明为path url，默认仍然支持
	if (false === strpos($query_string, 'c=')) {
		$url = p2q($query_string);
		extract($url);
		unset($url);
	} else {
		// $c controller
		$c = null != v('c') ? v('c') : CONTROLLER;
		// $a action
		$a = null != v('a') ? v('a') : ACTION;
	}
}

//filter '..'
$c = str_replace('..', '', $c);
$a = str_replace('..', '', $a);

//加载控制器和方法
if (file_exists(APP_PATH . '/controller/' . strtolower($c) . '.php')) {

	require APP_PATH . '/controller/' . strtolower($c) . '.php';
	
	//支持多层次目录,先放到一个数组中，取最后一个为控制器
	$c_array = explode('/', $c);
	$c = array_pop($c_array);
	if (!class_exists($c)) show_404('Unkonw file!' . $c);
	if (!method_exists($c, $a)) show_404('Unkown file!' . $c . '/' . $a);
} else {
	show_404('Page not found!');
}

//设置当前控制器和方法名
define('CUR_CONTROLLER', $c);
define('CUR_ACTION', $a);

//实例化类
$c = ucfirst(strtolower($c));
$c = new $c;

//实例化的时候重执行数据库操作
$c->$a();

//关闭 session
//$_SESSION = array();
//session_destroy();

//end