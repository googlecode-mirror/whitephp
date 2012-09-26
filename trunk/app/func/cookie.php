<?php
/**
 * COOKIE 操作函数
 *
 * filename:	cookie.php
 * charset:		UTF-8
 * create date: 2012-9-25
 *
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

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