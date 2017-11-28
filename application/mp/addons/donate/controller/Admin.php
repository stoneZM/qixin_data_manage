<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\mp\addons\donate\controller;
use app\mp\controller\Addons;
use \think\View;
class Admin extends Addons{

	/**
	 * 捐赠管理
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function donations() {
		
		
		$list = db('mp_donate_list')->where(array('mpid'=>get_mpid(),'openid'=>array('neq', ''),'pay_status'=>1))->order('id desc')->paginate(15);
		if (!$list) {
			foreach ($list as $k => $v) {
				
				$v['openid_head'] = get_fans_headimg($v['openid']);
				$v['openid_nickname'] = get_fans_nickname($v['openid']);
				
			}
		}
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		
		$nav = $this->nav();
		$subnav = $nav['menu']['children'];
		$this->setMeta('业务导航');
		$this->assign($data);
		$this->assign('mpnav', $nav);	
		$this->assign('subnav', $subnav);
		$this->template();
		
		/*
		$this->setModel('idou_donate_list')
			 ->setListMap(array('mpid'=>get_mpid(),'openid'=>array('neq', ''), 'pay_status'=>1))
			 ->setListOrder('create_time desc')
			 ->addListItem('openid', '捐赠者头像', 'function', array('function_name'=>'get_fans_headimg'))
			 ->addListItem('openid', '捐赠者昵称', 'function', array('function_name'=>'get_fans_nickname'))
			 ->addListItem('money', '捐赠金额')
			 ->addListItem('content', '留言内容')
			 ->addListItem('create_time', '捐赠时间', 'function', array('function_name'=>'date','params'=>'Y-m-d H:i:s,###'))
			 ->common_lists();*/
	}

}

?>