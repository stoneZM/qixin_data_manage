<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

/**
* 设置模型
*/
class Server extends \app\common\model\Base{
	
	protected $type = array(
		'id'  => 'integer',
	);

	protected $auto = array('host', 'update_time', 'status'=>1);
	protected $insert = array('create_time');

    protected function setNameAttr($value){
        return strtolower($value);
    }

	public function lists(){
		$map    = array('status' => 1);
		$data   = $this->db()->where($map)->field('host,port,uname,password')->select();
		return $data;
	}

}