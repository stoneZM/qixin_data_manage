<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\goods\controller;
use app\common\controller\Admin;

class manage extends Admin {

	protected $model;

	public function _initialize() {
		parent::_initialize();
		
	}

	//应用列表
	public function index() {
		$list = db('goods')->where($map)->order('id desc')->paginate(10);
		$list_data = $list ->toArray();
		if($list_data['data']){
			foreach ($list_data['data'] as $key => &$vo) {
				if ($vo['entity'] == 1) {
					$vo['entity_name'] = lang('plugin');
				} else {
					$vo['entity_name'] = lang('module');  
				}
				$good_v = db('goods_version')->where('goods_id',$vo['id'])->order('id desc')->select();
				if($good_v){
					$vo['version'] = $good_v[0]['title'];
				}else{
					$vo['version'] = lang('no_version');
				}	
			}
		}
		$data = array(
			'list' => $list_data['data'],
			'page' => $list->render(),
			'type' => $type,
		);
		$this->assign($data);
		$this->setMeta(lang('application_list'));
		return $this->fetch();
	}

	//添加应用
	public function add() {
		if (IS_POST) {
			$data = input();
			if(!$data['title']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			if(!$data['etitle']){
				return $this->error(lang('identifying').lang('cannot_be_empty'));
			}
			$good_data = db('goods')->where(array('etitle'=>$data['etitle']))->find();
			if($good_data){
				return $this->error(lang('logo_is_occupied_please_replace'));
			}
			
			
			$savedata['title'] = $data['title'];
			$savedata['etitle'] = $data['etitle'];
			$savedata['content'] = $data['content'];
			$savedata['icon'] = $data['icon'];
			$savedata['cover_id'] = $data['cover_id'];
			$savedata['is_recommend'] = $data['is_recommend'];
			$savedata['entity'] = $data['entity'];
			$savedata['summary'] = $data['summary'];
			$savedata['is_com'] = $data['is_com'];
			$savedata['status'] = $data['status'];
			$savedata['create_time'] = time();
			$savedata['update_time'] = time();
			$savedata['uid'] = is_login();
			$result = db('goods')->insert($savedata);
			if ($result) {
				return $this->success(lang('add').lang('success'), url('goods/manage/index'));
			} else {
				return $this->error(lang('add').lang('fail'));
			}
		} else {
			$this->setMeta(lang('add').lang('application'));
			return $this->fetch('edit');
		}
	}

	//组编辑应用
	public function edit($id) {
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		if (IS_POST) {
			
			$data = input();
			
			if(!$data['title']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			if(!$data['etitle']){
				return $this->error(lang('identifying').lang('cannot_be_empty'));
			}
			
			$good_data = db('goods')->where(array('etitle' => $data['etitle'],'id' => array('neq',$id)))->find();
			if($good_data){
				return $this->error(lang('logo_is_occupied_please_replace'));
			}
			
			$savedata['id'] = $data['id'];
			$savedata['title'] = $data['title'];
			$savedata['etitle'] = $data['etitle'];
			$savedata['content'] = $data['content'];
			$savedata['icon'] = $data['icon'];
			$savedata['cover_id'] = $data['cover_id'];
			$savedata['is_recommend'] = $data['is_recommend'];
			$savedata['entity'] = $data['entity'];
			$savedata['summary'] = $data['summary'];
			$savedata['is_com'] = $data['is_com'];
			$savedata['status'] = $data['status'];
			$savedata['update_time'] = time();
			$savedata['uid'] = is_login();
			$result = db('goods')->where(array('id' => $id))->update($savedata);
			if ($result) {
				return $this->success(lang('edit').lang('success'), url('goods/manage/index'));
			} else {
				return $this->error(lang('edit').lang('fail'));
			}
		} else {
			$data = db('goods')->where(array('id' => $id))->find();
			$this->assign('data',$data);
			$this->setMeta(lang('edit').lang('application'));
			return $this->fetch('edit');
		}
	}
	
	//删除应用
	public function del($id) {
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		$result = db('goods')->where(array('id' => $id))->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}
	
	//版本列表
	public function goods_version() {
		$id = input('id','');
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		$good_data = db('goods')->where(array('id'=>$id))->find();
		$map['goods_id'] = $id;
		$list = db('goods_version')->where($map)->order('id desc')->paginate(10);
		$list_data = $list ->toArray();
		if($list_data['data']){
			foreach ($list_data['data'] as $key => &$vo) {
				if ($vo['file_id']) {
					$file_data = get_goods_file($vo['file_id']);
					$vo['file_url'] = $file_data['url'];
				} else {
					$vo['file_url'] = '';  
				}
				
			}
		}
		$data = array(
			'list' => $list_data['data'],
			'page' => $list->render(),
			'type' => $type,
		);
		$this->assign('good_data',$good_data);
		$this->assign('goods_id',$id);
		$this->assign($data);
		$this->setMeta($good_data['etitle'].' '.lang('version_manage'));
		return $this->fetch();
	}
	//添加版本
	public function version_add() {
		
		$goods_id = input('goods_id','');
		if (!$goods_id) {
			return $this->error(lang('illegal_operation'));
		}
		if (IS_POST) {
			$data = input();
			if(!$data['title']){
				return $this->error(lang('version_number').lang('cannot_be_empty'));
			}
			if(!$data['log']){
				return $this->error(lang('update_log').lang('cannot_be_empty'));
			}
			if(!$data['file_id']){
				return $this->error(lang('please_upload_version_file'));
			}
			
			$checkdata = db('goods_version')->where(array('goods_id' => $goods_id,'title' => $data['title']))->find();
			if($checkdata){
				return $this->error(lang('version_number_already_exists_please_replace'));
			}
			
			$savedata['title'] = $data['title'];
			$savedata['log'] = $data['log'];
			$savedata['status'] = $data['status'];
			$savedata['goods_id'] = $goods_id;
			$savedata['create_time'] = time();
			$savedata['update_time'] = time();
			$savedata['file_id'] = $data['file_id'];
			$filedata = get_goods_file($data['file_id']);
			$savedata['token'] = $filedata['md5'];
			$savedata['explain'] = $data['explain'];
			$result = db('goods_version')->insert($savedata);
			if ($result) {
				return $this->success(lang('add').lang('success'), url('goods/manage/goods_version',array('id'=>$goods_id)));
			} else {
				return $this->error(lang('add').lang('fail'));
			}
		} else {
			
			$this->assign('goods_id',$goods_id);
			$this->setMeta(lang('add').lang('version'));
			return $this->fetch('version_edit');
		}
	}
	//编辑版本
	public function version_edit() {
		
		$goods_id = input('goods_id','');
		if (!$goods_id) {
			return $this->error(lang('illegal_operation'));
		}
		$id = input('id','');
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		if (IS_POST) {
			$data = input();
			if(!$data['title']){
				return $this->error(lang('version_number').lang('cannot_be_empty'));
			}
			if(!$data['log']){
				return $this->error(lang('update_log').lang('cannot_be_empty'));
			}
			if(!$data['file_id']){
				return $this->error(lang('please_upload_version_file'));
			}
			
			$checkdata = db('goods_version')->where(array('goods_id' => $goods_id,'title' => $data['title'],'id' => array('neq',$id)))->find();
			if($checkdata){
				return $this->error(lang('version_number_already_exists_please_replace'));
			}
			
			$savedata['title'] = $data['title'];
			$savedata['log'] = $data['log'];
			$savedata['status'] = $data['status'];
			$savedata['goods_id'] = $goods_id;
			$savedata['update_time'] = time();
			$savedata['file_id'] = $data['file_id'];
			$filedata = get_goods_file($data['file_id']);
			$savedata['token'] = $filedata['md5'];
			$savedata['explain'] = $data['explain'];
			
			$result = db('goods_version')->where(array('id' => $id))->update($savedata);
			if ($result) {
				return $this->success(lang('edit').lang('success'), url('goods/manage/goods_version',array('id'=>$goods_id)));
			} else {
				return $this->error(lang('edit').lang('fail'));
			}
		} else {
			$data = db('goods_version')->where(array('id' => $id))->find();
			$this->assign('data',$data);
			$this->assign('goods_id',$goods_id);
			$this->setMeta(lang('edit').lang('version'));
			return $this->fetch('version_edit');
		}
	}
	
	//删除版本
	public function version_del($id) {
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		$result = db('goods_version')->where(array('id' => $id))->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}
	//系统版本列表
	public function system() {

		$list = db('goods_update')->where($map)->order('number desc')->paginate(10);
		$list_data = $list ->toArray();
		if($list_data['data']){
			foreach ($list_data['data'] as $key => &$vo) {
				if ($vo['file_id']) {
					$file_data =  get_goods_file($vo['file_id']);
					$vo['file_url'] = $file_data['url'];
				} else {
					$vo['file_url'] = '';  
				}
				
				if ($vo['install_id']) {
					$install_data =  get_goods_file($vo['install_id']);
					$vo['install_url'] = $install_data['url'];
				} else {
					$vo['install_url'] = '';  
				}
				
			}
		}
		$data = array(
			'list' => $list_data['data'],
			'page' => $list->render(),
			'type' => $type,
		);
		$this->assign('good_data',$good_data);
		$this->assign($data);
		$this->setMeta(lang('system_version_manage'));
		return $this->fetch();
	}
	
	//添加版本
	public function system_add() {
		
		if (IS_POST) {
			$data = input();
			if(!$data['title']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			if(!$data['name']){
				return $this->error(lang('version_number').lang('cannot_be_empty'));
			}
			if(!$data['log']){
				return $this->error(lang('updata_log').lang('cannot_be_empty'));
			}
			if(!$data['file_id']){
				return $this->error(lang('upgrade_package').lang('cannot_be_empty'));
			}
			$checkdata = db('goods_update')->where(array('number' => $data['number']))->find();
			if($checkdata){
				return $this->error(lang('number_record_already_exists_please_replace'));
			}		
			$checkdata = db('goods_update')->where(array('name' => $data['name']))->find();
			if($checkdata){
				return $this->error(lang('version_number_already_exists_please_replace'));
			}
			
			$savedata['number'] = $data['number'];
			$savedata['title'] = $data['title'];
			$savedata['name'] = $data['name'];
			$savedata['log'] = $data['log'];
			$savedata['url'] = $data['url'];
			$savedata['status'] = $data['status'];
			$savedata['create_time'] = time();
			$savedata['update_time'] = time();
			$savedata['file_id'] = $data['file_id'];
			$savedata['install_id'] = $data['install_id'];
			$savedata['is_current'] = 1;
			
			$result = db('goods_update')->insert($savedata);
			if ($result) {
				db('goods_update')->where('number != '.$data['number'])->update(array('is_current'=>0));
				return $this->success(lang('add').lang('success'), url('goods/manage/system'));
			} else {
				return $this->error(lang('add').lang('fail'));
			}
		} else {
			
			$data['number'] = date('Ymd',time());
			$this->assign('data',$data);
			$this->setMeta(lang('add').lang('version'));
			return $this->fetch('system_edit');
		}
	}
	//编辑版本
	public function system_edit() {
		
		$number = input('number','');
		if (!$number) {
			return $this->error(lang('illegal_operation'));
		}
		if (IS_POST) {
			$data = input();
			if(!$data['title']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			if(!$data['name']){
				return $this->error(lang('version_number').lang('cannot_be_empty'));
			}
			if(!$data['log']){
				return $this->error(lang('updata_log').lang('cannot_be_empty'));
			}
			if(!$data['file_id']){
				return $this->error(lang('upgrade_package').lang('cannot_be_empty'));
			}
				
			$checkdata = db('goods_update')->where(array('name' => $data['name'],'number' => array('neq',$number)))->find();
			if($checkdata){
				return $this->error(lang('version_number_already_exists_please_replace'));
			}
			
			
			$savedata['number'] = $data['number'];
			$savedata['title'] = $data['title'];
			$savedata['name'] = $data['name'];
			$savedata['log'] = $data['log'];
			$savedata['url'] = $data['url'];
			$savedata['status'] = $data['status'];
			$savedata['update_time'] = time();
			$savedata['file_id'] = $data['file_id'];
			$savedata['install_id'] = $data['install_id'];
			
			$result = db('goods_update')->where(array('number' => $number))->update($savedata);
			if ($result) {
				return $this->success(lang('edit').lang('success'), url('goods/manage/system'));
			} else {
				return $this->error(lang('edit').lang('fail'));
			}
		} else {
			$data = db('goods_update')->where(array('number' => $number))->find();
			$this->assign('number',$number);
			$this->assign('data',$data);
			$this->setMeta(lang('edit').lang('version'));
			return $this->fetch('system_edit');
		}
	}
	
	//删除版本
	public function system_del($number) {
		if (!$number) {
			return $this->error(lang('illegal_operation'));
		}
		$result = db('goods_update')->where(array('number' => $number))->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}



	/**
	 * 动态列表
	 */
	public function feed(){
		
		$type_arr = array('1'=>'动态','2'=>'产品','3'=>'方案');
		$list = db('goods_feed')->where($map)->order('id desc')->paginate(15);
		$list_data = $list ->toArray();
		if($list_data['data']){
			foreach ($list_data['data'] as $k => &$v) {
				$v['type'] = 	$type_arr[$v['type']];
			}
		}
		$data = array(
				'list' => $list_data['data'],
				'page' => $list->render(),
				'type' => $type_arr,
		);

		$this->assign($data);
		$this->setMeta(lang('feed_list'));
		return $this->fetch();
	}

	/**
	 * 添加动态
	 */
	public function feed_add(){

		if (IS_POST) {
			$data = input();
			if(!$data['title']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			if(!$data['summary']){
				return $this->error(lang('summary').lang('cannot_be_empty'));
			}
			if(!$data['content']){
				return $this->error(lang('content').lang('cannot_be_empty'));
			}
			$savedata['title'] = $data['title'];
			$savedata['summary'] = $data['summary'];
			$savedata['cover_id'] = $data['cover_id'];
			$savedata['content'] = $data['content'];
			$savedata['status'] = $data['status'];
			$savedata['type'] = $data['type'];
			$savedata['is_recommend'] = $data['is_recommend'];
			$savedata['create_time'] = time();
			$result = db('goods_feed')->insert($savedata);
			if ($result) {
				return $this->success(lang('add').lang('success'), url('goods/manage/feed'));
			} else {
				return $this->error(lang('add').lang('fail'));
			}
		} else {
			$type_arr = array('1'=>'动态','2'=>'产品','3'=>'方案');
			$this->assign('types',$type_arr);
			$this->setMeta(lang('add').lang('feed'));
			return $this->fetch('feed_edit');
		}
	}

	/**
	 *   动态的编辑
	 */
	public function feed_edit(){

		$id = input('id');
		if (IS_POST) {
			$data = input();
			if(!$data['title']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			if(!$data['summary']){
				return $this->error(lang('summary').lang('cannot_be_empty'));
			}

			if(!$data['content']){
				return $this->error(lang('content').lang('cannot_be_empty'));
			}
			$savedata['title'] = $data['title'];
			$savedata['summary'] = $data['summary'];
			$savedata['cover_id'] = $data['cover_id'];
			$savedata['content'] = $data['content'];
			$savedata['status'] = $data['status'];
			$savedata['type'] = $data['type'];
			$savedata['is_recommend'] = $data['is_recommend'];
			$savedata['create_time'] = time();
			$result = db('goods_feed')->where(array('id' => $id))->update($savedata);
			if ($result) {
				return $this->success(lang('edit').lang('success'), url('goods/manage/feed'));
			} else {
				return $this->error(lang('edit').lang('fail'));
			}
		   } else {
			
			$data = db('goods_feed')->where(array('id' => $id))->find();
			$this->assign('feed',$data);
			$type_arr = array('1'=>'动态','2'=>'产品','3'=>'方案');
			$this->assign('types',$type_arr);
			$this->setMeta(lang('edit').lang('feed'));
			return $this->fetch('feed_edit');
		 }
	}

	/**
	 * 动态删除
	 */
	public function feed_del($id) {

		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		$result = db('goods_feed')->where(array('id' => $id))->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}


	/**
	 *   广告列表
	 */
	public  function advert(){

		$list = db('goods_advert')->where($map)->order('id desc')->paginate(10);
		$list_data = $list ->toArray();
		$data = array(
				'list' => $list_data['data'],
				'page' => $list->render(),
				'type' => $type,
		);

		$this->assign($data);
		$this->setMeta(lang('advert_list'));
		return $this->fetch();
	}

	/**
	 *   添加广告
	 */
    public  function advert_add(){

		if (IS_POST) {
			$data = input();
			if(!$data['title']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			if(!$data['cover_id']){
				return $this->error(lang('cover_id').lang('cannot_be_empty'));
			}

			if(!$data['link']){
				return $this->error(lang('link').lang('cannot_be_empty'));
			}

			$savedata['title'] = $data['title'];
			$savedata['cover_id'] = $data['cover_id'];
			$savedata['link'] = $data['link'];
			$savedata['status'] = $data['status'];
			$savedata['level'] = $data['level'];
			$savedata['create_time'] = time();
			$result = db('goods_advert')->insert($savedata);
			if ($result) {
				return $this->success(lang('add').lang('success'), url('goods/manage/advert'));
			} else {
				return $this->error(lang('add').lang('fail'));
			}
		} else {
			$this->setMeta(lang('add').lang('advert'));
			return $this->fetch('advert_edit');
		}
	}

	/**
	 *   编辑广告
	 */
	public function advert_edit(){

		$id = input('id');
		if (IS_POST) {
			$data = input();
			if(!$data['title']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			if(!$data['cover_id']){
				return $this->error(lang('cover_id').lang('cannot_be_empty'));
			}

			if(!$data['link']){
				return $this->error(lang('link').lang('cannot_be_empty'));
			}
			$savedata['title'] = $data['title'];
			$savedata['cover_id'] = $data['cover_id'];
			$savedata['link'] = $data['link'];
			$savedata['status'] = $data['status'];
			$savedata['level'] = $data['level'];
			$savedata['create_time'] = time();
			$result = db('goods_advert')->where(array('id' => $id))->update($savedata);

			if ($result) {
				return $this->success(lang('edit').lang('success'), url('goods/manage/advert'));
			} else {
				return $this->error(lang('edit').lang('fail'));
			}
		} else {
			$data = db('goods_advert')->where(array('id' => $id))->find();
			$this->assign('advert',$data);
			$this->setMeta(lang('edit').lang('advert'));
			return $this->fetch('advert_edit');
		}
	}


	/**
	 *   删除广告
	 */
   public function advert_del($id){

	   if (!$id) {
		   return $this->error(lang('illegal_operation'));
	   }
	   $result = db('goods_advert')->where(array('id' => $id))->delete();
	   if ($result) {
		   return $this->success(lang('delete').lang('success'));
	   } else {
		   return $this->error(lang('delete').lang('fail'));
	   }
   }
}