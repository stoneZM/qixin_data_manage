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
class Config extends Admin {

	public function _initialize() {
		parent::_initialize();
		$this->model = model('Config');
	}

	/**
	 * 配置管理
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function index() {
		$group = input('group', 0, 'trim');
		$name  = input('name', '', 'trim');

		/* 查询条件初始化 */
		$map     = array('status' => 1);
		if ($group) {
			$map['group'] = $group;
		}

		if ($name) {
			$map['name'] = array('like', '%' . $name . '%');
		}

		$list = $this->model->where($map)->order('id desc')->paginate(25);
		// 记录当前列表页的cookie
		Cookie('__forward__', $_SERVER['REQUEST_URI']);

		$data = array(
			'group'       => config('config_group_list'),
			'config_type' => config('config_config_list'),
			'page'        => $list->render(),
			'group_id'    => $group,
			'list'        => $list,
		);


		$this->assign($data);
		$this->setMeta(lang('advanced_config'));
		return $this->fetch();
	}

	public function group($id = 4) {
		if (IS_POST) {
			$config = $this->request->post('config/a');
            $lock_time = $config['lock_time'];
            session('lock_time',$lock_time);
            $config['lock_time'] = 5;
			$model  = model('Config');
			foreach ($config as $key => $value) {
				$model->where(array('name' => $key))->setField('value', $value);
			}
			//清除db_config_data缓存
			cache('db_config_data', null);
			//记录行为
			action_log('update_config', 'config', $id, session('user_auth.uid'));
			return $this->success(lang('update').lang('success'));
		} else {

			$type = config('config_group_list');
			$list = db("Config")->where(array('status' => 1, 'group' => $id))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
			if ($list) {
				foreach ($list as $key => &$value) {
                        if ($value['name']=='lock_time'){
                            $value['value'] =  session('lock_time')?:5;
                        }else{
                            $value['title'] = lang($value['name']);
                        }
				}
			}

			$config_group_list = config('config_group_list');
			$mp= model('Module')->getModule('mp');
			if(!$mp || $mp['is_setup']==0){
				foreach ($list as $unkey => &$un_value) {
					
					if($un_value['name'] =='wechat_appid'){
						unset($list[$unkey]);
					}
					if($un_value['name'] =='wechat_appsecret'){
						unset($list[$unkey]);
					}
					if($un_value['name'] =='mobile_url'){
						unset($list[$unkey]);
					}
				}
			}
			unset($config_group_list[99],$config_group_list[3],$config_group_list[1]);
			$this->assign('list', $list);
			$this->assign('config_group_list', $config_group_list);
			$this->assign('id', $id);
			$this->setMeta(lang($type[$id]). lang('Set_up'));
			return $this->fetch();
		}
	}

	/**
	 * 新增配置
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function add() {
		if (IS_POST) {
			$config = model('Config');
			$data   = $this->request->post();
			if ($data) {
				$id = $config->validate(true)->save($data);
				if ($id) {
					cache('db_config_data', null);
					//记录行为
					action_log('update_config', 'config', $id, session('user_auth.uid'));
					return $this->success(lang('add').lang('success'), url('index'));
				} else {
					return $this->error(lang('update').lang('fail'));
				}
			} else {
				return $this->error($config->getError());
			}
		} else {
			$this->setMeta(lang('add_config'));
			$this->assign('info', null);
			return $this->fetch('edit');
		}
	}

	/**
	 * 编辑配置
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function edit($id = 0) {
		if (IS_POST) {
			$config = model('Config');
			$data   = $this->request->post();
			if ($data) {
				$result = $config->validate('Config.edit')->save($data, array('id' => $data['id']));
				if (false !== $result) {
					cache('db_config_data', null);
					//记录行为
					action_log('update_config', 'config', $data['id'], session('user_auth.uid'));
					return $this->success(lang('update').lang('success'), Cookie('__forward__'));
				} else {
					return $this->error($config->getError(), '');
				}
			} else {
				return $this->error($config->getError());
			}
		} else {
			$info = array();
			/* 获取数据 */
			$info = db('Config')->field(true)->find($id);

			if (false === $info) {
				return $this->error('获取配置信息错误');
			}
			$this->assign('info', $info);
			$this->setMeta(lang('edit_config'));
			return $this->fetch();
		}
	}
	/**
	 * 批量保存配置
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function save($config) {
		if ($config && is_array($config)) {
			$Config = db('Config');
			foreach ($config as $name => $value) {
				$map = array('name' => $name);
				$Config->where($map)->setField('value', $value);
			}
		}
		cache('db_config_data', null);
		return $this->success(lang('update').lang('success'));
	}
	/**
	 * 删除配置
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function del() {
		$id = array_unique((array) input('id', 0));

		if (empty($id)) {
			return $this->error('请选择要操作的数据!');
		}

		$map = array('id' => array('in', $id));
		if (db('Config')->where($map)->delete()) {
			cache('db_config_data', null);
			//记录行为
			action_log('update_config', 'config', $id, session('user_auth.uid'));
			return $this->success('删除成功');
		} else {
			return $this->error('删除失败！');
		}
	}

	/**
	 * 配置排序
	 * @author huajie <banhuajie@163.com>
	 */
	public function sort() {
		if (IS_GET) {
			$ids = input('ids');
			//获取排序的数据
			$map = array('status' => array('gt', -1));
			if (!empty($ids)) {
				$map['id'] = array('in', $ids);
			} elseif (input('group')) {
				$map['group'] = input('group');
			}
			$list = db('Config')->where($map)->field('id,title')->order('sort asc,id asc')->select();

			$this->assign('list', $list);
			$this->setMeta('配置排序');
			return $this->fetch();
		} elseif (IS_POST) {
			$ids = input('post.ids');
			$ids = explode(',', $ids);
			foreach ($ids as $key => $value) {
				$res = db('Config')->where(array('id' => $value))->setField('sort', $key + 1);
			}
			if ($res !== false) {
				return $this->success('排序成功！', Cookie('__forward__'));
			} else {
				return $this->error('排序失败！');
			}
		} else {
			return $this->error('非法请求！');
		}
	}
}