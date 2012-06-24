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
 * @todo 完善prepare函数，防止注入攻击
 * @author Think
 *
 */
class Model {
	//数据库资源，可以这样访问函数  xx->db->query();
	//可以直接 xx->db-> 访问原生的  mysqli 对象
	//通过 xx->get() 获取数据
	
	//连接之后的数据库资源
	public $db;
	
	//从数据库
	public $dbS;
	
	//数据表名
	private static $tb_name;
	
	//数据库组
	private static $db_group;
	
	//数据库配置数组
	private static $db_conf;
	
	/**
	 * 连接数据库
	 */
	public function __construct($tb_name = null, $db_group = 'default') {
		if (is_null($tb_name)) show_error('you must supply a table name when instant a model class!');
		self::$tb_name = $tb_name;
		self::$db_group = $db_group;
		$this->db = db_init(self::$db_group);
		
		self::$db_conf = get_conf('db_conf');
		
		//因为从数据库并不是必须连接的，所以先判断一下，如果没有配置则不进行尝试而不终止程序运行。
		if (array_key_exists('slave', self::$db_conf)) {
			$this->dbS = db_init('slave');			
		}
	}
	
	/**
	 * 增加数据
	 * array('id'=>'3', 'username'=>'胡锦涛')
	 * INSERT INTO `user` (`id`, `username`, `password`) 
	 * VALUES (NULL, '胡锦涛', '123456abc');
	 */
	public function add($data = array()) {
		//组合后的 sql 语句
		$sql = '';
		
		//组合后的字段
		$fields = '';
		//组合后的字段值
		$values = '';
		
		$array_keys = array_keys($data);
		$array_values = array_values($data);
		
		foreach ($array_keys as $v) {
			$fields .= ', `' . $v . '`';
		}
		
		foreach ($array_values as $v) {
			$values .= ', "' . $v . '"';
		}
		$fields = trim($fields, ', ');
		$values = trim($values, ', ');
		
		$sql .= 'INSERT INTO';
		$sql .= ' `' . self::$tb_name . '`';
		$sql .= ' (' . $fields . ') ';
		$sql .= ' VALUES (' . $values . ')';

		$q = $this->db->query($sql);

		return $q;
	}
	
	/**
	 * 删除数据，参数为必须
	 * @param string $where
	 * @return bool $q 只要语句正常执行了就会是 true
	 */
	public function delete($where = null) {
		
		if (is_null($where)) show_error('sql语句错误,条件不能为空 ');
		$where = ' WHERE ' . trim($where);
		
		$sql = 'DELETE FROM `' . self::$tb_name . '`' . $where;
		$q = $this->db->query($sql);
		return $q;
	}
	
	/**
	 * 修改数据
	 * @param string|array $data
	 * @param string $where
	 * @return bool $q 只有语句执行成功就返回 true
	 */
	public function update($data = array(), $where = null) {
		if (is_null($where)) show_error('sql语句错误,条件不能为空');
		$update_data = '';
		$where = ' WHERE ' . trim($where);
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$update_data .= ", `{$k}` = '{$v}'";
			}
		} else {
			$update_data = $data;
		}
		
		$update_data = trim($update_data, ', ');
		
		$sql = 'UPDATE `' . self::$tb_name . '` SET ' . $update_data . $where;
		
		$q = $this->db->query($sql);
		return $q;
	}
	
	/**
	 * 查询数据
	 * @param string|array $field
	 * @param string $where
	 * @return array $ret
	 */
	public function get($field = '*', $where = null) {
		$ret = array();
		$field_new = '';
		if (is_array($field)) {
			foreach ($field as $f) {
				$field_new .= ', `' . $f . '`';
			}
		} else {
			$field_new = $field;
		}
		$field_new = trim($field_new, ',');
		
		if (!is_null($where)) $where = ' WHERE ' . trim($where);
		
		$sql = 'SELECT ' . $field_new . ' FROM `' . self::$tb_name . '`' . $where;

		if ($this->dbS) {
			$q = $this->dbS->query($sql);
		} else {
			$q = $this->db->query($sql);
		}
		
		if ($q && $q->num_rows > 0) {
			while ($r = $q->fetch_array(MYSQLI_ASSOC)) {
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
	public function get_line($field = '*', $where = null) {
		$ret = array();
		$field_new = '';
		if (is_array($field)) {
			foreach ($field as $f) {
				$field_new .= ', `' . $f . '`';
			}
		} else {
			$field_new = $field;
		}
		$field_new = trim($field_new, ',');
	
		if (!is_null($where)) $where = ' WHERE ' . trim($where);
	
		$sql = 'SELECT ' . $field_new . ' FROM `' . self::$tb_name . '`' . $where . ' LIMIT 1';
		
		//仅仅查询一条数据
		$sql = preg_replace('/limit.*/i', 'LIMIT 1', $sql);
	
		if ($this->dbS) {
			$q = $this->dbS->query($sql);
		} else {
			$q = $this->db->query($sql);
		}
	
		if ($q && $q->num_rows > 0) {
			while ($r = $q->fetch_array(MYSQLI_ASSOC)) {
				$ret = $r;
			}
			$q->close();
		}
		return $ret;
	}
	
	/**
	 * 提供原生的查询接口
	 * @param unknown_type $sql
	 */
	public function query($sql) {
		return $this->dbS->query($sql);
	}
	
	/**
	 * 防注入攻击，替换 ?
	 */
	public function prepare() {
		
	}
	
	/**
	 * 关闭数据库
	 */
	public function __destruct() {
		if ($this->db) {
			if ($this->db->error) {
				show_error($this->db->error);
			}
			
			$this->db->close();
		}
		
		if ($this->dbS) {
			$this->dbS->close();
		}
	}
}

//end

/*---------------- 使用说明 -----------------*/
/*
1，可以直接实例化，并为参数提供表名即可返回一个数据库资源。
控制器某个方法中
$user = new Model('user');
$user_info = $user->get();
var_dump($user_info);

2，在模型文件夹建立模型文件，在书写 __construct() 函数时提供默认参数为表名，用 load_model() 函数加载之后，可以自由使用。
muser.php
class Muser extends Model {
	public function __construct($tb_name = 'user') {
		parent::__construct($tb_name);
	}
}

控制器某个方法中
$user = new Model('user');//如若不传递第二个参数，获取的是 default 组的数据库配置文件
$user = new Muser();
$user = new Muser('user');
$user_info = $user->get();
$info = $user->db->query();//利用原生资源
var_dump($user_info);

*/


// class Muser extends Model {
	
// 	public function __construct($tb_name = 'user') {
// 		parent::__construct($tb_name);
// 	}
	
// }

// // $user = new Model('user');
// $user = new Muser();
// $user_info = $user->get_line('*', '1 order by id asc');
// var_dump($user_info);

// die;
// echo __FILE__;die;