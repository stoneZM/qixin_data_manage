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
class Addons extends \app\common\model\Base {

	protected $auto   = array('status', 'isinstall', 'update_time');
	protected $insert = array('create_time');

	protected function setStatusAttr($value) {
		return $value ? $value : 0;
	}

	protected function setIsinstallAttr($value) {
		return $value ? $value : 0;
	}

	protected function getStatusTextAttr($value, $data) {
		return $data['status'] ? lang('enable') : lang('disable');
	}

	protected function getUninstallAttr($value, $data) {
		return 0;
	}

	/**
	 * 更新插件列表
	 * @param string $addon_dir
	 */
	public function refresh($addon_dir = '') {
		if (!$addon_dir) {
			$addon_dir = QINFO_ADDON_PATH;
		}
		$dirs = array_map('basename', glob($addon_dir . '*', GLOB_ONLYDIR));
		if ($dirs === FALSE || !file_exists($addon_dir)) {
			$this->error = lang('The_plugin_directory_is_unreadable_or_does_not_exist');
			return FALSE;
		}
		$where['name'] = array('in', $dirs);
		$addons        = $this->where($where)->column('*', 'name');
		
		foreach ($dirs as $value) {
			$value = ucfirst($value);
			if (!isset($addons[$value])) {
				$class = get_addon_class($value);
				if (!class_exists($class)) {
					// 实例化插件失败忽略执行
					\think\Log::record(lang('Plug_in_file_does_not_exist',array('value' => $value)));
					continue;
				}
				$obj            = new $class;
				$save = $obj->info;
				$save['create_time'] = time();
				self::create($save);
			}
		}
	}

	/**
	 * 获取插件的后台列表
	 */
	public function getAdminList() {
		$admin     = array();
		$db_addons = db('Addons')->where("status=1 AND has_adminlist=1")->field('title,name')->select();
		if ($db_addons) {
			foreach ($db_addons as $value) {
				$admin[] = array('title' => $value['title'], 'url' => "Addons/adminList?name={$value['name']}");
			}
		}
		return $admin;
	}

	public function install($data) {
		if ($data) {
			$info = $this->where('name', $data['name'])->value('id');
			if ($info) {
				$result = $this->save(array('isinstall'=>1, 'status'=>1), array('id'=>$info));
			}else{
				$result = false;
			}
			if (false !== $result) {
				return model('Hooks')->addHooks($data['name']);
			}else{
				return false;
			}
		} else {
			return false;
		}
	}

	public function uninstall($id) {
		$info = $this->get($id);
		if (!$info) {
			$this->error = lang('Without_this_plugin');
			return false;
		}
		$class = get_addon_class($info['name']);
		if (class_exists($class)) {
			//插件卸载方法
			$addons = new $class;
			if (!method_exists($addons, 'uninstall')) {
				$this->error = lang('Plug_in_unloading_method');
				return false;
			}
			$result = $addons->uninstall();
			if ($result) {
				//卸载挂载点中的插件
				$result = model('Hooks')->removeHooks($info['name']);
				//删除插件表中数据
				$this->where(array('id' => $id))->delete();
				return true;
			} else {
				$this->error = lang('Unable_to_uninstall_plug-ins');
				return false;
			}
		}
	}

	public function build() {

	}
}