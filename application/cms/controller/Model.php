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

class Model extends Admin {

	public function _initialize() {
		parent::_initialize();
		$this->getContentMenu();
	}

	/**
	 * 模型管理首页
	 * @author huajie <banhuajie@163.com>
	 */
	public function index() {
		$map = array('status' => array('gt', -1));

		$order = "id desc";
		$list  = model('Model')->where($map)->order($order)->paginate(10);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);

		// 记录当前列表页的cookie
		Cookie('__forward__', $_SERVER['REQUEST_URI']);

		$this->assign($data);
		$this->setMeta(lang('model_manage'));
		return $this->fetch();
	}

	/**
	 * 新增页面初始化
	 * @author huajie <banhuajie@163.com>
	 */
	public function add() {

		//获取所有的模型
		$models = db('Model')->where(array('extend' => 0))->field('id,title')->select();

		$this->assign('models', $models);
		$this->setMeta(lang('add').lang('model'));
		return $this->fetch();
	}

	/**
	 * 编辑页面初始化
	 * @author huajie <banhuajie@163.com>
	 */
	public function edit() {
		$id = input('id', '', 'trim,intval');
		if (empty($id)) {
			return $this->error(lang('parameter_error'));
		}

		/*获取一条记录的详细数据*/
		$model = model('Model');
		$data  = $model::find($id);
		if (!$data) {
			return $this->error($Model->getError());
		}
		$data['attribute_list'] = empty($data['attribute_list']) ? '' : explode(",", $data['attribute_list']);

		// 是否继承了其他模型
		if ($data['extend'] == 1) {
			$map['model_id'] = array('IN', array($data['id'], $data['extend']));
		} else {
			$map['model_id'] = $data['id'];
		}
		$map['is_show'] = 1;
		$fields         = db('Attribute')->where($map)->select();

		// 梳理属性的可见性
		foreach ($fields as $key => $field) {
			if (!empty($data['attribute_list']) && !in_array($field['id'], $data['attribute_list'])) {
				$field['is_show'] = 0;
			}
			$field['group']           = -1;
			$field['sort']            = 0;
			$fields_tem[$field['id']] = $field;
		}

		// 获取模型排序字段
		$field_sort = json_decode($data['field_sort'], true);
		if (!empty($field_sort)) {
			foreach ($field_sort as $group => $ids) {
				foreach ($ids as $key => $value) {
					if (!empty($fields_tem[$value])) {
						$fields_tem[$value]['group'] = $group;
						$fields_tem[$value]['sort']  = $key;
					}
				}
			}
		}

		if (isset($fields_tem) && $fields_tem) {
			// 模型字段列表排序
			$fields = list_sort_by($fields_tem, "sort");
		}

		$this->assign('fields', $fields);
		$this->assign('info', $data);
		$this->setMeta(lang('edit').lang('model'));
		return $this->fetch();
	}

	/**
	 * 删除一条数据
	 * @author huajie <banhuajie@163.com>
	 */
	public function del() {
		$mdoel  = model('Model');
		$result = $mdoel->del();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error($mdoel->getError());
		}
	}

	/**
	 * 更新一条数据
	 * @author huajie <banhuajie@163.com>
	 */
	public function update() {
		$res = \think\Loader::model('Model')->change();
		if ($res['status']) {
			return $this->success($res['info'], url('index'));
		} else {
			return $this->error($res['info']);
		}
	}

	/**
	 * 更新数据
	 * @author colin <register@qinfo360.com>
	 */
	public function status() {
		//$map['id'] = $this->request->param('id');
		
		$map['id'] = $this->getArrayParam('id');
		if (null == $map['id']) {
			return $this->error(lang('parameter_error'));
		}
		$data['status'] = input('param.status');
		if (is_array($map['id'])) {
			//实现多条数据同时修改
			$map['id'] = array('IN', $map['id']);
			db('Model')->where($map)->update($data);
		} else {
			//实现单条数据数据修改
			$status         = db('Model')->where($map)->field('status')->find();
			$data['status'] = $status['status'] ? 0 : 1;
			db('Model')->where($map)->update($data);
			
		}
		return $this->success(lang('Set_up').lang('success'));
	}
}