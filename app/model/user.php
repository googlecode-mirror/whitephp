<?php defined('INDEX_PAGE') or die('no entrance'); ?>
<?php
/**
 * 模型示例
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

class User extends Model {
	/**
	 * 实例类，建议设置当前表名和数据库配置【非必须】
	 * 使用系统提供的方法，若使用系统函数（如insert,delete,update,select,select_line）
	 * 必须保证正确设置了表名即 $model = Model::singleton('user');
	 * @param unknown_type $tb_name
	 * @param unknown_type $db_group
	 */
	public static function singleton($tb_name = 'user', $db_group = 'default') {
		return parent::singleton($tb_name, $db_group);
	}
}