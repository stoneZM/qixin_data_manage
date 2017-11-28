<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
* 用户模型
*/
class Log extends Base{

	protected $name = "Log";
	protected $createTime = 'create_time';

	protected $type = array(
		'uid'  => 'integer',
	);
	protected $insert = array('salt', 'password', 'status', 'reg_time');
	protected $update = array();

	public $editfield = array(
		array('name'=>'uid','type'=>'hidden'),
		array('name'=>'username','title'=>'username','type'=>'readonly','help'=>''),
		array('name'=>'nickname','title'=>'nickname','type'=>'text','help'=>''),
		array('name'=>'password','title'=>'password','type'=>'password','help'=>'Is_not_modified_for_space'),
		array('name'=>'sex','title'=>'sex','type'=>'select','option'=>array('0'=>'secrecy','1'=>'man','2'=>'woman'),'help'=>''),
//		array('name'=>'email','title'=>'email','type'=>'text','help'=>'Used_to_retrieve_passwords_and_other_security_operations'),
//		array('name'=>'mobile','title'=>'phone','type'=>'text','help'=>''),
//		array('name'=>'qq','title'=>'QQ','type'=>'text','help'=>''),
//		array('name'=>'score','title'=>'score','type'=>'text','help'=>''),
//		array('name'=>'signature','title'=>'signature','type'=>'textarea','help'=>''),
//		array('name'=>'status','title'=>'state','type'=>'select','option'=>array('0'=>'disable','1'=>'enable'),'help'=>''),
	);

	public $addfield = array(
		array('name'=>'username','title'=>'username','type'=>'text','help'=>'User_name_will_be_used_as_default_nickname'),
		array('name'=>'password','title'=>'password','type'=>'password','help'=>'Password_can_not_be_less_than_6'),
		array('name'=>'repassword','title'=>'confirm_password','type'=>'password','help'=>'confirm_password'),
//		array('name'=>'email','title'=>'email','type'=>'text','help'=>'Used_to_retrieve_passwords_and_other_security_operations'),
	);
    
	public $useredit = array(
		array('name'=>'uid','type'=>'hidden'),
		array('name'=>'nickname','title'=>'nickname','type'=>'text','help'=>''),
		array('name'=>'sex','title'=>'sex','type'=>'select','option'=>array('0'=>'secrecy','1'=>'man','2'=>'woman'),'help'=>''),
//		array('name'=>'email','title'=>'email','type'=>'text','help'=>'Used_to_retrieve_passwords_and_other_security_operations'),
//		array('name'=>'mobile','title'=>'phone','type'=>'text','help'=>''),
//		array('name'=>'qq','title'=>'qq','type'=>'text','help'=>''),
//		array('name'=>'signature','title'=>'signature','type'=>'textarea','help'=>''),
	);

	public $userextend = array(
		array('name'=>'company','title'=>'单位名称','type'=>'text','help'=>''),
		array('name'=>'company_addr','title'=>'单位地址','type'=>'text','help'=>''),
		array('name'=>'company_contact','title'=>'单位联系人','type'=>'text','help'=>''),
		array('name'=>'company_zip','title'=>'单位邮编','type'=>'text','help'=>''),
		array('name'=>'company_depart','title'=>'所属部门','type'=>'text','help'=>''),
		array('name'=>'company_post','title'=>'所属职务','type'=>'text','help'=>''),
		array('name'=>'company_type','title'=>'单位类型','type'=>'select', 'option'=>'', 'help'=>''),
	);

	protected function setStatusAttr($value){
		return 1;
	}

	protected function setPasswordAttr($value, $data){
		return md5($value.$data['salt']);
	}
	
	public function initialize() {
		parent::initialize();
		foreach ($this->editfield as $key => $value) {
			if ($value['name'] == 'status' || $value['name'] == 'sex') {	
				foreach ($value['option'] as &$item) {
					$item = lang($item);
				}
			}
			$this->editfield[$key] = $value;
		}
	}
	

