<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\index\controller;
use app\index\controller\Base;

class Index extends Base{

	//网站首页
	public function index(){
		$goods_module = db('module')->where(array('name'=>'goods','is_setup'=>1))->find();
		if(!$goods_module){
			return \think\Response::create(\think\Url::build('/admin'), 'redirect');
		}
		//设置SEO
		$this->setMeta('齐信软件');
		return $this->fetch();
	}
}
