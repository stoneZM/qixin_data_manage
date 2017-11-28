<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\common\controller;

class Fornt extends Base {

	public function _initialize() {
		parent::_initialize();

		//判读是否为关闭网站
		if (\think\Config::get('web_site_close')) {
			header("Content-type:text/html;charset=utf-8");
			echo $this->fetch('common@public/close');exit();
		}

		//设置SEO
		$this->setSeo();

		$this->setHoverNav();
	}

	//当前栏目导航
	protected function setHoverNav() {
		//dump($_SERVER['PHP_SELF']);
	}
}
