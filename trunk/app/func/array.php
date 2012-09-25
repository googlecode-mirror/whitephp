<?php
/**
 * 数组处理
 * 
 * filename:	array.php
 * charset:		UTF-8
 * create date: 2012-9-25
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

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