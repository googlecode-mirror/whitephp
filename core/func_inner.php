<?php
/**
 * 框架私有核心函数
 * 常量私有前缀 WPHP_
 * 函数私有前缀 wphp_
 * 变量私有前缀 wphp_
 * 
 * filename:	func.php
 * charset:		UTF-8
 * create date: 2012-9-24
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

/*------------------------ 配置函数（请求级别的） ----------------------------*/

//清空全局变量
$GLOBALS[WPHP_GLOBAL_CONFIG_NAME] = array();

/**
 * 添加配置项
 * @param unknown_type $key
 * @param unknown_type $value
 */
function set_conf($key = '', $value = '') {
	if (is_string($key)) {
		$GLOBALS[WPHP_GLOBAL_CONFIG_NAME][$key] = $value;
		return true;
	}
	return false;
}

/**
 * 以数组形式添加配置
 * @param unknown_type $data
 * @return boolean
 */
function set_array_conf($data = array()) {
	if (is_array($data)) {
		foreach ($data as $k => $v) {
			$GLOBALS[WPHP_GLOBAL_CONFIG_NAME][$k] = $v;
		}
		return true;
	}
	return false;
}

/**
 * 获取配置
 * @param unknown_type $key
 */
function get_conf($key = '') {
	return isset($GLOBALS[WPHP_GLOBAL_CONFIG_NAME][$key]) ? $GLOBALS[WPHP_GLOBAL_CONFIG_NAME][$key] : null;
}

/**
 * 获取数组形式配置
 * @param unknown_type $keys
 * @return multitype:NULL
 */
function get_array_conf($keys = array()) {
	$ret = array();
	if (is_array($keys)) {
		foreach ($keys as $k) {
			$ret[$k] = isset($GLOBALS[WPHP_GLOBAL_CONFIG_NAME][$k]) ? $GLOBALS[WPHP_GLOBAL_CONFIG_NAME][$k] : null;
		}
	}
	return $ret;
}

/**
 * 获取所有配置
 * @return unknown
 */
function get_all_conf() {
	return $GLOBALS[WPHP_GLOBAL_CONFIG_NAME];
}

/*------------------------ 框架内部函数 ----------------------------*/

//根据 ca 加载控制器
function _load_ca($ca) {
	$spilt = explode('/', $ca);
	$count = count($spilt);
	$n     = 1; //控制器位于第几层，从零开始。后面方法用n时，只需传递n即可获取方法所在索引。
	$con   = $spilt[0];
	$c     = CONTROLLER;
	$a     = ACTION;
	while (!file_exists(APP_NAME . 'controller/' . strtolower($con) . '.php')) {
		if ($n < $count) {
			$n++;
		} else {
			//如果不存在文件直接终止
			show_404();
			break;
		}
		$con = '';
		for ($i = 0; $i < $n; $i++) {
			$con .= $spilt[$i] . '/';
		}
		$con = trim($con, '/');
	}
	// 	echo $n;
	if (file_exists(APP_NAME . 'controller/' . strtolower($con) . '.php')) {
		$c = $con;
		if (isset($spilt[$n])) {
			$a = $spilt[$n];
		}
	}
	unset($spilt);
	unset($count);
	unset($con);
	return array(
		'c' => $c,
		'a' => $a,
		'n' => $n
	);
}

/**
 * path2query 路径转url查询函数
 *
 * @todo 如果有传递参数怎么办。。。
 *
 * 将控制器字符串转换成网址
 * @param unknown_type $string
 * @return multitype:Ambigous <string, unknown> Ambigous <string, mixed> Ambigous <string, multitype:>
 */
