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
 * 伪静态
 */
class Rewrite extends Base{
	
	protected $autoWriteTimestamp = true;

	public $keyList = array(
		array('name'=>'id','title'=>'identifying','type'=>'hidden'),
		array('name'=>'rule','title'=>'name','type'=>'text','option'=>'','help'=>''),
		array('name'=>'url','title'=>'url','type'=>'text','option'=>'','help'=>''),
	);

	/**
	 * 数据修改
	 * @return [bool] [是否成功]
	 */
	public function change(){
		$data = \think\Request::instance()->post();
		if (isset($data['id']) && $data['id']) {
			return $this->validate(true)->save($data, array('id'=>$data['id']));
		}else{
			return $this->validate(true)->save($data);
		}
	}
}