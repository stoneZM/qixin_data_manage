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

class Index extends Base
{

	//网站首页
	public function index(){
		
		
		
		$app_module = db('goods')->where(array('entity'=>2,'status'=>1))->order('is_recommend desc,id desc')->limit(0,4)->select();
		$recommend_module = db('goods')->where(array('entity'=>2,'status'=>1,'is_recommend'=>1))->limit(0,3)->order('id desc')->select();
		$app_plugin = db('goods')->where(array('entity'=>1,'status'=>1))->order('id desc')->limit(0,4)->select();
		$recommend_plugin = db('goods')->where(array('entity'=>1,'status'=>1,'is_recommend'=>1))->limit(0,3)->order('id desc')->select();
		$feed_list = db('goods_feed')->where(array('status'=>1))->limit(0,4)->order('id desc')->select();
		$recommend_feed_top = db('goods_feed')->where(array('status'=>1,'is_recommend'=>1,'cover_id'=>array('neq',0)))->limit(0,1)->order('id desc')->select();
		$advert_list = db('goods_advert')->where(array('status'=>1))->order('level desc')->limit(0,3)->select();
		$this->assign('app_module',$app_module);
		$this->assign('app_plugin',$app_plugin);
		
		$this->assign('recommend_module',$recommend_module);
		$this->assign('recommend_plugin',$recommend_plugin);
		
		$this->assign('feed_list',$feed_list);
		$this->assign('recommend_feed_top',$recommend_feed_top);
		$this->setMeta(lang('appstore'));
		$this->assign('advert',$advert_list);
		
		return $this->fetch();
		

	}
	public function downloads(){
		$map['status'] = 1;
		$version_info = db('goods_update')->where($map)->field('number,name,title,create_time,log,file_id,install_id')->order('create_time desc')->find();
		$hashids = hashids(8,"qinfo360");
		
		
		if($version_info['log']){
			$version_info['log'] = explode("\r\n", $version_info['log']);
		}
		
		if($version_info['install_id']){
			$version_info['link'] = $hashids->encode($version_info['install_id']); //加密
		}else{
			$version_info['link'] = $hashids->encode($version_info['file_id']); //加密	
		}
		
		unset($version_info['file_id']);
		unset($version_info['install_id']);
		$this->assign('version_info',$version_info);
		$this->setMeta('Qinfo安装包下载');
		return $this->fetch();
	}
	
	
	public function historyweb(){
		$map['status'] = 1;
		$version_info = db('goods_update')->where($map)->field('number,name,title,create_time,log,file_id,install_id')->order('create_time desc')->select();
		$hashids = hashids(8,"qinfo360");
		foreach ($version_info as $key => &$m) {
			if($m['file_id']){
				$m['upgrade'] = $hashids->encode($m['file_id']); //加密	
			}
			if($m['install_id']){
				$m['install'] = $hashids->encode($m['install_id']); //加密	
			}
			if($m['log']){
				$m['log'] = explode("\r\n", $m['log']);
			}
			unset($m['file_id']);
			unset($m['install_id']);
		}
		$this->assign('version_info',$version_info);
		$this->setMeta('历史版本');
		return $this->fetch();
	}	
	public function getSetupToken(){
		
		$version_id = input('version_id');
		$sn = input('sn');
		if(!$version_id){
			return $this->error("参数错误！");
		}
		if(!$sn){
			return $this->error("授权码未找到，请刷新重试！");
		}
		
		
		$app_version = db('goods_version')->where(array('id'=>$version_id,'status'=>1))->find();
		if(!$app_version){
			return $this->error("版本文件不存在！");
		}
		
		$app_goods = db('goods')->where(array('id'=>$app_version['goods_id'],'status'=>1))->find();
		if(!$app_goods){
			return $this->error("应用不存在，或已下架！");
		}
		$license_data = db('license')->where(array('cdkey'=>$sn,'status'=>1))->find();
		if(!$license_data){
			return $this->error("授权码未找到,请重新授权");	
		}
		if($license_data['type'] == 1 ){
			if($license_data['expiration_time'] <= time()){
				return $this->error("授权码过期,请重新授权");	
			}
		}
		if(!$license_data['config_info']){
			return $this->error("授权码文件出错，请联系官方客服");	
		}
		if(!$license_data['activation_code']){
			return $this->error("当前授权码还未认证，请联系官方客服");	
		}
		$license_info  =  json_decode($license_data['config_info'],true);
		if(!in_array($app_goods['etitle'],$license_info['module'])){
			return $this->error("您的授权没有当前应用，请购买后重新授权！");
		}
		return $this->success("验证成功",'',$app_version['token'],'');
	}	
	
	
	public function link(){
		
		$token = input('id');
		if(!$token){
			return $this->error("参数错误！");
		}
		
		$hashids = hashids(8,"qinfo360");
		$file_id = $hashids->decode($token); //加密
		
		$file_data = get_goods_file($file_id);
		if($file_data['url']){
			return \File::download('.'.$file_data['url']);
		}else{
			return $this->error("版本不存在！");
		}
	}
	
