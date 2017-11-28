<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\mp\controller;
use app\common\controller\Admin;

class Addons extends Admin {

	protected $addons;
	private $signPackage;
	
	public function _initialize() {
		parent::_initialize();
		//加入菜单
		$this->getAddonsMenu();
		$this->addons = model('MpAddons');
		$appid = config('wechat_appid');
		$appsecret = config('wechat_appsecret');
		if($appid  && $appsecret){
			$jssdk = new \WechatSdk\JsSdk($appid,$appsecret);
			$this->signPackage = $jssdk->GetSignPackage();
			$this->signPackage['debug'] = false;
			$this->assign('signPackage',$this->signPackage);	
		}
	}
	/**
	 * 插件列表
	 */
	public function index($refresh = 0) {
		if ($refresh) {
			$this->addons->refresh();
		}
		$type = input('type', 'all');
        
		
		$list = $this->addons->order('id desc')->paginate(25);
		
		if ($type == 'yes') {//已安装的
            foreach ($list as $key => $value) {
                if ($value['status'] != 1) {
                    unset($list[$key]);
                }
            }
        } else if ($type == 'no') {
            foreach ($list as $key => $value) {
                if ($value['status'] == 1) {
                    unset($list[$key]);
                }
            }
        } else {
            $type = 'all';
        }
		
		
		// 记录当前列表页的cookie
		Cookie('__forward__', $_SERVER['REQUEST_URI']);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign('type', $type);
		$this->setMeta(lang('plugin_manage'));
		$this->assign($data);
		return $this->fetch();
	}
	

	/**
	 * 安装插件
	 */
	public function install() {
		$addon_name = input('addon_name', '', 'trim,ucfirst');
		$class      = get_mpaddon_class($addon_name);
		if (class_exists($class)) {
			$addons = new $class;
			$info   = $addons->info;
			if (!$info || !$addons->checkInfo()) {
				//检测信息的正确性
				return $this->error(lang('Missing_information'));
			}
			session('addons_install_error', null);
			$install_flag = $addons->install();
			if (!$install_flag) {
				return $this->error(lang('Failed_to_perform_pre_install_operation') . session('addons_install_error'));
			}
			$result = $this->addons->install($info);
			if ($result) {
				cache('hooks', null);
				return $this->success(lang('install').lang('success'));
			} else {
				return $this->error($this->addons->getError());
			}
		} else {
			return $this->error(lang('Plugin_does_not_exist'));
		}
	}

	/**
	 * 卸载插件
	 */
	public function uninstall($id) {
		$result = $this->addons->uninstall($id);
		if ($result === false) {
			return $this->error($this->addons->getError(), '');
		} else {
			return $this->success(lang('uninstall').lang('success'));
		}
	}



	public function nav() {
		
		$addon = get_addon();
		if ($addon && ACTION_NAME == 'config') {
			$addonnav['index'] = array(
				'title' => '功能导航',
				'url' => url('mpaddon/'.$addon.'/index'),
				'class' => 'active'
			);	
			// return $addonnav;
		}
		$addon_config = model('mp_addons')->get_addon_config();
		
		if ($addon_config['respond_rule'] == 1) {
			$addonnav['rule'] = array(
				'title' => '响应规则',
				'url' => url('/mpaddon/'.$addon.'/rule'),
				'class' => ACTION_NAME == 'rule' ? 'active' : ''
			);
		}
		if ($addon_config['setting'] == 1) {
			if (isset($addon_config['setting_list_group'])) {
				foreach ($addon_config['setting_list_group'] as $k => $v) {
					if ($v['is_show'] == 1) {
						if (input('type')) {
							$type = input('type');
						} elseif ($addon_config['setting_list_default_group']) {
							$type = $addon_config['setting_list_default_group'];
						} else {
							$types = array_keys($addon_config['setting_list_group']);
							$type = $types[0];
						}
						$children[] = array(
							'title' => $v['title'],
							'url' => url('/mpaddon/'.$addon.'/setting', array('type'=>$k)),
							'class' => $type == $k ? 'active' : ''
						);
					}
				}
			} else {
				$children = array(
					array(
						'title' => '默认配置',
						'url' => url('/mpaddon/'.$addon.'/setting'),
						'class' => 'active'
					)
				);
			}
			$addonnav['setting'] = array(
				'title' => '配置参数',
				'url' => url('/mpaddon/'.$addon.'/setting'),
				'class' => ACTION_NAME == 'setting' ? 'active' : '',
				'children' => $children
			);
		}
		if ($addon_config['entry'] == 1) {
			$entry_list = $this->parse_entry($addon_config['entry_list']);
			$addonnav['entry'] = array(
				'title' => '封面入口',
				'url' => !empty($entry_list) ? $entry_list[0]['url'] : '',
				'class' => $addon_config['entry_list'][input('act')] ? 'active' : '',
				'children' => $entry_list
			);
		}
		if ($addon_config['menu'] == 1) {
			$menu_list = $this->parse_menu($addon_config['menu_list']);
			
			$addonnav['menu'] = array(
				'title' => '业务导航',
				'url' => !empty($menu_list) ? $menu_list[0]['url'] : '',
				'class' => ACTION_NAME != 'rule' && ACTION_NAME != 'setting' && ACTION_NAME !='entry' ? 'active' : '',
				'children' => $menu_list
			);
		}
		return $addonnav;
	}


