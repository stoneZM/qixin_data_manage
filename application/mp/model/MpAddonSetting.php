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
use think\Session;
use think\Response;
use think\Request;
use think\Url;
use think\Db;

class MpAddonSetting extends Model {

	/**
	 * 获取插件所有配置参数

	 */
	public function get_addon_settings($addon = '', $mpid = '') {
		if ($addon == '') {
			$addon = get_addon();
		}
		if ($mpid == '') {
			$mpid = get_mpid();
		}
		if (!$addon || !$mpid) {
			return false;
		}

		$map['mpid'] = $mpid;
		$map['addon'] = $addon;
		$settings = model('mp_addon_setting')->where($map)->select();
		if (!$settings) {
			return false;
		}
		foreach ($settings as $k => $v) {
			$addon_settings[$v['name']] = $v['value'];
		}
		return $addon_settings;
	}

	/**
	 * 根据参数名获取参数信息

	 */
	public function get_addon_setting($name, $addon = '', $mpid = '') {
		if ($addon == '') {
			$addon = get_addon();
		}
		if ($mpid == '') {
			$mpid = get_mpid();
		}
		if (!$name || !$addon || !$mpid) {
			return false;
		}

		$map['name'] = $name;
		$map['mpid'] = $mpid;
		$map['addon'] = $addon;
		$setting = model('mp_addon_setting')->where($map)->find();
		if (!$setting) {
			return false;
		}
		return $setting;
	}

	/**
	 * 获取配置参数值

	 */
	public function get_setting_value($name, $addon = '', $mpid = '') {
		$setting = $this->get_addon_setting($name, $addon, $mpid);
		if (!$setting) {
			return false;
		}
		return $setting['value'];
	}
}

?>