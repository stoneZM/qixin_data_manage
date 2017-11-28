<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\mp\model;
use think\Model;

class MpMessage extends Model {

	/**
	 * 保存消息

	 */
	public function save_message($message) {
		$msgId = $message['MsgId'];
        $mpid = get_mpid();
		if (!$msgId || !$mpid) {
			return false;
		}
		$data['mpid'] = $mpid;
		$data['openid'] = $message['FromUserName'];
    	$data['msgid'] = $message['MsgId'];
    	$data['msgtype'] = $message['MsgType'];
    	$data['create_time'] = $message['CreateTime'];
    	
    	if ($message['MsgType'] == 'text') {
    		$data['content'] = $message['Content'];
    	}
    	if ($message['MsgType'] == 'image') {
    		$data['picurl'] = $message['PicUrl'];
    		$data['mediaid'] = $message['MediaId'];
    	}
    	if ($message['MsgType'] == 'voice') {
    		$data['mediaid'] = $message['MediaId'];
    		$data['format'] = $message['Format'];
			if($message['Recognition']){
				$data['recognition'] = $message['Recognition'];	
			}
    	}
    	if ($message['MsgType'] == 'video' || $message['MsgType'] == 'shortvideo') {
    		$data['mediaid'] = $message['MediaId'];
    		$data['thumb_mediaid'] = $message['ThumbMediaId'];
    	}
    	if ($message['MsgType'] == 'location') {
    		$data['location_x'] = $message['Location_X'];
    		$data['location_y'] = $message['Location_Y'];
    		$data['scale'] = $message['Scale'];
    		$data['label'] = $message['Label'];
    	}
    	if ($message['MsgType'] == 'link') {
    		$data['title'] = $message['Title'];
    		$data['description'] = $message['Description'];
    		$data['url'] = $message['Url'];
    	}
		if ($this->get_message($msgId)) {
            $map['msgid'] = $msgId;
			model('mp_message')->where($map)->update($data);	                  // 如果消息存在，则保存消息
		} else {
			model('mp_message')->insert($data);								  // 新增消息
		}
		return $data;
	}

	/**
	 * 获取消息

	 */
	public function get_message($msgId) {
		if (!$msgId) {
			return false;
		}
        $map['msgid'] = $msgId;
		$message = model('mp_message')->where($map)->find();
		return $message;
	}
	
}