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
* 设置模型
*/
class AuthRule extends Base{

	const rule_url = 1;
	const rule_mian = 2;

	protected $type = array(
		'id'    => 'integer',
	);

	public $keyList = array(
		array('name'=>'module','title'=>'module','type'=>'text'),
		array('name'=>'title','title'=>'name','type'=>'text','help'=>''),
		array('name'=>'name','title'=>'identifying','type'=>'text','help'=>''),
		array('name'=>'group','title'=>'grouping','type'=>'text','help'=>''),
		array('name'=>'status','title'=>'state','type'=>'select','option'=>array('1'=>'enable','0'=>'disable'),'help'=>''),
		array('name'=>'condition','title'=>'condition','type'=>'text','help'=>'')
	);
	public function initialize() {
		parent::initialize();
		foreach ($this->keyList as $key => $value) {
			if ($value['name'] == 'status') {	
				foreach ($value['option'] as &$item) {
					$item = lang($item);
				}
			}
			$this->keyList[$key] = $value;
		}
	}
	public function uprule($data){
		foreach ($data as $value) {
			$data = array(
				'module' => $value['module'],
				'type'   => 2,
				'name'   => $value['url'],
				'title'  => $value['title'],
				'group'  => $value['group'],
				'status' => 1,
			);
			$id = $this->where(array('name' => $data['name']))->value('id');
			if ($id) {
				$data['id'] = $id;
				$this->save($data, array('id' => $id));
			} else {
				self::create($data);
			}
		}
		return true;
	}
}