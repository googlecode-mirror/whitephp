<?php
/**
 * WPHP 框架的核心
 * note:不建议开启 pathurl
 * 
 * filename:	core.php
 * charset:		UTF-8
 * create date: 2012-5-25
 * update date: 2012-9-26 代码重构，将 core 从应用层分离出来
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */
//start

//版本号
define('VERSION', '0.4');

//加载主配置文件
require APP_PATH . 'config/main.php';

//设置时区，编码等信息
header('Content-Type:text/html; charset=' . CHARSET);
date_default_timezone_set(TIME_ZONE);

//工作环境
switch (SYS_MODE) {
	case 'development':
		error_reporting(E_ALL | E_STRICT);
		break;
	case 'testing':
		error_reporting(E_ALL & ~E_DEPRECATED);
		break;
	case 'production':
		error_reporting(0);
		break;
	default:
		error_reporting(0);
		show_error('bad SYS_MODE');
}

//关闭默认开启session
//session_start();

//核心函数、控制器、模型
require CORE_PATH . 'func_inner.php';
require CORE_PATH . 'func.php';
require CORE_PATH . 'controller.php';

if (IS_DB_ACTIVE) {
	require CORE_PATH . 'db.php';
	require CORE_PATH . 'model.php';
}

//调用函数的时候可能会用到
set_conf('rewrite_rules', $rewrite_rules);
set_conf('theme_package', $theme_package);

//路由开始，这里也可以配置成为分段路由模式
$query_string = '';
$query_string = $_SERVER['QUERY_STRING'];

//分段，从零开始，如$segments = get_conf('segments');$segments['0'] 可以取得第一个参数
set_conf('segments', explode('/', $query_string));
define('QUERY_STRING', $query_string);

//如果启用 path 网址

if (IS_PATH_URL) {

	//这一段用来设置类似 xxx.com/about 的跳转
	$query_string = ltrim($query_string, '/');
	if (key_exists($query_string, $rewrite_rules)) {
		$url = $rewrite_rules[$query_string];
		$url = p2q($url);
		extract($url);
		unset($url);
	//如果还是传统url类型的
	} else if (false !== strpos($query_string, 'c=') || false !== strpos($query_string, 'a=')) {
		// $c controller
		$c = get('c') ? get('c') : CONTROLLER;

		// $a action
		$a = get('a') ? get('a') : ACTION;
	} else {
		$url = p2q($query_string);
		extract($url);
		unset($url);
	}
	
} else {
	//这一段用来设置类似 xxx.com/about 的跳转
	$query_string = ltrim($query_string, '/');
	if (key_exists($query_string, $rewrite_rules)) {
		$url = $rewrite_rules[$query_string];
		$url = p2q($url);
		extract($url);
		unset($url);
	} else {
		
		// $c controller
		$c = get('c') ? get('c') : CONTROLLER;

		// $a action
		$a = get('a') ? get('a') : ACTION;
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
	$c       = array_pop($c_array);
	if (!class_exists($c)) {
		show_error('controller unexists');
		exit;
	}
	if (!method_exists($c, $a)) {
		show_error('action unexists');
		exit;
	}
} else {
	show_404('page not found!');
}

//设置当前控制器和方法名
define('CUR_CONTROLLER', $c);
define('CUR_ACTION', $a);

//实例化类，类名暂不允许下划线分隔
$c = ucfirst(strtolower($c));
$c = new $c;

$c->$a();

//关闭 session
//$_SESSION = array();
//session_destroy();

//end