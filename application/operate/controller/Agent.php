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

class Agent extends Admin
{
	/**
	* 授权码管理
	*/
    public function index()
    {
		
		$order = "id asc";
		$list  =db('agent')->where('')->order($order)->paginate(15);
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('agent_manage'));
        return $this->view->fetch();
    }
	
	
	
	/**
	* 授权码新增
	*/
	public function agent_add() {
		
		if (IS_POST) {
			
			$data = $this->request->post();
			if(!$data['level']){
				return $this->error(lang('level').lang('cannot_be_empty'));
			}
			if(!$data['name']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			
			if(!$data['alias']){
				return $this->error(lang('alias').lang('cannot_be_empty'));
			}
			
			$where['alias'] = array('eq',$data['alias']);
			//$where['status'] = array('neq',-1);
			$check_alias = db('agent')->where($where)->find();
			if($check_alias){
				return $this->error(lang('alias_already_exists'));
			}
			
			if(!$data['telephone']){
				return $this->error(lang('telephone').lang('cannot_be_empty'));
			}
			if(!$data['email']){
				return $this->error(lang('email').lang('cannot_be_empty'));
			}
			
			
			$save_data['level'] = $data['level'];
			$save_data['creation_time'] = time();
			$save_data['status'] = $data['status'];
			$save_data['name'] = $data['name'];
			$save_data['alias'] = $data['alias'];
			$save_data['telephone'] = $data['telephone'];
			$save_data['email'] = $data['email'];
			$save_data['address'] = $data['address'];
			$reuslt=db('agent')->insert($save_data);

			if (false !== $reuslt) {
				return $this->success(lang('add').lang('success'),url('agent/index'));
			} else {
				return $this->error(lang('add').lang('fail'), '');
			}
		} else {
			$this->setMeta(lang('agent_add'));
			return $this->fetch('agent_edit');
		}
	}
	
	/**
	* 授权码修改
	*/
	public function agent_edit() {
		
		$id = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));	
		}
		$agent_data = db('agent')->where(array('id'=>$id))->find();
		if(!$agent_data){
			return $this->error(lang('user_does_not_exist'));	
		}
		
		
		
		if (IS_POST) {
			
			$data = $this->request->post();
			if(!$data['level']){
				return $this->error(lang('level').lang('cannot_be_empty'));
			}
			if(!$data['name']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			
			if(!$data['alias']){
				return $this->error(lang('alias').lang('cannot_be_empty'));
			}
			
			$where['alias'] = array('eq',$data['alias']);
			$where['id'] = array('neq',$id);
			$check_alias = db('agent')->where($where)->find();
			if($check_alias){
				return $this->error(lang('alias_already_exists'));
			}
			
			
			if(!$data['telephone']){
				return $this->error(lang('telephone').lang('cannot_be_empty'));
			}
			if(!$data['email']){
				return $this->error(lang('email').lang('cannot_be_empty'));
			}
			
			
			
			$save_data['level'] = $data['level'];
			$save_data['creation_time'] = time();
			$save_data['status'] = $data['status'];
			
			$save_data['name'] = $data['name'];
			$save_data['alias'] = $data['alias'];
			$save_data['telephone'] = $data['telephone'];
			$save_data['email'] = $data['email'];
			$save_data['address'] = $data['address'];
			
			$reuslt=db('agent')->where(array('id'=>$id))->update($save_data);

			if (false !== $reuslt) {
				return $this->success(lang('edit').lang('success'),url('agent/index'));
			} else {
				return $this->error(lang('edit').lang('fail'), '');
			}
		} else {
		
		
			$this->assign('info',$agent_data);
			$this->setMeta(lang('agent_edit'));
			return $this->fetch('agent_edit');
		}
	}
	/**
	 * 客户删除
	 */
	public function agent_del() {
		$id  = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));	
		}
		$check_data = db('agent')->where(array('id'=>$id))->find();
		if(!$check_data){
			return $this->error(lang('user_does_not_exist'));	
		}
	
		$reuslt = db('agent')->where(array('id'=>$check_data['id']))->delete();
		if($reuslt){
			return $this->success(lang('delete').lang('success'));	
		}else{
			return $this->error(lang('delete').lang('fail'));	
		}
	}


}