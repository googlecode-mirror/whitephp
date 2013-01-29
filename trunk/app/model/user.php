<?php defined('INDEX_PAGE') or die('no entrance'); ?>
<?php
/**
 * 模型示例
 * 建议将对数据库的操作作为一个属性来处理，以便获得更好的扩展性
 * 
 * filename:	user.php
 * charset:		UTF-8
 * create date: 2012-8-14
 * 
 * @author Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2012 Zhao Binyan
 * @link http://yungbo.com
 * @link http://weibo.com/itbudaoweng
 */

class User {
	
	//数据库操作基类实例
	public $db;
	
	public function __construct() {
		$this->db = Model::singleton($tb_name = 'user', $db_group = 'default');
	}

	public function get_row($id='1') {
		$where = 'id='.(int)$id;
		return $this->db->select_line('*', $where);
	}
}
