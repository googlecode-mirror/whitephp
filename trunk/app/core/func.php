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
