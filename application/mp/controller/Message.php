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
 * 公众号消息管理控制器

 */
class Message extends Admin {

	/**
	 * 消息列表
	 */
	public function index() {
		$list = model('MpMessage')->where(array('mpid'=>get_mpid()))->order('create_time desc')->paginate(10);
		$options= array('text'=>'文本消息','image'=>'图片消息','voice'=>'语音消息','shortvideo'=>'短视频消息','video'=>'视频消息','location'=>'地理位置消息','link'=>'链接消息');
		if($list){
			foreach ($list as $k => &$v) {
				$v['msgtype'] = $options[$v['msgtype']];
				$v['contnet'] = $this->get_message_content($v['msgid']);
				$v['openid_head'] = get_fans_headimg($v['openid']);
				$v['openid_nickname'] = get_fans_nickname($v['openid']);
			}
		}
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('message_list'));
		return $this->fetch();
	}

	/**
	 * 保存为素材
	
	 */
	public function save_to_material() {
		$msgid = input('msgid');
		$message = model('MpMessage')->where(array('mpid'=>get_mpid(),'msgid'=>$msgid))->find();
		if (!$message) {
			return $this->error('消息不存在');
		} elseif ($message['save_status'] == 1) {
			return $this->error('该消息已保存为素材');
		} else {
			$msgtype = $message['msgtype'];
			switch ($msgtype) {
				case 'text':
					$insert['content'] = $message['content'];
					break;
				default:
					$this->error('此类型消息暂时不支持保存为素材');
					break;
			}
			$insert['mpid'] = get_mpid();
			$insert['type'] = $msgtype;
			$insert['create_time'] = time();
			if (!model('MpMaterial')->insert($insert)) {
				return $this->error('保存素材失败');
			} else {
				$save_data['save_status'] = 1;
				model('MpMessage')->where(array('mpid'=>get_mpid(),'msgid'=>$msgid))->update($save_data);
				return $this->success('保存素材成功', url('index'));
			}
		}
	}

	/**
	 * 回复消息
	
	 */
	public function reply_message() {
		if (IS_POST) {
			$data = input('post.');
			$content = $data['content'];
			if (!$content) {
				return $this->error('请填写回复内容');
			} else {
				$reply = array(
					'touser' => $data['openid'],
					'msgtype' => 'text',
					'text' => array(
						'content' => $data['content']
					)
				);
				$result = send_custom_message($reply);
				
			
				if ($result['errcode'] == 0) {
					$save_data['reply_status'] = 1;
					model('MpMessage')->where(array('mpid'=>get_mpid(),'msgid'=>input('msgid')))->update($save_data);
					return $this->success('回复成功', url('index'));
				} else {
					return $this->error($result['errmsg']);
				}
			}
		} else {
			$message = model('MpMessage')->where(array('mpid'=>get_mpid(),'msgid'=>input('msgid')))->find();
			if (!$message) {
				return $this->error('消息不存在');
			}
			if (time()-strtotime($message['create_time']) > 48*3600) {
				return $this->error('该消息发送时间距离此刻已超过48小时，不能回复');
			}
			$this->assign('message',$message);
			$this->setMeta(lang('reply_message'));
			return $this->fetch();
		}
	}
		
	public function get_img_url($url) {
		$to='http://read.html5.qq.com/image?src=forum&q=5&r=0&imgflag=7&imageUrl=';
		return $to.$url;
	}
	

	/**
	 * 获取消息内容
	
	 */
	public function get_message_content($msgid) {
		$map['msgid'] = $msgid;
		$map['mpid'] = get_mpid();
		$message = model('MpMessage')->where($map)->find();
		if (!$message) {
			return '';
		}
		switch ($message['msgtype']) {
			case 'text':
				return $message['content'];
				break;
			case 'image':
				// 感谢 @  平凡<58000865@qq.com> 提供的微信图片防盗链解决方案
            	return '<img src="'.$this->get_img_url($message['picurl']).'" width="100" height="100" />';      
				break;
			case 'voice':
				return '【语音】';
				break;
			case 'shortvideo':		
				return '【视频】';
				break;
			case 'video':		
				return '【视频】';
				break;	
			case 'location':
				return '【位置】'.$message['label'];
				break;
			case 'link':
				return '【链接】<a style="color:#08a5e0" href="'.$message['url'].'" target="_blank">'.$message['title'].'</a>';
				break;
			default:
				return '';
				break;
		}
	}

}



 ?>