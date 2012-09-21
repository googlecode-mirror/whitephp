<?php
/**
 * 核心函数
 * 常量私有前缀 WPHP_
 * 函数私有前缀 wphp_
 * 变量私有前缀 wphp_
 * 
 * filename:	func.php
 * charset:		UTF-8
 * create date: 2012-5-25
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
	while (!file_exists(APP_PATH . '/controller/' . strtolower($con) . '.php')) {
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
	if (file_exists(APP_PATH . '/controller/' . strtolower($con) . '.php')) {
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
		$log_path = SYS_PATH . '/' . APP_PATH . '/' . LOG_PATH . '/' . date('Y-m') . '.txt';
		
		if (function_exists('error_log')) {
			if (!is_writeable(SYS_PATH . '/' . APP_PATH . '/' . LOG_PATH . '/')) {
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
	if (!file_exists(APP_PATH . '/view/' . $theme_package . '/404.php')) {
		require APP_PATH . '/error/404.php';
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

	$realfile = APP_PATH . '/view/' . $theme_package . '/' . $file;
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
	$realfile = APP_PATH . '/model/' . $file;
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
	$realfile = APP_PATH . '/lib/' . $file;
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
	$realfile = APP_PATH . '/static/' . $file;

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

/*------------------------ 通用函数，可以拿出去单独使用或稍作修改 --------------------------*/

/**
 * 获取 post 或者 get 的值
 * @param string $k
 * @param string $default 默认返回值
 * @return Ambigous <NULL, unknown>
 */
function v($k, $defalut = '') {
	return isset($_REQUEST[$k]) ? $_REQUEST[$k] : $defalut;
}

/**
 * 获取 get 的值
 * @param string $k
 * @param string $default 默认返回值
 * @return Ambigous <NULL, unknown>
 */
function get($k, $defalut = '') {
	return isset($_GET[$k]) ? $_GET[$k] : $defalut;
}

/**
 * 获取 post 的值
 * @param string $k
 * @param string $default 默认返回值
 * @return Ambigous <NULL, unknown>
 */
function post($k, $defalut = '') {
	return isset($_POST[$k]) ? $_POST[$k] : $defalut;
}

/**
 * 将未知编码的字符串转换为期望的编码（配置文件中设置的编码）
 * 
 * 不建议使用，尽量统一编码，明确要转码的字符原来的编码
 * @deprecated
 * @param string $str
 * @param string $toEncoding
 * @return string
 */
function convert_str($str, $toEncoding = null) {
	//加此字符集列表数组，解决误将 改变 2312 识别为 utf-8 的情况
	$charset_list = array(
		'ascii',
		'gb2312',
		'gbk',
		'utf-8'
	);
	$strEncoding  = mb_detect_encoding($str, $charset_list);
	//如果没有提供要转码的类型，使用系统设置的编码
	if (!$toEncoding)
		$toEncoding = CHARSET;
	
	if (strtolower($strEncoding) != strtolower($toEncoding)) {
		$str = iconv($strEncoding, $toEncoding, $str);
	}
	return $str;
}

/**
 * 查看字符长度
 * @param unknown_type $str
 */
function real_strlen($str) {
	$charset = mb_detect_encoding($str);
	return mb_strlen($str, $charset);
}

/**
 * 获得服务器端网址，即获取当前网址
 * @param boolean $with_query_string 是否附带 query_string 部分
 * @return Ambigous <string, unknown>
 */
function get_server_url($with_query_string = true) {
	$url = 'http://localhost';
	
	if (isset($_SERVER['HTTP_HOST'])) {
		$url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
		$url .= '://' . $_SERVER['HTTP_HOST'];
		if ($with_query_string) {
			$url .= $_SERVER['REQUEST_URI'];
		} else {
			$url .= $_SERVER['SCRIPT_NAME'];
		}
	}
	return $url;
}

/**
 * 获取网址跟目录
 * @return string
 */
function get_server_root() {
	$url = 'http://localhost';
	
	if (isset($_SERVER['HTTP_HOST'])) {
		$url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
		$url .= '://' . $_SERVER['HTTP_HOST'];
		$url .= dirname($_SERVER['SCRIPT_NAME']);
	}
	
	$url = rtrim($url, '/') . '/';
	return $url;
}

