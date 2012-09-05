<?php
/**
 * 数据库模型，建议继承此模型
 * 
 * filename:	model.php
 * charset:		UTF-8
 * create date: 2012-5-25
 * 
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
 * 于 2012-08-14 改用单例模式，调用方法发生变化
 * $model = Model::singleton();
 * $model->query();
 * $model::$db->query();
 * $model::$dbS->query();
 * 
 * 或者直接不用此模型类
 * $user = db_init();//默认使用主数据库
 * $user->query();
 * 
 * @todo 完善prepare函数，防止注入攻击
 * @author Think
 *
 */
class Model {
	//数据库资源，可以这样访问函数  xx::$db->query();
	//可以直接 xx::$db-> 访问原生的  mysqli 对象
	public static $singleton;
	
	//连接之后的数据库资源
	public static $db;
	
	//从数据库
	public static $dbS;
	
	//数据表名
	private static $tb_name;
	
	//数据库组
	private static $db_group;
	
	//数据库配置数组
	private static $db_conf;
	
	//是否输出 sql 语句
	private static $show_sql = false;
	
	/**
	 * 连接数据库
	 */
	private function __construct($tb_name = 'sample_table_name', $db_group = 'default') {
		//return self::singleton();
	}
	
	/**
	 * 题外话，单例并不能保证一定单例。。。
	 */
	public static function singleton($tb_name = 'sample_table_name', $db_group = 'default') {
		if (!isset(self::$singleton)) {
			// if (is_null($tb_name)) show_error('you must supply a table name when instant a model class!');
			self::$tb_name  = $tb_name;
			self::$db_group = $db_group;
			self::$db       = db_init(self::$db_group);
			
			self::$db_conf = get_conf('db_conf');
			
			//因为从数据库并不是必须连接的，所以先判断一下，如果没有配置则不进行尝试而不终止程序运行。
			if (array_key_exists('slave', self::$db_conf)) {
				self::$dbS = db_init('slave');
			}
			$c               = __CLASS__;
			self::$singleton = new $c;
		}
		return self::$singleton;
	}
	
	/**
	 * 增加数据
	 * $data
	 * array('id'=>'3', 'username'=>'胡锦涛')
	 * INSERT INTO `user` (`id`, `username`, `password`) 
	 * VALUES (NULL, '胡锦涛', '123456abc');
	 * $where WHERE 条件以及后续语句
	 */
	public function insert($data = array(), $where) {
		//组合后的 sql 语句
		$sql = '';
		
		//组合后的字段
		$fields = '';
		//组合后的字段值
		$values = '';
		
		$array_keys   = array_keys($data);
		$array_values = array_values($data);
		
		foreach ($array_keys as $v) {
			$fields .= ", ` $v `";
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
		
		$where = ' WHERE ' . trim($where);
		$sql .= $where;
		
		self::show_mysql($sql);
		
		$q = self::$db->query($sql);
		
		return $q;
	}
	
	/**
	 * 删除数据，参数为必须
	 * @param string $where WHERE 条件以及后续语句
	 * @return bool $q 只要语句正常执行了就会是 true
	 */
	public function delete($where = null) {
		if (is_null($where))
			show_error('sql语句错误,条件不能为空 ');
		$where = ' WHERE ' . trim($where);
		
		$sql = 'DELETE FROM `' . self::$tb_name . '`' . $where;
		
		self::show_mysql($sql);
		
		$q = self::$db->query($sql);
		return $q;
	}
	
	/**
	 * 修改数据
	 * @param string|array $data
	 * @param string $where WHERE 条件以及后续语句
	 * @return bool $q 只有语句执行成功就返回 true
	 */
	public function update($data = array(), $where = null) {
		if (is_null($where))
			show_error('sql语句错误,条件不能为空');
		$update_data = '';
		$where       = ' WHERE ' . trim($where);
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$update_data .= ", `{$k}` = '" . self::$db->real_escape_string($v) . "'";
			}
		} else {
			$update_data = $data;
		}
		
		$update_data = trim($update_data, ', ');
		
		$sql = 'UPDATE `' . self::$tb_name . '` SET ' . $update_data . $where;
		
		self::show_mysql($sql);
		
		$q = self::$db->query($sql);
		return $q;
	}
	
	/**
	 * 查询数据
	 * @param string|array $field
	 * @param string $where WHERE 条件，及limit等片段
	 * @return array $ret
	 */
	public function select($field = '*', $where = null) {
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
		
		if (!is_null($where))
			$where = ' WHERE ' . trim($where);
		
		$sql = 'SELECT ' . $field_new . ' FROM `' . self::$tb_name . '`' . $where;
		
		self::show_mysql($sql);
		
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
		return $ret;
	}
	
	/**
	 * 查询一行数据
	 * @param string|array $field
	 * @param string $where
	 * @return array $ret
	 */
	public function select_line($field = '*', $where = null) {
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
		
		if (!is_null($where))
			$where = ' WHERE ' . trim($where);
		
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
		return $ret;
	}
	
	/**
	 * 提供原生的查询接口，并且自动设置主从
	 * 强烈建议使用该函数或者使用原生的 xxx->db->query()
	 * @param unknown_type $sql
	 */
	public function query($sql) {
		self::show_mysql($sql);
		if (preg_match('/^select /i', $sql) && self::$dbS) {
			return self::$dbS->query($sql);
		} else {
			return self::$db->query($sql);
		}
	}
	
	/**
	 * 防注入攻击，替换 ?
	 * @deprecated
	 */
	// 	public function prepare() {
	
	// 	}
	
	public static function show_mysql($sql) {
		if (self::$show_sql) {
			echo $sql . "<br>\n";
		}
	}
	
	/**
	 * 关闭数据库
	 */
	public function __destruct() {
		if (self::$db) {
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
