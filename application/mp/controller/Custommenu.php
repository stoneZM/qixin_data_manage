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

/**
 * 自定义菜单控制器
 */
class Custommenu extends Admin {


	/**
	 * 发布菜单
	 */
	public function index() {
		//$menu = get_menu();
		//$this->assign('menu',$menu['menu']);
		$this->setMeta(lang('mp').lang('configure'));
		return $this->fetch();
	}

	public function get_menu() {
		$flag = 'custom_menu_'.get_mpid();
		if (cache($flag)) {
			$menu = cache($flag);
		} else {
			$menu = get_menu();
			cache($flag, $menu, 3600);
		}
		$return['errcode'] = 0;
		$return['errmsg'] = '获取菜单成功';
		$return['data'] = $menu['menu'];
		
		
		return json($return);
	}

	/**
	 * 创建菜单
	 */
	public function create_menu() {
		
		
		$menu = input('post.menu/a');
		
		$button = $menu['button'];
		
		if(!$button){
			
			$return['errcode'] = 1000;
			$return['errmsg'] = lang('menu').lang('cannot_be_empty');
			return json($return);
		}
		
		
		foreach ($button as $k => &$v) {
			if ($v == null) {
				continue;
			}
			$item['name'] = $v['name'];
			if (count($v['sub_button']) != 0) {				// 二级菜单存在
				foreach ($v['sub_button'] as $kk => $vv) {
					if ($vv == null) {
						continue;
					}
					$two['name'] = $vv['name'];
					$two['type'] = $vv['type'];
					if ($vv['type'] == 'view') {
						if (!$vv['url']) {
							$return['errcode'] = 1002;
							$return['errmsg'] = '菜单链接不能为空';
							$return['data_id'] = $kk;
							return json($return);
						} elseif (!preg_match('/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/', $vv['url'])) {
							$return['errcode'] = 1004;
							$return['errmsg'] = '菜单链接地址不合法';
							$return['data_id'] = $kk;
							return json($return);
						}
						$two['url'] = $vv['url'];
					} elseif ($vv['type'] == 'click') {
						if (!$vv['key']) {
							$return['errcode'] = 1003;
							$return['errmsg'] = '菜单关键词不能为空';
							$return['data_id'] = $kk;
							return json($return);
						}
						$two['key'] = $vv['key'];
					} else {
						$return['errcode'] = 1001;
						$return['errmsg'] = '菜单动作必选';
						$return['data_id'] = $kk;
						return json($return);
					}
					$tmp[] = $two;
				}
				
				if (count($tmp) == 0) {
					$item['type'] = $v['type'];
					if ($v['type'] == 'view') {
						if (!$v['url']) {
							$return['errcode'] = 1002;
							$return['errmsg'] = '菜单链接不能为空';
							$return['data_id'] = $k;
							return json($return);
						} elseif (!preg_match('/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/', $v['url'])) {
							$return['errcode'] = 1004;
							$return['errmsg'] = '菜单链接地址不合法';
							$return['data_id'] = $k;
							return json($return);
						}
						$item['url'] = $v['url'];
					} elseif ($v['type'] == 'click') {
						if (!$v['key']) {
							$return['errcode'] = 1003;
							$return['errmsg'] = '菜单关键词不能为空';
							$return['data_id'] = $k;
							return json($return);
						}
						$item['key'] = $v['key'];
					} else {
						$return['errcode'] = 1001;
						$return['errmsg'] = '菜单动作必选';
						$return['data_id'] = $k;
						return json($return);
					}
				} else {
					$item['sub_button'] = $tmp;
				}
				unset($tmp);
			} else {
				$item['type'] = $v['type'];
				if ($v['type'] == 'view') {
					if (!$v['url']) {
						$return['errcode'] = 1002;
						$return['errmsg'] = '菜单链接不能为空';
						$return['data_id'] = $k;
						return json($return);
					} elseif (!preg_match('/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/', $v['url'])) {
						$return['errcode'] = 1004;
						$return['errmsg'] = '菜单链接地址不合法';
						$return['data_id'] = $k;
						return json($return);
					}
					$item['url'] = $v['url'];
				} elseif ($v['type'] == 'click') {
					if (!$v['key']) {
						$return['errcode'] = 1003;
						$return['errmsg'] = '菜单关键词不能为空';
						$return['data_id'] = $k;
						return json($return);
					}
					$item['key'] = $v['key'];
				} else {
					$return['errcode'] = 1001;
					$return['errmsg'] = '菜单动作必选';
					$return['data_id'] = $k;
					return json($return);
				}
			}
			$custome_button[] = $item;
			unset($item);
		}
		if (!$custome_button) {
			$return['errcode'] = 1009;
			$return['errmsg'] = '菜单不能为空';
			$return['data'] = $custom_menu;
			return json($return);
		}
		$custom_menu['button'] = $custome_button;
		$result = create_menu($custom_menu);
		if ($result === true) {
			$menu['menu'] = $custom_menu;
			$flag = 'custom_menu_'.get_mpid();
			cache($flag, $menu, 3600);
			$return['errcode'] = 0;
			$return['errmsg'] = '发布菜单成功';
			$return['data'] = $custom_menu;
			return json($return);
		} else {
			$return['errcode'] = 1008;
			$return['errmsg'] = '发布菜单失败，错误说明：'.$result['errmsg'];
			$return['data'] = $custom_menu;
			return json($return);
		}
		
	}

	// 删除菜单
	public function delete_menu() {
		$result = delete_menu();
		if ($result === true) {
			$flag = 'custom_menu_'.get_mpid();
			cache($flag, null);
			$return['errcode'] = 0;
			$return['errmsg'] = '删除菜单成功';
			return json($return);
		} else {
			$return['errcode'] = 1007;
			$return['errmsg'] = '删除菜单失败，错误说明：'.$result;
			return json($return);
		}
	}
}

 ?>