function p2q($ca = null) {
	$ca            = trim($ca, '/');
	$rewrite_rules = get_conf('rewrite_rules');
	$a             = $c = $spilt = $extra = '';
	if (!$ca) {
		$c = CONTROLLER;
		$a = ACTION;
	} else {
		//如果有跳转
		if (key_exists($ca, $rewrite_rules)) {
			$ca = $rewrite_rules[$ca];
		}
		
		$spilt = explode('/', $ca);
		$count = count($spilt);
		
		if ($count == 1) {
			$c = $ca;
			$a = ACTION;
		} else {
			//处理不在控制器不在根目录的情况
			$ret_tmp = _load_ca($ca);
			extract($ret_tmp);
			foreach ($spilt as $k => $v) {
				// 				$extra .= "&globalparam{$k}={$v}";
			}
		}
	}
	$url_query = "c={$c}&a={$a}" . $extra;
	return array(
		'c' => $c,
		'a' => $a,
		'segment' => $spilt,
		'url_query' => $url_query
	);
}

/**
 * 建立一个链接
 * @param string $ca 如 hello/index
 * @param array $extra 如 array('id'=5)
 */
function href($ca, $extra = array()) {
	$query_string = '';
	if (IS_PATH_URL) {
		$query_string .= $ca;
		if ($extra) {
			$query_string .= '/' . implode('/', $extra);
		}
	} else {
		$tmp = p2q($ca);
		$query_string .= $tmp['url_query'];
		foreach ($extra as $k => $v) {
			$query_string .= "&{$k}={$v}";
		}
	}
	
	if (!IS_HIDE_INDEX_PAGE) {
		$query_string = INDEX_PAGE . '?' . $query_string;
	}
	return $query_string;
}

/**
 * 建立带有协议的的包含完整路径超链接
 * @param unknown_type $ca
 * @param unknown_type $extra
 */
function hard_href($ca, $extra = array()) {
	$href         = '';
	$query_string = '';
	if (IS_PATH_URL) {
		$query_string .= $ca;
		if ($extra) {
			$query_string .= '/' . implode('/', $extra);
		}
		$href = get_server_root() . $query_string;
	} else {
		$tmp = p2q($ca);
		$query_string .= $tmp['url_query'];
		foreach ($extra as $k => $v) {
			$query_string .= "&{$k}={$v}";
		}
		$href = get_server_root() . $query_string;
	}
	
	if (!IS_HIDE_INDEX_PAGE) {
		$query_string = INDEX_PAGE . '?' . $query_string;
		$href         = get_server_root() . $query_string;
	}
	// 	return $query_string;
	return $href;
}

/*------------------------ 日志处理函数 ----------------------------*/

/**
 * 记录日志
 * 写日志用绝对路径
 * @param string $message
 */
function log_error($message = '') {
	//sae 环境未解决日志记录问题，不建议写到 storage里，可尝试sae_debug
	if (defined('SAE_APPNAME')) {
		_sae_log_error($message);
	} else {
		if (!IS_LOG) {
			echo '未启用错误日志，错误信息为：' . $message;
			return;
		}
		
		$error = date('Y-m-d H:i:s');
		$error .= ' ' . $message;
		$error = trim($error) . "\r\n";
		
		//error_log 函数需要启用完整路径
		//其实这个完全可以直接 a+ 方式写文件，没必要非得用 error_log 函数
		$log_path = SYS_PATH . '/' . APP_NAME . '' . LOG_PATH . '/' . date('Y-m') . '.txt';
		
		if (function_exists('error_log')) {
			if (!is_writeable(SYS_PATH . '/' . APP_NAME . '' . LOG_PATH . '/')) {
				echo "\n", 'error log permition denied!';
				die;
			}
			error_log($error, 3, $log_path);
		} else {
			_wphp_log_error($error, $log_path);
		}
	}
	
	return $message;
}

/**
 * 记录日志的同时，显示到页面上
 * @param string $message
 */
function show_error($message = '') {
	if (defined('SAE_APPNAME')) {
		_sae_show_error($message);
	} else {
		echo $message;
		log_error($message);
	}
	// 	exit;
}