	public function download(){
		
		$token = input('token');
		$id = input('id');

		if(!$token && !$id){
			return $this->error("错误的版本信息！");
		}
		if($token){
			$map['token'] = $token;
		}
		if($id){
			$map['id'] = $id;
		}
		
		$map['status'] = 1;
		$down_data = Db::name('goods_version')->where($map)->find();
		if($down_data){
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
	}


	/**
	 *   商品详情页
	 */
	public function goodsdetail(){

		$id = input('id');
		$token =  input('token');
		
		if(!$id && !$token){
			return $this->error(lang('parameter_error'));
		}
		if(!$id){
			$token_data = db('goods_version')->where(array('token'=>$token))->find();
			$id = $token_data['goods_id'];
		}
		$app_detail = db('goods')->where(array('id'=>$id,'status'=>'1'))->find();
		if(!$app_detail){
			return $this->error(lang('goods_does_not_exist'));
		}
		
		$app_version = db('goods_version')->where(array('goods_id'=>$id,'status'=>1))->order('title desc')->select();
		if($app_version){
			foreach ($app_version as $key => &$m) {
				if($m['log']){
					$m['log'] = explode("\r\n", $m['log']);
				}
			}
			
			$newest_version = $app_version[0];
		}
		
		
		$this->assign('newest_version',$newest_version);
		$this->assign('app_version',$app_version);
		$this->assign('goods_detail',$app_detail);
		$this->setMeta($app_detail['title']);
		return $this->fetch('goods_detail');
	}
	
	/**
	 *   商品详情页
	 */
	public function setup(){

		$id = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));
		}
		$app_version = db('goods_version')->where(array('id'=>$id,'status'=>'1'))->find();
		if(!$app_version){
			return $this->error('版本不存在');
		}
		if($app_version['log']){
			$app_version['log'] = explode("\r\n", $app_version['log']);
		}
		
		
		$goods_detail = db('goods')->where(array('id'=>$app_version['goods_id'],'status'=>'1'))->find();
		if(!$goods_detail){
			return $this->error(lang('goods_does_not_exist'));
		}
		
		
		$this->assign('app_version',$app_version);
		$this->assign('goods_detail',$goods_detail);
		$this->setMeta('安装商品');
		return $this->fetch();
	}	
	
	

	/**
	 *   获取更多推荐模块
	 */
	public function module(){
		$list = db('goods')->where(array('entity'=>2,'status'=>1))->order('id desc')->paginate(10);
		$list_data = $list ->toArray();
		
		
		if($list_data['data']){
			foreach ($list_data['data'] as $key => &$m) {
					$good_version = db('goods_version')->where(array('goods_id'=>$m['id'],'status'=>1))->find();
					$m['version'] = $good_version['title'];
			}
		}
		$data = array(
				'app_module' => $list_data['data'],
				'page' => $list->render(),
				'type' => $type,
		);

		$title = '模块 Module';
		$summary = '模块存放在/Application目录下，可以自由安装卸载';

		$this->assign($data);
		$this->assign('title',$title);
		$this->assign('summary',$summary);
		$this->setMeta('模块');
		return $this->fetch();
	}

	/**
	 *   获取更多插件
	 */
	public function plugin(){

		$list = db('goods')->where(array('entity'=>1,'status'=>1))->order('id desc')->paginate(10);
		$list_data = $list ->toArray();
		$data = array(
				'app_module' => $list_data['data'],
				'page' => $list->render(),
				'type' => $type,
		);

		$title = '插件 Plugin';
		$summary = '插件存放在/Addons目录下，可以自由开启关闭，插件依赖于钩子。';

		$this->assign($data);
		$this->assign('title',$title);
		$this->assign('summary',$summary);
		$this->setMeta('插件');
		return $this->fetch('module');

	}


	/**
	 * 获取动态
	 */
	public function feed(){

		$list = db('goods_feed')->where(array('status'=>1))->order('id desc')->paginate(12);
		$list_data = $list ->toArray();
		$data = array(
				'feed_data' => $list_data['data'],
				'page' => $list->render(),
				'type' => $type,
		);
		$this->setMeta('动态');
		$this->assign($data);
		return $this->fetch();
	}

	/**
	 * 动态的详情
	 */
	public function feed_detail(){

		$id = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));
		}
		$feed_detail = db('goods_feed')->where(array('id'=>$id,'status'=>'1'))->find();
		if(!$feed_detail){
			return $this->error(lang('feed_does_not_exist'));
		}
		$this->setMeta($feed_detail['title']);
		$this->assign('feed_detail',$feed_detail);
		return $this->fetch('feed_detail');
	}


}
