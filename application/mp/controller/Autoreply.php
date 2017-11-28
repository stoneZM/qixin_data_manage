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
 * 自动回复控制器
 */
class Autoreply extends Admin {

	public function _initialize() {
		parent::_initialize();
	}
	/**
	 * 关键词回复
	 */
	public function keyword() {
		
		$list = model('MpAutoReply')->where(array('mpid'=>get_mpid(),'type'=>'keyword'))->order('id asc')->paginate(25);
		if($list){
			$options= array('text'=>'文本','image'=>'图片','news'=>'图文');
			foreach ($list as $k => &$v) {
				$v['keyword'] = $this->get_keyword($v['id']);
				$v['material_id'] = $this->get_reply_content($v['material_id']);
				$v['reply_type'] = $options[$v['reply_type']];
			}
		}
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta('自动回复');
		return $this->fetch();
		
		
		
	}

	/**
	 * 添加文本回复
	 */
	public function add() {
		if (IS_POST) {
			$data = input('post.');
			$type = $data['reply_type'];
			$result = model('MpAutoReply')->add_auto_reply($type, $data);
			if ($result['errcode'] != 0) {
				return $this->error($result['errmsg']);
			} else {
				return $this->success($result['errmsg'], url('keyword'));
			}
		} else {
			$type = input('type');
			$type_arr = array('text'=>'文本','image'=>'图片','news'=>'图文');
			$this->assign('type',$type);
			$this->setMeta('添加'.$type_arr[$type].'回复');
			return $this->fetch('edit');
		}
	}

	/**
	 * 编辑自动回复
	 */
	public function edit() {
		if (IS_POST) {
			$data = input('post.');
			$type = $data['reply_type'];
			$result = model('MpAutoReply')->edit_auto_reply($type, $data);
			if ($result['errcode'] != 0) {
				return $this->error($result['errmsg']);
			} else {
				return $this->success($result['errmsg'], url('keyword'));
			}
		} else {
			
			$id = input('id');
			$result = model('MpAutoReply')->get_auto_reply($id);
			if ($result['errcode'] != 0) {
				$this->error($result['errmsg']);
			}
			$form_data = $result['result'];
			$type = $form_data['reply_type'];
			$type_arr = array('text'=>'文本','image'=>'图片','news'=>'图文');
			$this->assign('data',$form_data);
			$this->assign('id',$id);
			$this->assign('type',$type);
			$this->setMeta('编辑'.$type_arr[$type].'回复');
			return $this->fetch('edit');
		}
	}

	/**
	 * 获取自动回复关键词
	 
	 */
	public function get_keyword($reply_id) {
		$reply_rule = model('MpRule')->get_auto_reply_rule($reply_id);
		return $reply_rule['keyword'];
	}

	/**
	 * 获取回复内容
	 
	 */
	public function get_reply_content($material_id) {
		return model('MpMaterial')->get_material($material_id);
	}

	/**
	 * 删除关键词回复
	 
	 */
	public function delete() {
		$result = model('MpAutoReply')->get_auto_reply(input('id'));
		if ($result['errcode'] != 0) {
			$this->error($result['errmsg']);
		} else {
			$data = $result['result'];
			$type = $data['reply_type'];
			unset($result);
			$result = model('MpAutoReply')->delete_auto_reply($type, $data);
			if ($result['errcode'] != 0) {
				return $this->error($result['errmsg']);
			} else {
				return $this->success($result['errmsg']);
			}
		}
	}

	/**
	 * 非关键词回复
	 
	 */
	public function special() {
		if (IS_POST) {
			config('TOKEN_ON', false);
			$types = input('type/a');
			if (!$types || count($types) == 0) {
				return $this->error('无法设置非关键词回复');
			}
			$AutoReply = model('MpAutoReply');
			$data['mpid'] = get_mpid();
			foreach ($types as $k => $v) {
				$data['type'] = $v;
				$data['reply_type'] = input($v);
				$data['keyword'] = input($v.'_keyword');
				$data['addon'] = input($v.'_addon');
				$res = $AutoReply->get_auto_reply_by_type($v);
				if ($res) {
					$AutoReply->where(array('id'=>$res['id']))->update($data);
				} else {
					unset($data['id']);
					$AutoReply->insert($data);
				}
			}
			return $this->success('保存特殊消息回复成功');
		} else {
			$AutoReply = model('MpAutoReply');
			$show = array(
				array(
					'name' => 'image',
					'title' => '图片消息',
					'value' => $AutoReply->get_auto_reply_by_type('image')
				),
				array(
					'name' => 'voice',
					'title' => '语音消息',
					'value' => $AutoReply->get_auto_reply_by_type('voice')
				),
				array(
					'name' => 'shortvideo',
					'title' => '短视频消息',
					'value' => $AutoReply->get_auto_reply_by_type('shortvideo')
				),
				array(
					'name' => 'location',
					'title' => '位置消息',
					'value' => $AutoReply->get_auto_reply_by_type('location')
				),
				array(
					'name' => 'link',
					'title' => '链接消息',
					'value' => $AutoReply->get_auto_reply_by_type('link')
				),
			);
			$this->assign('show', $show);
			$addons = model('MpAddons')->get_installed_addons();
			$this->assign('addons', $addons);
			
			$tip = '当用户在公众号发送以下几种类型消息时，如果选择了响应插件，系统会把消息分发到指定的插件进行处理。如果绑定了关键词，系统会根据关键词回复中设置的内容直接回复。';
			$this->assign('tip', $tip);
			$this->setMeta('特殊消息回复');
			return $this->fetch();
		}
	}

