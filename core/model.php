<?php defined('INDEX_PAGE') or die('no entrance'); ?>
<?php
/**
 * 数据库模型，建议继承此模型
 * 
 * filename:	model.php
 * charset:		UTF-8
 * create date: 2012-5-25
 * update date: 2012-8-14 改用单例模式，调用方法发生变化，具体方式见类说明
 * update date: 2012-9-20 更新单例模式，启用新数据库配置时重新链接，增加数据库时间检测
 * 
 * XXX 增加数据库执行时间，当显示 sql 时显示执行时间
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

/**
 * 模型基类
 * 
 * 建议使用原生 sql 语句，配合 check_input() 函数保证变量的安全
 * 
 * 如需显示sql请提前设置 xxx::$show_sql 为 true
 * $model = Model::singleton();
 * $model->query();
 * Model::$db->query();
 * Model::$dbS->query();
 * 以下注释中，Model 也可以是其子类，不再赘述 
 * 
 * 或者直接不用此模型类
 * $model = db_init();//默认使用主数据库，db_init() 直接连库，返回一个数据库资源
 * 
 * @author Think
 */
class Model {

	//连接之后的数据库资源
	public static $db;
	
	//从数据库资源
	public static $dbS;
	
	//数据表名
	public static $tb_name;
		
	//是否输出 sql 语句
	public static $show_sql = false;
	
	//数据库组
	private static $db_group;
	
	//数据库配置数组
	private static $db_conf;
	
	//实例的对象
	private static $singleton;
	
	/**
	 * 连接数据库
	 */
	private function __construct() {
		//echo 'please call Model::singleton() instead';die;
		//return self::singleton();
	}
	
	/**
	 * 单例
	 * Model::singleton()
	 * @param string $tb_name 默认表名，使用过程中可以随时变更 Model::$tb_name = 'new_tb_name';
	 * @param string $db_group 数据库配置文件
	 */
	public static function singleton($tb_name = 'sample_table_name', $db_group = 'default') {
		
		//当不存在对象或者启用新的数据库时重新连库
		if (!(self::$singleton instanceof self) || (self::$singleton instanceof self && $db_group != self::$db_group)) {
			$t1 = microtime(true);
			self::$db_group = $db_group;
			self::$db       = db_init(self::$db_group);
			
			self::$db_conf = get_conf('db_conf');
			
			//因为从数据库并不是必须连接的，所以先判断一下，如果没有配置则不进行尝试而不终止程序运行。
			if (array_key_exists('slave', self::$db_conf)) {
				self::$dbS = db_init('slave');
			}
			$t2 = microtime(true);

			self::$singleton = new self();
			
			//$c               = __CLASS__;
			//self::$singleton = new $c;
			
			$t3 = microtime(true);

			if (self::$show_sql) {
				echo "db group ${db_group} contructed!<br>\r\n";
				$time = self::timer($t1, $t2);
				//$time2 = self::timer($t2, $t3);
				echo "db connection: $time<br>\n";
				//echo "instance: $time2<br>\n";
			}
		}
		
		//总是保持表名最新
		self::$tb_name = $tb_name;

		return self::$singleton;
	}
	
	/**
	 * 增加数据
	 * $data
	 * array('id'=>'3', 'username'=>'胡锦涛')
	 * INSERT INTO `user` (`id`, `username`, `password`) 
	 * VALUES (NULL, '胡锦涛', '123456abc');
	 */
	public function insert($data = array()) {
		$t1 = microtime(true);
		
		//组合后的 sql 语句
		$sql = '';
		
		//组合后的字段
		$fields = '';
		//组合后的字段值
		$values = '';
		
		$array_keys   = array_keys($data);
		$array_values = array_values($data);
		
		foreach ($array_keys as $v) {
			$fields .= ", `$v`";//whatch out the space bettwen `
		}
		
		foreach ($array_values as $v) {
			$values .= ", " . check_input($v); //过滤下
		}
		$fields = trim($fields, ', ');
		$values = trim($values, ', ');
		
		$sql .= 'INSERT INTO';
		$sql .= ' `' . self::$tb_name . '`';
		$sql .= ' (' . $fields . ') ';
		$sql .= ' VALUES (' . $values . ')';
				
		$q = self::$db->query($sql);

		$t2 = microtime(true);

		self::show_mysql($sql, $t1, $t2);

		return $q;
	}
	
	/**
	 * 删除数据，参数为必须
	 * @param string $where WHERE 条件以及后续语句
	 * @return bool $q 只要语句正常执行了就会是 true
	 */
	public function delete($where = null) {
		$t1 = microtime(true);
		$sql = '';
		$q = false;

		$sqlwhere = ' WHERE ' . trim($where);

		$sql = 'DELETE FROM `' . self::$tb_name . '`' . $sqlwhere;
		if (!is_null($where)) {
			$q = self::$db->query($sql);
		} else {
			show_error("sql error: $sql");
		}
			
		$t2 = microtime(true);

		self::show_mysql($sql, $t1, $t2);

		return $q;
	}
	
