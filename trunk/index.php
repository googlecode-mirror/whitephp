<?php
/**
 * 前端控制器
 * 针对某个应用的请求会先经过这里
 * 
 * filename:	index.php
 * charset:		UTF-8
 * create date: 2012-5-25
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

//定义应用的路径，无尾部斜线
define('APP_PATH', 'app');

//定义系统目录，无尾部斜线
define('SYS_PATH', dirname(__FILE__));

//定义入口文件
define('INDEX_PAGE', basename(__FILE__));

//载入核心文件
require APP_PATH . '/core/core.php';
