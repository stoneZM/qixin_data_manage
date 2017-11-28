<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\common\controller\Admin;

class Addons extends Admin {

	protected $addons;

	public function _initialize() {
		parent::_initialize();
		//加入菜单
		$this->getAddonsMenu();
		$this->addons = model('Addons');
		$this->hooks  = db('Hooks');
	}
	/**
	 * 插件列表
	 */
	public function index($refresh = 0) {
		if ($refresh) {
			$this->addons->refresh();
		}
		$type = input('type', 'all');
        
		
		$list = $this->addons->order('id desc')->paginate(25);
		
		if ($type == 'yes') {//已安装的
            foreach ($list as $key => $value) {
                if ($value['isinstall'] != 1) {
                    unset($list[$key]);
                }
            }
        } else if ($type == 'no') {
            foreach ($list as $key => $value) {
                if ($value['isinstall'] == 1) {
                    unset($list[$key]);
                }
            }
        } else {
            $type = 'all';
        }
		
		
		// 记录当前列表页的cookie
		Cookie('__forward__', $_SERVER['REQUEST_URI']);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign('type', $type);
		$this->setMeta(lang('plugin_manage'));
		$this->assign($data);
		return $this->fetch();
	}

	//创建向导首页
	public function add() {
		if (IS_POST) {
			$data = $this->addons->create();
			if ($data) {
				if ($result) {
					return $this->success(lang('create').lang('success'), url('admin/addons/index'));
				} else {
					return $this->error(lang('create').lang('fail'));
				}
			} else {
				return $this->error($this->addons->getError());
			}
		} else {
			$hooks = db('Hooks')->field('name,description')->select();
			$this->assign('Hooks', $hooks);
			$hook = db('Hooks')->field(true)->select();
			foreach ($hook as $key => $value) {
				$addons_opt[$value['name']] = $value['name'];
			}
			$addons_opt = array(array('type' => 'select', 'opt' => $addons_opt));
			if (!is_writable(QINFO_ADDON_PATH)) {
				return $this->error(lang('You_cannot_use_this_function_because_you_do_not_create_directory_write_permissions'));
			}
			$this->setMeta(lang('add_plugin'));
			return $this->fetch();
		}
	}

	//预览
	public function preview($output = true) {
	}

	/**
	 * 安装插件
	 */
	public function install() {
		$addon_name = input('addon_name', '', 'trim,ucfirst');
		$class      = get_addon_class($addon_name);
		if (class_exists($class)) {
			$addons = new $class;
			$info   = $addons->info;
			if (!$info || !$addons->checkInfo()) {
				//检测信息的正确性
				return $this->error(lang('Missing_information'));
			}
			session('addons_install_error', null);
			$install_flag = $addons->install();
			if (!$install_flag) {
				return $this->error(lang('Failed_to_perform_pre_install_operation') . session('addons_install_error'));
			}
			$result = $this->addons->install($info);
			if ($result) {
				cache('hooks', null);
				return $this->success(lang('install').lang('success'));
			} else {
				return $this->error($this->addons->getError());
			}
		} else {
			return $this->error(lang('Plugin_does_not_exist'));
		}
	}

	/**
	 * 卸载插件
	 */
	public function uninstall($id) {
		$result = $this->addons->uninstall($id);
		if ($result === false) {
			return $this->error($this->addons->getError(), '');
		} else {
			return $this->success(lang('uninstall').lang('success'));
		}
	}

	/**
	 * 启用插件
	 */
	public function enable() {
		$id = input('id');
		cache('hooks', null);
		$model  = model('Addons');
		$result = $model::where(array('id' => $id))->update(array('status' => 1));
		if ($result) {
			return $this->success(lang('enable').lang('success'));
		} else {
			return $this->error(lang('enable').lang('fail'));
		}
	}

	/**
	 * 禁用插件
	 */
	public function disable() {
		$id = input('id');
		cache('hooks', null);
		$model  = model('Addons');
		$result = $model::where(array('id' => $id))->update(array('status' => 0));
		if ($result) {
			return $this->success(lang('disable').lang('success'));
		} else {
			return $this->error(lang('disable').lang('fail'));
		}
	}

