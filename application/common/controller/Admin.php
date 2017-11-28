<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\common\controller;

use app\device\model\Storage;
use think\Url;
use think\View;
use think\Request;
use think\Session;
use think\Db;
use think\Response;
use think\Config;
use think\Loader;
use think\response\Redirect;
use think\Exception;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\Lang;
use think\Cookie;
use app\common\model\AuthGroup;
use app\common\model\AuthRule;

class Admin extends Base {
	// Request实例
    protected $request;
    // 黑名单方法，禁止访问某些方法
    protected static $blacklist = [];
    // 是否删除标志，0-正常|1-删除|false-不包含该字段
    protected static $isdelete = 0;


	public function _initialize() {
		parent::_initialize();
		$request = Request::instance();
		if (!is_login() and !in_array($this->url, array('admin/index/login', 'admin/index/logout', 'admin/index/verify'))) {
			$this->redirect('admin/index/login');
		}

		if (!in_array($this->url, array('admin/index/login', 'admin/index/logout', 'admin/index/verify'))) {

						// 黑名单方法
			if ($this::$blacklist && in_array($this->request->action(), $this::$blacklist)) {
				throw new HttpException(404, 'method not exists:' . (new \ReflectionClass($this))->getName() . '->' . $this->request->action());
			}
			// 是否是超级管理员
			define('IS_ROOT', is_administrator());
			if (!IS_ROOT && \think\Config::get('admin_allow_ip')) {
				// 检查IP地址访问
				if (!in_array(get_client_ip(), explode(',', \think\Config::get('admin_allow_ip')))) {
					$this->error('403:禁止访问');
				}
			}

			// 检测系统权限
			if (!IS_ROOT) {
				$access = $this->accessControl();
//				$access = true;
				if (false === $access) {
					$this->error('403:禁止访问');
				} elseif (null === $access) {
					$dynamic = $this->checkDynamic(); //检测分类栏目有关的各项动态权限
					if ($dynamic === null) {
						//检测访问权限
						if (!$this->checkRule($this->url, array('in', '1,2'))) {
							$this->error('未授权访问!');
						} else {
							// 检测分类及内容有关的各项动态权限
							$dynamic = $this->checkDynamic();
							if (false === $dynamic) {
								$this->error('未授权访问!');
							}
						}
					} elseif ($dynamic === false) {
						$this->error('未授权访问!');
					}
				}
			}
			
			$this->setLicense();
			//菜单设置
			$this->setMenu();
			$this->setMeta();
			$this->setUser();

			$this->checkStorageNetCapacity();
			
			// 前置方法
			$beforeAction = "before" . $this->request->action();
			if (method_exists($this, $beforeAction)) {
				$this->$beforeAction();
			}
			
			
		}
	}


	/**
	 *  检查授权容量是否快超标
	 */

	protected function checkStorageNetCapacity($is_func=false){

		$storage_net_size = get_storage_net_size();
		$storage = Storage::where(array('type'=>1))->field(array('id'))->select();
		$used_total_size = 0;
		$storage_path_model = db('storage_path');
		foreach($storage as $key=>$value){
			$size = $storage_path_model->where(array('storage_id'=>$value['id']))->column('used_size');
			foreach($size as $k=>$v){
				$used_total_size += $v;
			}
		}

		$used_percent = round($used_total_size/$storage_net_size,2);
		$data['is_display'] = "";
		$data['warning_msg'] = "";
		$data['warning_level'] = "";
		$data['storage_warning_code'] = 1;
		if($used_percent>0.90) {
			$data['storage_warning_code'] = 0;
			$data['is_display'] = "display";
			$data['warning_msg'] = "存储容量不足10%";
			$data['warning_level'] = "warning";
			$data['used_percent'] = $used_percent;
		}
		if($used_percent>0.99){
			$data['storage_warning_code'] = 0;
			$data['is_display'] = "display";
			$data['warning_msg'] = "存储容量不足1%";
			$data['warning_level'] = "error";
			$data['used_percent'] = $used_percent;
		}
		if($is_func){
			return $data;
		}else{
			$this->assign($data);
		}

	}

