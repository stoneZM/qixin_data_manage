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
class AdPlace extends \app\common\model\Base{

	protected $auto = array('update_time');
	protected $insert = array('create_time');
	protected $type = array(
		'start_time'   => 'integer',
		'end_time'   => 'integer',
	);

	public $show_type = array(
			'1'   => 'slide',
			'2'   => 'couplet',
			'3'   => 'list_of_pictures',
			'4'   => 'graphic_list',
			'5'   => 'text_list',
			'6'   => 'code_advertising'
		);

	public $keyList = array(
		array('name'=>'id', 'title'=>'ID', 'type'=>'hidden', 'help'=>'', 'option'=>''),
		array('name'=>'title', 'title'=>'name', 'type'=>'text', 'help'=>'', 'option'=>''),
		array('name'=>'name', 'title'=>'identifying', 'type'=>'text', 'help'=>'{:ad("广告位标识",参数)}', 'option'=>''),
		array('name'=>'show_type', 'title'=>'type', 'type'=>'select', 'help'=>'', 'option'=>''),
		array('name'=>'show_num', 'title'=>'number_of', 'type'=>'num', 'help'=>'', 'option'=>''),
		array('name'=>'start_time', 'title'=>'start_time', 'type'=>'datetime', 'help'=>'', 'option'=>''),
		array('name'=>'end_time', 'title'=>'end_time', 'type'=>'datetime', 'help'=>'', 'option'=>''),
		array('name'=>'template', 'title'=>'ad_templates', 'type'=>'text', 'help'=>'', 'option'=>''),
		array('name'=>'status', 'title'=>'state', 'type'=>'select', 'help'=>'', 'option'=>array('1'=>'enable','0'=>'disable')),
	);

	public function initialize(){
		parent::initialize();
		foreach ($this->keyList as $key => $value) {
			if ($value['name'] == 'show_type') {
				$value['option'] = $this->show_type;
			}
			$this->keyList[$key] = $value;
		}
	}
}