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

/*------------------------ 配置函数 ----------------------------*/

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
			return ;
		}
		
		$error = date('Y-m-d H:i:s');
		$error .= ' ' . $message;
		$error = trim($error) . "\r\n";
		
		//error_log 函数需要启用完整路径
		//其实这个完全可以直接 a+ 方式写文件，没必要非得用 error_log 函数
		$log_path = SYS_PATH . '/' . APP_PATH . '/' . LOG_PATH . '/' . date('Y-m') . '.txt';
		
		if (function_exists('error_log') ) {
			if (!is_writeable(SYS_PATH . '/' . APP_PATH . '/' . LOG_PATH . '/')) {
				echo "\n", 'error log permition denied!';die;
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
		render('404', array('message'=>$message));
	}
	exit;
}

/*------------------------ 其它函数 ----------------------------*/

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

//根据 ca 加载控制器
function _load_ca($ca) {
	
	$spilt = explode('/', $ca);
	$count = count($spilt);
	$n = 1;//控制器位于第几层，从零开始。后面方法用n时，只需传递n即可获取方法所在索引。
	$con = $spilt[0];
	$c = CONTROLLER;
	$a = ACTION;
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
	return array('c' => $c, 'a' => $a, 'n'=>$n);
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
	
	$ca = trim($ca, '/');
	$rewrite_rules = get_conf('rewrite_rules');
	$a = $c = $spilt = $extra = '';
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
	$url_query = "c={$c}&a={$a}".$extra;
	return array('c'=>$c, 'a'=>$a, 'segment'=>$spilt, 'url_query'=>$url_query);
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
	$href = '';
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
		$href = get_server_root() . $query_string;
	}
// 	return $query_string;
	return $href;
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
 * 渲染页面
 * @param unknown_type $file
 * @param unknown_type $data
 */
function render($file, $data = array()) {
	global $theme_package;
	if (!file_exists(APP_PATH . '/view/' . $theme_package . '/' . rtrim($file, '.php') . '.php')) {
		show_404('文件 ' . $file . ' 不存在！');
	} else {
		extract($data);
		unset($data);
		require APP_PATH . '/view/' . $theme_package . '/' . rtrim($file, '.php') . '.php';
	}
}

/**
 * 加载 model
 */
function load_model($file) {
	if (!file_exists(APP_PATH . '/model/' . rtrim($file, '.php') . '.php')) {
		show_404('模型文件 ' . $file . ' 不存在！');
	} else {
		require APP_PATH . '/model/' . rtrim($file, '.php') . '.php';
	}
}

/**
 * 加载类库 lib
 */
function load_lib($file) {
	if (!file_exists(APP_PATH . '/lib/' . rtrim($file, '.php') . '.php')) {
		show_404('类库文件 ' . $file . ' 不存在！');
	} else {
		require APP_PATH . '/lib/' . rtrim($file, '.php') . '.php';
	}
}

/**
 * 加载静态文件
 * @param unknown_type $file
 */
function load_static($file = 'jquery.js') {
	if (!file_exists(APP_PATH . '/static/' . $file)) {
		show_404('静态文件 ' . $file . ' 不存在！');
	} else {
		require APP_PATH . '/static/' . $file;
	}
}

/**
 * 将未知编码的字符串转换为期望的编码（配置文件中设置的编码）
 * @param string $str
 * @param string $toEncoding
 * @return string
 */
