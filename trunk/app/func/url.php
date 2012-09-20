<?php
/**
 * URL 处理函数集合
 * 
 * 处理 URL 中 QUERY_STRING 部分
 * 
 * filename:	url.php
 * charset:		UTF-8
 * create date: 2012-9-21
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

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