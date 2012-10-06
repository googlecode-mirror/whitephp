<?php defined('INDEX_PAGE') or die('no entrance'); ?>
<?php
/**
 * WPHP 框架的核心
 * 
 * filename:	core.php
 * charset:		UTF-8
 * create date: 2012-5-25
 * update date: 2012-9-26 代码重构，将 core 从应用层分离出来
 * update date: 2012-10-6 删除 pathurl 模式
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

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
		ini_set('display_errors', 'On');
		error_reporting(E_ALL | E_STRICT);
		break;
	case 'testing':
		error_reporting(E_ALL & ~E_NOTICE | E_STRICT);
		ini_set('display_errors', 'On');
		break;
	case 'production':
		error_reporting(E_ALL & ~E_NOTICE);
		ini_set('display_errors', 'Off');
		break;
	default:
		error_reporting(0);
		ini_set('display_errors', 'On');
		show_error('bad SYS_MODE');
}

//核心函数、控制器、模型
require CORE_NAME . 'func_inner.php';
require CORE_NAME . 'func.php';
require CORE_NAME . 'controller.php';

if (IS_DB_ACTIVE) {
	require CORE_NAME . 'db.php';
	require CORE_NAME . 'model.php';
}

//取消使用 $_SERVER['QUERY_STRING']
//改用 $_SERVER['REQUEST_URI'] - $_SERVER['SCRIPT_NAME']
//目前可以再未添加问号的情况下获取到 $_SERVER['QUERY_STRING'] 的值
//即使不设置服务器，默认也可以获取等值
//即 index.php/a=3 和 index.php?a=3 在 $query_string 里是相同的，都是 a=3

if (IS_HIDE_INDEX_PAGE) {
	$_tmp_script_name = dirname($_SERVER['SCRIPT_NAME']);
} else {
	$_tmp_script_name = $_SERVER['SCRIPT_NAME'];
}

$query_string = str_replace($_tmp_script_name, '', $_SERVER['REQUEST_URI']);
$query_string = ltrim($query_string, '?/');

//若 / 访问
if (strlen($_SERVER['REQUEST_URI']) < strlen($_tmp_script_name)) {
	$query_string = '';
}

function_exists('wphp_hook_change_query_string') && $query_string = wphp_hook_change_query_string($query_string);

set_conf('query_string', $query_string);
set_conf('theme_package', $theme_package);

// $c controller
$c = get_param('c') ? get_param('c') : CONTROLLER;

// $a action
$a = get_param('a') ? get_param('a') : ACTION;

//filter '..'
$c = str_replace('..', '', $c);
$a = str_replace('..', '', $a);

//设置当前控制器和方法名
define('CUR_CONTROLLER', $c);
define('CUR_ACTION', $a);

function_exists('wphp_hook_before_instance') && wphp_hook_before_instance();

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
	show_404('page not found');
}

//实例化类，类名暂不允许下划线分隔
$c = ucfirst(strtolower($c));
$c = new $c;

$c->$a();

function_exists('wphp_hook_after_instance') && wphp_hook_after_instance();

//end