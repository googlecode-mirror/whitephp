<?php
/**
 * 个人的一些配置
 * 这是你可以修改的文件
 * 
 * filename:	main.php
 * charset:		UTF-8
 * create date: 2012-5-25
 * 
 * @todo define('IS_SECURITY', FALSE);
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
 * pruduction
 */
define('SYS_MODE', 'development');

//跳转规则，网址为索引部分，跳转到值部分
$rewrite_rules = array(
			'about'=>'hello/about',
			'contact'=>'hello/content',
		);

//主题包
// 可使用 get_conf['theme_package']访问
$theme_package = 'default';

//是否使用 path 类型的网址，即 xxx/yyy/zzz
define('IS_PATH_URL', FALSE);

//是否进行安全过滤，暂未实现
define('IS_SECURITY', FALSE);

//是否记录错误日志
define('IS_LOG', TRUE);

//设置字符集，utf-8, gbk, gb2312 等
//这将影响到网页的header信息以及和数据库交互，建议统一保持 utf-8 编码
//直接使用 utf-8 即可，已自动在操作数据库那里转换为 utf8
define('CHARSET', 'utf-8');

//设置时区
define('TIME_ZONE', 'PRC');

//默认控制器
define('CONTROLLER', 'hello');

//默认方法
define('ACTION', 'index');

//是否隐藏前端控制器，需要配合 apache 的 rewrite 模块
define('IS_HIDE_INDEX_PAGE', FALSE);
/**
 * 注意，仅可以隐藏一个入口文件
 * 如果需要隐藏入口文件，请保证服务器开启了 rewrite 模块，并且在前端控制器目录存在文件 .htaccess
 * 并写好了跳转规则，apache服务器可参考下面的代码
 *
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?$1 [L]
 * sae 环境隐藏入口文件代码可参考
handle:
 - rewrite: if( !is_file() && !is_dir()) goto "index.php?%{QUERY_STRING}"
 */

//加载扩展函数
// require APP_PATH . '/func/xxx.php';