	/**
	 * 权限检测
	 * @param string  $rule    检测的规则
	 * @param string  $mode    check模式
	 * @return boolean
	 * @author 朱亚杰  <xcoolcc@gmail.com>
	 */
	final protected function checkRule($rule, $type = AuthRule::rule_url, $mode = 'url') {
		
		static $Auth = null;
		if (!$Auth) {
			$Auth = new \com\Auth();
		}
		if (!$Auth->check($rule, session('user_auth.uid'), $type, $mode)) {
			return false;
		}
		return true;
	}

	/**
	 * 检测是否是需要动态判断的权限
	 * @return boolean|null
	 *      返回true则表示当前访问有权限
	 *      返回false则表示当前访问无权限
	 *      返回null，则表示权限不明
	 *
	 * @author 朱亚杰  <xcoolcc@gmail.com>
	 */
	protected function checkDynamic() {
		if (IS_ROOT) {
			return true; //管理员允许访问任何页面
		}
		return null; //不明,需checkRule
	}

	/**
	 * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
	 *
	 * @return boolean|null  返回值必须使用 `===` 进行判断
	 *
	 *   返回 **false**, 不允许任何人访问(超管除外)
	 *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
	 *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
	 * @author 朱亚杰  <xcoolcc@gmail.com>
	 */
	final protected function accessControl() {
		$allow = \think\Config::get('allow_visit');
		$deny  = \think\Config::get('deny_visit');
		$check = strtolower($this->request->controller() . '/' . $this->request->action());
		if (!empty($deny) && in_array_case($check, $deny)) {
			return false; //非超管禁止访问deny中的方法
		}
		if (!empty($allow) && in_array_case($check, $allow)) {
			return true;
		}
		return null; //需要检测节点权限
	}
	
	protected function setMenu() {
		
		$config_updata = config('systemconfig.is_updata');
		if($config_updata === null){
			$is_os_updata = true;
		}else if($config_updata === 1){
			$is_os_updata = true;
		}else{
			$is_os_updata = false;
		}
		
	
		
		
		$hover_url  = strtolower($this->request->module() . '/' . $this->request->controller());
		$controller = $this->url;
		$menu       = array(
			'main'  => array(),
			'child' => array(),
		);
		$where['pid']  = 0;
		$where['hide'] = 0;
		if (!config('develop_mode')) {
			// 是否开发者模式
			$where['is_dev'] = 0;
		}
		$row = db('menu')->field('id,title,url,icon,group,"" as style')->where($where)->order('sort asc')->select();
		foreach ($row as $key => $value) {
			//此处用来做权限判断
			if (!IS_ROOT && !$this->checkRule($value['url'], 2, null)) {
				unset($menu['main'][$value['id']]);
				continue; //继续循环
			}
			if ($controller == $value['url']) {
				$value['style'] = "active";
			}
			$value['title'] = lang($value['title']);
			if($value['group']){
				$value['group'] = lang($value['group']);	
			}
			$menu['main'][$value['id']] = $value;
		}

		// 查找当前子菜单
		$pid = db('menu')->where("pid !=0 AND url like '%{$hover_url}%'")->value('pid');
		$id  = db('menu')->where("pid = 0 AND url like '%{$hover_url}%'")->value('id');
		$pid = $pid ? $pid : $id;
		if ($hover_url == 'cms/content' || $hover_url == 'cms/attribute') {
			//内容管理菜单
			$pid = db('menu')->where("pid =0 AND url like '%cms/category%'")->value('id');
		}
		if ($pid) {
			$map['pid']  = $pid;
			$map['hide'] = 0;
			$row         = db('menu')->field('id,title,url,icon,group,pid,"" as style')->where($map)->order('sort asc')->select();
			foreach ($row as $key => $value) {
				if (IS_ROOT || $this->checkRule($value['url'], 2, null)) {
					if(!$is_os_updata){
						if($value['url'] == 'admin/cloud/index' || $value['url'] == 'admin/cloud/update'){
							continue; //继续循环
						}
					}
					if ($controller == $value['url']) {
						$menu['main'][$value['pid']]['style'] = "active";
						$value['style']                       = "active";
					}
					
					$value['title'] = lang($value['title']);
					if($value['group']){
						$value['group'] = lang($value['group']);	
					}
					$menu['child'][$value['group']][] = $value;
					//$menu['child'][] = $value;
				}
			}
		}
		$menu['parent'] = $this ->get_parent_dir();
		$this->assign('__menu__', $menu);
	}
	/**
	 * 
	 * 获取父栏目ID列表
	 * @param integer $catid              栏目ID
	 * @param array $arrparentid          父目录ID
	 * @param integer $n                  查找的层次
	 */
	private function get_parent_dir() {
		
		$now_url  = $this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action();
		$parent = 	$this ->get_parent($now_url);
		if($parent){
			foreach ($parent as $key => &$value) {
				$value['title'] = lang($value['title']);
			}
			$parent = array_sort($parent,'sort','desc');
			return  $parent;
		}
	}

