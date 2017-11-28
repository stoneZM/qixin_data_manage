<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\mp\addons\feedback\controller;
use app\mp\controller\Addons;
use \think\View;
class Admin extends Addons{
	
	/**
	 * 反馈列表
	 */
	public function lists() {
		$list = db('mp_feedback')->where(array('mpid'=>get_mpid()))->order('id desc')->paginate(15);
		$list_data = $list ->toArray();
		if($list_data['data']){
			foreach ($list_data['data'] as $key => &$m) {
					
				if($m['openid']){
					$m['openid_head'] = get_fans_headimg($m['openid']);
					$m['openid_nickname'] = get_fans_nickname($m['openid']);
				}		
			}
		}
		$data = array(
			'list' => $list_data['data'],
			'page' => $list->render(),
		);
		
		$nav = $this->nav();
		$subnav = $nav['menu']['children'];
		$this->setMeta('业务导航');
		$this->assign($data);
		$this->assign('mpnav', $nav);	
		$this->assign('subnav', $subnav);
		$this->template();
	}
	
	
}
