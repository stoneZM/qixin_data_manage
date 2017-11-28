<?php 
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\mp\controller;
use app\common\controller\Admin;

/**
 * 公众号支付控制器

 */
class Payment extends Admin {

	/**
	 * 微信支付
	
	 */
	public function index() {
		config('TOKEN_ON', false);
		$MpSetting = model('MpSetting');
		if (IS_POST) {
			$settings = input('post.');
			if (!$MpSetting->add_settings($settings)) {
				return $this->error('保存设置失败');
			} else {
				return $this->success('保存设置成功');
			}
		} else {
			$mpsetting = $MpSetting->get_settings();
			$this->assign('mpsetting',$mpsetting);
			$this->setMeta(lang('pay_config'));
			return $this->fetch();
		}
	}

	/**
	 * 支付记录
	 */
	public function record() {
		$list = model('mp_payment')->where(array('mpid'=>get_mpid()))->order('create_time desc')->paginate(25);
		if($list){
			foreach ($list as $k => &$v) {
				
				$v['transaction_id'] = get_transaction_id($v['id']);
				$v['total_fee'] = get_total_fee($v['id']);
				$v['trade_type'] = get_trade_type($v['id']);
				$v['result_code'] = get_result_code($v['id']);
				$v['fans_headimg'] = get_fans_headimg($v['openid']);
				$v['fans_nickname'] = get_fans_nickname($v['openid']);
			}
		}
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('pay_record'));
		return $this->fetch();
	}

	/**
	 * 获取支付信息详情
	
	 */
	public function get_payment_detail($id, $field) {
		$payment = model('mp_payment')->find($id);
		$detail = json_decode($payment['detail'], true);
		return $detail[$field];
	} 

	/**
	 * 获取支付金额
	
	 */
	public function get_total_fee($id) {
		$total_fee = $this->get_payment_detail($id, 'total_fee');
		return floatval($total_fee)/100;
	}

	/**
	 * 获取微信支付订单号
	
	 */
	public function get_transaction_id($id) {
		return $this->get_payment_detail($id, 'transaction_id');
	}

	/**
	 * 获取支付方式
	
	 */
	public function get_trade_type($id) {
		return $this->get_payment_detail($id, 'trade_type');
	}

	/**
	 * 获取交易结果
	
	 */
	public function get_result_code($id) {
		return $this->get_payment_detail($id, 'result_code');
	}

}

?>