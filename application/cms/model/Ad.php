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
 * 分类模型
 */
class Ad extends \app\common\model\Base{

	protected $auto = array('update_time');
	protected $insert = array('create_time');
	protected $type = array(
		'id'   => 'integer',
		'cover_id'   => 'integer',
	);

	public $keyList = array(
		array('name'=>'id', 'title'=>'ID', 'type'=>'hidden', 'help'=>'', 'option'=>''),
		array('name'=>'place_id', 'title'=>'PLACE_ID', 'type'=>'hidden', 'help'=>'', 'option'=>''),
		array('name'=>'title', 'title'=>'name', 'type'=>'text', 'help'=>'', 'option'=>''),
		array('name'=>'cover_id', 'title'=>'advertising_picture', 'type'=>'image', 'help'=>'', 'option'=>''),
		array('name'=>'url', 'title'=>'link', 'type'=>'text', 'help'=>'', 'option'=>''),
		array('name'=>'photolist', 'title'=>'auxiliary_picture', 'type'=>'images', 'help'=>'', 'option'=>''),
		array('name'=>'listurl', 'title'=>'auxiliary_link', 'type'=>'textarea', 'help'=>'auxiliary_link_help', 'option'=>''),
		array('name'=>'background', 'title'=>'background_color', 'type'=>'text', 'help'=>'', 'option'=>''),
		array('name'=>'content', 'title'=>'description', 'type'=>'textarea', 'help'=>'', 'option'=>''),
		array('name'=>'status', 'title'=>'state', 'type'=>'select', 'help'=>'', 'option'=>array('1'=>'enable','0'=>'disable')),
	);
}
