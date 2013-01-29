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

WPHP框架
WPHP 是 WhitePHP 的缩写，它是一个简单高效的 PHP 框架。WhitePHP 极其简单，就像一张任你书写的白纸一样。

WhitePHP，即WPHP，是一个非常容易上手的PHP框架。因为现有的框架结构有点复杂，有时候是杀鸡用牛刀。所以在一天晚上，自己写了这个框架。此框架借鉴了CodeIgniter和Lazyphp，谢谢你们，谢谢开源。
我将会保持这个框架简单、高效。框架将安全相关的东西交给了开发人员。

从0.4版本开始，不在PHP端支持伪静态，请利用服务器进行配置。

WPHP 有以下特点：

极其小巧，简单高效
单入口文件
数据库主从机制
多数据库支持

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

