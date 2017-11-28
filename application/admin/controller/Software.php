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



class Software extends Admin {


	public function _initialize() {
		parent::_initialize();
	}
	
	public function index() {
		$soft_list = db('software')->where($map)->order('id asc')->select();
		$this->assign('soft_list', $soft_list);
		$this->setMeta(lang('software_download'));
		return $this->fetch();
	}


	public function progress() {
		set_time_limit(0);		
		$tmp_path ="data/software/progress";
		if ($file = fopen($tmp_path, "r")) {
			$progress = fread($file,filesize($tmp_path));
			fclose($file);
		}else{
			$progress = 0;
		}
		//$progress = @file_get_contents($progress_path);
		ob_flush();
		flush();
		return json(array('size' => $progress));
	}
	
	public function download_state() {
		$id =input('id');
		if (empty($id)) {
			return $this->error(lang('parameter_error'));
		}
		
		$map['id'] = $id;
		$soft_data = db('software')->where($map)->order('id desc')->find();
		if(!$soft_data){
			return $this->error('数据不存在');
		}
		
		// 下载缓存文件夹
		$download_cache = "data/software";
		if (!is_dir($download_cache)) {
			if (false === mkdir($download_cache)) {
				return $this->error('创建下载缓存文件夹失败，请检查目录权限。');
			}
		}
		return $this->success('success','',$soft_data);
	}	





	public function download_file() {

		$id =input('id');
		if (empty($id)) {
			return $this->error(lang('parameter_error'));
		}
		
		$soft_data = db('software')->where($map)->order('id desc')->find();
		if(!$soft_data){
			return $this->error('数据不存在');
		}
		
		$remote_url  =$soft_data['url'];
		$down_path = $soft_data['path'];
		set_time_limit(0);
		//需要下载的远程文件url
		//ob_start();
		$mh = curl_multi_init();
		
		$ch=curl_init();//Initialize a cURL session.
		$save_file = fopen(ROOT_PATH.$down_path,'w');	
		curl_setopt($ch, CURLOPT_URL, $remote_url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_NOPROGRESS, FALSE);
		curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'downsofeware_progress');
		curl_setopt($ch, CURLOPT_FILE, $save_file);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_BUFFERSIZE, 64000);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		//curl_exec($ch);
		//curl_close($ch);
		curl_multi_add_handle($mh, $ch);
		$running = 0;   
		do {    
			curl_multi_exec($mh, $running);  
		}   
		while($running > 0);
		
		//$res = curl_multi_getcontent($ch);
		
		curl_multi_remove_handle($mh,$ch);  
		curl_multi_close($mh);    

		fclose($save_file);
		ob_flush();
		flush();
		$result = db('software')->where(array('id' => $id))->update(array('status' => 1));
		//return(0);
		return $this->success(lang('download').lang('success'));
	}	
	
}