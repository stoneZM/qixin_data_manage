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

class User extends Admin {

	/**
	 * 用户管理首页
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function index() {
		$nickname      = input('nickname');
		$map['status'] = array('egt', 0);
		$map['uid'] = array('gt', 1);


		if (is_numeric($nickname)) {
			$map['uid|nickname'] = array(intval($nickname), array('like', '%' . $nickname . '%'), '_multi' => true);
		} else {
			$map['nickname'] = array('like', '%' . (string) $nickname . '%');
		}

		$order = "uid desc";
		$list  = model('User')->where($map)->order($order)->paginate(15);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('user_list'));
		return $this->fetch();
	}

	/**
	 * 添加用户
	 * @author colin <register@qinfo360.com>
	 */
	public function add() {
		$model = \think\Loader::model('User');
		if (IS_POST) {
			$data = $this->request->param();
			//创建注册用户
			$uid = $model->register($data['username'], $data['password'], $data['repassword'], $data['email'], false);

			if (0 < $uid) {
				$userinfo = array('nickname' => $data['username'], 'status' => 1, 'reg_time' => time(), 'last_login_time' => time(), 'last_login_ip' => get_client_ip(1));
				/*保存信息*/
				if (!db('Member')->where(array('uid' => $uid))->update($userinfo)) {
					return $this->error(lang('add').lang('fail'), '');
				} else {
					return $this->success(lang('add').lang('success'), url('admin/user/index'));
				}
			} else {
				return $this->error($model->getError());
			}
		} else {
			
			$keylist = $model->addfield;
			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
				$item['help'] = lang($item['help']);
			}
			$data = array(
				'keyList' => $keylist,
			);
			$this->assign($data);
			$this->setMeta(lang('add').lang('user'));
			return $this->fetch('common@public/edit');
		}
	}

	/**
	 * 修改昵称初始化
	 * @author huajie <banhuajie@163.com>
	 */
	public function edit() {
		$model = model('User');
		if (IS_POST) {
			$data = $this->request->post();

			$reuslt = $model->editUser($data, true);

			if (false !== $reuslt) {
				return $this->success(lang('edit').lang('success'), url('admin/user/index'));
			} else {
				return $this->error($model->getError(), '');
			}
		} else {
			$info = $this->getUserinfo();

			$keylist = $model->editfield;

			foreach ($keylist as &$item) {
				$item['title'] = lang($item['title']);
				if($item['help']){
					$item['help'] = lang($item['help']);	
				}
			}
			$data = array(
				'info'    => $info,
				'keyList' => $keylist,
			);

			$this->assign($data);
			$this->setMeta(lang('edit').lang('user'));
			return $this->fetch('common@public/edit');
		}
	}

	/**
	 * del
	 * @author colin <register@qinfo360.com>
	 */
	public function del($id) {
		if($id == 1){
			return $this->error(lang('System_users_are_prohibited_to_delete'));
		}
		$uid = array('IN', is_array($id) ? implode(',', $id) : $id);
		//获取用户信息
		$find = $this->getUserinfo($uid);
		model('User')->where(array('uid' => $uid))->delete();
		return $this->success(lang('delete').lang('success'));
	}

	public function auth() {
		$access = model('AuthGroupAccess');
		$group  = model('AuthGroup');
		if (IS_POST) {
			$uid = input('uid', '', 'trim,intval');
			$access->where(array('uid' => $uid))->delete();
			$group_id = input('group', '', 'trim,intval');


			if ($group_id) {
				$add = array(
					'uid'      => $uid,
					'group_id' => $group_id,
				);
				$access->insert($add);

				$group_info = $group->where(array('id'=>$group_id))->field('title')->find();
				$update_field['nickname'] = $group_info['title'];
				db('member')->where(array('uid'=>$uid))->update($update_field);

			}
			return $this->success(lang('Set_up').lang('success'),url('/admin/user/index'));
		} else {

			$uid  = input('id', '', 'trim,intval');
			$row  = $group::select();
			$auth = $access::where(array('uid' => $uid))->select();
			$auth_list = array();
			foreach ($auth as $key => $value) {
				$auth_list[] = $value['group_id'];
			}
			$data = array(
				'uid'       => $uid,
				'auth_list' => $auth_list,
				'list'      => $row,
			);
			
			$this->assign($data);
			$this->setMeta(lang('user').lang('grouping'));
			return $this->fetch();
		}
	}
	public function auth_device() {
		if (IS_POST) {
			
			$uid  = input('id', '', 'trim,intval');
			
			$device_group  = input('device_group/a');
			if(empty($device_group)){
				$savedata['device_id'] = '';
			}else{
				$savedata['device_id'] = implode(',',$device_group);
			}
			
			$check_user_device = db('device_auth')->where(array('uid'=>$uid))->find();
			if($check_user_device){
			   db('DeviceAuth')->where(array('uid' => $uid))->update($savedata);
			}else{
				$savedata['uid'] =$uid;
				db('device_auth')->insert($savedata);
			}
			
			
			return $this->success(lang('update').lang('success'),url('/admin/user/index'));
		} else {
			$uid  = input('id', '', 'trim,intval');
			$auth_list = db('device_auth')->where(array('uid'=>$uid))->find();
			if($auth_list){
				$auth_device = explode(',',$auth_list['device_id']);
			}else{
				$auth_device = array();
			}
			$device_data = db('device')->where('')->select();
			$this->assign('auth_device', $auth_device);
			$this->assign('uid', $uid);
			$this->assign('list', $device_data);
			$this->setMeta(lang('device').lang('auth'));
			return $this->fetch();
		}
	}
	/**
	 * 获取某个用户的信息
	 * @var uid 针对状态和删除启用
	 * @var pass 是查询password
	 * @var errormasg 错误提示
	 * @author colin <register@qinfo360.com>
	 */
	private function getUserinfo($uid = null, $pass = null, $errormsg = null) {
		$user = model('User');
		$uid  = $uid ? $uid : input('id');
		//如果无UID则修改当前用户
		$uid        = $uid ? $uid : session('user_auth.uid');
		$map['uid'] = $uid;
		if ($pass != null) {
			unset($map);
			$map['password'] = $pass;
		}
		$list = $user->where($map)->field('uid,username,nickname,sex,mobile,email,qq,score,signature,status,salt')->find();
		if (!$list) {
			return $this->error($errormsg ? $errormsg : lang('user_does_not_exist'));
		}
		return $list;
	}

	/**
	 * 修改昵称提交
	 * @author huajie <banhuajie@163.com>
	 */
	public function submitNickname() {

		//获取参数
		$nickname = input('post.nickname');
		$password = input('post.password');
		if (empty($nickname)) {
			return $this->error(lang('nickname').lang('cannot_be_empty'));
		}
		if (empty($password)) {
			return $this->error(lang('password').lang('cannot_be_empty'));
		}

		//密码验证
		$User = new UserApi();
		$uid  = $User->login(UID, $password, 4);
		if ($uid == -2) {
			return $this->error(lang('incorrect_password'));
		}

		$Member = model('User');
		$data   = $Member->create(array('nickname' => $nickname));
		if (!$data) {
			return $this->error($Member->getError());
		}

		$res = $Member->where(array('uid' => $uid))->save($data);

		if ($res) {
			$user             = session('user_auth');
			$user['username'] = $data['nickname'];
			session('user_auth', $user);
			session('user_auth_sign', data_auth_sign($user));
			return $this->success(lang('edit').lang('success'));
		} else {
			return $this->error(lang('edit').lang('fail'));
		}
	}

	/**
	 * 修改密码初始化
	 * @author huajie <banhuajie@163.com>
	 */
	public function editpwd() {
		if (IS_POST) {
			$user = model('User');
			$data = $this->request->post();

			$res = $user->editpw($data);
			if ($res) {
				return $this->success(lang('edit').lang('success'));
			} else {
				return $this->error($user->getError());
			}
		} else {
			$this->setMeta(lang('edit').lang('password'));
			return $this->fetch();
		}
	}
	/**
	 * 修改头像
	 * @author huajie <banhuajie@163.com>
	 */
	public function avatar() {
		if (IS_POST) {
			$user = model('User');
			$data = $this->request->post();

			$res = $user->editpw($data);
			if ($res) {
				return $this->success(lang('edit').lang('success'));
			} else {
				return $this->error($user->getError());
			}
		} else {
			$this->setMeta(lang('edit_avatar'));
			return $this->fetch();
		}
	}
	
	
	public function updata_avatar(){
		
		$file = $this->request->file('UpFile');
		$return_avatar_url = '/uploads/avatar/'.setavatardir(session('user_auth.uid'));
		$avatar_url = '.'.$return_avatar_url;
		$info = $file->setUploadInfo(array('name'=>'original.jpg'))->move($avatar_url,'');
		if ($info) {
			$thumb = \org\Image::init();
			$thumb_file = $info->getPathname();
			$thumb->open($thumb_file);
            $thumb->thumb(120,120);
            $thumb->save($avatar_url.'avatar_big.png');
			$thumb->thumb(100,100);
            $thumb->save($avatar_url.'avatar_middle.png');
			$thumb->thumb(60,60);
            $thumb->save($avatar_url.'avatar_small.png');
			
			$path = realpath(str_replace("\\","",$thumb_file));
			$t_time = time();
			
			$data = array(
				array('ImgUrl' => $return_avatar_url.'avatar_big.png?t='.$t_time),
				array('ImgUrl' => $return_avatar_url.'avatar_middle.png?t='.$t_time),
				array('ImgUrl' => $return_avatar_url.'avatar_small.png?t='.$t_time),
			);
			return json_encode($data);
		} else {
			$data['erro']     = $info->getError();
			return json_encode($data);
		}
		
	}
	
	
	/**
	 * 会员状态修改
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function changeStatus($method = null) {
		$id = array_unique((array) input('id', 0));
		if (in_array(config('user_administrator'), $id)) {
			return $this->error(lang('This_operation_is_not_allowed_on_the_super_administrator'));
		}
		$id = is_array($id) ? implode(',', $id) : $id;
		if (empty($id)) {
			return $this->error(lang('Please_select_the_data_you_want_to_operate'));
		}
		$map['uid'] = array('in', $id);
		switch (strtolower($method)) {
		case 'forbiduser':
			$this->forbid('Member', $map);
			break;

		case 'resumeuser':
			$this->resume('Member', $map);
			break;

		case 'deleteuser':
			$this->delete('Member', $map);
			break;

		default:
			return $this->error(lang('illegal_operation'));
		}
	}
}