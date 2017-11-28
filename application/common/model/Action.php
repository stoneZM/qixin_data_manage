<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 分类模型
 */
class Action extends Base{

	protected function getStatusTextAttr($value, $data){
		$status = array(-1=>lang('delete'),0=>lang('disable'),1=>lang('normal'),2=>lang('pending_audit'));
		return $status[$data['status']];
	}

	public $fieldlist = array(
		array('name'=>'id','title'=>'id','type'=>'hidden'),
		array('name'=>'name','title'=>'identifying','type'=>'text','help'=>''),
		array('name'=>'title','title'=>'name','type'=>'text','help'=>''),
		array('name'=>'type','title'=>'type','type'=>'select','option'=>array('1'=>'system','2'=>'user'),'help'=>''),
		array('name'=>'remark','title'=>'description','type'=>'textarea','help'=>''),
		array('name'=>'rule','title'=>'behavior_rules','type'=>'textarea','help'=>''),
		array('name'=>'log','title'=>'log_rules','type'=>'textarea','help'=>'support_variable_function'),
	);

}