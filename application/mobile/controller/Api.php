<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\mobile\controller;


class Api extends Base {
	
	protected $device;
	public function _initialize() {
		parent::_initialize();
	}
	
	public function index() {
		
		
		$signature = input('signature');
		$timestamp = input('timestamp');
		$nonce  =input('nonce');
		$token = 'qinfo360';
		
		
		$tmpArr = array($token,$timestamp,$nonce);
		
		sort($tmpArr);
		
		if(sha1(implode($tmpArr)) == $signature){
			return true;
		}else{
			return false;
		}
		
		
		
		
		
		
	}

}