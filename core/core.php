<?php
/**
 * WPHP 框架的核心
 * note:不建议使用 pathurl 模式
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
require APP_NAME . 'config/main.php';

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
require CORE_NAME . 'func_inner.php';
require CORE_NAME . 'func.php';
require CORE_NAME . 'controller.php';

if (IS_DB_ACTIVE) {
	require CORE_NAME . 'db.php';
	require CORE_NAME . 'model.php';
}

//调用函数的时候可能会用到
set_conf('rewrite_rules', $rewrite_rules);
set_conf('theme_package', $theme_package);

//取消使用 $_SERVER['QUERY_STRING'],以免未添加问号的情况下不能使用
$_tmp_script_name = $_SERVER['SCRIPT_NAME'];

//如果启用 pathurl 链接路径发生改变
if (IS_HIDE_INDEX_PAGE) {
	$_tmp_script_name = dirname($_SERVER['SCRIPT_NAME']);
}

$query_string = str_replace($_tmp_script_name, '', $_SERVER['REQUEST_URI']);
$query_string = ltrim($query_string, '?/');

//若 / 访问
if (strlen($_SERVER['REQUEST_URI']) < strlen($_tmp_script_name)) {
	$query_string = '';
}

// echo $query_string;die;
// print_r($_SERVER);die;

//分段，从零开始，如$segments = get_conf('segments');$segments['0'] 可以取得第一个段
//该变量在 pathurl 启用的情况下使用
set_conf('segments', explode('/', $query_string));
define('QUERY_STRING', $query_string);

//如果启用 path 网址
if (IS_PATH_URL) {

	//执行 main.php 配置文件中的跳转规则，简单跳转，不涉及正则
	if (key_exists($query_string, $rewrite_rules)) {
		$url = $rewrite_rules[$query_string];
		$url = p2q($url);
		extract($url);
		unset($url);
	} else {
		$url = p2q($query_string);
		extract($url);
		unset($url);
	}
	
} else {
	//执行 main.php 配置文件中的跳转规则，简单跳转，不涉及正则
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
if (file_exists(APP_NAME . 'controller/' . strtolower($c) . '.php')) {
	require APP_NAME . 'controller/' . strtolower($c) . '.php';
	
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