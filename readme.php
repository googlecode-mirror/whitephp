<?php
/**
 * wphp is a simple framework for you.
 * 
 * filename:	readme.php
 * charset:		UTF-8
 * create date: 2012-5-25
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

/*
WPHP

WhitePHP, or WPHP for short, a PHP framework like whitepaper that you can deal with it as you like. I was bored with big framework. It just gives me too much. Guess what, I dont't found the right framework even. So why not write a framework myself. This is how this framework was born. This framework learn from codeigniter, lazyphp. Thanks you all!
I will keep this framework simple, secure and high efficient.

WhitePHP，即WPHP，是一个非常容易上手的PHP框架。因为现有的框架结构有点复杂，有时候是杀鸡用牛刀。所以在一天晚上，自己写了这个框架。此框架借鉴了CodeIgniter和Lazyphp，谢谢你们，谢谢开源。
我将会保持这个框架简单、安全、高效。

WPHP框架
WPHP 是 WhitePHP 的缩写，它是一个简单高效的 PHP 框架。WhitePHP 极其简单，就像一张任你书写的白纸一样。

该框架遵循 MVC 模式，MVC 各个部分可以从清晰的目录结构中辨别出来。即使没用过其它框架也会很快上手，因为此框架设计之初就是因为其他框架给的太多，定义了太多的函数，所以该框架尽可能的降低了学习成本。

注意框架将安全相关的东西交给了开发人员。

WPHP 有以下特点：

极其小巧，简单高效
单入口文件
完整的 MVC 模式支持
完美支持原生 PHP
自定义函数少，学习成本低
多主题支持
易扩展
数据库主从机制
多数据库支持
错误处理机制
兼容 SAE 环境

注意：
-1，PHP version >= 5.2.6
0，方法不能和类名同名（因为相当于构造函数）
1，文件名全部小写，类名首字母大写（只要保持小写状态一致即可）
2，系统函数均为下划线分割，类首字母大写，属性、方法和函数命名保持一致
3，单入口文件，所有相对路径都是相对于前端控制器的

系统级别常量表
VERSION					框架版本号
SYS_PATH				入口文件所在目录
INDEX_PAGE				入口文件文件名

APP_NAME				应用文件夹名称，带结尾斜线
APP_PATH				APP_NAME 的绝对路径
CORE_NAME				核心文件夹名称，带结尾斜线
CORE_PATH				CORE_NAME 的绝对路径

CUR_CONTROLLER			当前控制器名称
CUR_ACTION				当前方法名称

数据库配置常量
IS_DB_ACTIVE			是否启用数据库

自定义配置常量
SYS_MODE 				开发模式， development testing production

IS_LOG 					是否记录错误日志
IS_HIDE_INDEX_PAGE 		是否隐藏入口文件，需要配合 apche 的rewrite 模块或相关模块
CHARSET 				字符集设计，建议保持 utf-8，默认即可
TIME_ZONE 				时区设置，东八区使用 PRC 即可，默认即可

DEFAULT_CONTROLLER		默认控制器，hello
DEFAULT_ACTION			默认方法，index

PARAM_CONTROLLER		获取控制器的参数名 c
PARAM_ACTION			获取方法的参数名 a

LOG_PATH 				日志路径
WPHP_GLOBAL_CONFIG_NAME 全局变量存储索引

全局变量
可以通过 get_conf('变量名')访问
theme_package			主题包名
query_string			
autoload_config			自动加载类库目录，保持文件名部分和类名一致，文件名小写
db_conf 				返回数据库配置数据，索引是数据库组

系统函数
具体内容直接参考 core/func.php 下的代码即可

升级
直接覆盖 core 目录即可

---------------- 数据库使用说明 -----------------
 * 详见控制器示例文件
 * $model = Model::singleton();
 * $model->query();
 
1，可以直接实例化，并为参数提供表名即可返回一个数据库资源。
在控制器的某个方法中
$user = Model::singleton('user');	//如若不传递第二个参数，获取的是 default 组的数据库配置文件
$user_info = $user->select('*', '1 LIMIT 2');
var_dump($user_info);

2，在模型文件夹建立模型文件
在书写 __construct() 函数时提供默认参数为表名，用 load_model() 函数加载之后，可以使用该资源。
muser.php
class Muser extends Model {
}

//在控制器方法中
$user = Muser::singleton();	//其实可以传递任意值，如果只是使用 query 而不使用框架函数就无所谓
$user_info = $user->query('SELECT * FROM user');
var_dump($user_info->num_rows);

//1，2情况下，如果需要解决主从延时的问题时，可以直接调用主数据库资源
// $user->db->query(); 使用主数据库
// $user->db_slave->query(); 使用从数据库

3，直接使用最原始的资源
$user = db_init();	//默认获取的是 default 组的数据库配置文件
$user_info = $user->query('SELECT * FROM user');
var_dump($user_info->num_rows);

为解决安全问题，可使用系统函数 check_input 处理组成sql语句前的变量。
即：
$user = check_input(post('username'));
$sql = "select count(*) from user where username = $user";
...