	/**
	 * 设置插件页面
	 */
	public function config() {
		$this->assign('sidenavs', $this->nav());
		$this->setMeta('功能导航');
		return $this->fetch();
	}
	
	
	/**
	 * 响应规则设置

	 */
	public function rule() {
		if (IS_POST) {
			$data = input('post.');
			$Rule = model('MpRule');
			if ($data['id']) {
				$ruledata['keyword'] = $data['keyword'];
				$Rule->where('id', $data['id'])->update($ruledata);
			} else {
				$ruledata['mpid'] = get_mpid();
				$ruledata['addon'] = get_addon();
				$ruledata['keyword'] = $data['keyword'];
				$ruledata['type'] = 'respond';
				
				$Rule->insert($ruledata);
			}
			$this->success('保存响应规则成功');
			
			
		} else {
			$addon = get_addon();
			$addon_info = model('MpAddons')->get_addon_info();
			$rule = model('MpRule')->get_respond_rule();
			
			
			$nav = $this->nav();
			$this->assign('rule', $rule);	
			$this->assign('mpnav', $nav);	
			$this->assign('tip', '用户在微信中发送的文本消息匹配到此处设置的关键词时，系统会把用户发送的消息分发到此插件的交互控制器进行处理');
			$this->setMeta('响应规则');
			return $this->fetch();
		}		
	}
	
	
	/**
	 * 配置参数

	 */
	public function setting($settings = array()) {
		if (IS_POST) {
			$settings = input('post.');
			model('mp_addon_setting')->where(array('mpid'=>get_mpid(), 'addon'=>get_addon()))->delete();		// 删除旧的配置项
			foreach ($settings as $k => $v) {
				if ($k == config('TOKEN_NAME')) {
					continue;
				}
				$data['mpid'] = get_mpid();
				$data['addon'] = get_addon();
				$data['name'] = $k;
				$data['value'] = $v;
				$datas[] = $data;
			}
			$res = model('mp_addon_setting')->insertAll($datas);
			if (!$res) {
				return $this->error('保存配置失败');
			} else {
				return $this->success('保存配置成功');
			}
		} else {		
			$addon = get_addon();
			$addon_info = model('mp_addons')->get_addon_info();
			$addon_config = $addon_info['config'];
			$setting_list = $addon_config['setting_list'];
			if (isset($addon_config['setting_list_group'])) {
				if (input('type')) {
					$type = input('type');
				} elseif ($addon_config['setting_list_default_group']) {
					$type = $addon_config['setting_list_default_group'];
				} else {
					$types = array_keys($addon_config['setting_list_group']);
					$type = $types[0];
				}
				foreach ($addon_config['setting_list'] as $k => $v) {
					if ($addon_config['setting_list_group'][$type]['is_show'] == 1) {
						if ($v['group'] == $type) {
							$fields[$k] = $v;
						} else {
							$v['type'] = 'hidden';
							$fields[$k] = $v;
						}
					}
				}
			} else {
				$fields = $addon_config['setting_list'];
			}
			$settingdata = model('mp_addon_setting')->get_addon_settings();
			if($settingdata){
				foreach ($fields as $k => &$v) {
					$v['value'] = $settingdata[$k];
				}
			}
			
			$nav = $this->nav();
			$subnav = $nav['setting']['children'];
			$this->assign('fields', $fields);	
			$this->assign('mpnav', $nav);	
			$this->assign('subnav', $subnav);
			$this->setMeta('配置参数');
			return $this->fetch();
		}
	}
	
	/**
	 * 页面预览

	 */
	public function preview($act, $params=array()){
		if (!$params['mpid']) {
			$params['mpid'] = get_mpid();
		}
		$url = url('/mpaddon/'.get_addon().'/mobile/'.$act, $params);
	    $this->assign('url',$url);
		return $this->fetch();
	}
	
