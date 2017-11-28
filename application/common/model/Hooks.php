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
 * 友情链接类
 * @author molong <register@qinfo360.com>
 */
class Hooks extends Base {

	public $keyList = array(
		array('name' => 'name', 'title' => 'hook_name', 'type' => 'text', 'help' => ''),
		array('name' => 'description', 'title' => 'hook_description', 'type' => 'text', 'help' => ''),
		array('name' => 'type_text', 'title' => 'hook_type', 'type' => 'select', 'help' => ''),
		array('name' => 'addons', 'title' => 'hook_sort', 'type' => 'kanban'),
	);

	public function initialize() {
		parent::initialize();
		foreach ($this->keyList as $key => $value) {
			if ($value['name'] == 'type_text') {
				$value['option'] = \think\Config::get('hooks_type');				
				foreach ($value['option'] as &$item) {
					$item = lang($item);
				}
			}
			$this->keyList[$key] = $value;
		}
	}

	protected function setAddonsAttr($value) {
		if ($value) {
			$string = implode(",", $value);
			return $string;
		}
	}

	protected function getTypeTextAttr($value, $data) {
		$hooks_type = config('hooks_type');
		return $hooks_type[$data['type']];
	}

	/**
	 * 处理钩子挂载插件排序
	 */
	public function getaddons($addons = '') {
		if ($addons) {
			$hook_list = explode(',', $addons);
			foreach ($hook_list as $key => $value) {
				$field_list[] = array('id' => $value, 'title' => $value, 'name' => $value, 'is_show' => 1);
			}
			$option[1] = array('name' => lang('sort'), 'list' => $field_list);
		} else {
			$option[] = array('name' => lang('sort'), 'list' => array());
		}
		foreach ($this->keyList as $key => $value) {
			if ($value['name'] == 'addons') {
				$value['option'] = $option;
			}
			$keylist[] = $value;
		}
		return $keylist;
	}

	public function addHooks($addons_name) {
		$addons_class = get_addon_class($addons_name); //获取插件名
		if (!class_exists($addons_class)) {
			$this->error = lang('Plug_in_file_does_not_exist',array('addons_name' => $addons_name));
			return false;
		}
		$methods = array_diff(get_class_methods($addons_class), get_class_methods('\app\common\controller\Addons'));
		$methods = array_diff($methods, array('install', 'uninstall'));
		foreach ($methods as $item) {
			$info = $this->where('name', $item)->find();
			if (null == $info) {
				$save = array(
					'name'        => $item,
					'description' => '',
					'type'        => 1,
					'addons'      => array($addons_name),
					'update_time' => time(),
					'status'      => 1,
				);
				self::create($save);
			} else {
				if ($info['addons']) {
					$addons = explode(',', $info['addons']);
					array_push($addons, $addons_name);
				} else {
					$addons = array($addons_name);
				}
				$this->where('name', $item)->setField('addons', implode(',', $addons));
			}
		}
		return true;
	}

	public function removeHooks($addons_name) {
		$addons_class = get_addon_class($addons_name); //获取插件名
		if (!class_exists($addons_class)) {
			$this->error = lang('Plug_in_file_does_not_exist',array('addons_name' => $addons_name));
			return false;
		}
		$row = $this->where(array('addons' => array('like', '%' . $addons_name . '%')))->select();
		foreach ($row as $value) {
			if ($addons_name === $value['addons']) {
				$this->where('id', $value['id'])->delete();
			} else {
				$addons = explode(',', $value['addons']);
				$key    = array_search($addons_name, $addons);
				if ($key) {
					unset($value['addons'][$key]);
					$addons = implode(',', $addons);
					$this->where('id', $value['id'])->setField('addons', $addons);
				}
			}
		}
		return true;
	}
}