<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\mp\model;
use think\Model;

class Mp extends Model {

	protected $auto   = array('status');
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
	
	/**
	 * 获取公众号信息

	 */
	public function get_mp_info($mpid = '') {
		!$mpid && $mpid = get_mpid();
		if (!$mpid) {
			return false;
		}
		$map['id'] = $mpid;
		$mp_info = model('mp')->where($map)->find();
		return $mp_info;
	}
	
	
}