	/**
	 * 设置插件页面
	 */
	public function config() {
		if (IS_POST) {
			# code...
		} else {
			$id = input('id', '', 'trim,intval');
			if (!$id) {
				return $this->error(lang('illegal_operation'));
			}
			$info = $this->addons->find($id);
			if (!empty($info)) {
				$class = get_addon_class($info['name']);

				$keyList = array();
				$data    = array(
					'keyList' => $keyList,
				);
				$this->assign($data);
				$this->setMeta($info['title'] . " - ".lang('Set_up'));
				return $this->fetch('common@public/edit');
			} else {
				return $this->error(lang('This_plugin_is_not_installed'));
			}
		}
	}

	/**
	 * 获取插件所需的钩子是否存在，没有则新增
	 * @param string $str  钩子名称
	 * @param string $addons  插件名称
	 * @param string $addons  插件简介
	 */
	public function existHook($str, $addons, $msg = '') {
		$hook_mod      = db('Hooks');
		$where['name'] = $str;
		$gethook       = $hook_mod->where($where)->find();
		if (!$gethook || empty($gethook) || !is_array($gethook)) {
			$data['name']        = $str;
			$data['description'] = $msg;
			$data['type']        = 1;
			$data['update_time'] = time();
			$data['addons']      = $addons;
			if (false !== $hook_mod->create($data)) {
				$hook_mod->add();
			}
		}
	}

	/**
	 * 删除钩子
	 * @param string $hook  钩子名称
	 */
	public function deleteHook($hook) {
		$model     = db('hooks');
		$condition = array(
			'name' => $hook,
		);
		$model->where($condition)->delete();
		S('hooks', null);
	}

	/**
	 * 钩子列表
	 */
	public function hooks() {

		$map   = array();
		$order = "id desc";
		$list  = model('Hooks')->where($map)->order($order)->paginate(10);

		// 记录当前列表页的cookie
		Cookie('__forward__', $_SERVER['REQUEST_URI']);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->setMeta(lang('hook_list'));
		$this->assign($data);
		return $this->fetch();
	}

	public function addhook() {
		$hooks = model('Hooks');
		if (IS_POST) {
			$result = $hooks->change();
			if ($result !== false) {
				return $this->success(lang('add').lang('success'));
			} else {
				return $this->error($hooks->getError());
			}
		} else {
			$keylist = $hooks->getaddons();
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
			}
			$data    = array(
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('add_hook'));
			return $this->fetch('common@public/edit');
		}
	}

	//钩子出编辑挂载插件页面
	public function edithook($id) {
		$hooks = model('Hooks');
		if (IS_POST) {
			$result = $hooks->change();
			if ($result !== false) {
				return $this->success(lang('edit').lang('success'));
			} else {
				return $this->error($hooks->getError());
			}
		} else {
			$info    = db('Hooks')->find($id);
			$keylist = $hooks->getaddons($info['addons']);
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
			}
		
		
			$data    = array(
				'info'    => $info,
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('edit_hook'));
			return $this->fetch('common@public/edit');
		}
	}

	//超级管理员删除钩子
	public function delhook() {
		$id        = $this->getArrayParam('id');
		$map['id'] = array('IN', $id);
		$result    = $this->hooks->where($map)->delete();
		if ($result !== false) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}

	public function updateHook() {
		$hookModel = D('Hooks');
		$data      = $hookModel->create();
		if ($data) {
			if ($data['id']) {
				$flag = $hookModel->save($data);
				if ($flag !== false) {
					S('hooks', null);
					$this->success(lang('update').lang('success'), Cookie('__forward__'));
				} else {
					$this->error(lang('update').lang('fail'));
				}
			} else {
				$flag = $hookModel->add($data);
				if ($flag) {
					S('hooks', null);
					$this->success(lang('add').lang('success'), Cookie('__forward__'));
				} else {
					$this->error(lang('add').lang('fail'));
				}
			}
		} else {
			$this->error($hookModel->getError());
		}
	}
}