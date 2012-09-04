<?php
/**
 * 获取IP接口
 * 
 * 采用直接读文件形式，非常快
 * qqwry.dat 文件为纯真网络的IP库文件
 * 升级非常方便，如果安装了纯真数据库，只需先对 ip.exe 进行升级，
 * 然后将目录下的 qqary.dat 文件复制过来覆盖掉旧的文件即可。
 * 
 * filename:	getip.php
 * charset:		UTF-8
 * create date: 2012-8-25
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */
?>
<?php

// header('Content-Type:text/html; charset=utf-8');

error_reporting(0);
ini_set('memory_limit', '500M');
ini_set('post_max_size', '200M');
ini_set('upload_max_filesize', '200M');
ini_set('max_execution_time', '120');
ini_set('max_input_time', '120');

$hidetime = isset($_GET['hidetime']) ? $_GET['hidetime'] : 0;

//测试代码段执行时间开始
$time_ = explode(' ', microtime());
$time0 = $time_[1] + $time_[0];

$ip = isset($_GET['ip']) ? $_GET['ip'] : get_ip();

$ip = gethostbyname($ip);

//测试代码段执行时间开始
$time_ = explode(' ', microtime());
$time1 = $time_[1] + $time_[0];

$ipope = new IpLocation();
$location = $ipope->get_info($ip);
echo $location;

//测试代码段执行时间结束
$time_ = explode(' ', microtime());
$time2 = $time_[1] + $time_[0];

if (!$hidetime) {
	echo "<br>\n<br>\n get ip : ", $time1 - $time0, ' seconds';
	echo "<br>\n<br>\n get info : ", $time2 - $time1, ' seconds';
}


/*以下内容一般不需要改动*/

/**
 * IP 地理位置查询类
 * 
 * <code>
 * $ipope = new IpLocation();
 * $location = $ipope->get_info($ip);
 * </code>
 * 
 * 此类被赵彬言于2012年8月25日升级
 * 1，更新为 PHP5 的规范
 * 2，增加 wphp_ip2long 方法
 * 3，增加 wphp_iconv 转码方法
 * 4，增加 get_province 方法
 * 5，增加 get_isp 方法
 * 6，增加 is_valid_ip 方法
 * 
 *
 * @author 马秉尧
 * @version 1.5
 * @copyright 2005 CoolCode.CN
 */
class IpLocation {
    /**
     * qqwry.dat文件指针
     *
     * @var resource
     */
    private $fp;

    /**
     * 第一条IP记录的偏移地址
     *
     * @var int
     */
    private $firstip;

    /**
     * 最后一条IP记录的偏移地址
     *
     * @var int
     */
    private $lastip;

    /**
     * IP记录的总条数（不包含版本信息记录）
     *
     * @var int
     */
    private $totalip;
    
    /**
     * 输出的字符编码
     * 
     * 默认是 utf-8
     * @var string
     */
    public $out_charset = 'utf-8';
    
    /**
     * 省份词典
     * @var unknown_type
     */
    public $dict_province = array(
'北京', '天津', '重庆', '上海',

'河北', '山西', '辽宁', '吉林',
'黑龙江', '江苏', '浙江', '安徽',
'福建', '江西', '山东', '河南',
'湖北', '湖南', '广东', '海南',
'四川', '贵州', '云南', '陕西',
'甘肃', '青海', '台湾',

'内蒙古', '广西', '宁夏', '新疆', '西藏',

'香港', '澳门',

);
    
    /**
     * 运营商词典
     * @var unknown_type
     */
    public $dict_isp = array(
    		'联通', '移动', '铁通', '电信', '长城宽带', '聚友宽带'
    		);
    
    /**
     * 构造函数，打开 qqwry.dat 文件并初始化类中的信息
     *
     * @param string $filename
     * @return IpLocation
     */
    public function __construct($filename = "qqwry.dat") {
    	$this->fp = 0;
    	if (($this->fp = fopen($filename, 'rb')) !== false) {
    		$this->firstip = $this->getlong();
    		$this->lastip = $this->getlong();
    		$this->totalip = ($this->lastip - $this->firstip) / 7;
    	}
    }
    
