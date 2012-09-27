<?php
/**
 * 常用函数
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

/*------------------------ 通用函数，可以拿出去单独使用或稍作修改 --------------------------*/

/*-- array --*/
/**
 * 一维数组按索引相减
 * @param  array $to    被减的数组
 * @param  array $param 减数
 * @return array        处理后的结果
 */
function array_minus($to, $param) {
	foreach ($param as $key => $value) {
		if (array_key_exists($key, $to)) {
			unset($to[$key]);
		}
	}
	return $to;
}

/*-- cookie --*/
function set_cookie($name, $value = null, $expire = null, $path = '/', $domain = null, $secure = null, $httponly = null) {
	return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}

function get_cookie($name, $default = null) {
	return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
}

function clear_cookie($name = null, $path = '/') {
	if ($name && isset($_COOKIE[$name])) {
		return setcookie($name, null, 0, $path);
	}
	if (null === $name) {
		foreach ($_COOKIE as $key => $val) {
			return setcookie($key, null, 0, $path);
		}
	}
}

/*-- deal with data io --*/
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
 * 模拟简单的 post 请求
 * @param  string $url
 * @param  string $data 类型和 QUERY_STRING 相同，即由 & 连接的字符串 a=1&b=2
 * @param  array  $header header数组
 * @return all    返回服务器返回的结果
 */
function post_data($url, $data = array(), $header = array()) {
	$ch = curl_init($url);
	ob_start();
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_exec($ch);
	$output = ob_get_contents();
	ob_clean();
	curl_close($ch);
	return $output;
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
 */
function get_ip() {
    if (isset($_SERVER["HTTP_X_REAL_IP"])) {
        $ip = $_SERVER["HTTP_X_REAL_IP"];
	} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	} else if (isset($_SERVER["REMOTE_ADDR"])) {
		$ip = $_SERVER["REMOTE_ADDR"];
	} else {
		$ip = "0.0.0.0";
	}
	return $ip;
}

/**
 * 判断 ip 是否合法（仅限于IPV4）
 * @param string $ip
 */
function is_valid_ip($ip) {
	$preg       = '/^(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])\.(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])\.(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])\.(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])$/';
	$is_matched = false;
	if (preg_match($preg, $ip, $m)) {
		$is_matched = true;
	}
	return $is_matched;
}

/*-- url --*/
/**
 * 删除 url 中某个参数
 * @param  string $url        匹配url
 * @param  string $name       参数名
 * @param  string $seperation 参数分隔符
 * @return string             删除参数之后的结果
 */
function query_string_delete($url, $name) {
	$seperation = '&';
	$url = preg_replace("/[?|&]$name=[^&]*/", '', $url);

	//找出第一个&，第一个?的位置，若?不存在（或者问号位于后面），将第一个&替换成?
	$str_and = strpos($url, $seperation);
	$str_q = strpos($url, '?');
	if (false === $str_q || $str_q > $str_and) {
		$url = preg_replace("/(^[^$seperation]*)&(.*)$/", '${1}?${2}', $url);
	}
	return $url;
}

/**
 * 替换 url 中的参数
 * @param  string $name        参数名
 * @param  string $replacement 参数值的替换结果
 * @param  string $seperation  参数分隔符
 * @return string              替换参数值之后的结果
 */
function query_string_replace($url, $name, $replacement) {
	$seperation = '&';
	$url = preg_replace("/([?|&]$name=)[^&]*/", '${1}'.$replacement, $url);
	return $url;
}

/*-- string --*/
/**
 * 将字符串转码
 * 
 * 建议编码格式尽量统一为 utf-8
 * 若引用外部文件造成编码格式未知可使用该函数
 * @param string $str
 * @param string $out_charset
 * @return string
 */
function convert_str($str, $out_charset = null) {
	$in_charset = strtolower(mb_detect_encoding($str, array('utf-8', 'gbk', 'gb2312')));
	echo $in_charset;
	! defined('CHARSET') && define('CHARSET', 'utf-8');
	! strtolower($out_charset) && $out_charset = strtolower(CHARSET);
	
	if ($in_charset != $out_charset) {
		'cp936' == $in_charset && $in_charset = 'gbk';
		$str = iconv($in_charset, $out_charset, $str);
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
 * json 编码
 * 
 * 解决中文经过 json_encode() 处理后显示不直观的情况
 * 如默认会将“中文”变成"\u4e2d\u6587"，不直观
 * json_encode() 的参数编码格式为 UTF-8 时方可正常工作
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
