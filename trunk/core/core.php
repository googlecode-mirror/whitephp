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
 * update date: 2012-10-8 更新 query_string 获取方式，配合 PATH_INFO
 * update date: 2012-10-12 增加输出控制
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

ob_start();

//版本号
define('VERSION', '0.4.4');

//加载主配置文件
require APP_NAME . 'config/main.php';

//设置时区，编码等信息
header('Content-Type:text/html; charset=' . CHARSET);

//工作环境
switch (SYS_MODE) {
	case 'development':
		ini_set('display_errors', 'On');
		error_reporting(E_ALL | E_STRICT);
		break;
	case 'testing':
		ini_set('display_errors', 'On');
		error_reporting(E_ALL & ~E_NOTICE | E_STRICT);
		break;
	case 'production':
		ini_set('display_errors', 'Off');
		error_reporting(E_ALL & ~E_NOTICE);
		break;
	default:
		ini_set('display_errors', 'Off');
		error_reporting(0);
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

$query_string = $_SERVER['QUERY_STRING'];

//如需要做伪静态，除了服务器配置，也可以交给 wphp_custom_change_query_string 处理，可能用到 $_SERVER['PATH_INFO']
function_exists('wphp_custom_change_query_string') && $query_string = wphp_custom_change_query_string($query_string);

set_conf('query_string', $query_string);
set_conf('theme_package', $theme_package);

// $c controller
$c = get_param(PARAM_CONTROLLER) ? get_param(PARAM_CONTROLLER) : DEFAULT_CONTROLLER;

// $a action
$a = get_param(PARAM_ACTION) ? get_param(PARAM_ACTION) : DEFAULT_ACTION;

//filter '..'
$c = str_replace('..', '', $c);
$a = str_replace('..', '', $a);

//设置当前控制器和方法名
define('CUR_CONTROLLER', $c);
define('CUR_ACTION', $a);

function_exists('wphp_custom_before_instance') && wphp_custom_before_instance();

//加载控制器和方法
$file = APP_NAME . 'controller/' . strtolower($c) . '.php';
if (file_exists($file)) {
	require $file;
	
	//支持多层次目录,先放到一个数组中，取最后一个为控制器
	$c_array = explode('/', $c);
	$c       = array_pop($c_array);
	if (!class_exists($c)) {
		function_exists('wphp_controller_unexists') ? wphp_controller_unexists($c) : show_error('not found');
		exit;
	}
	if (!method_exists($c, $a)) {
		function_exists('wphp_action_unexists') ? wphp_action_unexists($a) : show_error('not found');
		exit;
	}
} else {
	function_exists('wphp_page_unexists') ? wphp_page_unexists($file) : show_error('not found');
	exit;
}

$c = ucfirst(strtolower($c));
$c = new $c;

$c->$a();

function_exists('wphp_custom_after_instance') && wphp_custom_after_instance();

ob_end_flush();
//end