	/**
	 * 事件回复
	 
	 */
	public function event() {
		if (IS_POST) {
			
			config('TOKEN_ON', false);
			$types = input('type/a');
			if (!$types || count($types) == 0) {
				return $this->error('无法设置非关键词回复');
			}
			$AutoReply = model('MpAutoReply');
			$data['mpid'] = get_mpid();
			foreach ($types as $k => $v) {
				$data['type'] = $v;
				$data['reply_type'] = input($v);
				$data['keyword'] = input($v.'_keyword');
				$data['addon'] = input($v.'_addon');
				$res = $AutoReply->get_auto_reply_by_type($v);
				if ($res) {
					$AutoReply->where(array('id'=>$res['id']))->update($data);
				} else {
					unset($data['id']);
					$AutoReply->insert($data);
				}
			}
			return $this->success('保存事件回复成功');
		} else {
			$AutoReply = model('MpAutoReply');
			$show = array(
				array(
					'name' => 'subscribe',
					'title' => '用户关注',
					'value' => $AutoReply->get_auto_reply_by_type('subscribe')
				),
				array(
					'name' => 'unsubscribe',
					'title' => '用户取消关注',
					'value' => $AutoReply->get_auto_reply_by_type('unsubscribe'),
					'tip' => '用户取消关注，自动回复内容不生效。可以为用户取消关注事件指定一个插件进行响应，从而进行诸如减少积分之类的操作。'
				),
				array(
					'name' => 'scan',
					'title' => '扫描二维码',
					'value' => $AutoReply->get_auto_reply_by_type('scan')
				),
				array(
					'name' => 'report_location',
					'title' => '上报地理位置',
					'value' => $AutoReply->get_auto_reply_by_type('report_location')
				),
				array(
					'name' => 'click',
					'title' => '点击菜单拉取消息',
					'value' => $AutoReply->get_auto_reply_by_type('click'),
					'tip' => '用户点击菜单时，默认会根据菜单推送的KEY值响应对应的关键词。此处的设置会把用户点击菜单拉取消息事件分发到指定的插件进行响应'
				),
			);
			$this->assign('show', $show);
			$addons = model('MpAddons')->get_installed_addons();
			$this->assign('addons', $addons);
			
			$tip = '当用户在公众号触发以下几种类型事件时，如果选择了响应插件，系统会把消息分发到指定的插件进行处理。如果绑定了关键词，系统会根据关键词回复中设置的内容直接回复。';
			$this->assign('tip', $tip);
			$this->setMeta('事件回复');
			return $this->fetch();
		}
	}

	// 未识别回复
	public function unrecognize() {
		if (IS_POST) {
			config('TOKEN_ON', false);
			$types = input('type/a');
			if (!$types || count($types) == 0) {
				return $this->error('无法设置非关键词回复');
			}
			$AutoReply = model('MpAutoReply');
			$data['mpid'] = get_mpid();
			foreach ($types as $k => $v) {
				$data['type'] = $v;
				$data['reply_type'] = input($v);
				$data['keyword'] = input($v.'_keyword');
				$data['addon'] = input($v.'_addon');
				$res = $AutoReply->get_auto_reply_by_type($v);
				if ($res) {
					$AutoReply->where(array('id'=>$res['id']))->update($data);
				} else {
					unset($data['id']);
					$AutoReply->insert($data);
				}
			}
			return $this->success('保存未识别回复成功');
		} else {
			$AutoReply = model('MpAutoReply');
			$show = array(
				array(
					'name' => 'unrecognize',
					'title' => '未识别回复',
					'value' => $AutoReply->get_auto_reply_by_type('unrecognize')
				)
			);
			$this->assign('show', $show);
			$addons = model('MpAddons')->get_installed_addons();
			$this->assign('addons', $addons);
			$tip = '当用户在公众号发送的消息未触发关键词回复、特殊消息回复、事件回复几种回复规则时，如果有设置未识别回复规则，则按此处设置的规则进行回复。';
			$this->assign('tip', $tip);
			$this->setMeta('未识别回复');
			return $this->fetch();
		}
	}

}

 ?>