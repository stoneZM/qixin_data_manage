<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\mp\addons\donate\controller;
use app\mp\controller\Mobiles;

class mobile extends Mobiles{
	
	/**
	 * 反馈首页
	 */
	public function index() {
		//$config = get_addon_settings('Feedback');
		$settings = get_addon_settings();
		if ($settings['money']) {
			if (strpos($settings['money'], ',')) {
				$money = explode(',', $settings['money']);
			} else {
				$money = explode('，', $settings['money']);
			}
		}
		$this->assign('money', $money);
		$this->assign('meta_title', '捐赠');
		$this->template();
	}

	/**
	 * 预捐赠

	 */
	public function pre_donate() {
		$data['mpid'] = get_mpid();
		$data['openid'] = get_openid();
		$data['money'] = floatval(input('price'));
		$data['is_anonymous'] = intval(input('is_anonymous'));
		$data['pay_status'] = 0;
		$data['create_time'] = time();
		$data['content'] = input('content');
		$data['is_show'] = 0;
		$data['orderid'] = $data['mpid'] . time();
		$res = db('mp_donate_list')->insert($data);
		if (!$res) {
			return $this->error('捐赠失败');
		} else {
			$data['notify'] = create_mobile_url('pay_ok');
			$data['jump_url'] = create_mobile_url('pay_ok');
			
			return $this->success('捐赠成功');
			
		}
	}

	/**
	 * 支付成功

	 */
	public function pay_ok() {
		if (input('result_code') == 'SUCCESS' && input('return_code') == 'SUCCESS') {
			$map['orderid'] = input('out_trade_no');
			$data['pay_status'] = 1;
			$data['is_show'] = 1;
			db('idou_donate_list')->where($map)->update($data);
		}
		$this->display();
	}

	/**
	 * 捐赠列表

	 */
	public function donate_list() {
		$map['mpid'] = get_mpid();
		$map['pay_status'] = 1;
		$map['is_show'] = 1;
		$lists = db('mp_donate_list')->where($map)->order('create_time desc')->select();
		if ($lists) {
			foreach ($lists as $k => &$v) {
				$fans = get_fans_info($v['openid']);
				if($fans){
					$v['fans'] = $fans;		
				}else{
					$v['fans']['nickname'] = '匿名';	
					$v['fans']['headimgurl'] = '__IMG__/noname.jpg';	
				}	
			}
		}
		$this->assign('lists', $lists);
		$this->assign('meta_title', '捐赠列表');
		$this->template();
	}
}
