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

class Category extends Admin {

	public function _initialize() {
		parent::_initialize();
		$this->getContentMenu();
	}

	public function index() {
		$map  = array('status' => array('gt', -1));
		$list = db('Category')->where($map)->order('sort asc,id asc')->column('*', 'id');

		if (!empty($list)) {
			$tree = new \com\Tree();
			$list = $tree->toFormatTree($list);
		}

		$this->assign('tree', $list);
		$this->setMeta(lang('category_manage'));
		return $this->fetch();
	}

	/* 单字段编辑 */
	public function editable($name = null, $value = null, $pk = null) {
		if ($name && ($value != null || $value != '') && $pk) {
			db('Category')->where(array('id' => $pk))->setField($name, $value);
		}
	}

	/* 编辑分类 */
	public function edit($id = null, $pid = 0) {
		if (IS_POST) {
			$category = model('Category');
			//提交表单
			$result = $category->change();
			if (false !== $result) {
				//记录行为
				action_log('update_category', 'category', $id, session('user_auth.uid'));
				return $this->success(lang('edit').lang('success'), url('index'));
			} else {
				$error = $category->getError();
				return $this->error(empty($error) ? lang('unknown_error') : $error);
			}
		} else {
			$cate = '';
			if ($pid) {
				/* 获取上级分类信息 */
				$cate = db('Category')->find($pid);
				if (!($cate && 1 == $cate['status'])) {
					return $this->error(lang('The_specified_superior_classification_does_not_exist_or_is_disabled'));
				}
			}
			/* 获取分类信息 */
			$info = $id ? db('Category')->find($id) : '';

			$this->assign('info', $info);
			$this->assign('category', $cate);
			$this->setMeta(lang('edit').lang('type'));
			return $this->fetch();
		}
	}
	/* 新增分类 */
	public function add($pid = 0) {
		$Category = model('Category');

		if (IS_POST) {
			//提交表单
			$id = $Category->change();
			if (false !== $id) {
				action_log('update_category', 'category', $id, session('user_auth.uid'));
				return $this->success(lang('add').lang('success'), url('index'));
			} else {
				$error = $Category->getError();
				return $this->error(empty($error) ? lang('unknown_error') : $error);
			}
		} else {
			$cate = array();
			if ($pid) {
				/* 获取上级分类信息 */
				$cate = $Category->info($pid, 'id,name,title,status');
				if (!($cate && 1 == $cate['status'])) {
					return $this->error(lang('The_specified_superior_classification_does_not_exist_or_is_disabled'));
				}
			}
			/* 获取分类信息 */
			$this->assign('info', null);
			$this->assign('category', $cate);
			$this->setMeta(lang('add').lang('type'));
			return $this->fetch('edit');
		}
	}
	/**
	 * 删除一个分类
	 * @author huajie <banhuajie@163.com>
	 */
	public function remove($id) {
		if (empty($id)) {
			return $this->error(lang('parameter_error'));
		}
		//判断该分类下有没有子分类，有则不允许删除
		$child = db('Category')->where(array('pid' => $id))->field('id')->select();
		if (!empty($child)) {
			return $this->error(lang('Please_delete_the_sub_category_under_this_category_first'));
		}
		//判断该分类下有没有内容
		$document_list = db('Document')->where(array('category_id' => $id))->field('id')->select();
		if (!empty($document_list)) {
			return $this->error(lang('Please_delete_the_article_under_this_categoryincluding_recycle_bin'));
		}
		//删除该分类信息
		$res = db('Category')->where(array('id' => $id))->delete();
		if ($res !== false) {
			//记录行为
			action_log('update_category', 'category', $id, session('user_auth.uid'));
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}

	/**
	 * 操作分类初始化
	 * @param string $type
	 * @author huajie <banhuajie@163.com>
	 */
	public function operate($type = 'move', $from = '') {
		//检查操作参数
		if ($type == 'move') {
			$operate = lang('move');
		} elseif ($type == 'merge') {
			$operate = lang('merge');
		} else {
			return $this->error(lang('parameter_error'));
		}

		if (empty($from)) {
			return $this->error(lang('parameter_error'));
		}
		//获取分类
		$map  = array('status' => 1, 'id' => array('neq', $from));
		$list = db('Category')->where($map)->field('id,pid,title')->select();
		//移动分类时增加移至根分类
		if ($type == 'move') {
			//不允许移动至其子孙分类
			$list = tree_to_list(list_to_tree($list));

			$pid = db('Category')->getFieldById($from, 'pid');
			$pid && array_unshift($list, array('id' => 0, 'title' => lang('root_classification')));
		}

		$this->assign('type', $type);
		$this->assign('operate', $operate);
		$this->assign('from', $from);
		$this->assign('list', $list);
		$this->setMeta($operate . lang('type'));
		return $this->fetch();
	}
	/**
	 * 移动分类
	 * @author huajie <banhuajie@163.com>
	 */
	public function move() {
		$to   = input('post.to');
		$from = input('post.from');
		$res  = db('Category')->where(array('id' => $from))->setField('pid', $to);
		if ($res !== false) {
			return $this->success(lang('move').lang('success'), url('index'));
		} else {
			return $this->error(lang('move').lang('fail'));
		}
	}
	/**
	 * 合并分类
	 * @author huajie <banhuajie@163.com>
	 */
	public function merge() {
		$to    = input('post.to');
		$from  = input('post.from');
		$Model = model('Category');
		//检查分类绑定的模型
		$from_models = explode(',', $Model->getFieldById($from, 'model'));
		$to_models   = explode(',', $Model->getFieldById($to, 'model'));
		foreach ($from_models as $value) {
			if (!in_array($value, $to_models)) {
				return $this->error(lang('Please_bind_model_to_target_classification',array('module' => get_document_model($value, 'title'))));
			}
		}
		//检查分类选择的文档类型
		$from_types = explode(',', $Model->getFieldById($from, 'type'));
		$to_types   = explode(',', $Model->getFieldById($to, 'type'));
		foreach ($from_types as $value) {
			if (!in_array($value, $to_types)) {
				$types = config('document_model_type');
				return $this->error(lang('Please_bind_the_document_type_to_the_target_classification').'：' . $types[$value]);
			}
		}
		//合并文档
		$res = db('Document')->where(array('category_id' => $from))->setField('category_id', $to);

		if ($res !== false) {
			//删除被合并的分类
			$Model->delete($from);
			return $this->success(lang('merge').lang('success'), url('index'));
		} else {
			return $this->error(lang('merge').lang('fail'));
		}
	}

	public function status() {
		$id     = $this->getArrayParam('id');
		$status = input('status', '0', 'trim,intval');

		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}

		$map['id'] = array('IN', $id);
		$result    = db('Category')->where($map)->setField('status', $status);
		if ($result) {
			return $this->success(lang('Set_up').lang('success'));
		} else {
			return $this->error(lang('Set_up').lang('fail'));
		}
	}
}