    /**
     * 
     * @param unknown_type $ip
     */
    public function get_info($ip) {
    	header('Content-Type:text/html; charset=' . $this->out_charset);
    	$ret = array();
    	
    	//结果
    	$result = array();
    	
    	if (!$this->is_valid_ip($ip)) {
    		$result['error'] = 'ip invalid';
    	} else {
    		$location = $this->getlocation($ip);
    		
    		$location = $this->conv_encoding($location, $this->out_charset);
    		$a = $this->get_province($location['country']);
    		 
    		$b = $this->get_isp($location['area']);
    		
    		$ret = array_merge($location, $a);
    		$ret = array_merge($ret, $b);
    		
    		$result['ip'] = $ret['ip'];
    		$result['country'] = $ret['country'];
    		$result['province'] = $ret['province'];
    		$result['area'] = $ret['area'];
    		$result['isp'] = $ret['isp'];
    		$result['province_id'] = $ret['province_id'];
    	}
    	return $this->ch_json_encode($result);
    }
    
    /**
     * 根据所给 IP 地址或域名返回所在地区信息
     *
     * @access public
     * @param string $ip
     * @return array
     */
    private function getlocation($ip) {
    	if (!$this->fp) return null;            // 如果数据文件没有被正确打开，则直接返回空
    	
    	$location['ip'] = $ip;
//     	$location['ip'] = gethostbyname($ip);   // 将输入的域名转化为IP地址
    	
    	$ip = $this->packip($location['ip']);   // 将输入的IP地址转化为可比较的IP地址
    	// 不合法的IP地址会被转化为255.255.255.255
    	// 对分搜索
    	$l = 0;                         // 搜索的下边界
    	$u = $this->totalip;            // 搜索的上边界
    	$findip = $this->lastip;        // 如果没有找到就返回最后一条IP记录（qqwry.dat的版本信息）
    	while ($l <= $u) {              // 当上边界小于下边界时，查找失败
    		$i = floor(($l + $u) / 2);  // 计算近似中间记录
    		fseek($this->fp, $this->firstip + $i * 7);
    		$beginip = strrev(fread($this->fp, 4));     // 获取中间记录的开始IP地址
    		// strrev函数在这里的作用是将little-endian的压缩IP地址转化为big-endian的格式
    		// 以便用于比较，后面相同。
    		if ($ip < $beginip) {       // 用户的IP小于中间记录的开始IP地址时
    			$u = $i - 1;            // 将搜索的上边界修改为中间记录减一
    		}
    		else {
    			fseek($this->fp, $this->getlong3());
    			$endip = strrev(fread($this->fp, 4));   // 获取中间记录的结束IP地址
    			if ($ip > $endip) {     // 用户的IP大于中间记录的结束IP地址时
    				$l = $i + 1;        // 将搜索的下边界修改为中间记录加一
    			}
    			else {                  // 用户的IP在中间记录的IP范围内时
    				$findip = $this->firstip + $i * 7;
    				break;              // 则表示找到结果，退出循环
    			}
    		}
    	}
    
    	//获取查找到的IP地理位置信息
    	fseek($this->fp, $findip);
    	$location['beginip'] = long2ip($this->getlong());   // 用户IP所在范围的开始地址
    	$offset = $this->getlong3();
    	fseek($this->fp, $offset);
    	$location['endip'] = long2ip($this->getlong());     // 用户IP所在范围的结束地址
    	$byte = fread($this->fp, 1);    // 标志字节
    	switch (ord($byte)) {
    		case 1:                     // 标志字节为1，表示国家和区域信息都被同时重定向
    			$countryOffset = $this->getlong3();         // 重定向地址
    			fseek($this->fp, $countryOffset);
    			$byte = fread($this->fp, 1);    // 标志字节
    			switch (ord($byte)) {
    				case 2:             // 标志字节为2，表示国家信息被重定向
    					fseek($this->fp, $this->getlong3());
    					$location['country'] = $this->getstring();
    					fseek($this->fp, $countryOffset + 4);
    					$location['area'] = $this->getarea();
    					break;
    				default:            // 否则，表示国家信息没有被重定向
    					$location['country'] = $this->getstring($byte);
    					$location['area'] = $this->getarea();
    					break;
    			}
    			break;
    		case 2:                     // 标志字节为2，表示国家信息被重定向
    			fseek($this->fp, $this->getlong3());
    			$location['country'] = $this->getstring();
    			fseek($this->fp, $offset + 8);
    			$location['area'] = $this->getarea();
    			break;
    		default:                    // 否则，表示国家信息没有被重定向
    			$location['country'] = $this->getstring($byte);
    			$location['area'] = $this->getarea();
    			break;
    	}
    	if ($location['country'] == " CZ88.NET") {  // CZ88.NET表示没有有效信息
    		$location['country'] = "未知";
    	}
    	if ($location['area'] == " CZ88.NET") {
    		$location['area'] = "";
    	}
    	
    	return $location;
    }
    
