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
 * 素材管理控制器

 */
class Material extends Admin {
	
	public function _initialize() {
		parent::_initialize();
	}
    /**
     * 文本素材列表
    
     */
    public function text() {
		$list = model('MpMaterial')->where(array('mpid'=>get_mpid(),'type'=>'text'))->order('create_time desc')->paginate(24);
		if($list){
			
		}
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta('文本素材');
		return $this->fetch();
    }

	/**
	 * 图片素材列表
	
	 */
	public function image() {
		$list = model('MpMaterial')->where(array('mpid'=>get_mpid(),'type'=>'image'))->order('create_time desc')->paginate(24);
		if($list){
			
		}
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta('图片素材');
		return $this->fetch();
	}

    /**
     * 图文素材列表
    
     */
    public function news() {
		
		
		$list = model('MpMaterial')->where(array('mpid'=>get_mpid(),'type'=>'news'))->order('create_time desc')->paginate(24);
		if($list){
			
		}
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta('图文素材');
		return $this->fetch();
    }

    /**
     * 添加素材
    
     */
    public function add() {
		
		
		
		if (IS_POST) {
			
			$data = input();
			$type = $data['type'];
			unset($data['id']);
			switch ($type) {
				case 'text':
					if(!$data['content']){
						return $this->error(lang('text_content').lang('cannot_be_empty'));
					} 
					
					break;
				case 'image':
					if(!$data['image']){
						return $this->error(lang('reply_picture').lang('cannot_be_empty'));
					}   
					break;
				case 'news':
					if(!$data['title']){
						return $this->error(lang('title').lang('cannot_be_empty'));
					}
					if(!$data['picurl']){
						return $this->error(lang('cover').lang('cannot_be_empty'));
					}
					
					if(!$data['description']){
						return $this->error(lang('description').lang('cannot_be_empty'));
					}
					
					if(!$data['url']){
						return $this->error(lang('link').lang('cannot_be_empty'));
					}
					break;
				default:
					# code...
					break;
			}
			
			$data['mpid'] = get_mpid();
			$data['create_time'] = time();
			$result = model('MpMaterial')->insert($data);
			if ($result) {
				return $this->success(lang('add').lang('success'), url($data['type']));
			} else {
				return $this->error(lang('add').lang('fail'));
			}
		} else {
			
			$type = input('type');
			if(!$type){
				$type = 'text';
			}
			$type_arr = array('text'=>'文本','image'=>'图片','news'=>'图文');
			$this->assign('type',$type);
			$this->setMeta('添加'.$type_arr[$type].'素材');
			return $this->fetch('edit');
		}
    }

    /**
     * 编辑素材
    
     */
    public function edit() {
		
		
		if (IS_POST) {
			$data = input('post.');
			$type = $data['type'];
			switch ($type) {
				case 'text':
					if(!$data['content']){
						return $this->error(lang('text_content').lang('cannot_be_empty'));
					} 
					break;
				case 'image':
					if(!$data['image']){
						return $this->error(lang('reply_picture').lang('cannot_be_empty'));
					}   
					break;
				case 'news':
					if(!$data['title']){
						return $this->error(lang('title').lang('cannot_be_empty'));
					}
					if(!$data['picurl']){
						return $this->error(lang('cover').lang('cannot_be_empty'));
					}
					
					if(!$data['description']){
						return $this->error(lang('description').lang('cannot_be_empty'));
					}
					
					if(!$data['url']){
						return $this->error(lang('link').lang('cannot_be_empty'));
					}
					break;
				default:
					# code...
					break;
			}
			$result = model('MpMaterial')->where(array('id'=>$data['id']))->update($data);
			if ($result) {
				return $this->success(lang('edit').lang('success'), url($data['type']));
			} else {
				return $this->error(lang('edit').lang('fail'));
			}
		} else {
			
			$id = input('id');
			$material = model('MpMaterial')->where(array('mpid'=>get_mpid(),'id'=>$id))->find();
			if (!$material) {
				$this->error('素材不存在');
			}
			$type_arr = array('text'=>'文本','image'=>'图片','news'=>'图文');
			$this->assign('data',$material);
			$this->assign('id',$id);
			$this->assign('type',$material['type']);
			$this->setMeta('编辑'.$type_arr[$material['type']].'素材');
			return $this->fetch('edit');
		}
    }

    /**
     * 删除素材
    
     */
    public function delete() {
        $id = input('id');
        if (!model('MpMaterial')->where(array('mpid'=>get_mpid(),'id'=>$id))->find()) {
            $this->error('素材不存在');
        } else {
            if (!model('MpMaterial')->where(array('mpid'=>get_mpid(),'id'=>$id))->delete()) {
                $this->error('删除素材失败');
            } else {
                $this->success('删除素材成功');
            }
        }
    }

    /**
     * 下载微信素材库里面的图片到本地
    
     */
    public function download_image_from_wechat() {
        $wechatInfo = get_mp_info();
        $options = array(
            'token'             =>  $wechatInfo['valid_token'],                 
            'encodingaeskey'    =>  $wechatInfo['encodingaeskey'],      
            'appid'             =>  $wechatInfo['appid'],               
            'appsecret'         =>  $wechatInfo['appsecret']            
        );
        $wechatObj = new Wechat($options);
        $images = $wechatObj->getForeverList('image', 0, 10);
        $upload_time = time();
        $upload_path = './Uploads/Pictures/' . date('Ymd', $upload_time) . '/';
        if (!file_exists($upload_path)) {
            $dirs = explode('/', $upload_path);
            $dir = $dirs[0] . '/';
            for ($i=1, $j=count($dirs)-1; $i<$j; $i++) {
                $dir .= $dirs[$i] . '/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0777);
                }
            }
        }
        foreach ($images['item'] as $k => $v) {
            $file_extension = substr($v['url'], intval(strpos($v['url'], '='))+1);   
            $file_name = md5($v['media_id']) . '.'  . $file_extension;
            $file_path = $upload_path . $file_name;
            if ($v['url']) {
                $file_contents = file_get_contents($v['url']);
            } else {
                $file_contents = $wechatObj->getForeverMedia($v['media_id']);
            }
            $create_time = time();
            $file_size = file_put_contents($file_path, $file_contents);
            $attach['mpid'] = get_mpid();
            $attach['user_id'] = get_user_id();
            $attach['file_name'] = $file_name;
            $attach['file_extension'] = $file_extension;
            $attach['file_size'] = $file_size;
            $attach['file_path'] = $file_path;
            $attach['hash'] = md5_file($file_path);
            $attach['create_time'] = $create_time;
            $attach['item_type'] = 'image';
            if (db('mp_picture')->where(array('mpid'=>get_mpid(),'hash'=>$attach['hash']))->find()) {
                db('mp_picture')->where(array('mpid'=>get_mpid(),'hash'=>$attach['hash']))->update($attach);
            } else {
                db('mp_picture')->insert($attach);
            }

            $material['mpid'] = get_mpid();
            $material['type'] = 'image';
            $material['image_name'] = $file_name;
            $material['image_url'] = $file_path;
            $material['media_id'] = $v['media_id'];
            $material['from'] = 'wechat';
            $material['create_time'] = $create_time;
            if (model('mp_material')->where(array('mpid'=>get_mpid(),'type'=>'image','media_id'=>$material['media_id']))->find()) {
                model('mp_material')->where(array('mpid'=>get_mpid(),'type'=>'image','media_id'=>$material['media_id']))->update($material);
            } else {
                model('mp_material')->insert($material);
            }
        }
        $this->success('同步素材成功');
    }
}

 ?>
