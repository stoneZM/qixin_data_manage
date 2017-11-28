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

class MpAddons extends Model {

	protected $auto   = array('status');

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
			$addon_dir = QINFO_MPADDON_PATH;
		}
		$dirs = array_map('basename', glob($addon_dir . '*', GLOB_ONLYDIR));
		if ($dirs === FALSE || !file_exists($addon_dir)) {
			$this->error = lang('The_plugin_directory_is_unreadable_or_does_not_exist');
			return FALSE;
		}
		
		$where['bzname'] = array('in', $dirs);
		$addons        = $this->where($where)->column('*', 'bzname');
		
		foreach ($dirs as $value) {
			$value = ucfirst($value);
			if (!isset($addons[$value])) {
				$class = get_mpaddon_class($value);
				if (!class_exists($class)) {
					// 实例化插件失败忽略执行
					\think\Log::record(lang('Plug_in_file_does_not_exist',array('value' => $value)));
					continue;
				}
				$obj = new $class;
				$save = $obj->info;
				
				$save['status'] = 0;
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
			$info = $this->where('bzname', $data['bzname'])->value('id');
			if ($info) {
				$data_config = get_mpaddon_config($data['bzname']);	
				if($data_config){
					$data_config = json_encode($data_config);	
				}
				return $this->save(array('status'=>1,'config'=>$data_config), array('id'=>$info));
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
		$class = get_mpaddon_class($info['bzname']);
		if (class_exists($class)) {
			//插件卸载方法
			$addons = new $class;
			if (!method_exists($addons, 'uninstall')) {
				$this->error = lang('Plug_in_unloading_method');
				return false;
			}
			$result = $addons->uninstall();
			if ($result) {
				//删除插件表中数据
				return  $this->save(array('status'=>0), array('id'=>$info['id']));
			} else {
				$this->error = lang('Unable_to_uninstall_plug-ins');
				return false;
			}
		}
	}

	public function build() {

	}
	
	
	
	/**
	 * 获取用户权限范围内的插件

	 */
	public function get_access_addons() {
		foreach ($installed_addons as $k => $v) {
			$arr['title'] = $v['name'];
			$arr['bzname'] = $v['bzname'];
			preg_match('/.*index.php/', $v['index_url'], $m);
			$arr['url'] = str_replace($m[0], SITE_URL.'index.php', $v['index_url']);
			$arr['class'] = '';
			
			$addon_info = $this->get_addon_info($v['bzname']);
			$arr['config'] = $addon_info['config'];
			$access_addons[] = $arr;
		}
		return $access_addons;
	}

	/**
	 * 判断插件是否禁用

	 */
	public function is_addon_forbidden($addon, $mpid) {
		$status = db('mp_addons_access')->where(array('addon'=>$addon,'mpid'=>$mpid))->value('status');
		if ($status == 2) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 获取已安装的插件

	 */
	public function get_installed_addons($type = '') {
		$map['status'] = 1;
		if ($type) {
			$map['type'] = $type;
		}
		$addons = model('mp_addons')->where($map)->field('id,name,bzname,type')->select();
		foreach ($addons as $k => &$v) {
			$addon_dir_info = $this->get_addon_dir_info($v['bzname']);
			$v['last_version'] = $addon_dir_info['version'];
			if ($addon_dir_info['config']['index_url']) {
				$v['index_url'] = $addon_dir_info['config']['index_url'];
			} elseif ($addon_dir_info['config']['respond_rule']) {
				// $v['index_url'] = U('Mp/Web/rule', array('addon'=>$v['bzname']));
				$v['index_url'] = url('/mpaddon/'.$v['bzname'].'/rule');
			} elseif ($addon_dir_info['config']['setting']) {
				// $v['index_url'] = U('Mp/Web/setting', array('addon'=>$v['bzname']));
				$v['index_url'] = url('/mpaddon/'.$v['bzname'].'/setting');
			} else {
				// $v['index_url'] = U('Mp/Web/index', array('addon'=>$v['bzname']));
				$v['index_url'] = url('/mpaddon/'.$v['bzname'].'/index');
			}
			if (!$v['last_version']) {
				unset($addons[$k]);
			}
		}
		if (!$addons) {
			return false;
		}
		return $addons;
	}

	/**
	 * 根据插件标识名获取插件信息

	 */
	public function get_addon_info_by_bzname($bzname) {
		if (!$bzname) {
			return false;
		}
		$map['bzname'] = $bzname;
		$addon_info = model('mp_addons')->where($map)->find();
		if (!$addon_info) {
			return false;
		}
		return $addon_info;
	} 

	/**
	 * 获取插件信息

	 */
	public function get_addon_info($addon='', $type='db') {
		if (empty($addon)) {
			$addon = get_addon();
		}
		if ($type == 'file') {				// 取插件信息文件里面的插件信息
			$info_path = QINFO_MPADDON_PATH . strtolower($addon) . DIRECTORY_SEPARATOR . 'config.php';
			if (!is_file($info_path)) {
				return false;
			}
			$addon_info = include $info_path;
			if ($addon_info['bzname'] != $addon) {
				return false;
			}
			if (!$addon_info['name'] || !$addon_info['version'] || !$addon_info['author']) {
				return false;
			}
			return $addon_info;
		} else {							// 取数据库addons表的插件信息
			$map['bzname'] = $addon;
			$addon_info = $this->where($map)->find();
			if (!$addon_info) {
				return false;
			}
			$addon_info['config'] = json_decode($addon_info['config'], true);
			return $addon_info;
		}
	}

	/**
	 * 获取插件配置信息

	 */
	public function get_addon_config($addon='') {

		if (empty($addon)) {
			$addon = get_addon();
		}
		$addon_info = $this->get_addon_info($addon);
		if (!$addon_info || empty($addon_info['config'])) {
			return false;
		}
		return $addon_info['config'];
	}

	/**
	 * 获取插件模型信息

	 */
	public function get_addon_model($model) {
		if (empty($model)) {
			return false;
		}
		$addon_info = $this->get_addon_info();
		if (empty($addon_info['model'][$model])) {
			return false;
		}
		return $addon_info['model'][$model];
	}

	/**
	 * 获取业务导航信息

	 */
	public function get_addon_menu($act, $addon = '') {
		if ($addon == '') {
			$addon = get_addon();
		}
		if (!$act || !$addon) {
			return false;
		}


		/*$map['bzname'] = $addon;
		$addon_info = model('mp_addons')->where($map)->find();
		
		$info_path = QINFO_MPADDON_PATH . strtolower($addon) . DIRECTORY_SEPARATOR . 'config.php';*/
		
		
		
		$addon_info = $this->get_addon_info_by_bzname($addon);
		$addon_config = $this->get_addon_config($addon);
		$addon_config = json_decode($addon_config, true);
		$menu_list = $addon_config['menu_list'];
		
		foreach ($menu_list as $k => $v) {
			if ($k == $act) {
				$menu['act'] = $k;
				$menu['title'] = $v;
				break;
			}
		}
		return $menu;		
	}

	/**
	 * 更新插件配置信息

	 */
	public function save_addon_config($config, $addon) {
		if (!$addon) {
			return false;
		}
		$map['bzname'] = $addon;
		$data['config'] = $config;
		model('mp_addons')->where($map)->update($data);
		return true;
	}

	/**
	 * 获取插件文件夹信息

	 */
	public function get_addon_dir_info($bzname) {
		if (!$bzname) {
			return false;
		}
		
		$map['bzname'] = $bzname;
		$addon_info = model('mp_addons')->where($map)->find();
		if($addon_info['config']){
			$addon_info['config'] = json_decode($addon_info['config'], true);
		}
		if ($addon_info['bzname'] != $bzname) {
			return false;
		}
		if (!$addon_info['name'] || !$addon_info['version'] || !$addon_info['author']) {
			return false;
		}
		return $addon_info;
	}
}