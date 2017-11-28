<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\mobile\controller;

class Index extends Base {
	private $signPackage;
	
	public function _initialize() {
		parent::_initialize();
		
		
		$appid = config('wechat_appid');
		$appsecret = config('wechat_appsecret');
		if($appid  && $appsecret){
			$jssdk = new \WechatSdk\JsSdk($appid,$appsecret);
			$this->signPackage = $jssdk->GetSignPackage();
			$this->signPackage['debug'] = false;
			$this->assign('signPackage',$this->signPackage);	
		}
	}
	
	
	public function api() {

	}
	
	
	public function index($group_id = 1) {
		
		$app_module = db('goods')->where(array('entity'=>2,'status'=>1))->order('id desc')->limit(0,4)->select();
		$recommend_module = db('goods')->where(array('entity'=>2,'status'=>1,'is_recommend'=>1))->limit(0,3)->order('id desc')->select();
		
		$app_plugin = db('goods')->where(array('entity'=>1,'status'=>1))->order('id desc')->limit(0,4)->select();
		$recommend_plugin = db('goods')->where(array('entity'=>1,'status'=>1,'is_recommend'=>1))->limit(0,3)->order('id desc')->select();
		
		$feed_list = db('goods_feed')->where(array('status'=>1,'type'=>1))->limit(0,4)->order('id desc')->select();
		$recommend_feed = db('goods_feed')->where(array('status'=>1,'type'=>1,'is_recommend'=>1,'cover_id'=>array('neq',0)))->limit(0,1)->order('id desc')->select();
		
		
		$product_list = db('goods_feed')->where(array('status'=>1,'type'=>2))->limit(0,4)->order('id desc')->select();
		$recommend_product = db('goods_feed')->where(array('status'=>1,'type'=>2,'is_recommend'=>1,'cover_id'=>array('neq',0)))->limit(0,1)->order('id desc')->select();
		
		$plan_list = db('goods_feed')->where(array('status'=>1,'type'=>3))->limit(0,4)->order('id desc')->select();
		$recommend_plan = db('goods_feed')->where(array('status'=>1,'type'=>3,'is_recommend'=>1,'cover_id'=>array('neq',0)))->limit(0,1)->order('id desc')->select();
		
		
		$advert_list = db('goods_advert')->where(array('status'=>1))->order('level desc')->limit(0,3)->select();
		$this->assign('app_module',$app_module);
		$this->assign('app_plugin',$app_plugin);
		
		$this->assign('recommend_module',$recommend_module);
		$this->assign('recommend_plugin',$recommend_plugin);
		
		$this->assign('feed_list',$feed_list);
		$this->assign('recommend_feed',$recommend_feed_top);
		
		
		$this->assign('product_list',$product_list);
		$this->assign('recommend_product',$recommend_product);
		
		$this->assign('plan_list',$plan_list);
		$this->assign('recommend_plan',$recommend_plan);
		
		$this->assign('advert',$advert_list);
		$this->setMeta('齐信软件');
		return $this->fetch();
	}
	
	
	public function product() {
		$list = db('goods_feed')->where(array('status'=>1,'type'=>2))->order('id desc')->paginate(10);
		//$list_data = $list ->toArray();
		//$list_data['data']
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta('应用产品');
		return $this->fetch();
	}
	public function plan() {
		$list = db('goods_feed')->where(array('status'=>1,'type'=>3))->order('id desc')->paginate(10);
		//$list_data = $list ->toArray();
		//$list_data['data']
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta('解决方案');
		return $this->fetch();
	}
	
	public function feed() {
		$list = db('goods_feed')->where(array('status'=>1,'type'=>1))->order('id desc')->paginate(10);
		//$list_data = $list ->toArray();
		//$list_data['data']
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta('行业动态');
		return $this->fetch();
	}
	
	public function app() {
		$list = db('goods')->where(array('entity'=>2,'status'=>1))->order('id desc')->paginate(10);
		//$list_data = $list ->toArray();
		//$list_data['data']
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta('应用模块');
		return $this->fetch();
	}
	public function service() {
		$this->setMeta('服务');
		return $this->fetch();
	}	
	
	
	public function after_sale() {
		$this->setMeta('售后咨询');
		return $this->fetch();
	}		
	

	
	public function license() {
		
		$cdkey = input('cdkey');
		$sn = input('sn');
		
	
		
		if($cdkey){
			$map['cdkey'] = $cdkey;
		}
		if($sn && !$cdkey){
			$hashids = hashids(8,"qinfo360");
			$sn = $hashids->decode($sn); //解密
			$map['id'] = $sn;
		}
		
		if($map){
			$license_data=db('license')->where($map)->find();
			if($license_data){
				$use_info = json_decode($license_data['use_info'],true);
				$this->assign('use_info',$use_info);
				$config_info = json_decode($license_data['config_info'],true);
				if($config_info['storage']){
					$options= array('storage_net'=>'网络存储','storage_ali'=>'阿里存储','storage_qiniu'=>'七牛存储');
					$storage_data = 	$config_info['storage'];
					if($storage_data['storage_net']){
						$storage_list[0]['name'] = $options['storage_net'];
						$storage_list[0]['size'] = format_bytes($storage_data['storage_net_size']);
					}
					if($storage_data['storage_ali']){
						$storage_list[1]['name'] = $options['storage_ali'];
						$storage_list[1]['size'] = format_bytes($storage_data['storage_ali_size']);
					}
					if($storage_data['storage_qiniu']){
						$storage_list[2]['name'] = $options['storage_qiniu'];
						$storage_list[2]['size'] = format_bytes($storage_data['storage_qiniu_size']);
					}
					$this->assign('storage_list',$storage_list);
				
				}
				$this->assign('config_info',$config_info);
			
			}
			$this->assign('license_data',$license_data);	
		}
			
		$this->setMeta('授权查询');
		return $this->fetch();
	}
	
	/**
	 * 产品详情
	 */
	public function detail($id) {

		$feed_data = db('goods_feed')->where(array('status'=>1,'id'=>$id))->find();
		$feed_type = $feed_data['type'];
		
		if($feed_data['type'] == 1){
			$feed_data['type'] = '新闻详情';
		}elseif($feed_data['type'] == 2){
			$feed_data['type'] = '产品介绍';
		}elseif($feed_data['type'] == 3){
			$feed_data['type'] = '方案介绍';
		}
		$this->assign('feed_data',$feed_data);
		$this->setMeta('齐信软件');
		if($feed_type == 3){
			return $this->fetch('plan_detail');		
		}elseif($feed_type == 2){
			return $this->fetch('product_detail');		
		}else{
			return $this->fetch();	
		}
	}
	
	
	/**
	 * 产品详情
	 */
	public function module_detail($id) {
		
		$app_module = db('goods')->where(array('id'=>$id,'status'=>1))->find();
		if($app_module){
			$module_version = db('goods_version')->where(array('goods_id'=>$id,'status'=>1))->order('id desc')->select();
			if($module_version){
				foreach ($module_version as $key => &$vo) {
					$vo['log'] = explode("\r\n", $vo['log']);
				}
				$current_version = $module_version[0];	
			}
			
		}
		
		$this->assign('current_version',$current_version);
		$this->assign('module_version',$module_version);
		$this->assign('app_module',$app_module);
		$this->setMeta('齐信软件');
		return $this->fetch();
	}
	
}