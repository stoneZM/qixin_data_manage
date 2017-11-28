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
 * 场景二维码管理控制器

 */
class Sceneqrcode extends Admin {

	/**
	 * 场景二维码列表
	
	 */
	public function index() {
		
		
		$list = model('MpSceneQrcode')->where(array('mpid'=>get_mpid()))->order('ctime desc')->paginate(15);
		$options= array('0'=>lang('temp_qr_code'),'1'=>lang('permanent_qr_code'));
		if($list){
			foreach ($list as $k => &$v) {
				$v['scene_type'] = $options[$v['scene_type']];
				
				if(!$v['expire']){
					$v['expire'] = '永不过期';
				}
				if($v['short_url']){
					$v['short_url'] = '<img src="'.$v['short_url'].'" width=100 height=100>';
				}
				
				
				
				$v['scan_count'] = $this->get_scan_count($v['id']);
				$v['scan_times'] = $this->get_scan_times($v['id']);
				
				
				
			}
		}
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('sceneqrcode_manage'));
		return $this->fetch();
	}

	/**
	 * 获取扫码总人数
	 */
	public function get_scan_count($id) {
		$results = model('MpSceneQrcodeStatistics')->distinct(true)->field('openid')->where(array('mpid'=>get_mpid(),'qrcode_id'=>$id))->select();
		return count($results);
	}

	/**
	 * 获取扫码总次数
	 */
	public function get_scan_times($id) {
		$count = model('MpSceneQrcodeStatistics')->where(array('mpid'=>get_mpid(),'qrcode_id'=>$id))->count();
		return $count;
	}

	/**
	 * 场景二维码类型
	
	 */
	public function add() {
		if (IS_POST) {
			$post_data = input('post.');
			$data['mpid'] = get_mpid();
			$data['ctime'] = time();
			$data['scene_name'] = $post_data['scene_name'];
			$data['keyword'] = $post_data['keyword'];
			if (!$data['scene_name']) {
				return $this->error('场景名称必填');
			}
			if (!$data['keyword']) {
				return $this->error('关联关键词必填');
			}
			if ($post_data['scene_type'] == 0) {     // 创建临时二维码
				$data['expire'] = $post_data['expire'] ? $post_data['expire'] : 1800;
				$qrCode = model('MpSceneQrcode')->where(array('mpid'=>get_mpid(),'scene_type'=>0))->order('scene_id desc')->find();
				if (!$qrCode['scene_id']) {
					$data['scene_id'] = 50001;
				} else {
					$data['scene_id'] = intval($qrCode['scene_id'])+1;
				}
				
				$data['scene_type'] =0;
				$qrCode = get_qr_code($data['scene_id'], 0, $data['expire']);
				if ($qrCode['errcode']) {
					return $this->error('未能成功创建二维码，错误信息：'.$qrCode['errmsg']);
				}
				$data['ticket'] = $qrCode['ticket'];
				$data['url'] = $qrCode['url'];
				$data['short_url'] = get_short_url(get_qr_url($qrCode['ticket']));
			} elseif ($post_data['scene_type'] == 1) {		// 创建永久二维码
				if ($post_data['scene_str'] == '') {	// 如果没有填场景值字符串，则系统自动生成场景值ID
					$qrCode = model('MpSceneQrcode')->where(array('mpid'=>get_mpid(),'scene_type'=>1))->order('scene_id desc')->find();
					if (!$qrCode['scene_id']) {
						$data['scene_id'] = 60001;
					} else {
						$data['scene_id'] = intval($qrCode['scene_id'])+1;
					}
					$qrCode = get_qr_code($data['scene_id'], 1);
					
				} else {	// 如果填了场景值字符串，则使用场景值字符串
					$data['scene_str'] = $post_data['scene_str'];
					$qrCode = get_qr_code($data['scene_str'], 2);
				}
				$data['scene_type'] = 1;
				if ($qrCode['errcode']) {
					return $this->error('未能成功创建二维码，错误信息：'.$qrCode['errmsg']);
				}
				$data['ticket'] = $qrCode['ticket'];
				$data['url'] = $qrCode['url'];
				$data['short_url'] = get_short_url(get_qr_url($qrCode['ticket']));
			} else {
				return $this->error('二维码类型有误');
			}
			$res = model('MpSceneQrcode')->insert($data);
			if ($res) {
				return $this->success('创建场景二维码成功',url('index'));
			} else {
				return $this->error('创建场景二维码失败');
			}
		} else {
			
			$data['scene_type'] = 1;
			$this->assign('data',$data);
			$this->setMeta(lang('add_sceneqrcode'));
			return $this->fetch('edit');
		}
		
	}

	/**
	 * 扫码统计
	
	 */
	public function statistics() {
		
		if (input('qrcode_id')) {
			$map = array('mpid'=>get_mpid(),'qrcode_id'=>input('qrcode_id'));
		} else {
			$map = array('mpid'=>get_mpid());
		}
		$list = model('MpSceneQrcodeStatistics')->where($map)->order('ctime desc')->paginate(15);
		$options= array('subscribe'=>'扫码关注','scan'=>'扫码带参数');
		if($list){
			foreach ($list as $k => &$v) {
				$v['scan_type'] = $options[$v['scan_type']];
				$v['openid_head'] = get_fans_headimg($v['openid']);
				$v['openid_nickname'] = get_fans_nickname($v['openid']);
			}
		}
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('sweep_statistics'));
		return $this->fetch();
	}

}



 ?>