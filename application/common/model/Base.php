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
* 模型基类
*/
class Base extends \think\Model{

	protected $param;
	protected $type = array(
		'id'  => 'integer',
		'cover_id'  => 'integer',
	);

	protected $resultSetType = 'collection';
	protected $list_row = 25;


	public function initialize(){
		parent::initialize();
		$this->param = \think\Request::instance()->param();
		$this->list_row = config('paginate')['list_rows'];
	}

	/**
	 * 数据修改
	 * @return [bool] [是否成功]
	 */
	public function change(){
		$data = \think\Request::instance()->post();
		if (isset($data['id']) && $data['id']) {
			return $this->save($data, array('id'=>$data['id']));
		}else{
			return $this->save($data);
		}
	}

}