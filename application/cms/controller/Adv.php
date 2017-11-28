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

class Adv extends Admin {

	protected $ad;
	protected $adplace;

	public function _initialize() {
		parent::_initialize();
		$this->ad      = db('Ad');
		$this->adplace = db('AdPlace');
	}
	/**
	 * 插件列表
	 */
	public function index() {
		$map   = array();
		$order = "id desc";

		$list = db('AdPlace')->where($map)->order($order)->paginate(10);
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('advertising_position'));
		return $this->fetch();
	}

	/**
	 * 广告位添加
	 */
	public function add() {
		$place = model('AdPlace');
		if (IS_POST) {
			$result = $place->change();
			if ($result !== false) {
				return $this->success(lang('add').lang('success'));
			} else {
				return $this->error($place->getError());
			}
		} else {
			
			$keylist = $place->keyList;
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
			$this->setMeta(lang('add').lang('advertising_position'));
			return $this->fetch('common@public/edit');
		}
	}

	public function edit($id = null) {
		$place = model('AdPlace');
		if (IS_POST) {
			$result = $place->change();
			if ($result) {
				return $this->success(lang('edit').lang('success'), url('adv/index'));
			} else {
				return $this->error($this->adplace->getError());
			}
		} else {
			$info = db('AdPlace')->where(array('id' => $id))->find();
			if (!$info) {
				return $this->error(lang('illegal_operation'));
			}
			$keylist = $place->keyList;
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
				'info'    => $info,
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('edit').lang('advertising_position'));
			return $this->fetch('common@public/edit');
		}
	}

	public function del() {
		$id = $this->getArrayParam('id');

		if (empty($id)) {
			return $this->error(lang('illegal_operation'));
		}
		$map['id'] = array('IN', $id);
		$result    = $this->adplace->where($map)->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}

	public function lists($id = null) {
		$map['place_id'] = $id;
		$order           = "id desc";

		$list = db('Ad')->where($map)->order($order)->paginate(10);
		$data = array(
			'id'   => $id,
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('advertising_manage'));
		return $this->fetch();
	}

	public function addad($id) {
		$ad = model('ad');
		if (IS_POST) {
			$result = $ad->change();
			if ($result) {
				return $this->success(lang('add').lang('success'), url('adv/lists', array('id' => $this->param['place_id'])));
			} else {
				return $this->error($ad->getError());
			}
		} else {
			$info['place_id'] = $id;
			
			
			$keylist = $ad->keyList;
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
			
			$data             = array(
				'info'    => $info,
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('add').lang('Advertisement'));
			return $this->fetch('common@public/edit');
		}
	}

	public function editad($id = null) {
		$ad = model('ad');
		if (IS_POST) {
			$result = $ad->change();
			if ($result) {
				return $this->success(lang('edit').lang('success'), url('adv/lists', array('id' => $this->param['place_id'])));
			} else {
				return $this->error($ad->getError());
			}
		} else {
			$info = db('ad')->where(array('id' => $id))->find();
			if (!$info) {
				return $this->error(lang('illegal_operation'));
			}
			$keylist = $ad->keyList;
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
				'info'    => $info,
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('edit').lang('Advertisement'));
			return $this->fetch('common@public/edit');
		}
	}

	public function delad() {
		$id = $this->getArrayParam('id');

		if (empty($id)) {
			return $this->error(lang('illegal_operation'));
		}
		$map['id'] = array('IN', $id);
		$result    = db('ad')->where($map)->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}
}