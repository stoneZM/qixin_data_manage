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
class Channel extends \app\common\model\Base{

	protected $type = array(
		'id'  => 'integer',
	);

	protected $auto = array('update_time', 'status'=>1);
	protected $insert = array('create_time');
}