/**
 * 获取客户端 IP
 * 搜集自网络，原作者未知
 */
function get_ip() {
    if (isset($_SERVER["HTTP_X_REAL_IP"]))
        $ip = $_SERVER["HTTP_X_REAL_IP"];
    else if (isset($_SERVER["HTTP_CLIENT_IP"]))
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    else if (isset($_SERVER["REMOTE_ADDR"]))
        $ip = $_SERVER["REMOTE_ADDR"];
    else if (getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
		$ip = "0.0.0.0";
	return $ip;
}

/**
 * 判断 ip 是否合法
 * @param unknown_type $ip
 */
function is_valid_ip($ip) {
	$preg       = '/^(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])\.(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])\.(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])\.(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])$/';
	$is_matched = false;
	if (preg_match($preg, $ip, $m)) {
		$is_matched = true;
	}
	return $is_matched;
}

/**
 * json 编码
 * 
 * 解决 json_encode() 不支持中文的情况
 * 
 * @param array|object $data
 * @return array|object
 */
function ch_json_encode($data) {
	/**
	 * 将中文编码
	 * @param array $data
	 * @return string
	 */
	function ch_urlencode($data) {
		if (is_array($data) || is_object($data)) {
			foreach ($data as $k => $v) {
				if (is_scalar($v)) {
					if (is_array($data)) {
						$data[$k] = urlencode($v);
					} else if (is_object($data)) {
						$data->$k = urlencode($v);
					}
				} else if (is_array($data)) {
					$data[$k] = ch_urlencode($v); //递归调用该函数
				} else if (is_object($data)) {
					$data->$k = ch_urlencode($v);
				}
			}
		}
		return $data;
	}
	
	$ret = ch_urlencode($data);
	$ret = json_encode($ret);
	return urldecode($ret);
}

/**
 * 转义特殊字符
 * 
 * 书写mysql语句时的可先对变量进行过滤
 * 此函数会自动对字符串加引号
 * @param unknown_type $value
 * @return string
 */
function check_input($value) {
	if (get_magic_quotes_gpc()) {
		$value = stripslashes($value);
	}
	if (!is_numeric($value)) {
		$value = "'" . wphp_escape($value) . "'";
	}
	return $value;
}

/**
 * 转义函数，用来替代 mysql*_escape_* 函数
 * @param unknown_type $str
 */
function wphp_escape($str) {
	$search  = array(
		"\\",
		"\0",
		"\n",
		"\r",
		"\x1a",
		"'",
		'"'
	);
	$replace = array(
		"\\\\",
		"\\0",
		"\\n",
		"\\r",
		"\Z",
		"\'",
		'\"'
	);
	return str_replace($search, $replace, $str);
}

/**
 * Ip 地址转为数字地址
 *
 * php 的 ip2long 这个函数有问题
 * 133.205.0.0 ==>> 2244804608
 * @param string $ip 要转换的 ip 地址
 * @return int    转换完成的数字
 */
function wphp_ip2long($ip) {
	$ip_arr = explode('.', $ip);
	$iplong = (16777216 * intval($ip_arr[0])) + (65536 * intval($ip_arr[1])) + (256 * intval($ip_arr[2])) + intval($ip_arr[3]);
	return $iplong;
}

/**
 * 对字符串、对象、数组进行转码
 * 
 * 和 iconv 参数使用方式相同
 * @param string $in_charset
 * @param string $out_charset
 * @param array|string $data
 * @return string|array
 */
function wphp_iconv($in_charset, $out_charset, $data) {
	if (is_array($data) || is_object($data)) {
		foreach ($data as $k => $v) {
			if (is_scalar($v)) {
				if (is_array($data)) {
					$data[$k] = iconv($in_charset, $out_charset, $v);
				} else if (is_object($data)) {
					$data->$k = iconv($in_charset, $out_charset, $v);
				}
			} else if (is_array($data)) {
				$data[$k] = wphp_iconv($in_charset, $out_charset, $v);
			} else if (is_object($data)) {
				$data->$k = wphp_iconv($in_charset, $out_charset, $v);
			}
		}
	} else if (is_scalar($data)) {
		$data = iconv($in_charset, $out_charset, $data);
	}
	return $data;
}
