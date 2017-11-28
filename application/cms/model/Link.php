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
 * 友情链接类
 * @author molong <register@qinfo360.com>
 */
class Link extends \app\common\model\Base {

	public $keyList = array(
		array('name'=>'id' ,'title'=>'id', 'type'=>'hidden'),
		array('name'=>'title' ,'title'=>'title', 'type'=>'text', 'help'=>''),
		array('name'=>'url' ,'title'=>'url', 'type'=>'text', 'help'=>''),
		array('name'=>'ftype' ,'title'=>'type', 'type'=>'select', 'option'=>array(
			'1' => 'default',
		), 'help'=>''),
		array('name'=>'cover_id' ,'title'=>'icon', 'type'=>'image', 'help'=>''),
		array('name'=>'status' ,'title'=>'state', 'type'=>'select','option'=>array('1'=>'enable','0'=>'disable'), 'help'=>''),
		array('name'=>'sort' ,'title'=>'sort', 'type'=>'text', 'help'=>''),
		array('name'=>'descrip' ,'title'=>'description', 'type'=>'textarea', 'help'=>'')
	);

    protected $auto = array('update_time');

	protected $type = array(
		'cover_id'  => 'integer',
		'sort'  => 'integer',
	);
}