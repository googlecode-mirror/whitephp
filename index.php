<?php
/**
 * 前端控制器，即入口文件
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

//定义入口文件所在目录
//如无特殊说明，定义目录均存在尾部斜线，框架内同
define('SYS_PATH', trim(dirname(__FILE__), '\/') . '/');

//定义入口文件名称
define('INDEX_PAGE', basename(__FILE__));

// APP_NAME 和 CORE_NAME 可自己定义
/* ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓ */

define('APP_NAME', 'app');

define('CORE_NAME', 'core');

/* ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑ */

//定义应用的路径，如无特殊情况，均使用绝对路径，框架内同
define('APP_PATH', SYS_PATH . APP_NAME . '/');

//定义核心目录
define('CORE_PATH', SYS_PATH . CORE_NAME . '/');

//载入核心文件
require CORE_PATH . 'core.php';
