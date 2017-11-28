<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\common\validate;

/**
* 设置模型
*/
class Document extends Base{

	protected $rule = array(
		'title'   => 'require',
	);
	
	protected $message = array(
		'title.require'   => '字段标题不能为空！',
	);
	
	protected $scene = array(
		'add'   => 'title',
		'edit'   => 'title'
	);
}