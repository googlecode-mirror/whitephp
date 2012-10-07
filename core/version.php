<?php defined('INDEX_PAGE') or die('no entrance'); ?>
<?php
/**
 * 版本更新记录
 * 
 -----------------------------------
 v0.4
 2012-09-26
 RECONSTRUCT
 重构
 将核心目录（入口文件定义的 CORE_PATH 目录）从应用中迁移出来
 方便升级，仅需覆盖核心目录即可，提高安全性

 ADD
 增加常量 CORE_PATH APP_NAME 和 CORE_NAME
 增加用户自定义函数，方便不动核心目录的前提下变更框架处理细节
 query_string 配置项

 UPDATE
 约定框架中所有定义的目录尾部均包含斜线
 query_string 改为 PATH_INFO 和 QUERY_STRING 配合使用
 
 DELETE
 QUERY_STRING 常量
 删除 PATHURL 支持

 -----------------------------------
 v0.3
 2012-08-19
 UPDATE
 对数据库实行单例模式
 增加函数 get() 和 post()
 增加过滤函数 check_input()
 增加ip函数 wphp_ip2long()
 优化 readme.php 帮助文件
 
 BUG
 优化 log_error（） 函数
 
 -----------------------------------
 v0.2
 2012-07-02
 UPDATE
 完善 MODEL 主从
 自动设置数据库和脚本交互的字符集
 优化 log_error 函数
 
 BUG
 解决多层目录下控制器不能正常调用问题
 解决数据库连接失败后中午错误提示乱码问题
 
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