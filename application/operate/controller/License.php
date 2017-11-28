<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------


namespace app\operate\controller;
use app\common\controller\Admin;
use think\Db;
use think\Exception;
use think\Request;

class License extends Admin
{
	/**
	* 授权码管理
	*/
    public function index()
    {
		
		$order = "id asc";
		$list  =db('license')->where('')->order($order)->paginate(15);
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('license_manage'));
        return $this->view->fetch();
    }
	
	
	
	/**
	* 授权码新增
	*/
	public function license_add() {
		
		if (IS_POST) {
			
			$data = $this->request->post();
			if(!$data['type']){
				return $this->error(lang('type').lang('cannot_be_empty'));
			}
			if($data['type'] == 1){
				if(!$data['expiration_time']){
					return $this->error(lang('expiration_time').lang('cannot_be_empty'));
				}
			}
			if(!$data['agent_id']){
				return $this->error(lang('agent').lang('cannot_be_empty'));
			}

			$agent_info = db('agent')->where(array('status'=>1,'id'=>$data['agent_id']))->field('id,name,alias')->find();
			if(!$agent_info){
				return $this->error(lang('agent_info_does_not_exist'));
			}
			
			$license_module = input('license_module/a');
			$license_software = input('license_software/a');
			
			if(!$license_module){
				return $this->error(lang('please_select').lang('available_module'));
			}
			if($data['storage_check_net']){
				if(!$data['storage_size_net']){
					return $this->error(lang('net_model').lang('storage_size').lang('cannot_be_empty'));
				}
			}
			
			if($data['storage_check_ali']){
				if(!$data['storage_size_ali']){
					return $this->error(lang('ali_cloud').lang('storage_size').lang('cannot_be_empty'));
				}
			}
			
			if($data['storage_check_qiniu']){
				if(!$data['storage_size_qiniu']){
					return $this->error(lang('qiniu_cloud').lang('storage_size').lang('cannot_be_empty'));
				}
			}
			if(!$data['compute_size']){
				return $this->error(lang('compute_size').lang('cannot_be_empty'));
			}
			
			if(!in_array('cdp',$license_module)){
				$data['cdp_size'] = 0;
			}else{
				if(!$data['cdp_size']){
					return $this->error('CB-CDP'.lang('cannot_be_empty'));
				}
			}
			
			if(!in_array('sb',$license_module)){
				$data['sb_size'] = '';
			}else{
				if(!$data['sb_size']){
					return $this->error('CB-SB'.lang('cannot_be_empty'));
				}
			}
			
			if(!in_array('move',$license_module)){
				$data['mv_size'] = '';
			}else{
				if(!$data['mv_size']){
					return $this->error('MV'.lang('cannot_be_empty'));
				}
			}
			

			$save_data['type'] = $data['type'];
			$save_data['creation_time'] = time();
			if($data['type']==1){
				$save_data['expiration_time'] = strtotime($data['expiration_time']);
			}else{
				$save_data['expiration_time'] = '';
			}
			$save_data['status'] = $data['status'];
			
			
			$info_config['module'] = $license_module;
			$info_config['software_info'] = $license_software;
			
			if($data['storage_check_net']){
				
				$info_config['storage']['storage_net'] = intval($data['storage_check_net']);
				$info_config['storage']['storage_net_unit'] = $data['storage_unit_net'];
				if(strtolower($data['storage_unit_net'])=='gb'){
					$info_config['storage']['storage_net_size'] = $data['storage_size_net']*1024*1024*1024;
				}elseif(strtolower($data['storage_unit_net'])=='tb'){
					$info_config['storage']['storage_net_size'] = $data['storage_size_net']*1024*1024*1024*1024;
				}
			}
			
			if($data['storage_check_ali']){
				$info_config['storage']['storage_ali'] = intval($data['storage_check_ali']);
				$info_config['storage']['storage_ali_unit'] = $data['storage_unit_ali'];
				if(strtolower($data['storage_unit_ali'])=='gb'){
					$info_config['storage']['storage_ali_size'] = $data['storage_size_ali']*1024*1024*1024;
				}elseif(strtolower($data['storage_unit_ali'])=='tb'){
					$info_config['storage']['storage_ali_size'] = $data['storage_size_ali']*1024*1024*1024*1024;
				}
			}
			
			
			if($data['storage_check_qiniu']){
				$info_config['storage']['storage_qiniu'] = intval($data['storage_check_qiniu']);
				$info_config['storage']['storage_qiniu_unit'] = $data['storage_unit_qiniu'];
				if(strtolower($data['storage_unit_qiniu'])=='gb'){
					$info_config['storage']['storage_qiniu_size'] = $data['storage_size_qiniu']*1024*1024*1024;
				}elseif(strtolower($data['storage_unit_qiniu'])=='tb'){
					$info_config['storage']['storage_qiniu_size'] = $data['storage_size_qiniu']*1024*1024*1024*1024;
				}
			}
			
			$info_config['cdp_size'] = intval($data['cdp_size']);
			$info_config['sb_size'] = intval($data['sb_size']);
			$info_config['mv_size'] = intval($data['mv_size']);
			$info_config['compute_size'] = intval($data['compute_size']);
			$save_data['version_type'] = $data['version_type'];
			$save_data['config_info'] = json_encode($info_config);
			$save_data['agent_info'] = json_encode($agent_info);
			$save_data['cdkey'] = create_guid();
			
			if($data['type'] == 1){
				$activation['cdkey'] = $save_data['cdkey'];
				$activation['type'] = $save_data['type'];
				$activation['creation_time'] = $save_data['creation_time'];
				$activation['expiration_time'] = $save_data['expiration_time'];
				$activation['status'] = $save_data['status'];
				$activation['config_info'] = $info_config;
				$activation['agent_info'] = $agent_info;
				$activation['version_type'] = $data['version_type'];
				$activation['use_info'] = '';
				$activation['hardware_info'] = '';
				$activation['reviewer'] = 1;
				$save_data['activation_code'] = licensecrypto(json_encode($activation),'encode');
			}

			$reuslt=db('license')->insert($save_data);

			if (false !== $reuslt) {
				return $this->success(lang('add').lang('success'),url('license/index'));
			} else {
				return $this->error(lang('add').lang('fail'), '');
			}
		} else {
			$module_data = db('module')->where(array('show_nav'=>1))->order('id desc')->select();
			$agent_data = db('agent')->where(array('status'=>1))->order('id asc')->select();
			$software_data = array(array('id'=>'move','name'=>'Move'),array('id'=>'clone','name'=>'Clone'));
			$this->assign('software_data',$software_data);
			$this->assign('module_data',$module_data);
			$this->assign('agent_data',$agent_data);
			$this->assign('license_data',$license_data);
			$this->setMeta(lang('license_add'));
			return $this->fetch('license_edit');
		}
	}
	
	/**
	* 授权码修改
	*/
	public function license_edit() {
		
		$id = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));	
		}
		$license_data = db('license')->where(array('id'=>$id))->find();
		if(!$license_data){
			return $this->error(lang('authorization_code_does_not_exist'));	
		}
		
		
		
		if (IS_POST) {
			
			$data = $this->request->post();
			if(!$data['type']){
				return $this->error(lang('type').lang('cannot_be_empty'));
			}
			
			if($data['type'] == 1){
				if(!$data['expiration_time']){
					return $this->error(lang('expiration_time').lang('cannot_be_empty'));
				}
			}
			if(!$data['agent_id']){
				return $this->error(lang('agent').lang('cannot_be_empty'));
			}

			$agent_info = db('agent')->where(array('status'=>1,'id'=>$data['agent_id']))->field('id,name,alias')->find();
			if(!$agent_info){
				return $this->error(lang('agent_info_does_not_exist'));
			}

			$license_module = input('license_module/a');
			$license_software = input('license_software/a');

			if(!$license_module){
				return $this->error(lang('please_select').lang('available_module'));
			}
			if($data['storage_check_net']){
				if(!$data['storage_size_net']){
					return $this->error(lang('net_model').lang('storage_size').lang('cannot_be_empty'));
				}
			}
			if($data['storage_check_ali']){
				if(!$data['storage_size_ali']){
					return $this->error(lang('ali_cloud').lang('storage_size').lang('cannot_be_empty'));
				}
			}
			if($data['storage_check_qiniu']){
				if(!$data['storage_size_qiniu']){
					return $this->error(lang('qiniu_cloud').lang('storage_size').lang('cannot_be_empty'));
				}
			}
			if(!$data['compute_size']){
				return $this->error(lang('compute_size').lang('cannot_be_empty'));
			}
			

			
			if(!in_array('cdp',$license_module)){
				$data['cdp_size'] = '';
			}else{
				if(!$data['cdp_size']){
					return $this->error('CB-CDP'.lang('cannot_be_empty'));
				}
			}
			if(!in_array('sb',$license_module)){
				$data['sb_size'] = '';
			}else{
				if(!$data['sb_size']){
					return $this->error('CB-SB'.lang('cannot_be_empty'));
				}
			}
			if(!in_array('move',$license_module)){
				$data['mv_size'] = '';
			}else{
				if(!$data['mv_size']){
					return $this->error('MV'.lang('cannot_be_empty'));
				}
			}
			
			$save_data['type'] = $data['type'];
			
			
			if($data['type']==1){
				$save_data['expiration_time'] = strtotime($data['expiration_time']);
			}else{
				$save_data['expiration_time'] = '';
			}
			
			$save_data['status'] = $data['status'];
			$info_config['module'] = $license_module;
			$info_config['software_info'] = $license_software;
			if($data['storage_check_net']){
				
				$info_config['storage']['storage_net'] = $data['storage_check_net'];
				$info_config['storage']['storage_net_unit'] = $data['storage_unit_net'];
				if(strtolower($data['storage_unit_net'])=='gb'){
					$info_config['storage']['storage_net_size'] = $data['storage_size_net']*1024*1024*1024;
				}elseif(strtolower($data['storage_unit_net'])=='tb'){
					$info_config['storage']['storage_net_size'] = $data['storage_size_net']*1024*1024*1024*1024;
				}
			}
			
			if($data['storage_check_ali']){
				$info_config['storage']['storage_ali'] = $data['storage_check_ali'];
				$info_config['storage']['storage_ali_unit'] = $data['storage_unit_ali'];
				if(strtolower($data['storage_unit_ali'])=='gb'){
					$info_config['storage']['storage_ali_size'] = $data['storage_size_ali']*1024*1024*1024;
				}elseif(strtolower($data['storage_unit_ali'])=='tb'){
					$info_config['storage']['storage_ali_size'] = $data['storage_size_ali']*1024*1024*1024*1024;
				}
			}
			
			
			if($data['storage_check_qiniu']){
				$info_config['storage']['storage_qiniu'] = $data['storage_check_qiniu'];
				$info_config['storage']['storage_qiniu_unit'] = $data['storage_unit_qiniu'];
				if(strtolower($data['storage_unit_qiniu'])=='gb'){
					$info_config['storage']['storage_qiniu_size'] = $data['storage_size_qiniu']*1024*1024*1024;
				}elseif(strtolower($data['storage_unit_qiniu'])=='tb'){
					$info_config['storage']['storage_qiniu_size'] = $data['storage_size_qiniu']*1024*1024*1024*1024;
				}
			}
			
			$info_config['cdp_size'] = $data['cdp_size'];
			$info_config['sb_size'] = $data['sb_size'];
			$info_config['mv_size'] = $data['mv_size'];
			$info_config['compute_size'] = $data['compute_size'];
			$save_data['version_type'] = $data['version_type'];
			$save_data['config_info'] = json_encode($info_config);
			$save_data['agent_info'] = json_encode($agent_info);
			
			if($data['type'] == 1){
				$activation['cdkey'] = $license_data['cdkey'];
				$activation['type'] = $license_data['type'];
				$activation['creation_time'] = $license_data['creation_time'];
				$activation['expiration_time'] = $save_data['expiration_time'];
				$activation['status'] = $save_data['status'];
				$activation['config_info'] = $info_config;
				$activation['agent_info'] = $agent_info;
				$activation['version_type'] = $data['version_type'];
				$activation['use_info'] = '';
				$activation['hardware_info'] = '';
				$activation['reviewer'] = 1;
				
				$save_data['activation_code'] = licensecrypto(json_encode($activation),'encode');
			}

			$reuslt=db('license')->where(array('id'=>$id))->update($save_data);

			if (false !== $reuslt) {
				return $this->success(lang('edit').lang('success'),url('license/index'));
			} else {
				return $this->error(lang('edit').lang('fail'), '');
			}
		} else {
		
			$license_config = json_decode($license_data['config_info'],true);
			
			$license_config['storage']['storage_net_unit'] = strtolower($license_config['storage']['storage_net_unit']);
			$license_config['storage']['storage_ali_unit'] = strtolower($license_config['storage']['storage_ali_unit']);
			$license_config['storage']['storage_qiniu_unit'] = strtolower($license_config['storage']['storage_qiniu_unit']);
			$license_config['storage']['storage_net_size'] = format_bytes($license_config['storage']['storage_net_size'],false);
			$license_config['storage']['storage_ali_size'] = format_bytes($license_config['storage']['storage_ali_size'],false);
			$license_config['storage']['storage_qiniu_size'] = format_bytes($license_config['storage']['storage_qiniu_size'],false);
			
			
			$software_data = array(array('id'=>'move','name'=>'Move'),array('id'=>'clone','name'=>'Clone'));
			
			$this->assign('software_data',$software_data);
			$agent_info =  json_decode($license_data['agent_info'],true);
			
			
			
			$module_data = db('module')->where(array('show_nav'=>1))->order('id desc')->select();
			$agent_data = db('agent')->where(array('status'=>1))->order('id asc')->select();
			$this->assign('module_data',$module_data);
			$this->assign('agent_data',$agent_data);
			$this->assign('agent_info',$agent_info);
			$this->assign('license_data',$license_data);
			$this->assign('license_config',$license_config);
			$this->setMeta(lang('license_edit'));
			return $this->fetch('license_edit');
		}
	}
	
	/**
	* 授权码查看
	*/
	public function license_see() {
		
		$id = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));	
		}
		$license_data = db('license')->where(array('id'=>$id))->find();
		if(!$license_data){
			return $this->error(lang('authorization_code_does_not_exist'));	
		}


		$license_config = json_decode($license_data['config_info'],true);
		
		$use_info = json_decode($license_data['use_info'],true);
		$agent_info = json_decode($license_data['agent_info'],true);
		$hardware_info = json_decode($license_data['hardware_info'],true);
		
		
		$license_config['storage']['storage_net_unit'] = strtolower($license_config['storage']['storage_net_unit']);
		$license_config['storage']['storage_ali_unit'] = strtolower($license_config['storage']['storage_ali_unit']);
		$license_config['storage']['storage_qiniu_unit'] = strtolower($license_config['storage']['storage_qiniu_unit']);
		$license_config['storage']['storage_net_size'] = format_bytes($license_config['storage']['storage_net_size'],false);
		$license_config['storage']['storage_ali_size'] = format_bytes($license_config['storage']['storage_ali_size'],false);
		$license_config['storage']['storage_qiniu_size'] = format_bytes($license_config['storage']['storage_qiniu_size'],false);
		
		$module_data = db('module')->where(array('show_nav'=>1))->order('id desc')->select();
		$this->assign('module_data',$module_data);
		$this->assign('license_data',$license_data);
		$this->assign('license_config',$license_config);
		$this->assign('agent_info',$agent_info);
		$this->assign('use_info',$use_info);
		$this->assign('hardware_info',$hardware_info);
		
		
		$this->setMeta(lang('license_see'));
		return $this->fetch();
	
	}
	
	
	/**
	* 授权码注册
	*/
	public function activation() {
		
		if (IS_POST) {
		
			$activation_code = input('activation_code');
			if(!$activation_code){
				return $this->error(lang('authentication_code').lang('cannot_be_empty'));
			}
			
			$service_time = input('service_time');
			if(!$service_time){
				return $this->error(lang('service_time').lang('cannot_be_empty'));
			}
			
			list($start_time, $end_time) = split (' - ', $service_time);
			if(!$start_time || !$end_time){
				return $this->error(lang('time_format_error'));	
			}
			
			
			$start_time = strtotime($start_time);
			$end_time = strtotime($end_time);
			if($start_time>=$end_time){
				return $this->error(lang('end_time_is_greater_than_start_time'));	
			}
		
		
		
			
			$activation_code=str_replace("\r\n","",$activation_code);
			
			$activation_code = licensecrypto($activation_code);
			
			if(!$activation_code){
				return $this->error(lang('authentication_code_error'));	
			}
			
			$activation_data = json_decode($activation_code,true);

			if(!$activation_data['cdkey']){
				return $this->error(lang('cdkey_does_not_exist'));	
			}
			
			if(!$activation_data['use_info']){
				return $this->error(lang('no_registration_information_found'));	
			}
			if(!$activation_data['hardware_info']){
				return $this->error(lang('no_hardware_information_found'));	
			}
			
			$license_data = db('license')->where(array('cdkey'=>$activation_data['cdkey']))->find();
			if(!$license_data){
				return $this->error(lang('cdkey_does_not_exist'));	
			}
			
			if($license_data['status'] == 0){
				return $this->error(lang('activation_code').lang('disable'));	
			}
			if($license_data['use_status'] == 1){
				return $this->error(lang('activation_code').lang('already_use'));	
			}
			
			$hashids = hashids(8,"qinfo360");
			$activation['sn'] = $hashids->encode($license_data['id']); //加密
			$activation['cdkey'] = $license_data['cdkey'];
			$activation['type'] = $license_data['type'];
			$activation['creation_time'] = $license_data['creation_time'];
			$activation['expiration_time'] = $license_data['expiration_time'];
			$activation['status'] = $license_data['status'];
			$activation['use_status'] = 1;
			$activation['use_time'] = time();
			$activation['start_time'] = $start_time;
			$activation['end_time'] = $end_time;
			
			$activation['agent_info'] = json_decode($license_data['agent_info'],true);
			$activation['version_type'] = $license_data['version_type'];
			$activation['config_info'] = json_decode($license_data['config_info'],true);
			$activation['use_info'] = $activation_data['use_info'];
			$activation['hardware_info'] = $activation_data['hardware_info'];
			$activation['reviewer'] = 1;
			$save_data['use_info'] = json_encode($activation_data['use_info']);
			$save_data['hardware_info'] = json_encode($activation_data['hardware_info']);
			$save_data['use_status'] = $activation['use_status'];
			$save_data['use_time'] = $activation['use_time'];
			
			$save_data['start_time'] = $activation['start_time'];
			$save_data['end_time'] = $activation['end_time'];

			$save_data['activation_code'] = licensecrypto(json_encode($activation),'encode');
			$reuslt=db('license')->where(array('id'=>$license_data['id']))->update($save_data);

			if (false !== $reuslt) {
				return $this->success(lang('license').lang('success'),url('license/index'));
			} else {
				return $this->error(lang('license').lang('fail'), '');
			}
			
			
		
		}
		
	}
	
	
	/**
	 * 授权码删除
	 */
	public function license_del() {
		$id  = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));	
		}
		$check_data = db('license')->where(array('id'=>$id))->find();
		if(!$check_data){
			return $this->error(lang('authorization_code_does_not_exist'));	
		}
	
		if($check_data['status'] == 1){
		//判断是否能删除	
		}
		
		
		$reuslt = db('license')->where(array('id'=>$check_data['id']))->delete();
		if($reuslt){
			return $this->success(lang('delete').lang('success'));	
		}else{
			return $this->error(lang('delete').lang('fail'));	
		}
	}


}