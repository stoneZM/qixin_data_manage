<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\cms\controller;
use app\common\controller\Admin;

class Link extends Admin {

	public function index() {
		$map = array();

		$order = "id desc";
		$list  = db('Link')->where($map)->order($order)->paginate(10);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('links'));
		return $this->fetch();
	}

	//添加
	public function add() {
		$link = model('Link');
		if (IS_POST) {
			$data = input('post.');
			if ($data) {
				unset($data['id']);
				$result = $link->save($data);
				if ($result) {
					return $this->success(lang('add').lang('success'), url('Link/index'));
				} else {
					return $this->error($link->getError());
				}
			} else {
				return $this->error($link->getError());
			}
		} else {
			
			$keylist = $link->keyList;
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
				if($item['help']){
					$item['help'] = lang($item['help']);	
				}
				if($item['option']){
					foreach ($item['option'] as $o_key => &$o_value) {
						$o_value = lang($o_value);
					}	
				}
			}
			
			
			$data = array(
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('add').lang('links'));
			return $this->fetch('common@public/edit');
		}
	}

	//修改
	public function edit() {
		$link = model('Link');
		$id   = input('id', '', 'trim,intval');
		if (IS_POST) {
			$data = input('post.');
			if ($data) {
				$result = $link->save($data, array('id' => $data['id']));
				if ($result) {
					return $this->success(lang('edit').lang('success'), url('Link/index'));
				} else {
					return $this->error(lang('edit').lang('fail'));
				}
			} else {
				return $this->error($link->getError());
			}
		} else {
			$map  = array('id' => $id);
			$info = db('Link')->where($map)->find();
			$keylist = $link->keyList;
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
				if($item['help']){
					$item['help'] = lang($item['help']);	
				}
				if($item['option']){
					foreach ($item['option'] as $o_key => &$o_value) {
						$o_value = lang($o_value);
					}	
				}
			}
			
			$data = array(
				'keyList' => $keylist,
				'info'    => $info,
			);
			$this->assign($data);
			$this->setMeta(lang('edit').lang('links'));
			return $this->fetch('common@public/edit');
		}
	}

	//删除
	public function delete() {
		$id = $this->getArrayParam('id');
		if (empty($id)) {
			return $this->error(lang('illegal_operation'));
		}
		$link = db('Link');

		$map    = array('id' => array('IN', $id));
		$result = $link->where($map)->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}
}