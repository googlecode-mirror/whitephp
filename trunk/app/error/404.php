<?php
/**
 * default 404 file
 * 
 * filename:	404.php
 * charset:		UTF-8
 * create date: 2012-5-25
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */
header('HTTP/1.1 404 Not Found');
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>404 File Not Found</title>
</head>
<body>
	<h1>404 File Not Found</h1>
	<hr />
	<p><?php echo $message; ?></p>

</body>
</html>