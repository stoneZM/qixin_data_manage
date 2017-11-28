<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\cms\model;

/**
* 设置模型
*/
class Category extends \app\common\model\Base{

	protected $name = "Category";
	protected $auto = array('update_time', 'icon'=>1, 'status'=>1);

	protected $type = array(
		'icon'  => 'integer',
	);

	public function change(){
		$data = input('post.');
		if ($data['id']) {
			$result = $this->save($data,array('id'=>$data['id']));
		}else{
			unset($data['id']);
			$result = $this->save($data);
		}
		if (false !== $result) {
			return true;
		}else{
			$this->error = lang('fail');
			return false;
		}
	}

	public function info($id, $field = true){
		return $this->db()->where(array('id'=>$id))->field($field)->find();
	}
}