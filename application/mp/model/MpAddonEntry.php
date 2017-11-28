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

class MpAddonEntry extends Model {

	/**
	 * 检测入口是否存在

	 */
	protected function is_entry_exists($act) {
		if ($this->get_addon_entry($act)) {
			return true;
		}
		return false;
	}

	/**
	 * 获取功能入口

	 */
	public function get_addon_entry($act, $addon = '', $mpid = '') {
		if ($addon == '') {
			$addon = get_addon();
		}
		if ($mpid == '') {
			$mpid = get_mpid();
		}
		if (!$act || !$addon || !$mpid) {
			return false;
		}
		
		$addon_info_file = QINFO_MPADDON_PATH . get_addon() . '/config.php';
		if (is_file($addon_info_file)) {	// 如果插件信息文件存在
			$addon_info = include $addon_info_file;
		}
		if ($addon_info) {
			$addon_config = $addon_info;
		} else {
			$addon_config = model('MpAddons')->get_addon_config($addon);
			$addon_config = json_decode($addon_config, true);
		}
		if (!$addon_config || !$addon_config['entry'] || !$addon_config['entry_list']) {
			return false;
		}
		$entry_list = $addon_config['entry_list'];
		foreach ($entry_list as $k => $v) {
			if ($k == $act) {
				$map['mpid'] = $mpid;
				$map['addon'] = $addon;
				$map['act'] = $act;
				$entry = model('mp_addon_entry')->where($map)->find();
				$entry['rule'] = model('MpRule')->get_entry_rule($entry['id']);
				$entry['act'] = $act;
				$entry['name'] = $v;
				$entry['url'] = url('/mpaddon/'.$addon.'/mobile/'.$k.'/mpid/'.$mpid);
				break;
			}
		}
		return $entry;
	}

	/**
	 * 获取入口信息

	 */
	public function get_entry_info($entry_id) {
		if (!$entry_id) {
			return false;
		}
		$map['id'] = intval($entry_id);
		$entry_info = model('mp_addon_entry')->where($map)->find();
		if (!$entry_info['mpid'] || !$entry_info['addon'] || !$entry_info['act']) {
			return false;
		}
		return $entry_info;
	}
}

?>