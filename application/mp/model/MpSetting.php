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

class MpSetting extends Model {

	
	/**
	* 获取设置

	*/
	public function get_setting_info($name) {
		if (!$name) {
			return false;
		}
		$map['mpid'] = get_mpid();
		$map['name'] = $name;
		$setting = $this->where($map)->find();
		if (!$setting) {
			return false;
		}
		return $setting;
	}

	/**
	 * 获取所有的设置项

	 */
	public function get_settings() {
		$map['mpid'] = get_mpid();
		$results = $this->where($map)->select();
		if (!$results) {
			return false;
		}
		foreach ($results as $k => $v) {
			$settings[$v['name']] = $v['value'];
		}
		if (!$settings) {
			return false;
		}
		return $settings;
	}

	/**
	 * 添加设置项

	 */
	public function add_settings($settings) {
		foreach ($settings as $k => $v) {
			$setting = $this->get_setting_info($k);
			if ($setting) {
				$this->where(array('id'=>$setting['id']))->update(array('value'=>$v));
			} else {
				$data['mpid'] = get_mpid();
				$data['name'] = $k;
				$data['value'] = $v;
				$this->insert($data);
			}
		}
		return true;
	}
	
}