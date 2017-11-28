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

class Group extends Admin {

	protected $model;
	protected $rule;

	public function _initialize() {
		parent::_initialize();
		$this->group = model('AuthGroup');
		$this->rule  = model('AuthRule');
	}

	//会员分组首页控制器
	public function index() {
		$list = db('AuthGroup')->where($map)->order('id desc')->paginate(10);
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('user_group'));
		return $this->fetch();
	}

	//会员分组添加控制器
	public function add() {
		if (IS_POST) {
			$result = $this->group->change();
			if ($result) {
				return $this->success(lang('add').lang('success'), url('admin/group/index'));
			} else {
				return $this->error(lang('add').lang('fail'));
			}
		} else {
			
			$keylist = $this->group->keyList;
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
				if($item['help']){
					$item['help'] = lang($item['help']);	
				}
			}
			
			$data = array(
				'info'    => array( 'status' => 1),
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('add').lang('user_group'));
			return $this->fetch('common@public/edit');
		}
	}

	//会员分组编辑控制器
	public function edit($id) {
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		if (IS_POST) {
			$result = $this->group->change();
			if ($result) {
				return $this->success(lang('edit').lang('success'), url('admin/group/index'));
			} else {
				return $this->error(lang('edit').lang('fail'));
			}
		} else {
			$info = $this->group->where(array('id' => $id))->find();
			
			$keylist = $this->group->keyList;
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
				if($item['help']){
					$item['help'] = lang($item['help']);	
				}
			}
			
			$data = array(
				'info'    => $info,
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('edit').lang('user_group'));
			return $this->fetch('common@public/edit');
		}
	}

	//会员分组编辑字段控制器
	public function editable() {
		$pk     = input('pk', '', 'trim,intval');
		$name   = input('name', '', 'trim');
		$value  = input('value', '', 'trim');
		$result = $this->group->where(array('id' => $pk))->setField($name, $value);
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}

	//会员分组删除控制器
	public function del() {
		$id = $this->getArrayParam('id');
		if (empty($id)) {
			return $this->error(lang('illegal_operation'));
		}
		$result = $this->group->where(array('id' => array('IN', $id)))->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}

	//权限节点控制器
	public function access() {
		
		$list = db('AuthRule')->where($map)->order('id desc')->paginate(15);
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('permission_node'));
		return $this->fetch();
	}

	//根据菜单更新节点
	public function upnode() {
		$rule = model('Menu')->getAuthNodes();
		$reuslt = $this->rule->uprule($rule);
		return $this->success(lang('update').lang('success'));
	}

	/**
	 * 授权
	 */
	public function auth($id) {
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		if (IS_POST) {
			$rule          = $this->request->post('rule/a', array());
			$extend_rule   = $this->request->post('extend_rule/a', array());
			$extend_result = $rule_result = false;
			//扩展权限
			$extend_data = array();
			foreach ($extend_rule as $key => $value) {
				foreach ($value as $item) {
					$extend_data[] = array('group_id' => $id, 'extend_id' => $item, 'type' => $key);
				}
			}
			if (!empty($extend_data)) {
				db('AuthExtend')->where(array('group_id' => $id))->delete();
				$extend_result = db('AuthExtend')->insertAll($extend_data);
			}
			if ($rule) {
				$rules       = implode(',', $rule);
				$rule_result = $this->group->where(array('id' => $id))->setField('rules', $rules);
			}

			if ($rule_result !== false || $extend_result !== false) {
				return $this->success(lang('auth').lang('success'), url('admin/group/index'));
			} else {
				return $this->error(lang('auth').lang('fail'));
			}
		} else {
			$group = $this->group->where(array('id' => $id))->find();
			$row           = db('AuthRule')->where($map)->order('id asc')->select();
			$list = array();
			foreach ($row as $key => $value) {
				
				$value['title'] = lang($value['title']);
				$value['group'] = lang($value['group']);
				
				$list[lang($value['group'])][] = $value;
			}
			//扩展权限
			$extend_auth = db('AuthExtend')->where(array('group_id' => $id, 'type' => 2))->column('extend_id');
			
	
			
			$data        = array(
				'list'        => $list,
				'extend_auth' => $extend_auth,
				'auth_list'   => explode(',', $group['rules']),
				'id'          => $id,
			);
			$this->assign($data);
			$this->setMeta(lang('auth'));
			return $this->fetch();
		}
	}

	public function addnode() {
		if (IS_POST) {
			$result = $this->rule->change();
			if ($result) {
				return $this->success(lang('create').lang('success'), url('admin/group/access'));
			} else {
				return $this->error($this->rule->getError());
			}
		} else {
			
			$keylist = $this->rule->keyList;
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
				if($item['help']){
					$item['help'] = lang($item['help']);	
				}
			}
			$data = array(
				'info'    => array('status' => 1),
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('add').lang('node'));
			return $this->fetch('common@public/edit');
		}
	}

	public function editnode($id) {
		if (IS_POST) {
			$result = $this->rule->change();
			if (false !== $result) {
				return $this->success(lang('update').lang('success'), url('admin/group/access'));
			} else {
				return $this->error(lang('update').lang('fail'));
			}
		} else {
			if (!$id) {
				return $this->error(lang('illegal_operation'));
			}
			
			$keylist = $this->rule->keyList;
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
				if($item['help']){
					$item['help'] = lang($item['help']);	
				}
			}
			$info = $this->rule->find($id);
			$data = array(
				'info'    => $info,
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('edit').lang('node'));
			return $this->fetch('common@public/edit');
		}
	}

	public function delnode($id) {
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		$result = $this->rule->where(array('id' => $id))->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}
}