    /**
     * Ip 地址转为数字地址
     *
     * php 的 ip2long 这个函数有问题
     * 133.205.0.0 ==>> 2244804608
     * @param string $ip 要转换的 ip 地址
     * @return int    转换完成的数字
     */
    private function wphp_ip2long($ip) {
    	$ip_arr = explode('.',$ip);
    	$iplong = (16777216 * intval($ip_arr[0])) + (65536 * intval($ip_arr[1])) + (256 * intval($ip_arr[2])) + intval($ip_arr[3]);
    	return $iplong;
    }
    
	/**
	 * 对字符串、对象、数组进行转码
	 * 
	 * 和 iconv 参数使用方式相同
	 * @param string $in_charset
	 * @param string $out_charset
	 * @param array|string $data
	 * @return string|array
	 */
	private function wphp_iconv($in_charset, $out_charset, $data) {
	
		if (is_array($data) || is_object($data)) {
			foreach ($data as $k => $v) {
				if (is_scalar($v)) {
					if (is_array($data)) {
						$data[$k] = iconv($in_charset, $out_charset, $v);
					} else if (is_object($data)) {
						$data->$k = iconv($in_charset, $out_charset, $v);
					}
				} else if (is_array($data)) {
					$data[$k] = $this->wphp_iconv($in_charset, $out_charset, $v);
				} else if (is_object($data)) {
					$data->$k = $this->wphp_iconv($in_charset, $out_charset, $v);
				}
			}
		}
		 else if (is_scalar($data)) {
			$data = iconv($in_charset, $out_charset, $data);
		}
		return $data;
	}

    /**
     * 返回读取的长整型数
     *
     * @access private
     * @return int
     */
    private function getlong() {
        //将读取的little-endian编码的4个字节转化为长整型数
        $result = unpack('Vlong', fread($this->fp, 4));
        return $result['long'];
    }

    /**
     * 返回读取的3个字节的长整型数
     *
     * @access private
     * @return int
     */
    private function getlong3() {
        //将读取的little-endian编码的3个字节转化为长整型数
        $result = unpack('Vlong', fread($this->fp, 3).chr(0));
        return $result['long'];
    }

    /**
     * 返回压缩后可进行比较的IP地址
     *
     * @access private
     * @param string $ip
     * @return string
     */
    private function packip($ip) {
        // 将IP地址转化为长整型数，如果在PHP5中，IP地址错误，则返回False，
        // 这时intval将Flase转化为整数-1，之后压缩成big-endian编码的字符串
        return pack('N', intval($this->wphp_ip2long($ip)));
    }

    /**
     * 返回读取的字符串
     *
     * @access private
     * @param string $data
     * @return string
     */
    private function getstring($data = "") {
        $char = fread($this->fp, 1);
        while (ord($char) > 0) {        // 字符串按照C格式保存，以\0结束
            $data .= $char;             // 将读取的字符连接到给定字符串之后
            $char = fread($this->fp, 1);
        }
        return $data;
    }
    
    /**
     * 返回地区信息
     * 
     * @access private
     * @return string
     */
    private function getarea() {
    	$byte = fread($this->fp, 1);    // 标志字节
    	switch (ord($byte)) {
    		case 0:                     // 没有区域信息
    			$area = "";
    			break;
    		case 1:
    		case 2:                     // 标志字节为1或2，表示区域信息被重定向
    			fseek($this->fp, $this->getlong3());
    			$area = $this->getstring();
    			break;
    		default:                    // 否则，表示区域信息没有被重定向
    			$area = $this->getstring($byte);
    			break;
    	}
    	return $area;
    }
    
