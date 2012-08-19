<?php
/**
 * 介绍文件
 * 
 * filename:	vhello.php
 * charset:		UTF-8
 * create date: 2012-6-21
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title><?php echo $title; ?></title>
</head>
<body>
<h2><?php echo $title; ?></h2>
<hr />
<p><?php echo $content; ?></p>
<p>该框架遵循 MVC 模式，MVC 各个部分可以从清晰的目录结构中辨别出来。即使没用过其它框架也会很快上手，因为此框架设计之初就是因为其他框架给的太多，定义了太多的函数，所以该框架尽可能的降低了学习成本。</p>
<p><?php echo_code(); ?></p>
<p>WPHP 有以下特点：</p>
<ol>
	<li>极其小巧，简单高效</li>
	<li>单入口文件</li>
	<li>完整的 MVC 模式支持</li>
	<li>完美支持原生 PHP</li>
	<li>自定义函数少，学习成本低</li>
	<li>多主题支持</li>
	<li>易扩展</li>
	<li>数据库主从机制</li>
	<li>多数据库支持</li>
	<li>错误处理机制</li>
	<li>兼容 SAE 环境</li>
</ol>

<p>更多特性请查看该框架内部说明。</p>
<hr />
<p>WPHP 官网 <a href="http://wphp.sinaapp.com" title="WPHP官网" target="_blank">http://wphp.sinaapp.com</a></p>
<p>WPHP GOOGLE CODE <a href="http://code.google.com/p/whitephp/" title="WPHP GOOGLE CODE" target="_blank">http://code.google.com/p/whitephp/</a></p>
<p>作者微博 <a href="http://weibo.com/itbudaoweng" title="@IT不倒翁" target="_blank">@IT不倒翁</a></p>
<p>作者博客 <a href="http://yungbo.com" title="IT不倒翁" target="_blank">http://yungbo.com</a></p>
<p>WPHP 当前版本：<?php echo VERSION; ?></p>
<p>最后更新 <?php echo date('Y-m-d H:i:s', filemtime(__FILE__)); ?></p>

</body>
</html>