	/**
	 * 
	 * 获取父栏目ID列表
	 * @param integer $catid              栏目ID
	 * @param array $arrparentid          父目录ID
	 * @param integer $n                  查找的层次
	 */
	private function get_parent($id, $category = array(),$key = 0) {
		if(is_numeric($id)){
			$category_arr = db('menu')->where("id = ".$id)->field('id,title,pid,url')->find();
		}else{
			$id = strtolower($id);
			$pid_arr = db('menu')->where("pid !=0 AND url like '%{$id}%'")->field('id,title,pid,url')->find();
			$id_arr  = db('menu')->where("pid = 0 AND url like '%{$id}%'")->field('id,title,pid,url')->find();
			$category_arr = $pid_arr ? $pid_arr : $id_arr;
		}
		if($category_arr){
			if($key==0){
				$category_arr['action'] ='action';
			}else{
				$category_arr['action'] ='';
			}
			$category_arr['sort'] =$key;
			$category[] = $category_arr;
			if($category_arr['pid']){
				$category  = 	$this->get_parent($category_arr['pid'],$category,++$key);
			}
			return $category;
		}
	}
	
	protected function getContentMenu() {
		$model = \think\Loader::model('Model');
		$list  = array();
		$map   = array(
			'status' => array('gt', 0),
			'extend' => array('gt', 0),
		);
		$list = $model::where($map)->field("name,id,title,icon,'' as 'style'")->select();

		//判断是否有模型权限
		$models = AuthGroup::getAuthModels(session('user_auth.uid'));
		foreach ($list as $key => $value) {
			if (IS_ROOT || in_array($value['id'], $models)) {
				if ('cms/content/index' == $this->url && input('model_id') == $value['id']) {
					$value['style'] = "active";
				}
				$value['url']   = "cms/content/index?model_id=" . $value['id'];
				$value['title'] = $value['title'] ;
				$value['icon']  = $value['icon'] ? $value['icon'] : 'file';
				$menu[]         = $value;
			}
		}
		if (!empty($menu)) {
			$this->assign('extend_menu', array(lang('content') => $menu));
		}
	}

	protected function getAddonsMenu() {
		$model = db('Addons');
		$list  = array();
		$map   = array(
			'isinstall' => array('gt', 0),
			'status' => array('gt', 0),
		);
		$list = $model->field("name,id,title,'' as 'style'")->where($map)->select();

		$menu = array();
		foreach ($list as $key => $value) {
			$class = "\\addons\\" . strtolower($value['name']) . "\\controller\\Admin";
			if (is_file(ROOT_PATH . $class . ".php")) {
				$action       = get_class_methods($class);
				$value['url'] = "admin/addons/execute?mc=" . strtolower($value['name']) . "&ac=" . $action[0];
				$menu[$key]   = $value;
			}
		}
		if (!empty($menu)) {
			$this->assign('extend_menu', array(lang('plugin') => $menu));
		}
	}

