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

class Attribute extends Admin {

	//保存的Model句柄
	protected $model;
	protected $attr;

	//初始化
	public function _initialize() {
		parent::_initialize();
		$this->getContentMenu();
		$this->model = model('Attribute');
		//遍历属性列表
		foreach (get_attribute_type() as $key => $value) {
			$this->attr[$key] = $value[0];
		}
		$this->validate_rule = array(
			0            => lang('please_select'),
			'regex'      => lang('Regular_verification'),
			'function'   => lang('Function_verification'),
			'unique'     => lang('Only_verification'),
			'length'     => lang('Length_verification'),
			'in'         => lang('Verify_in_range'),
			'notin'      => lang('Validation_is_not_within_the_scope_of'),
			'between'    => lang('Interval_validation'),
			'notbetween' => lang('Not_in_interval_verification'),
		);
		$this->auto_type = array(0 => lang('please_select'), 'function' => lang('function'), 'field' => lang('field'), 'string' => lang('string'));
		$this->the_time  = array(0 => lang('please_select'), '3' => lang('Throughout'), '1' => lang('add'), '2' => lang('edit'));
		$this->field     = $this->getField();
	}

	/**
	 * index方法
	 * @author colin <register@qinfo360.com>
	 */
	public function index($model_id = null) {
		$map['model_id'] = $model_id;
		if (!$model_id) {
			return $this->error(lang('illegal_operation'));
		}

		$list = model('Attribute')->where($map)->order('id desc')->paginate(25);

		$data = array(
			'list'     => $list,
			'model_id' => $model_id,
			'page'     => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('field').lang('manage'));
		return $this->fetch();
	}

	/**
	 * 创建字段
	 * @author colin <register@qinfo360.com>
	 */
	public function add() {
		$model_id = input('model_id', '', 'trim,intval');
		if (IS_POST) {
			$result = $this->model->change();
			if ($result) {
				return $this->success(lang('add').lang('success'), url('Attribute/index', array('model_id' => $model_id)));
			} else {
				return $this->error($this->model->getError());
			}
		} else {
			if (!$model_id) {
				return $this->error(lang('illegal_operation'));
			}
			$data = array(
				'info'       => array('model_id' => $model_id),
				'fieldGroup' => $this->field,
			);
			$this->assign($data);
			$this->setMeta(lang('add').lang('field'));
			return $this->fetch('common@public/edit');
		}
	}

	/**
	 * 编辑字段方法
	 * @author colin <register@qinfo360.com>
	 */
	public function edit() {
		if (IS_POST) {
			$result = $this->model->change();
			if ($result) {
				return $this->success(lang('edit').lang('success'), url('Attribute/index', array('model_id' => $_POST['model_id'])));
			} else {
				return $this->error($this->model->getError());
			}
		} else {
			$id   = input('id', '', 'trim,intval');
			$info = db('Attribute')->find($id);
			$data = array(
				'info'       => $info,
				'fieldGroup' => $this->field,
			);
			$this->assign($data);
			$this->setMeta(lang('edit').lang('field'));
			return $this->fetch('common@public/edit');
		}
	}

	/**
	 * 删除字段信息
	 * @var delattr 是否删除字段表里的字段
	 * @author colin <register@qinfo360.com>
	 */
	public function del() {
		$id = input('id', '', 'trim,intval');
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}

		$result = $this->model->del($id);
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error($this->model->getError());
		}
	}

	//字段编辑所需字段
	protected function getField() {
		return array(
			lang('basics') => array(
				array('name' => 'id', 'title' => 'id', 'help' => '', 'type' => 'hidden'),
				array('name' => 'model_id', 'title' => 'model_id', 'help' => '', 'type' => 'hidden'),
				array('name' => 'name', 'title' => lang('field_name'), 'help' => '', 'type' => 'text'),
				array('name' => 'title', 'title' => lang('field_title'), 'help' => '', 'type' => 'text'),
				array('name' => 'type', 'title' => lang('type'), 'help' => '', 'type' => 'select', 'option' => $this->attr, 'help' => ''),
				array('name' => 'length', 'title' => lang('Field_length'), 'help' => '', 'type' => 'text'),
				array('name' => 'extra', 'title' => lang('parameter'), 'help' => '', 'type' => 'textarea'),
				array('name' => 'value', 'title' => lang('Default_value'), 'help' => '', 'type' => 'text'),
				array('name' => 'remark', 'title' => lang('field_remarks'), 'help' => '', 'type' => 'text'),
				array('name' => 'is_show', 'title' => lang('is_display'), 'help' => '', 'type' => 'select', 'option' => array('1' => lang('Always_show'), '2' => lang('Add_show'), '3' => lang('Edit_show'), '0' => lang('Not_show'),), 'value' => 1),
				array('name' => 'is_must', 'title' => lang('is_required'), 'help' => '', 'type' => 'select', 'option' => array('0' => lang('no'), '1' => lang('yes'))),
			),
			lang('advanced') => array(
				array('name' => 'validate_type', 'title' => lang('Verification_mode'), 'type' => 'select', 'option' => $this->validate_rule, 'help' => ''),
				array('name' => 'validate_rule', 'title' => lang('Validation_rule'), 'help' => '', 'type' => 'text'),
				array('name' => 'error_info', 'title' => lang('error_message'), 'type' => 'text', 'help' => ''),
				array('name' => 'validate_time', 'title' => lang('Verification_time'), 'help' => '', 'type' => 'select', 'option' => $this->the_time, 'help' => ''),
				array('name' => 'auto_type', 'title' => lang('Auto_completion_method'), 'help' => '', 'type' => 'select', 'option' => $this->auto_type, 'help' => ''),
				array('name' => 'auto_rule', 'title' => lang('Auto_completion_rule'), 'help' => '', 'type' => 'text'),
				array('name' => 'auto_time', 'title' => lang('Auto_completion_time'), 'help' => '', 'type' => 'select', 'option' => $this->the_time),
			),
		);
	}
}