function convert_str($str, $toEncoding = null) {
	//加此字符集列表数组，解决误将 改变 2312 识别为 utf-8 的情况
	$charset_list = array('ascii', 'gb2312', 'gbk', 'utf-8');
	$strEncoding = mb_detect_encoding($str, $charset_list);
	//如果没有提供要转码的类型，使用系统设置的编码
	if (!$toEncoding) $toEncoding = CHARSET;

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
 * 创建验证码
 * 
 * 
 * @param $array 一个数组
 * 
 * <code>
 * $array = array(
 * 		'width' => 200,					//验证码宽度
 * 		'height' => 40,					//验证码高度
 * 		'num' => 4,						//验证码数量
 * 		'verify_code' => 'verify_code', //验证码索引名
 * 		'ads' => array(),				//增加的额外验证码，一维数组
 * 		'teight' => 1,					//紧凑度
 * 		'angle' => 20,					//偏向角度
 * 		'fontfile' => 'carbon.ttf',		//字体文件，请放置于 static 文件夹内，默认不支持中文，如有需要请添加中文字体
 * 		'bgred'=>233,
 * 		'bggreen'=>233,
 * 		'bgblue'=>233,	//字体背景色
 * 		'fred'=>233,
 * 		'fgreen'=>233,
 * 		'fblue'=>233,	//字体前景色
 * );
 * </code>
 * 
 * @return string  向数组 $_SESSION[$verify_code] 写入一个字符串
 */
function verify_code($array = array()) {
	$width = 100;
	$height = 30;
	$num = 5;
	$verify_code = 'verify_code';
	$ads = array();
	$teight = 1;
	$angle = 20;
	$fontfile = 'carbon.ttf';
	
	//背景色
	$bgred = 244;
	$bggreen = 244;
	$bgblue = 244;

	//前景色
	$fred = 233;
	$fgreen = 233;
	$fblue = 233;
	
	//解压传入的数组参数
	extract($array);
	
	$ret = '';		//结果

	//初始坐标
	$x = $height*3/7;
	$y = $height*5/7;
	
	if (!function_exists('imagecreate')) show_error('you must enable gd2 extension to use image functions');

	$handle = imagecreate($width, $height);
	$bgcolor = imagecolorallocate($handle, $bgred, $bggreen, $bgblue);
	
	$words = 'abcdefghijklmnopqrstuvwxyz';
	$wordsUpper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$numbers = '0123456789';
	$array = array();
	$array = str_split($words . $wordsUpper . $numbers);
// 	$ads = array('白纸', '框架', '你');
	$array = array_merge($array, $ads);

	for ($i = 0; $i < $num; $i++) {

		$font = rand($y*2/3, $y*4/5);

		$string = array_rand($array);
		$string = $array[$string];

		$red = rand(10, $fred);
		$green = rand(10, $fgreen);
		$blue = rand(10, $fblue);

		$color = imagecolorallocate($handle, $red, $green, $blue);
		$angle_real = rand(-$angle, $angle);
		
		$font_file = APP_PATH . '/static/' . $fontfile;
		
		file_exists($font_file) or show_error('字体文件不存在，请检查 ' . $font_file);

		//注意全部以入口文件为参考，除非直接引入文件
		imagettftext($handle, $font, $angle_real, $x, $y, $color, $font_file, $string);
		
		$x = $x + $font * real_strlen($string) * $teight;
		
		$ret .= $string;
	}

  //session_start(); //在核心文件已经开启
	$_SESSION[$verify_code] = $ret;
	header("Content-type: image/png");
	imagepng($handle);
	imagedestroy($handle);
}

/**
 * 直接生成可视化的验证码和可点链接
 * 
 * @param $array 一个数组
 * 
 * <code>
 * $array = array(
 * 		'width' => 200,					//验证码宽度
 * 		'height' => 40,					//验证码高度
 * 		'num' => 4,						//验证码数量
 * 		'verify_code' => 'verify_code', //验证码索引名
 * 		'ads' => array(),				//增加的额外验证码，一维数组
 * 		'teight' => 1,					//紧凑度
 * 		'angle' => 20,					//偏向角度
 * 		'fontfile' => 'carbon.ttf',		//字体文件，请放置于 static 文件夹内，默认不支持中文，如有需要请添加中文字体
 * 		'bgred'=>233,
 * 		'bggreen'=>233,
 * 		'bgblue'=>233,	//字体背景色
 * 		'fred'=>233,
 * 		'fgreen'=>233,
 * 		'fblue'=>233,	//字体前景色
 * );
 * </code>
 * 
 * @param  string  $prompt      提示信息
 * @param bool $show_prompt     是否显示提示文字
 * @return [type]               [description]
 */
function echo_code($array = array(), $prompt='看不清？点击重新获取', $show_prompt = false) {
	//session_start(); //在核心文件已经开启

	//外部访问验证码的接口（控制器方法对）
	$verify = 'syscommon/verify_code';

	if (IS_PATH_URL) {
		echo '<img id="code" src="' . href($verify).'" onclick="this.src=\'' . href($verify) . '\'+ \'/\' + Math.random();" style="cursor:pointer" title=' . $prompt . '>';
		if ($show_prompt) {
			echo '<a href="javascript:void(0);" onclick="document.getElementById(\'code\').src=\'' . href($verify) . '\'+ \'/\' + Math.random();">' . $prompt . '</a>';
		}

	} else {
		echo '<img id="code" src="'.href($verify).'" onclick="this.src=\''.href($verify).'&r=\' + Math.random()" style="cursor:pointer" title=' . $prompt . '>';
		if ($show_prompt) {
			echo '<a href="javascript:void(0);" onclick="document.getElementById(\'code\').src=\''.href($verify).'&r=\'+Math.random();">' . $prompt . '</a>';			
		}
	}
// 	if (isset($_SESSION['verify_code'])) {
// 		echo '<br />Pre Code: ', $_SESSION['verify_code'];
// 	}
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
 * 处理  json_encode() 不支持中文的情况
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
					$data[$k] = ch_urlencode($v);//递归调用该函数
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
 * 书写mysql语句时的变量检查函数
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
	$search = array (
			"\\",
			"\0",
			"\n",
			"\r",
			"\x1a",
			"'",
			'"' 
	);
	$replace = array (
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
	$ip_arr = explode('.',$ip);
	$iplong = (16777216 * intval($ip_arr[0])) + (65536 * intval($ip_arr[1])) + (256 * intval($ip_arr[2])) + intval($ip_arr[3]);
	return $iplong;
}

/**
 * 获取客户端 IP
 */
function get_ip() {
	if (! empty ( $_SERVER ['HTTP_CLIENT_IP'] )) {
		$ip = $_SERVER ['HTTP_CLIENT_IP'];
	} elseif (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) 
	// to check ip is pass from proxy
	{
		$ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER ['REMOTE_ADDR'];
	}
	return $ip;
}