	protected function setMeta($title = '') {
		$this->assign('meta_title', $title);
	}
	protected function setUser() {
		$uid = session('user_auth.uid');
		$user_data = db('member')->where("uid=".$uid)->find();
		if(IS_ROOT){
			$grouptitle = '超级管理员';
		}else{
			$group_access = db('auth_group_access')->where("uid=".$user_data['uid'])->find();
			if($group_access){
				$auth_group = db('auth_group')->where("id=".$group_access['group_id'])->find();
				if($auth_group){
					$grouptitle = $auth_group['title'];
				}
			}
		}
		$admin_user['uid'] = $user_data['uid'];
		$admin_user['username'] = $user_data['username'];
		$admin_user['nickname'] = $user_data['nickname'];
		$admin_user['grouptitle'] = $grouptitle;
		$admin_user['avatar'] = avatar($user_data['uid']);
		$admin_user['last_login_time'] = date("H:i",$user_data['last_login_time']);
        $admin_user['lock_time'] = session('lock_time')?:5;


		$this->assign('admin_user', $admin_user);
	}

	protected function setLicense() {

		$license_data = get_license();

		$flag = self::check_licence();

		if(!in_array($this->url, array('admin/license/index','admin/license/license','admin/index/login', 'admin/index/logout', 'admin/index/verify'))){

			if(!$flag){
				$this->error('无效授权 !',url('admin/license/index'));
			}

			if(!$license_data){
				$this->error('授权文件损坏，请重新授权 !',url('admin/license/index'));
			}
			if( $license_data['status'] === 0){
				$this->error('授权码已经失效，请重新授权 !',url('admin/license/index'));
			}
			if( $license_data['reviewer'] === 0 ){
				$this->error('授权码未认证，请重新授权 !',url('admin/license/index'));
			}
			if($license_data['type'] == 1  ){
				if($license_data['expiration_time'] >0 && $license_data['expiration_time'] <= time()){
					$this->error('您的试用版本已经到期，请重新授权 !',url('admin/license/index'));
				}
			}
		}
		if($license_data && $license_data['type']==2){
			$license_data['img_url'] ='/data/license/license.png';
		}


		if(!$flag){

			$license_data['is_valid'] = -1;
			$license_data['error_msg'] = "无效授权 !";

		}else{

			if(!$license_data){
				$license_data['is_valid'] = -1;
				$license_data['error_msg'] = "未授权 !";
			}
			if($license_data['status'] === 0 ){
				$license_data['is_valid'] = -1;
				$license_data['error_msg'] = "授权码已经失效 !";
			}
			if($license_data['reviewer'] === 0 ){
				$license_data['is_valid'] = -1;
				$license_data['error_msg'] = "授权码未认证 !";

			}
			if($license_data['type'] == 1 ){
				if($license_data['expiration_time'] >0 && $license_data['expiration_time'] <= time()){
					$license_data['is_valid'] = -1;
					$license_data['error_msg'] = "授权已到期 !";
				}
			}
		}


		$this->assign('license_flag',$flag);
		$this->assign('license_info', $license_data);
		$lang_path = APP_PATH . 'lang\\'.$license_data['agent_info']['alias'].'\\'. config('default_lang').'.php';
		if(is_file($lang_path)){
			Lang::load($lang_path);	
		}
		
		$module_list = db('module')->where(['is_setup'=>1])->select();
		foreach ($module_list as $key => $value) {
			$lang_path = APP_PATH .  strtolower($value['name']) . '\\lang\\'.$license_data['agent_info']['alias'].'\\'. config('default_lang').'.php';
			if (is_file($lang_path)) {
				Lang::load($lang_path);	
			}
		}
	}
	
    /**
     * 自动搜索查询字段,给模型字段过滤
     */
    protected function search($model)
    {
        $map = [];
        $table_info = $model->getTableInfo();
        foreach ($this->request->param() as $key => $val) {
            if ($val !== "" && in_array($key, $table_info['fields'])) {
                $map[$key] = $val;
            }
        }

        return $map;
    }