/**
 * wphp 日志记录函数
 * 
 * 可在当系统禁用日志函数时使用，一般无需单独使用
 * 暂不兼容SAE环境，且sae官方建议使用 sae_debug 记录日志
 * @param unknown_type $mes
 * @param unknown_type $path
 */
function _wphp_log_error($message, $path) {
	$handle = fopen($path, 'a+');
	fputs($handle, $message);
	fclose($handle);
}

/**
 * 记录错误日志，SAE环境专用，一般无需单独使用
 * @param string $message
 */
function _sae_log_error($message = '') {
	$display_error = ini_get('display_error'); //获取当前错误显示状态
	//如果当前显示错误日志，则先改为不显示的，然后再将状态设置回去
	if ($display_error === 'on') {
		sae_set_display_errors(false);
		sae_debug($message);
		sae_set_display_errors(true);
	} else {
		sae_debug($message);
	}
}

/**
 * 记录错误日志并显示，SAE环境专用，一般无需单独使用
 * @param string $message
 */
function _sae_show_error($message = '') {
	$display_error = ini_get('display_error'); //获取当前错误显示状态
	if ($display_error === 'on') {
		sae_debug($message);
	} else {
		sae_set_display_errors(true);
		sae_debug($message);
		sae_set_display_errors(false);
	}
	// 	exit;
}

/**
 * 处理 404 页面
 * @param unknown_type $message
 */
function show_404($message = '') {
	global $theme_package;
	if (!file_exists(APP_NAME . 'view/' . $theme_package . '/404.php')) {
		require APP_NAME . 'error/404.php';
	} else {
		render('404', array(
			'message' => $message
		));
	}
	exit;
}

/**
 * 渲染页面
 * @param unknown_type $file
 * @param unknown_type $data
 */
function render($file, $data = array()) {
	global $theme_package;

	$realfile = APP_NAME . 'view/' . $theme_package . '/' . $file;
	$lastchar = substr($file, -5, 5);
	if (false === strpos($file, '.')) {
		$realfile = $realfile . '.php';
	}

	if (!file_exists($realfile)) {
		show_404('view file ' . $file . ' unexists!');
	} else {
		extract($data);
		//unset($data);
		require $realfile;
	}
}

/**
 * 跳转函数
 * @param string $ca 控制器和方法 如 hello/test
 * @param string $code
 */
function r($ca, $code = 302) {
	if (FALSE !== strpos($ca, 'http://') || FALSE !== strpos($ca, 'https://')) {
		header('Location: ' . $ca, TRUE, $code);
	}
	
	header('Location: ' . href($ca), TRUE, $code);
}

/**
 * 加载 model
 */
function load_model($file) {
	$realfile = APP_NAME . 'model/' . $file;
	$lastchar = substr($file, -5, 5);
	if (false === strpos($file, '.')) {
		$realfile = $realfile . '.php';
	}

	if (!file_exists($realfile)) {
		show_404('class model ' . $file . ' unexists');
	} else {
		require $realfile;
	}
}

/**
 * 加载类库 lib
 */
function load_lib($file) {
	$realfile = APP_NAME . 'lib/' . $file;
	$lastchar = substr($file, -5, 5);
	if (false === strpos($file, '.')) {
		$realfile = $realfile . '.php';
	}

	if (!file_exists($realfile)) {
		show_404('class ' . $file . ' unexists');
	} else {
		require $realfile;
	}
}

/**
 * 加载静态文件
 * @param unknown_type $file
 */
function load_static($file = 'jquery.js') {
	$realfile = APP_NAME . 'static/' . $file;

	if (!file_exists($realfile)) {
		show_404('static file ' . $file . ' unexists');
	} else {
		if (strtolower(substr($file, -3, 3)) == '.js') {
			echo "<script src=\"$realfile\"></script>\r\n";
		} else if (strtolower(substr($file, -4, 4)) == '.css') {
			echo "<link rel=\"stylesheet\" href=\"$realfile\" />\r\n";
		} else {
			require $realfile;
		}
	}
}