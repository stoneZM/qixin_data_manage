<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\common\controller\Admin;

class Log extends Admin
{
	/**
	* 日志列表
	*/
	public function index() {
			$type = input('type','all');
			$modeltype = array('all'=>'all','system'=>'system','clone'=>'clone','storage'=>'storage','computing'=>'computing','agent'=>'agent');
			if($type != 'all'){
				$map['model'] =$type;
			}
			$order = "create_time desc";
			$list  = model('Log')->where($map)->order($order)->paginate(15);
			

			$listdata = $list->toArray()['data'];
			foreach ($listdata as $key => &$item) {
				  $item['record_label'] = $this->get_label($item['model'],$item['record_id']);
			}
			$data = array(
				'list' => $listdata,
				'page' => $list->render(),
			);
			$this->assign($data);
			$this->assign('modeltype',$modeltype);
			$this->assign('type',$type);
			$this->setMeta(lang('log_report'));
			return $this->fetch();
		
	}
	
	private function get_label($model,$record_id='null')
    {
		$record_label = '';
		
		if($record_id == 'null'){
			return '';
		}
		switch ($model)
		{
			case 'clone':
			$deviceinfo = db('device')->field('unique_id,ip')->where(array('unique_id'=>$record_id))->find();
			$record_label = "(".$deviceinfo['ip'].")";
			break;  
			case 'storage':
			$storageinfo = db('storage')->field('unique_id,name,config')->where(array('unique_id'=>$record_id))->find();
			if($storageinfo['config']){
				$info_config = json_decode($storageinfo['config'],true);
			}
			$record_label = "(".$info_config['ip'].")";
			break;
			default:
		}
		return $record_label;
		
    }
}