    /**
     * 获取模型
     * @param string $controller
     * @return mixed
     */
    protected function getModel($controller = '')
    {
        $module = $this->request->module();
        if (!$controller) {
            $controller = $this->request->controller();
        }
        if (class_exists(Loader::parseClass($module, 'model', $controller))) {
            return Loader::model($controller);
        } else {
            return Db::name($this->parseTable($controller));
        }
    }

    /**
     * 获取实际的控制器名称(应用于多层控制器的场景)
     * @param $controller
     * @return mixed
     */
    protected function getRealController($controller = '')
    {
        if (!$controller) {
            $controller = $this->request->controller();
        }
        $controllers = explode(".", $controller);
        $controller = array_pop($controllers);

        return $controller;
    }

    /**
     * 默认更新字段方法
     * @param string $field     更新的字段
     * @param string|int $value 更新的值
     * @param string $msg       操作成功提示信息
     * @param string $pk        主键，默认为主键
     * @param string $input     接收参数，默认为主键
     */
    protected function updateField($field, $value, $msg = "操作成功", $pk = "", $input = "")
    {
        $model = $this->getModel();
        if (!$pk) {
            $pk = $model->getPk();
        }
        if (!$input) {
            $input = $model->getPk();
        }
        $ids = $this->request->param($input);
        $where[$pk] = ["in", $ids];
        if ($model->where($where)->update([$field => $value]) === false) {
            return ajax_return_adv_error($model->getError());
        }

        return ajax_return_adv($msg, '');
    }

    /**
     * 格式化表名，将 /. 转为 _ ，支持多级控制器
     * @param string $name
     * @return mixed
     */
    protected function parseTable($name = '')
    {
        if (!$name) {
            $name = $this->request->controller();
        }

        return str_replace(['/', '.'], '_', $name);
    }

    /**
     * 格式化类名，将 /. 转为 \\
     * @param string $name
     * @return mixed
     */
    protected function parseClass($name = '')
    {
        if (!$name) {
            $name = $this->request->controller();
        }

        return str_replace(['/', '.'], '\\', $name);
    }

    /**
     * 未登录处理
     */
    protected function notLogin()
    {
        // 跳转到认证网关
        if ($this->request->isAjax()) {
            $response = ajax_return_adv_error("登录超时，请先登陆", 400, "", "", false, "", Url::build("Pub/loginFrame"));
            throw new HttpResponseException($response);
        } else {
            if (strtolower($this->request->controller()) == 'index' && strtolower($this->request->action()) == 'index') {
                throw new HttpResponseException(new Redirect('Pub/login'));
            } else {
                // 判断是弹出登录框还是直接跳转到登录页
                $ret = '<script>' .
                    'if(window.parent.frames.length == 0) ' .
                    'window.location = "' . Url::build('Pub/login') . '?callback=' . urlencode($this->request->url(true)) . '";' .
                    ' else ' .
                    'parent.login("' . Url::build('Pub/loginFrame') . '");' .
                    '</script>';
                throw new HttpResponseException(new Response($ret));
            }
        }
    }
    /**
     * 过滤禁止操作某些主键
     * @param $filterData
     * @param string $error
     * @param string $method
     * @param string $key
     */
    protected function filterId($filterData, $error = '该记录不能执行此操作', $method = 'in_array', $key = 'id')
    {
        $data = $this->request->param();
        if (!isset($data[$key])) {
            throw new Exception('缺少必要参数');
        }
        $ids = is_array($data[$key]) ? $data[$key] : explode(",", $data[$key]);
        foreach ($ids as $id) {
            switch ($method) {
                case '<':
                case 'lt':
                    $ret = $id < $filterData;
                    break;
                case '>':
                case 'gt':
                    $ret = $id < $filterData;
                    break;
                case '=':
                case 'eq':
                    $ret = $id == $filterData;
                    break;
                case '!=':
                case 'neq':
                    $ret = $id != $filterData;
                    break;
                default:
                    $ret = call_user_func_array($method, [$id, $filterData]);
                    break;
            }
            if ($ret) {
                throw new Exception($error);
            }
        }
    }

