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

class Seo extends Admin {

	protected $seo;
	protected $rewrite;

	public function _initialize() {
		parent::_initialize();
		$this->seo     = model('SeoRule');
		$this->rewrite = model('Rewrite');
	}

	public function index($page = 1, $r = 20) {
		//读取规则列表
		$map = array('status' => array('EGT', 0));

		$list = $this->seo->where($map)->order('sort asc')->paginate(10);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('seo_settings'));
		return $this->fetch();
	}

	public function add() {
		if (IS_POST) {
			$data   = $this->request->post();
			$result = $this->seo->save($data);
			if ($result) {
				return $this->success(lang('add').lang('success'),'index');
			} else {
				return $this->error(lang('add').lang('fail'));
			}
		} else {
			$keylist = $this->seo->keyList;
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
			}
			$data = array(
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('add').lang('rules'));
			return $this->fetch('common@public/edit');
		}
	}

	public function edit($id = null) {
		if (IS_POST) {
			$data   = $this->request->post();
			$result = $this->seo->save($data, array('id' => $data['id']));
			if (false !== $result) {
				return $this->success(lang('edit').lang('success'),'index');
			} else {
				return $this->error(lang('edit').lang('fail'));
			}
		} else {
			$id   = input('id', '', 'trim,intval');
			$info = $this->seo->where(array('id' => $id))->find();
			$keylist = $this->seo->keyList;
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
			}
			$data = array(
				'info'    => $info,
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('edit').lang('rules'));
			return $this->fetch('common@public/edit');
		}
	}

	public function del() {
		$id = $this->getArrayParam('id');
		if (empty($id)) {
			return $this->error(lang('illegal_operation'));
		}
		$result = $this->seo->where(array('id' => array('IN', $id)))->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}

	public function rewrite() {
		$list = db('Rewrite')->paginate(10);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('routing_rules'));
		return $this->fetch();
	}

	public function addrewrite() {
		if (IS_POST) {
			$result = model('Rewrite')->change();
			if (false != $result) {
				return $this->success(lang('add').lang('success'), url('admin/seo/rewrite'));
			} else {
				return $this->error(model('Rewrite')->getError());
			}
		} else {
			
			$keylist = $this->rewrite->keyList;
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
			}
			$data = array(
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('add').lang('routing_rules'));
			return $this->fetch('common@public/edit');
		}
	}

	public function editrewrite() {
		if (IS_POST) {
			$result = model('Rewrite')->change();
			if (false != $result) {
				return $this->success(lang('update').lang('success'), url('admin/seo/rewrite'));
			} else {
				return $this->error(model('Rewrite')->getError());
			}
		} else {
			$id   = input('id', '', 'trim,intval');
			$info = db('Rewrite')->where(array('id' => $id))->find();
			$keylist = $this->rewrite->keyList;
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
			}
			$data = array(
				'info'    => $info,
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('edit').lang('routing_rules'));
			return $this->fetch('common@public/edit');
		}
	}

	public function delrewrite() {
		$id = $this->getArrayParam('id');
		if (empty($id)) {
			return $this->error(lang('illegal_operation'));
		}
		$result = db('Rewrite')->where(array('id' => array('IN', $id)))->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}
}