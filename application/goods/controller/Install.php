<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\goods\controller;
use think\Db; 


class Install extends \think\Controller {
	
	public function index(){
		
	}
	public function getversion(){
		$token = input('token');
		if(!$token){
			return false;	
		}
		$goods_version = $this->get_goods_version($token);
		if($goods_version){
			$goods = $this->getgoods($goods_version['goods_id']);
			$goods_version['goods'] = $goods ;
			$goods_version['goods']['version'] = $this->get_goods_version_all($goods['id']);
			$data['version'] = $goods_version;	
			$data['status'] = 1;
		}else{
			$data['info'] = 'token不存在';	
			$data['status'] = 0;
		}
		return json($data);
	}
	
	public function getgoods($id){
		if(!$id){
			return false;	
		}
		$map['id'] = $id;
		$map['status'] = 1;
		$goodsdata = Db::name('goods')->where($map)->find();
		return $goodsdata;
	}
	public function get_goods_version($token){
		
		if(!$token){
			return false;	
		}
		
		$map['token'] = $token;
		$map['status'] = 1;
		$versiondata = Db::name('goods_version')->where($map)->find();
		return $versiondata;
	}
	
	public function get_goods_version_all($goods_id,$desc='id desc'){
		if(!$goods_id){
			return false;	
		}
		$map['goods_id'] = $goods_id;
		$map['status'] = 1;
		$goodsdata = Db::name('goods_version')->where($map)->order($desc)->select();
		return $goodsdata;
	}
	
	public function getupdatelist(){
		
		$token = input('token');
		if(!$token){
			return false;	
		}

		$version_data = $this->get_goods_version($token);
		if($version_data){
			$all_version_data = $this->get_goods_version_all($version_data['goods_id']);
			foreach ($all_version_data as $key=> &$v) {
			  $updatelist[$key] = $v;
			  if($v['id'] == $version_data['id']){
				unset($updatelist[$key]);
				break;
			  }
			}
			$data['updateList'] = $updatelist;	
			$data['status'] = 1;
					
		}else{
			$data['info'] = 'token不存在';	
			$data['status'] = 0;
		}
		
		return json($data);
	}
	
	public function get_downlist($token=''){
		
		if(!$token){
			return false;	
		}
		$steps = model('Steps');
		$version_data = $this->get_goods_version($token);

		if($version_data){
			$all_version_data = $this->get_goods_version_all($version_data['goods_id']);
			if($all_version_data){
				foreach ($all_version_data as $key=> &$v) {
					$down_list[$v['id']] =$v;
					if($v['id'] == $version_data['id']){
						$current_key = $v['id'];
					}
					$steps->add($v['id']);
				}
				unset($key);
				$steps->setCurrent($current_key);
			 	$data['list'] = $down_list;
				$data['prev'] = $steps->getPrev();
				$data['current'] = $steps->getCurrent();
				$data['next'] = $steps->getNext();
				
				return $data;
			}else{
				return false;	
			}		
		}else{
			return false;
		}
	}
	
	
	
	public function download(){
		
		$token = input('token');
		if(!$token){
			return $this->error("错误的版本信息！");
		}
		$type = input('type','current');
		$type = strtolower($type);
		
		$downlits = $this->get_downlist($token);
		if($downlits){
			
			if($type =='next'){
				$down_key = $downlits['next'];
			}elseif($type =='prev'){
				$down_key = $downlits['prev'];
			}else{
				$down_key = $downlits['current'];
			}
			if($down_key){
				$down_data =  $downlits['list'][$down_key];
				if($down_data['file_id']){
					$file_data = get_goods_file($down_data['file_id']);
					if($file_data['url']){
						return \File::download('.'.$file_data['url']);
					}else{
						return $this->error("版本不存在！");
					}
				}else{
					return $this->error("版本不存在！");
				}
			}else{
				return $this->error("版本不存在！");
			}
		}else{
			return $this->error("版本不存在！");
		}
	}
	
	
}
