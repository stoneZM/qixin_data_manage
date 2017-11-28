<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 菜单模型类
 * @author molong <register@qinfo360.com>
 */
class Menu extends \app\common\model\Base {

	protected $type = array(
		'id'  => 'integer',
	);

	public function getAuthNodes(){
		$rows = $this->db()->field('id,pid,group,title,url,module')->where($map)->order('id asc')->select();
		foreach ($rows as $key => $value) {
			$data[$value['id']] = $value;
		}
		foreach ($data as $key => $value) {
			if ($value['pid'] > 0) {
				$value['group'] = $data[$value['pid']]['title'];
				$list[] = $value;
			}
		}
		return $list;
	}
}