    //获取省份数据，从字符串 $str 中获取省份数据
    //输入字符串 河北省邢台市
    //返回 Array ( [province] => 河北 [province_id] => 4 )
    private function get_province($str) {
    	//global $dict_province;
    	$country = '中国';
    	if ($this->out_charset == 'gbk' or $this->out_charset == 'gb2312') {
    		$country = $this->wphp_iconv('utf-8', $this->out_charset, $country);
    	}
    	
    	$ret = array();
    	$ret['province'] = '';
    	$ret['province_id'] = -1;
    	$ret['country'] = $str;
    	
    	if ($this->out_charset == 'gbk' or $this->out_charset == 'gb2312') {
    		$this->dict_province = $this->wphp_iconv('utf-8', $this->out_charset, $this->dict_province);
    	}
    
    	foreach ($this->dict_province as $k => $v) {
    		if (false !== strpos($str, $v)) {
    			$ret['province'] = $v;
    			$ret['province_id'] = $k;
    			$ret['country'] = $country;
    			break;
    		}
    	}
    	return $ret;
    }
    
    private function get_isp($str) {
    	$ret = array();
    	$ret['isp'] = '';
    	$ret['area'] = $str;
    	if ($this->out_charset == 'gbk' or $this->out_charset == 'gb2312') {
    		$this->dict_isp = $this->wphp_iconv('utf-8', $this->out_charset, $this->dict_isp);
    	}
    	
    	foreach ($this->dict_isp as $k => $v) {
    		if (false !== strpos($str, $v)) {
    			$ret['isp'] = $v;
    			break;
    		}
    	}
    	return $ret;
    }
    
    private function conv_encoding($data) {
    	$ret = null;
    	if (strtolower($this->out_charset) == 'gbk' or strtolower($this->out_charset) == 'gb2312') {
    		$ret = $data;
    	} else {
    		$ret = $this->wphp_iconv('gbk', $this->out_charset, $data);
    	}
    	
    	return $ret;
    }
    
	/**
	 * 判断 ip 是否合法
	 * @param unknown_type $ip
	 */
	private function is_valid_ip($ip) {
		$preg = '/^(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])\.(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])\.(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])\.(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])$/';
		$is_matched = false;
		if (preg_match($preg, $ip, $m)) {
			$is_matched = true;
		}
		return $is_matched;
	}
	
	/**
	 * 处理  json_encode() 不支持中文的情况
	 *
	 * @param array|object $data
	 * @return array|object
	 */
	private function ch_json_encode($data) {
		/**
		 * 将中文编码
		 * @param array $data
		 * @return string
		 */
		function ch_urlencode($data) {
			if (is_array($data) || is_object($data)) {
				foreach ($data as $k => $v) {
					if (is_scalar($v)) {
						if (is_array($data)) {
							$data[$k] = urlencode($v);
						} else if (is_object($data)) {
							$data->$k = urlencode($v);
						}
					} else if (is_array($data)) {
						$data[$k] = ch_urlencode($v);//递归调用该函数
					} else if (is_object($data)) {
						$data->$k = ch_urlencode($v);
					}
				}
			}
			return $data;
		}
	
		$ret = ch_urlencode($data);
		$ret = json_encode($ret);
		return urldecode($ret);
	}
	
    /**
     * 析构函数，用于在页面执行结束后自动关闭打开的文件。
     *
     */
    public function __destruct() {
        if ($this->fp) {
            fclose($this->fp);
        }
        $this->fp = 0;
    }
}

/*附加函数*/

/**
 * 获取客户端 IP
 * 搜集自网络，原作者未知
 */
function get_ip() {
    if ($_SERVER["HTTP_X_REAL_IP"])
        $ip = $_SERVER["HTTP_X_REAL_IP"];
    else if ($_SERVER["HTTP_CLIENT_IP"])
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    else if ($_SERVER["REMOTE_ADDR"])
        $ip = $_SERVER["REMOTE_ADDR"];
    else if (getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
		$ip = "0.0.0.0";
	return $ip;
}


