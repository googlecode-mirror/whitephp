<?php
// PHP文件夹清理工具
// 作者：赵彬言
// 网名：IT不倒翁
// 日期：2012-08-13
// 网址：http://yungbo.com/
// 功能说明：此小工具可以删除指定的文件夹和文件
// 使用方法：将该文件放入要清理的文件夹的根目录或者上级目录即可
// 注意事项：在 Linux 系统下使用时，注意赋予被清理文件夹合适的权限

//update: 2012-9-4 将 __DIR__ 变更为 dirname(__FILE__)

/*---------------------- 配置区域开始 -----------------------*/

// 需要清理的目录
$dir = './';

// 要删除的文件夹列表
$dir_to_del = array(
	'.svn',
	'.settings',
);

// 需要删除的文件
$file_to_del = array(
	'.project',
	'.buildpath',
);

/*---------------------- 配置区域结束 -----------------------*/

header("Content-type: text/html; charset=utf-8");
error_reporting(E_ALL);

$t0 = explode(' ', microtime());

// 清理文件夹
clean_dir($dir, $dir_to_del, $file_to_del);

// 显示提示信息
function show($data = '', $color = 'green', $dec = "<br>\r\n") {
	echo  "<span style='color:$color'>", $data, '</span>', $dec;
}

// 清理文件夹
function clean_dir($dir = './', $dir_to_del = array(), $file_to_del = array()) {
	if (is_dir($dir)) {
		$dir = rtrim($dir, '/') . '/';
		if (false !== ($fp = opendir($dir))) {
			while (false !== ($file = readdir($fp))) {
				if ($file != '.' and $file != '..') {
					if (in_array($file, $dir_to_del) or in_array($file, $file_to_del)) {
						rrmdir($dir . $file);
					} else if (is_dir($dir . $file)) {
						clean_dir($dir . $file, $dir_to_del, $file_to_del);
					}
				}
			}
			closedir($fp);
		}
	}
}

// 删除某文件夹及其下文件
function rrmdir($dir) {
	// for windows platform
	@chmod($dir, 0777);
	if (is_dir($dir) && $fp = opendir($dir)) {
		$dir = rtrim($dir, '/') . '/';
		while (false !== ($file = readdir($fp))) {
			if ($file != '.' and $file != '..') {
				if (is_dir($dir . $file)) {
					rrmdir($dir . $file);
				} else if (is_file($dir . $file)) {
					// for windows platform
					@chmod($dir . $file, 0777);
					unlink($dir . $file);
				}
			}
		}
		closedir($fp);
		if (rmdir($dir)) show("文件夹 $dir 删除成功！");
	} else {
		
		if (unlink($dir)) show("文件 $dir 删除成功！<br />");
	}
}

$t1 = explode(' ', microtime());
$telaspe = $t1[0] + $t1[1] - ($t0[0] + $t0[1]);

$msg = '文件夹 ' . dirname(__FILE__) . ' 清理成功！耗时 ' . $telaspe . " 秒<br>\r\n";
$msg .= "清理的文件夹为：" . implode(',', $dir_to_del) . "<br>\r\n";
$msg .= "清理的文件为：" . implode(',', $file_to_del);

show($msg);

