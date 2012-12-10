<?php defined('INDEX_PAGE') or die('no entrance'); ?>
<?php
/**
 * 配置文件
 * 这是你可以修改的文件
 *
 * filename:	main.php
 * charset:		UTF-8
 * create date: 2012-5-25
 *
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

/*
 * 共有三种环境，分别为开发环境、测试环境、生产环境
* development
* testing
* production
*/
define('SYS_MODE', 'development');

//是否记录错误日志
define('IS_LOG', TRUE);

//设置字符集，utf-8, gbk, gb2312 等
//这将影响到网页的header信息以及和数据库交互，建议统一保持 utf-8 编码
//直接使用 utf-8 即可，已自动在操作数据库那里转换为 utf8
define('CHARSET', 'utf-8');

//设置时区
define('TIME_ZONE', 'PRC');

//默认控制器
define('DEFAULT_CONTROLLER', 'hello');

//默认方法
define('DEFAULT_ACTION', 'index');

//获取控制器的参数名
define('PARAM_CONTROLLER', 'c');

//获取方法的参数名
define('PARAM_ACTION','a');

//定义日志目录，相对于APP_PATH，不必写尾部斜线
//请使用 ../../ 方式将目录置于框架内不能访问的位置
define('LOG_PATH', 'log');

//用来存储可变配置索引
define('WPHP_GLOBAL_CONFIG_NAME', 'wphp_config');

//是否隐藏前端控制器，同时注意修改服务器配置
define('IS_HIDE_INDEX_PAGE', true);

//参考
/**
 * 注意，仅可以隐藏一个入口文件
 * 如果需要隐藏入口文件，请保证服务器开启了 rewrite 模块
 * 并且将环境配置好，方选方式有
 * 1，直接在环境配置处写好规则
 * 2，将规则写入前端控制器目录中的 .htaccess
 * 相关规则可参考下面的代码【注意：index.php 代表入口文件】
 *
 RewriteEngine On
 RewriteCond %{REQUEST_FILENAME} !-f
 RewriteCond %{REQUEST_FILENAME} !-d
 RewriteRule ^(.*)$ index.php?$1 [L]
 * sae 环境隐藏入口文件代码可参考
 handle:
 - rewrite: if( !is_file() && !is_dir()) goto "index.php?%{QUERY_STRING}"
 */

// 定义，可在程序中随时变更
// 可使用 get_conf['theme_package']访问
$theme_package = 'default';

//自动加载某文件夹下的以某种名称结尾的类
//保持文件名和类名一致，文件名小写
//path 对于的为相对于 SYS_PATH 的路径
//ext 表示 class+ext 中的文件后缀 ext
$autoload_config = array(
		array(
				'path' => APP_NAME . 'lib/',
				'ext' => '.class.php',
		),
		array(
				'path' => APP_NAME . 'model/',
				'ext' => '.php',
		),
);

//加载扩展函数
// require APP_NAME . 'func/xxx.php';
require APP_NAME . 'func/custom.php';
require APP_NAME . 'func/verifycode.php';

//加载其它配置文件
// require APP_NAME . 'config/yyy.php';
require APP_NAME . 'config/database.php';