	/**
	* 用户登录模型
	*/
	public function login($username = '', $password = '', $type = 1){
		$map = array();
		if (\think\Validate::is($username,'email')) {
			$type = 2;
		}elseif (preg_match("/^1[34578]{1}\d{9}$/",$username)) {
			$type = 3;
		}
		switch ($type) {
			case 1:
				$map['username'] = $username;
				break;
			case 2:
				$map['email'] = $username;
				break;
			case 3:
				$map['mobile'] = $username;
				break;
			case 4:
				$map['uid'] = $username;
				break;
			case 5:
				$map['uid'] = $username;
				break;
			default:
				return 0; //参数错误
		}

		if($user = $this->db()->where($map)->find()){
			$user = $user->toArray();
		}else{
			return -1;
		}
		if(is_array($user) && $user['status']){
			/* 验证用户密码 */
			if(md5($password.$user['salt']) === $user['password']){
				$this->autoLogin($user); //更新用户登录信息
				return $user['uid']; //登录成功，返回用户ID
			} else {
				return -2; //密码错误
			}
		} else {
			return -1; //用户不存在或被禁用
		}
	}

	/**
	 * 用户注册
	 * @param  integer $user 用户信息数组
	 */
	function register($username, $password, $repassword, $email, $isautologin = true){

		$data['username'] = $username;
		$data['salt'] = rand_string(6);
		$data['password'] = $password;
		$data['repassword'] = $repassword;
		$data['email'] = $email;
	    	$data['reg_time'] = time();
		$data['status'] = 1;

		$result = $this->validate(true)->insert($data,false,true);
		if ($result) {
			$data['uid'] = $this->data['uid'];
//			$this->extend()->save($data);
			if ($isautologin) {
				$this->autoLogin($this->data);
			}
			return $result;
		}else{
			if (!$this->getError()) {
				$this->error = lang('regist').lang('fail');
			}
			return false;
		}
	}

	/**
	 * 自动登录用户
	 * @param  integer $user 用户信息数组
	 */
	private function autoLogin($user){
		/* 更新登录信息 */
		$data = array(
			'uid'             => $user['uid'],
			'login'           => array('exp', '`login`+1'),
			'last_login_time' => time(),
			'last_login_ip'   => get_client_ip(1),
		);
		$this->where(array('uid'=>$user['uid']))->update($data);
		$user = $this->where(array('uid'=>$user['uid']))->find();

		/* 记录登录SESSION和COOKIES */
		$auth = array(
			'uid'             => $user['uid'],
			'username'        => $user['username'],
			'last_login_time' => $user['last_login_time'],
		);

		session('user_auth', $auth);
		session('user_auth_sign', data_auth_sign($auth));
	}

	public function logout(){
		session('user_auth', null);
		session('user_auth_sign', null);
	}

	public function getInfo($uid){
		$data = $this->where(array('uid'=>$uid))->find();
		if ($data) {
			return $data->toArray();
		}else{
			return false;
		}
	}

	/**
	 * 修改用户资料
	 */
	public function editUser($data, $ischangepwd = false){
		if ($data['uid']) {
			if (!$ischangepwd || ($ischangepwd && $data['password'] == '')) {
				unset($data['salt']);
				unset($data['password']);
			}else{
				$data['salt'] = rand_string(6);
			}
			$result = $this->validate('member.edit')->update($data,array('uid'=>$data['uid']));
			return $result;
//			if ($result) {
//				return $this->extend->update($data, array('uid'=>$data['uid']));
//			}else{
//				return false;
//			}
		}else{
			$this->error = lang('illegal_operation');
			return false;
		}
	}

	public function editpw($data, $is_reset = false){
		$uid = $is_reset ? $data['uid'] : session('user_auth.uid');
		if (!$is_reset) {
			//后台修改用户时可修改用户密码时设置为true
			$this->checkPassword($uid,$data['oldpassword']);

			$validate = $this->validate('member.password');
			if (false === $validate) {
				return false;
			}
		}

		$data['salt'] = rand_string(6);

		return $this->save($data, array('uid'=>$uid));
	}

	protected function checkPassword($uid,$password){
		if (!$uid || !$password) {
			$this->error = lang('The_original_user_UID_and_password_cannot_be_empty');
			return false;
		}

		$user = $this->where(array('uid'=>$uid))->find();
		if (md5($password.$user['salt']) === $user['password']) {
			return true;
		}else{
			$this->error = lang('Original_password_error');
			return false;
		}
	}

	public function extend(){
		return $this->hasOne('MemberExtend', 'uid');
	}
}