<?php
/**
 * 系统的配置文件，如需配置内容请去 main.php
 * 
 * 若该文件夹下有其它配置文件，需要在此页面进行 require
 * 
 * filename:	system.php
 * charset:		UTF-8
 * create date: 2012-5-25
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

//版本号
define('VERSION', '0.1');

//定义日志目录，不必写尾部斜线
define('LOG_PATH', 'log');

//用来存储可变配置索引
define('WPHP_GLOBAL_CONFIG_NAME', 'config');

//加载数据库配置文件
require 'database.php';

//加载自
require 'main.php';

/**
 * 版本更新记录
 * 
 * 
-----------------------------------
v0.1
2012-06-24

初始版本
WPHP有以下特点：

及其小巧，简单高效
单入口文件
完整的MVC模式支持
完美支持原生PHP
自定义函数少，学习成本低
多主题支持
易扩展
数据库主从机制
多数据库支持
错误处理机制
兼容SAE环境
WPHP待完善的功能：

安全机制
缓存机制
设置数据库字符集
完善model类
模块设计（违背）
钩子机制（违背）
 */