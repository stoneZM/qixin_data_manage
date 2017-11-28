<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\goods\controller;
use think\Db; 


class Update extends \think\Controller {
	
	public function index(){
		
	}
	public function versions(){
		$goods_update = $this->get_goods_update_all();
		return json($goods_update);
	}
	
	public function get_goods_update_all($is_file=false,$desc='number asc'){
		
		$map['status'] = 1;
		if($is_file){
			$update_data = Db::name('goods_update')->where($map)->field('is_current',true)->order($desc)->select();
		}else{
			$update_data = Db::name('goods_update')->where($map)->field('file_id,is_current',true)->order($desc)->select();
		}
		return $update_data;
	}
	
	private function get_goods_update($number){
		$map['number'] = $number;
		$map['status'] = 1;
		$update_data = Db::name('goods_update')->field('is_current',true)->where($map)->find();
		return $update_data;
	}

	private function get_downlist($number=''){
		
		if(!$number){
			return false;	
		}
		$steps = model('Steps');
		
		$all_version_data = $this->get_goods_update_all(true);
		if($all_version_data){
			foreach ($all_version_data as $key=> &$v) {
				$down_list[$v['number']] =$v;
				$steps->add($v['number']);
			}
			unset($key);
			$steps->setCurrent($number);
			$data['list'] = $down_list;
			$data['prev'] = $steps->getPrev();
			$data['current'] = $steps->getCurrent();
			$data['next'] = $steps->getNext();
			
			return $data;
		}else{
			return false;	
		}		
		
	}
	
	public function download($type=''){
		
		$number = input('number');
		if(!$number){
			return $this->error("错误的版本信息！");
		}
		$downlits = $this->get_downlist($number);
		if($downlits){
			if($type =='old'){
				$down_key = $downlits['next'];
			}elseif($type =='new'){
				$down_key = $downlits['current'];
			}else{
				$down_key = $downlits['current'];
			}
			if($down_key){
				$down_data =  $downlits['list'][$down_key];
				if($down_data['file_id']){
					$file_data = get_goods_file($down_data['file_id']);
					if($file_data['url']){
						return \File::download('.'.$file_data['url']);
					}else{
						return $this->error("版本不存在！");
					}
				}else{
					return $this->error("版本不存在！");
				}
			}else{
				return $this->error("版本不存在！");
			}
		}else{
			return $this->error("版本不存在！");
		}
	}
	
	
}
