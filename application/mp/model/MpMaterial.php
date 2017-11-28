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
use think\Session;
use think\Response;
use think\Request;
use think\Url;
use think\Db;
class MpMaterial extends Model {

	/**
	 * 根据素材ID获取素材内容
	 */
	public function get_material($material_id) {
		if (!$material_id) {
			return false;
		}
		$material = $this->find(intval($material_id));
		switch ($material['type']) {
			case 'text':
				return $material['content'];
				break;
			case 'image':
				return '<img src="'.get_wechat_img($material['image'],'path').'" width="80" height="80" />';
				break;
			case 'news':
				return '<a href="'.$material['url'].'" target="_blank"><div style="width:200px;height:130px;background:#fff;border:1px solid #ccc;overflow:hidden;cursor:pointer">
							<div style="text-align:left;font-size:14px;font-weight:500;color:#000;margin:5px;border-bottom:1px solid #ccc;max-height:40px;overflow:hidden">'.$material['title'].'</div>
							<div style="text-align:left;font-size:12px;color:#333;margin:5px;">
								<div style="width:135px;height:50px;overflow:hidden;float:left;color:#666;margin-bottom:2px;">'.$material['description'].'</div>
								<div style="width:50px;height:50px;float:right"><img width="100%" height="100%" src="'.get_wechat_img($material['picurl'],'path').'" /></div>
							</div>
							<div style="border-top:1px solid #ccc;clear:both;text-align:left;font-size:9px;color:#999;margin:5px;padding-top:5px;">查看链接</div>
						</div></a>';
				break;
			default:
				# code...
				break;
		}
	}
}

?>