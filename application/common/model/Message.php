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
 * 用户组模型类
 * Class AuthGroupModel
 * @author molong <register@qinfo360.com>
 */
class Message extends \app\common\model\Base {

	protected $auto   = array('status', 'update_time');
	protected $insert = array('create_time');

	protected function setStatusAttr($value) {
		return $value ? $value : 0;
	}

	protected function setIsinstallAttr($value) {
		return $value ? $value : 0;
	}

	protected function getStatusTextAttr($value, $data) {
		return $data['status'] ? "启用" : "禁用";
	}

	protected function getUninstallAttr($value, $data) {
		return 0;
	}




	public function build() {

	}
}