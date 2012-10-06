<?php defined('INDEX_PAGE') or die('no entrance'); ?>
<?php
/**
 * 验证码生成文件
 *
 * 若需要使用验证码中的$_SESSION，需要在调用函数前开启 session
 * echo_code() 函数 依赖 syscommon/verify_code 控制器/方法
 * 
 * filename:	verifycode.php
 * charset:		UTF-8
 * create date: 2012-8-25
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

/**
 * 创建验证码
 *
 * @param $array 一个数组
 *
 * <code>
 * $array = array(
 * 		'width' => 200,					//验证码宽度
 * 		'height' => 40,					//验证码高度
 * 		'num' => 4,						//验证码数量
 * 		'verify_code' => 'verify_code', //验证码索引名
 * 		'ads' => array(),				//增加的额外验证码，一维数组
 * 		'teight' => 1,					//紧凑度
 * 		'angle' => 20,					//偏向角度
 * 		'fontfile' => 'carbon.ttf',		//字体文件，请放置于 static 文件夹内，默认不支持中文，如有需要请添加中文字体
 * 		'bgred'=>233,
 * 		'bggreen'=>233,
 * 		'bgblue'=>233,	//字体背景色
 * 		'fred'=>233,
 * 		'fgreen'=>233,
 * 		'fblue'=>233,	//字体前景色
 * );
 * </code>
 *
 * @return string  向数组 $_SESSION[$verify_code] 写入一个字符串
 */
function verify_code($array = array()) {
	$width       = 100;
	$height      = 30;
	$num         = 5;
	$verify_code = 'verify_code';
	$ads         = array();
	$teight      = 1;
	$angle       = 20;
	$fontfile    = 'carbon.ttf';
	
	//背景色
	$bgred   = 244;
	$bggreen = 244;
	$bgblue  = 244;
	
	//前景色
	$fred   = 233;
	$fgreen = 233;
	$fblue  = 233;
	
	//解压传入的数组参数
	extract($array);
	
	$ret = ''; //结果
	
	//初始坐标
	$x = $height * 3 / 7;
	$y = $height * 5 / 7;
	
	if (!function_exists('imagecreate'))
		show_error('you must enable gd2 extension to use image functions');
	
	$handle  = imagecreate($width, $height);
	$bgcolor = imagecolorallocate($handle, $bgred, $bggreen, $bgblue);
	
	$words      = 'abcdefghijklmnopqrstuvwxyz';
	$wordsUpper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$numbers    = '0123456789';
	$array      = array();
	$array      = str_split($words . $wordsUpper . $numbers);
	// 	$ads = array('白纸', '框架', '你');
	$array      = array_merge($array, $ads);
	
	for ($i = 0; $i < $num; $i++) {
		$font = rand($y * 2 / 3, $y * 4 / 5);
		
		$string = array_rand($array);
		$string = $array[$string];
		
		$red   = rand(10, $fred);
		$green = rand(10, $fgreen);
		$blue  = rand(10, $fblue);
		
		$color      = imagecolorallocate($handle, $red, $green, $blue);
		$angle_real = rand(-$angle, $angle);
		
		$font_file = load_static($fontfile);
		
		file_exists($font_file) or show_error('unexists static file ' . $font_file);
		
		//注意全部以入口文件为参考，除非直接引入文件
		imagettftext($handle, $font, $angle_real, $x, $y, $color, $font_file, $string);
		
		$x = $x + $font * wphp_strlen($string) * $teight;
		
		$ret .= $string;
	}
	
	$_SESSION[$verify_code] = $ret;
	header("Content-type: image/png");
	imagepng($handle);
	imagedestroy($handle);
}

/**
 * 直接生成可视化的验证码和可点链接
 * 依赖 syscommon/verify_code 控制器/方法
 *
 * @param $array 一个数组
 *
 * <code>
 * $array = array(
 * 		'width' => 200,					//验证码宽度
 * 		'height' => 40,					//验证码高度
 * 		'num' => 4,						//验证码数量
 * 		'verify_code' => 'verify_code', //验证码索引名
 * 		'ads' => array(),				//增加的额外验证码，一维数组
 * 		'teight' => 1,					//紧凑度
 * 		'angle' => 20,					//偏向角度
 * 		'fontfile' => 'carbon.ttf',		//字体文件，请放置于 static 文件夹内，默认不支持中文，如有需要请添加中文字体
 * 		'bgred'=>233,
 * 		'bggreen'=>233,
 * 		'bgblue'=>233,	//字体背景色
 * 		'fred'=>233,
 * 		'fgreen'=>233,
 * 		'fblue'=>233,	//字体前景色
 * );
 * </code>
 *
 * @param  string  $prompt      提示信息
 * @param bool $show_prompt     是否显示提示文字
 * @return [type]               [description]
 */
function echo_code($array = array(), $prompt = '看不清？点击重新获取', $show_prompt = false) {
	//session_start(); //在核心文件已经开启
	
	//外部访问验证码的接口（控制器方法对）
	$verify = 'syscommon/verify_code';
	
	echo '<img id="code" src="' . href($verify) . '" onclick="this.src=\'' . href($verify) . '&r=\' + Math.random()" style="cursor:pointer" title=' . $prompt . '>';
	if ($show_prompt) {
		echo '<a href="javascript:void(0);" onclick="document.getElementById(\'code\').src=\'' . href($verify) . '&r=\'+Math.random();">' . $prompt . '</a>';
	}

	// 	if (isset($_SESSION['verify_code'])) {
	// 		echo '<br />Pre Code: ', $_SESSION['verify_code'];
	// 	}
}