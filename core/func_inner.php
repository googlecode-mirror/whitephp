<?php defined('INDEX_PAGE') or die('no entrance'); ?>
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
 * update date: 2012-10-11 删除 load_model()、load_lib() 函数，改用自动加载
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
//为 p2q 函数准备数据
//仅用来将 /controller/action 转换成 $c=controller&a=action
function _load_ca($ca) {
	$ca = trim($ca, '/');
	$spilt = explode('/', $ca);
	$count = count($spilt);
	$n     = 1; //控制器位于第几层，从零开始。后面方法用n时，只需传递n即可获取方法所在索引。
	$con   = $spilt[0];
	$c     = DEFAULT_CONTROLLER;
	$a     = DEFAULT_ACTION;
	while (!file_exists(APP_NAME . 'controller/' . strtolower($con) . '.php')) {
		if ($n < $count) {
			$n++;
			
			$con = '';
			for ($i = 0; $i < $n; $i++) {
				$con .= $spilt[$i] . '/';
			}
			$con = trim($con, '/');
			
		} else {
			//如果不存在文件直接终止
			show_404($ca . ' error presreve pathurl');
			break;
		}
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
 * path2query
 * 
 * 路径转url函数
 * 为了 href 函数做准备
 * 将 /controller/action 转换成 $c=controller&a=action
 * 不涉及参数
 * 
 * 不对用户使用，不可传参
 *
 * 将控制器字符串转换成网址
 * @param unknown_type $string
 * @return multitype:Ambigous <string, unknown> Ambigous <string, mixed> Ambigous <string, multitype:>
 */
function p2q($ca = null) {
	$ca = ltrim($ca, '/');
	$a = $c = $extra = '';
	if (!$ca) {
		$c = DEFAULT_CONTROLLER;
		$a = DEFAULT_ACTION;
	} else {
		
		$spilt = explode('/', $ca);
		$count = count($spilt);
		
		if ($count == 1) {
			$c = $ca;
		} else {
			//处理不在控制器不在根目录的情况
			$ret_tmp = _load_ca($ca);
			extract($ret_tmp);
			for ($i = 1; $i < $n + 2; $i++) {
				array_shift($spilt);
			}
		}
	}
	//$url_query = "c={$c}&a={$a}" . $extra;
	$url_query = PARAM_CONTROLLER . '=' . $c . '&' . PARAM_ACTION . '=' . $a;

	return array(
		PARAM_CONTROLLER => $c,
		PARAM_ACTION => $a,
		//'segment' => $spilt,
		'url_query' => $url_query
	);
}


if (!function_exists('href')) {

	/**
	 * 建立完整超链接，对用户开放
	 * $ca 为 控制器方法，中间使用 / 分隔
	 * $extra 为其它 url 参数
	 * 
	 * @param string $ca controller/action
	 * @param array $extra array('param1' => 'value1')
	 * @return string $href http://xxx/xxx.php?c=controller&a=action&param1=value1
	 */
	function href($ca, $extra = array()) {
		$ca = ltrim($ca, '/');
		$href         = '';
		$query_string = '';

		$tmp = p2q($ca);
		$query_string .= $tmp['url_query'];
		foreach ($extra as $k => $v) {
			$query_string .= "&{$k}={$v}";
		}
			
		if (IS_HIDE_INDEX_PAGE) {
			$href = get_server_root() . '?' . $query_string;
		} else {
			$href = get_server_root() . INDEX_PAGE . '?' . $query_string;
		}

		return $href;
	}
	
}


if (!function_exists('make_href')) {

	/**
	 * 生成 url 链接
	 * 
	 * 此处使用的参数是常规参数 
	 * @param string $query_string c=hello&a=index&t=1
	 * @param unknown_type $server
	 * @return string xxx.com/?c=hello&a=index&t=1
	 */
	function make_href($query_string, $server = null) {
		$server or $server = get_server_root();
		if (IS_HIDE_INDEX_PAGE) {
			$href = $server . '?' . $query_string;
		} else {
			$href = $server . INDEX_PAGE . '?' . $query_string;
		}
		return $href;
	}

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
			//echo '未启用错误日志，错误信息为：' . $message;
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
	$theme_package = get_conf('theme_package');
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
function render($_wphp_render_param_file, $_wphp_render_param_data = array()) {
	$_wphp_render_realfile = APP_NAME . 'view/' . get_conf('theme_package') . '/' . $_wphp_render_param_file;
	$_wphp_render_lastchar = substr($_wphp_render_param_file, -5, 5);
	if (false === strpos($_wphp_render_lastchar, '.')) {
		$_wphp_render_realfile = $_wphp_render_realfile . '.php';
	}

	if (!file_exists($_wphp_render_realfile)) {
		show_404('view file ' . $_wphp_render_param_file . ' unexists!');
	} else {
		extract($_wphp_render_param_data);
		unset($_wphp_render_lastchar, $_wphp_render_param_file, $_wphp_render_param_data);
		require $_wphp_render_realfile;
	}

}

/**
 * 跳转函数
 * @param string $ca 控制器和方法 如 hello/test
 * @param string $code
 */
function r($ca, $extra = array(), $code = 302) {
	if (FALSE !== strpos($ca, 'http://') || FALSE !== strpos($ca, 'https://')) {
		header('Location: ' . $ca, TRUE, $code);
	}
	
	header('Location: ' . href($ca, $extra), TRUE, $code);
}

/**
 * 加载静态文件
 * 若是 css 或者 js 生成完整的代码
 * 否则返回文件名
 * @param unknown_type $file
 */
function load_static($file = 'jquery.js') {
	$realfile = APP_NAME . 'static/' . $file;

	if (!file_exists($realfile)) {
		show_404('static file ' . $file . ' unexists');
	} else {
		if (strtolower(substr($file, -3, 3)) == '.js') {
			return "<script src=\"$realfile\"></script>\r\n";
		} else if (strtolower(substr($file, -4, 4)) == '.css') {
			return "<link rel=\"stylesheet\" href=\"$realfile\" />\r\n";
		} else {
			return $realfile;
		}
	}
}

set_conf('autoload_config', $autoload_config);
/**
 * 自动加载类
 * @param unknown_type $class
 */
function __autoload($class) {
	$autoload_config = get_conf('autoload_config');
	$flag = false;
	foreach ($autoload_config as $autoload) {
		$file = $autoload['path'] . strtolower($class) . $autoload['ext'];

		if (is_file($file)) {
			require $file;
			$flag = true;
			break;
		}
	}
	$flag or show_error("class {$class} not found");
}