	/**
	 * 修改数据，参数为必须
	 * @param string|array $data
	 * @param string $where WHERE 条件以及后续语句
	 * @return bool $q 只有语句执行成功就返回 true
	 */
	public function update($data = array(), $where = null) {
		$t1 = microtime(true);
		$sql = '';
		$q = false;
		
		$sqlwhere       = ' WHERE ' . trim($where);

		$update_data = '';
		
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$update_data .= ", `{$k}` = " . check_input($v);
			}
		} else {
			$update_data = $data;
		}
		
		$update_data = trim($update_data, ', ');
		
		$sql = 'UPDATE `' . self::$tb_name . '` SET ' . $update_data . $sqlwhere;

		if (!is_null($where)) {
			$q = self::$db->query($sql);
		} else {
			show_error("sql error: $sql");
		}
		
		$t2 = microtime(true);
		
		self::show_mysql($sql, $t1, $t2);

		return $q;
	}
	
	/**
	 * 查询数据
	 * @param string|array $field
	 * @param string $where WHERE 条件，及limit等片段
	 * @return array $ret
	 */
	public function select($field = '*', $where = null) {
		$t1 = microtime(true);
		
		$ret       = array();
		$field_new = '';
		if (is_array($field)) {
			foreach ($field as $f) {
				$field_new .= ', `' . $f . '`';
			}
		} else {
			$field_new = $field;
		}
		$field_new = trim($field_new, ',');
		
		is_null($where) or $where = ' WHERE ' . trim($where);
		
		$sql = 'SELECT ' . $field_new . ' FROM `' . self::$tb_name . '`' . $where;
		
		if (self::$dbS) {
			$q = self::$dbS->query($sql);
		} else {
			$q = self::$db->query($sql);
		}
		
		if ($q && $q->num_rows > 0) {
			//mysqli_result::fetch_assoc
			while (null != ($r = $q->fetch_assoc())) {
				$ret[] = $r;
			}
			$q->close();
		}

		$t2 = microtime(true);
		
		self::show_mysql($sql, $t1, $t2);

		return $ret;
	}
	
	/**
	 * 查询一行数据
	 * @param string|array $field
	 * @param string $where
	 * @return array $ret
	 */
	public function select_line($field = '*', $where = null) {
		$t1 = microtime(true);
		
		$ret       = array();
		$field_new = '';
		if (is_array($field)) {
			foreach ($field as $f) {
				$field_new .= ', `' . $f . '`';
			}
		} else {
			$field_new = $field;
		}
		$field_new = trim($field_new, ',');
		
		is_null($where) or $where = ' WHERE ' . trim($where);
		
		$sql = 'SELECT ' . $field_new . ' FROM `' . self::$tb_name . '`' . $where . ' LIMIT 1';
		
		//仅仅查询一条数据
		// 		$sql = preg_replace('/limit.*/i', 'LIMIT 1', $sql);
		
		self::show_mysql($sql);
		
		if (self::$dbS) {
			$q = self::$dbS->query($sql);
		} else {
			$q = self::$db->query($sql);
		}
		
		if ($q && $q->num_rows > 0) {
			//mysqli_result::fetch_assoc
			while (null != ($r = $q->fetch_assoc())) {
				$ret = $r;
			}
			$q->close();
		}
		
		$t2 = microtime(true);
		
		self::show_mysql($sql, $t1, $t2);

		return $ret;
	}
	
	/**
	 * 提供原生的查询接口，并且自动设置主从
	 * 建议使用该函数或者使用原生的 Model::$db->query()
	 * @param string $sql
	 */
	public function query($sql) {
		$t1 = microtime(true);

		$ret = array();

		if (preg_match('/^select /i', $sql)) {
			if (self::$dbS) {
				$ret = self::$dbS->query($sql);
			} else {
				$ret = self::$db->query($sql);
			}
		}

		$t2 = microtime(true);

		self::show_mysql($sql, $t1, $t2);
		return $ret;
	}
	
	/**
	 * 是否显示 sql
	 * @param string $sql
	 */
	public static function show_mysql($sql, $t1 = 0, $t2 = 0) {
		if (self::$show_sql) {
			$time = self::timer($t1, $t2);
			echo "$sql<br>\n$time<br>\n";
		}
	}

	/**
	 * 计时器，从 $t1 到 $t2 消耗的时间
	 */
	public static function timer($t1 = 0, $t2 = 0) {
		$t = $t2 - $t1;
		if ($t > 1) {
			$t = "<span style='color:red'>$t seconds</span>";
		} else {
			$t = "<span style='color:green'>$t seconds</span>";
		}
		return $t;
	}
	
	/**
	 * 关闭数据库
	 */
	public function __destruct() {
		//echo "model destructed!<br>\r\n";
		if (self::$db->error) {
			
			if (self::$db->error) {
				show_error(self::$db->error);
			}
			self::$db->close();
		}
		
		if (self::$dbS) {
			self::$dbS->close();
		}
	}
}
