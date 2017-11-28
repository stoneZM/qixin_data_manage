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

class Action extends Admin {

	/**
	 * 用户行为列表
	 * @author huajie <banhuajie@163.com>
	 */
	public function index() {
		$map = array('status' => array('gt', -1));

		$order = "id desc";
		//获取列表数据
		$list = model('Action')->where($map)->order($order)->paginate(10);

		// 记录当前列表页的cookie
		Cookie('__forward__', $_SERVER['REQUEST_URI']);
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('user_behavior'));
		return $this->fetch();
	}

	/**
	 * 新建用户行为
	 * @author colin <register@qinfo360.com>
	 */
	public function add() {
		$model = model('Action');
		if (IS_POST) {
			$data   = input('post.');
			$result = $model->save($data);
			if (false != $result) {
				action_log('add_action', 'Action', $result, session('user_auth.uid'));
				return $this->success(lang('add').lang('success'), url('index'));
			} else {
				return $this->error($model->getError());
			}
		} else {
			
			$keylist = $model->fieldlist;
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
				if($item['help']){
					$item['help'] = lang($item['help']);	
				}
				
				if($item['option']){
					foreach ($item['option'] as  &$option_item) {
						$option_item = lang($option_item);
					}
				}
			}
			$data = array(
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('add').lang('behavior'));
			return $this->fetch('common@public/edit');
		}
	}

	/**
	 * 编辑用户行为
	 * @author colin <register@qinfo360.com>
	 */
	public function edit($id = null) {
		$model = model('Action');
		if (IS_POST) {
			$data   = input('post.');
			$result = $model->save($data, array('id' => $data['id']));
			if ($result !== false) {
				action_log('edit_action', 'Action', $id, session('user_auth.uid'));
				return $this->success(lang('edit').lang('success'), url('index'));
			} else {
				return $this->error($model->getError());
			}
		} else {
			$info = $model::where(array('id' => $id))->find();
			if (!$info) {
				return $this->error(lang('illegal_operation'));
			}
			
			$keylist = $model->fieldlist;
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
				if($item['help']){
					$item['help'] = lang($item['help']);	
				}
				
				if($item['option']){
					foreach ($item['option'] as  &$option_item) {
						$option_item = lang($option_item);
					}
				}
			}
			$data = array(
				'info'    => $info,
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('edit').lang('behavior'));
			return $this->fetch('common@public/edit');
		}
	}

	/**
	 * 删除用户行为状态
	 * @author colin <register@qinfo360.com>
	 */
	public function del() {
		$id = $this->getArrayParam('id');
		if (empty($id)) {
			return $this->error(lang('illegal_operation'));
		}
		$map['id'] = array('IN', $id);
		$result    = db('Action')->where($map)->delete();
		if ($result) {
			action_log('delete_action', 'Action', $id, session('user_auth.uid'));
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}

	/**
	 * 修改用户行为状态
	 * @author colin <register@qinfo360.com>
	 */
	public function setstatus() {
		$id = $this->getArrayParam('id');
		if (empty($id)) {
			return $this->error(lang('illegal_operation'));
		}
		$status = input('param.status', '0', 'trim,intval');
		$message   = !$status ? lang('disable') : lang('enable');
		$map['id'] = array('IN', $id);
		$result    = db('Action')->where($map)->setField('status', $status);
		if ($result !== false) {
			action_log('setstatus_action', 'Action', $id, session('user_auth.uid'));
			return $this->success(lang('Set_up').lang('success'));
		} else {
			return $this->error(lang('Set_up').lang('success'));
		}
	}

	/**
	 * 行为日志列表
	 * @author huajie <banhuajie@163.com>
	 */
	public function log() {

		//获取列表数据
		$map['status'] = array('gt', -1);

		$order = "id desc";
		//获取列表数据
		$list = model('ActionLog')->where($map)->order($order)->paginate(10);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('behavior_log'));
		return $this->fetch();
	}
	/**
	 * 查看行为日志
	 * @author huajie <banhuajie@163.com>
	 */
	public function detail($id = 0) {
		$model = model('ActionLog');
		if (empty($id)) {
			return $this->error(lang('illegal_request'));
		}
		$info = $model::get($id);
		$info['title']       = get_action($info['action_id'], 'title');
		$info['user_id']     = get_username($info['user_id']);
		$info['action_ip']   = long2ip($info['action_ip']);
		$this->assign('data',$info);
		$this->setMeta(lang('detailed').lang('behavior_log'));
		return $this->fetch();
	}
	/**
	 * 删除日志
	 * @param mixed $id
	 * @author huajie <banhuajie@163.com>
	 */
	public function dellog() {
		$id = $this->getArrayParam('id');
		if (empty($id)) {
			return $this->error(lang('illegal_operation'));
		}
		$map['id'] = array('IN', $id);
		$res       = db('ActionLog')->where($map)->delete();
		if ($res !== false) {
			action_log('delete_actionlog', 'ActionLog', $id, session('user_auth.uid'));
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}
	/**
	 * 清空日志
	 */
	public function clear($id = '') {
		$res = db('ActionLog')->where('1=1')->delete();
		if ($res !== false) {
			//记录行为
			action_log('clear_actionlog', 'ActionLog', $id, session('user_auth.uid'));
			return $this->success(lang('log_empty').lang('success'));
		} else {
			return $this->error(lang('log_empty').lang('fail'));
		}
	}
}