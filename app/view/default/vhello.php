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
<div><?php echo $content; ?></div>
<hr />
<p>WPHP 官网 <a href="http://wphp.sinaapp.com" title="WPHP官网" target="_blank">http://wphp.sinaapp.com</a></p>
<p>WPHP GOOGLE CODE <a href="http://code.google.com/p/whitephp/" title="WPHP GOOGLE CODE" target="_blank">http://code.google.com/p/whitephp/</a></p>
<p>作者微博 <a href="http://weibo.com/itbudaoweng" title="@IT不倒翁" target="_blank">@IT不倒翁</a></p>
<p>作者博客 <a href="http://yungbo.com" title="IT不倒翁" target="_blank">http://yungbo.com</a></p>
<p>WPHP 当前版本：<?php echo VERSION; ?></p>
<p>最后更新 <?php echo date('Y-m-d H:i:s', filemtime(__FILE__)); ?></p>

</body>
</html>