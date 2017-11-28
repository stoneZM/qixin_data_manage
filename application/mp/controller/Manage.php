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

class Manage extends Admin {


	public function _initialize() {
		parent::_initialize();
		
		$this->mp = model('mp');

	}
	/**
	 * MP列表
	 */
	public function index() {
		
		
		$mpid = get_mpid();
		if(!$mpid){
			$mpinfo = model('mp')->where(array('status'=>1))->order('id asc')->find();
			if($mpinfo){
				get_mpid($mpinfo['id']);
			}
		}
		
		$mp_info = get_mp_info();
		$list = $this->mp->order('id asc')->paginate(25);
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		
		$this->assign('mp_info',$mp_info);
		
		$this->setMeta(lang('mp_list'));
		$this->assign($data);
		return $this->fetch();
	}
	
	//添加MP
	public function add() {
		if (IS_POST) {
			$data = input();
			if(!$data['name']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			
			$mp_data = db('mp')->where(array('name'=>$data['name']))->find();
			if($mp_data){
				return $this->error(lang('mp_name_already_exist'));
			}
			
			if(!$data['origin_id']){
				return $this->error(lang('origin_id').lang('cannot_be_empty'));
			}
			
			if(!preg_match('/^gh_[0-9|a-z]{12}$/',$data['origin_id'])){
				return $this->error(lang('origin_id_format_error'));
			}
			
			$originid_check_data = db('mp')->where(array('origin_id'=>$data['origin_id']))->find();
			if($originid_check_data){
				return $this->error(lang('origin_id_already_exist'));
			}
			if(!$data['mp_number']){
				return $this->error(lang('mp_number').lang('cannot_be_empty'));
			}
			if(!$data['appid']){
				return $this->error('APPID'.lang('cannot_be_empty'));
			}
			if(!$data['appsecret']){
				return $this->error('Appsecret'.lang('cannot_be_empty'));
			}
			
			$savedata['name'] = $data['name'];
			$savedata['type'] = $data['type'];
			$savedata['origin_id'] = $data['origin_id'];
			$savedata['appid'] = $data['appid'];
			$savedata['appsecret'] = $data['appsecret'];
			$savedata['mp_number'] = $data['mp_number'];
			$savedata['status'] = $data['status'];
			$savedata['uid'] = is_login();
				
			$savedata['create_time'] = time();
			$savedata['valid_token'] = get_nonce();
			$savedata['token'] = get_nonce(32);
			$savedata['encodingaeskey'] = get_nonce(43);
			
			
			$result = db('mp')->insert($savedata);
			if ($result) {
				return $this->success(lang('add').lang('success'), url('mp/manage/index'));
			} else {
				return $this->error(lang('add').lang('fail'));
			}
		} else {
			$data['status'] = 1;
			$data['type'] = 4;
			$this->assign('data',$data);
			$this->setMeta(lang('add').lang('mp'));
			return $this->fetch('edit');
		}
	}
	
	//编辑MP
	public function edit($id) {
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		if (IS_POST) {
			
			$data = input();
			
			if(!$data['name']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			$mp_data = db('mp')->where(array('name' => $data['name'],'id' => array('neq',$id)))->find();
			if($mp_data){
				return $this->error(lang('mp_name_already_exist'));
			}
			
			if(!$data['origin_id']){
				return $this->error(lang('origin_id').lang('cannot_be_empty'));
			}
			
			$origin_id_format = preg_match("/^gh_[0-9|a-z]{12}$/",$data['origin_id']);
			if(!$origin_id_format){
				return $this->error(lang('origin_id_format_error'));
			}
			$originid_check_data = db('mp')->where(array('origin_id'=>$data['origin_id'],'id' => array('neq',$id)))->find();
			if($originid_check_data){
				return $this->error(lang('origin_id_already_exist'));
			}
			
			if(!$data['mp_number']){
				return $this->error(lang('mp_number').lang('cannot_be_empty'));
			}
			
			if(!$data['appid']){
				return $this->error('APPID'.lang('cannot_be_empty'));
			}
			if(!$data['appsecret']){
				return $this->error('Appsecret'.lang('cannot_be_empty'));
			}
			
			$savedata['id'] = $data['id'];
			$savedata['name'] = $data['name'];
			$savedata['type'] = $data['type'];
			$savedata['origin_id'] = $data['origin_id'];
			$savedata['appid'] = $data['appid'];
			$savedata['appsecret'] = $data['appsecret'];
			$savedata['mp_number'] = $data['mp_number'];
			$savedata['status'] = $data['status'];
			$result = db('mp')->where(array('id' => $id))->update($savedata);
			if ($result) {
				return $this->success(lang('edit').lang('success'), url('mp/manage/index'));
			} else {
				return $this->error(lang('edit').lang('fail'));
			}
		} else {
			$data = db('mp')->where(array('id' => $id))->find();
			$this->assign('data',$data);
			$this->setMeta(lang('edit').lang('mp'));
			return $this->fetch('edit');
		}
	}
	
	
	/**
	 * 查看配置
	 */
	public function configure($mpid) {
		if (!$mpid) {
			return $this->error(lang('illegal_operation'));
		}
		//$mp_data = db('mp')->where(array('id' => $id))->find();
		
		global $_G;
		$info = get_mp_info();
		$this->assign('info', $info);
		
		$this->assign('api_url', url('/interface/'.$info['token']));
		
		$this->assign('info',$info);
		$this->setMeta(lang('mp').lang('configure'));
		return $this->fetch();
		
	}	
	
	

	/**
	 * MP删除
	 */
	public function delete($id) {
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		$result = db('mp')->where(array('id' => $id))->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}

	/**
	 * 清除缓存
	 */
	public function clear_cache() {
		$mp_info = get_mp_info();
		$flag = 'wechat_access_token'.$mp_info['appid'];
		cache($flag,null);
		return $this->success(lang('clear_cache').lang('success'));
	}
}