	/**
	 * 封面入口设置

	 */
	public function entry() {
		if (IS_POST) {
			
			$data = input('post.');
			
			if (!$data['keyword']) {
				return $this->error('关键词不能为空');
			}
			// 添加入口
			$AddonEntry = model('MpAddonEntry');
			// /$AddonEntry->startTrans();			// 开启事务
			if ($data['id']) {
				$entrydata['title'] = $data['title'];
				$entrydata['cover'] = $data['cover'];
				$entrydata['desc'] = $data['desc'];
				$AddonEntry->where('id', $data['id'])->update($entrydata);
				$entry_id = $data['id'];
			} else {
				$entrydata['mpid'] = get_mpid();
				$entrydata['addon'] = get_addon();
				$entrydata['name'] = $data['name'];
				$entrydata['act'] = $data['act'];
				$entrydata['title'] = $data['title'];
				$entrydata['cover'] = $data['cover'];
				$entrydata['desc'] = $data['desc'];
				$entry_id = $AddonEntry->insertGetId($entrydata);
			}
			

			// 添加关键词
			$Rule = model('MpRule');
			config('TOKEN_ON', false);
			
			if ($data['rule_id']) {
				$ruledata['keyword'] = $data['keyword'];
				$ruledata['type'] = 'entry';
				$ruledata['entry_id'] = $entry_id;
				$Rule->where('id', $data['rule_id'])->update($ruledata);
				$rule_id = $data['rule_id'];;
			} else {
				$ruledata['mpid'] = get_mpid();
				$ruledata['addon'] = get_addon();
				$ruledata['keyword'] = $data['keyword'];
				$ruledata['type'] = 'entry';
				$ruledata['entry_id'] = $entry_id;
				
				$rule_id = $Rule->insertGetId($ruledata);
			}
			if ($entry_id && $rule_id) {
				return $this->success('保存功能入口成功');
			} else {
				$AddonEntry->rollback();		// 添加响应规则失败，事务回滚
				return $this->success('保存功能入口失败');
			}			
		} else {
			
			$addon_entry = model('mp_addon_entry')->get_addon_entry(input('act'));
			if (!$addon_entry) {
				foreach ($addon_config['entry_list'] as $k => $v) {
					if ($k == input('act')) {
						$addon_entry['act'] = $k;
						$addon_entry['name'] = $v;
						break;
					}
				}
			}
			$nav = $this->nav();
			$subnav = $nav['entry']['children'];
			$this->assign('entry', $addon_entry);	
			$this->assign('mpnav', $nav);	
			$this->assign('subnav', $subnav);
			$this->assign('tip', '用户在微信中发送的文本消息匹配到此处设置的关键词时，系统会根据此处设置的封面参数回复一条单图文消息，用户点击图文消息可进入对应的功能页面');
			$this->setMeta('封面入口');
			return $this->fetch();
		}
	}
	
	private function parse_entry($entry_list) {
		foreach ($entry_list as $k => $v) {
			$arr['title'] = $v;
			$arr['url'] = url('/mpaddon/'.get_addon().'/entry/'.$k);
			$arr['class'] = input('act') == $k ? 'active' : '';
			$children[] = $arr;
		}
		return $children;
	}

	private function parse_menu($menu_list) {
		foreach ($menu_list as $k => $v) {
			$arr['title'] = $v;
			$arr['url'] = url('/mpaddon/'.get_addon().'/admin/'.$k);
			$arr['class'] = input('ac') == $k ? 'active' : '';
			$children[] = $arr;
		}
		return $children;
	}
	
	

	
	public function template($template='') {
		$mc                         = $this->getAddonsName();
		$ac                         = input('ac', '', 'trim,strtolower');
		$op                         = input('op', '', 'trim,strtolower');
		$parse_str                  = \think\Config::get('parse_str');
		$parse_str['__ADDONROOT__'] = ROOT_PATH . "/application/mp/addons/{$mc}";
		\think\Config::set('parse_str', $parse_str);

		if ($template) {
			$template = $template;
		} else {
			$template = $op . "/" . $ac;
		}

		$this->view->engine(
			array('view_path' => "application/mp/addons/" . $mc . "/view/")
		);
		echo $this->fetch($template);
	}
	
	final public function getAddonsName() {
		$mc = input('mc', '', 'trim,strtolower');
		if ($mc) {
			return $mc;
		} else {
			$class = get_class($this);
			return strtolower(substr($class, strrpos($class, '\\') + 1));
		}
	}
}