    /**
     * 根据表单生成查询条件
     * 进行列表过滤
     *
     * 过滤条件
     * $map['_table']       可强制设置表名前缀
     * $map['_relation']    可强制设置关联模型预载入(需在模型里定义)
     * $map['_field']       可强制设置字段
     * $map['_order_by']    可强制设置排序字段(field asc|desc[,filed2 asc|desc...]或者false)
     * $map['_paginate']    是否开启分页，传入false可以关闭分页
     *
     * @param object $model     数据对象
     * @param array $map        过滤条件
     * @param string $field     查询的字段
     * @param string $sortBy    排序
     * @param boolean $asc      是否正序
     * @param boolean $return   是否返回数据，返回数据时返回paginate对象，不返回时直接模板赋值
     * @param boolean $paginate 是否开启分页
     */
    protected function datalist($model, $map, $field = '*', $sortBy = '', $asc = false, $return = false, $paginate = true)
    {
        // 排序字段 默认为主键名
        $order = $this->request->param('_order') ?: (empty($sortBy) ? $model->getPk() : $sortBy);

        // 接受 sort参数 0 表示倒序 非0都 表示正序
        $sort = null !== $this->request->param('_sort') ?
            ($this->request->param('_sort') == 'asc' ? 'asc' : 'desc') :
            ($asc ? 'asc' : 'desc');

        // 设置关联预载入
        if (isset($map['_relation'])) {
            $model = $model::with($map['_relation']);
        }

        // 设置字段
        if (isset($map['_field'])) {
            $field = $map['_field'];
        }

        // 设置有$map['_controller']表示存在关联模型
        if (isset($map['_table'])) {
            // 给排序字段强制加上表名前缀
            if (strpos($order, ".") === false) {
                $order = $map['_table'] . "." . $order;
            }
            // 给字段强制加上表名前缀
            $_field = is_array($field) ? $field : explode(",", $field);
            foreach ($_field as &$v) {
                if (strpos($v, ".") === false) {
                    $v = preg_replace("/([^\s\(\)]*[a-z0-9\*])/", $map['_table'] . '.$1', $v, 1);
                }
            }
            $field = implode(",", $_field);
        }

        // 设置排序字段 防止表无主键报错
        $order_by = $order ? "{$order} {$sort}" : false;
        if (isset($map['_order_by'])) {
            $order_by = $map['_order_by'];
        }

        // 是否开启分页
        $paginate = isset($map['_paginate']) ? boolval($map['_paginate']) : $paginate;

        // 删除设置属性的字段
        unset($map['_table'], $map['_relation'], $map['_field'], $map['_order_by'], $map['_paginate']);

        if ($paginate) {
            // 分页查询

            // 每页数据数量
            $listRows = $this->request->param('numPerPage') ?: Config::get("paginate.list_rows");

            $list = $model->field($field)->where($map)->order($order_by)->paginate($listRows, false, ['query' => $this->request->get()]);

            if ($return) {
                // 返回值
                return $list;
            } else {
                // 模板赋值显示
                $this->view->assign('list', $list);
                $this->view->assign("count", $list->total());
                $this->view->assign("page", $list->render());
                $this->view->assign('numPerPage', $list->listRows());
            }
        } else {
            // 不开启分页查询
            $list = $model->field($field)->where($map)->order($order_by)->select();

            if ($return) {
                // 返回值
                return $list;
            } else {
                // 模板赋值显示
                $this->view->assign('list', $list);
                $this->view->assign("count", count($list));
                $this->view->assign("page", '');
                $this->view->assign('numPerPage', 0);
            }
        }
    }





//	正式版 -- 验证 licence 信息
	 public function check_licence(){

			$licence = get_license();
			if($licence && $licence['type']!=1){
				$hardware_data = get_hardware();
				$hardware_info = $licence['hardware_info'];
				if(!$hardware_data){
					return false;
				}
				foreach($hardware_data as $key=>$value){
					if($hardware_info[$key] != $value){
						return false;
					}
				}
			}
		  return true;
	 }
}
