<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\mp\addons\feedback\controller;


use app\mp\controller\Mobiles;

class mobile extends Mobiles{
	
    public $wechat_only = true;
	
	/**
	 * 反馈首页
	 */
	public function index() {
		$config = get_addon_settings('Feedback');
		$config['top_title'] || $config['top_title'] = '意见反馈';
		$config['page_title'] || $config['page_title'] = '意见反馈';
		
		$config['url'] = create_mobile_url('deal');
		
		
		$this->assign('meta_title', $config['top_title']);
		$this->assign('config', $config);
		$this->template();
	}

	/**
	 * 处理反馈
	 */
	public function deal() {
		$data = array(
			'mpid' => get_mpid(),
			'openid' => get_openid(),
			'name' => input('name'),
			'contact_type' => input('contact_type'),
			'contact' => input('contact'),
			'content' => input('content'),
			'create_time' => time()
		);
		$result = db('mp_feedback')->insert($data);
		if ($result !== false) {
			return $this->success('反馈成功，感谢您的支持~');
		} else {
			return $this->error('反馈失败，请重新提交反馈内容');
		}
	}

}
