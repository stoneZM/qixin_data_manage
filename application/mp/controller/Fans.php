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
 * 公众号粉丝管理控制器

 */
class Fans extends Admin {

	/**
	 * 粉丝列表
	 */
	public function index() {
		
		$list = model('MpFans')->where(array('mpid'=>get_mpid()))->paginate(25);
		if($list){
			foreach ($list as $k => &$v) {
				if($v['headimgurl']){
					$v['headimgurl'] = 	'<img src="'.$v['headimgurl'].'" width=50 height=50>';
				}else{
					$v['headimgurl'] = 	'<img src="__IMG__/noname.jpg" width=50 height=50>';
				}
				if(!$v['nickname']){
					$v['nickname'] = 	lang('anonymous');
				}
				if($v['sex']=='' || $v['sex']==0){
					$v['sex'] = 	lang('unknown');
				}elseif($v['sex']=='1'){
					$v['sex'] = 	lang('man');
				}elseif($v['sex']=='2'){
					$v['sex'] = 	lang('woman');
				}
				if($v['is_subscribe']==0){
					$v['is_subscribe'] = 	lang('not_concern');
				}else{
					$v['is_subscribe'] = 	lang('already_concern');
				}
			}
		}
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('fans_list'));
		return $this->fetch();
	}
	
	/**
	 * 编辑粉丝信息
	 */
	public function edit_fans() {
		
		if (IS_POST) {
			$data = input('post.');
			$res_data = model('MpFans')->where(array('id'=>$data['id']))->update($data);
			if (!$res_data) {
				return $this->error(lang('edit').lang('fail'));
			} else {
				return $this->success(lang('edit').lang('success'));
			}
		} else {
			$openid = input('openid');
			$fans_data = model('MpFans')->where(array('openid'=>$openid))->find();			
			$this->assign('fansdata',$fans_data);
			$this->setMeta(lang('edit').lang('fans'));
			return $this->fetch();
		}
	}

	/**
	 * 粉丝配置
	 */
	public function setting() {
		config('TOKEN_ON', false);
		$MpSetting = model('MpSetting');
		if (IS_POST) {
			$settings = input('post.');
			if (!$MpSetting->add_settings($settings)) {
				return $this->error(lang('edit').lang('fail'));
			} else {
				return $this->success(lang('edit').lang('success'));
			}
		} else {
			$mpsetting = $MpSetting->get_settings();
			$this->assign('mpsetting',$mpsetting);
			$this->setMeta(lang('config'));
			return $this->fetch('setting');
		}
	}

}

?>