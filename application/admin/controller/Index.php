<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\common\controller\Admin;

class Index extends Admin {

	public function index() {

		$this->setMeta(lang('console'));
		return $this->fetch();	
	}

	public function login($username = '', $password = '', $verify = '') {
		if (IS_POST) {
			if (!$username || !$password) {
				return $this->error(lang('Username_or_password_cannot_be_empty'), '');
			}
			//验证码验证
			$this->checkVerify($verify);

			$user = model('User');
			$uid  = $user->login($username, base64_decode($password));
			if ($uid > 0) {
				
				//记录行为
				action_log('user_login', 'member', $uid, session('user_auth.uid'));
				
				return $this->success(lang('Login_success'), url('admin/index/index'));
			} else {
				switch ($uid) {
				case -1:$error = lang('User_does_not_exist_or_is_disabled');
					break; //系统级别禁用
				case -2:$error = lang('Password_error');
					break;
				default:$error = lang('unknown_error');
					break; // 0-接口参数错误（调试阶段使用）
				}
				return $this->error($error, '');
			}
		} else {

			$this->setMeta(lang('user_login'));
			return $this->fetch();
		}
	}

	public function logout() {
		$user = model('User');
		$user->logout();
		return $this->success(lang('sign_out').lang('success'),url('/admin/login'));
	}

	public function clear() {
		if (IS_POST) {
			$clear = input('post.clear/a', array());
			foreach ($clear as $key => $value) {
				if ($value == 'cache') {
					\think\Cache::clear(); // 清空缓存数据
				} elseif ($value == 'log') {
					\think\Log::clear();
				}
			}
			return $this->success(lang('update').lang('success'), url('admin/index/index'));
		} else {
			$keylist = array(
				array('name' => 'clear', 'title' => lang('Update_cache'), 'type' => 'checkbox', 'help' => '', 'option' => array(
					'cache' => lang('file_cache'),
					'log'   => lang('log_cache'),
				),
			),
			);
			$data = array(
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('Update_cache'));
			return $this->fetch('common@public/edit');
		}
	}

}