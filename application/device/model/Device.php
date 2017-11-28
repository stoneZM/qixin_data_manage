<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\device\model;

use app\cdp\model\Cdp;
use app\cdp\model\CdpSnap;
use app\cdp\model\CdpTask;
use think\Model;

class Device extends Model
{
    // 指定表名,不含前缀
    protected $name = 'device';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';


	
	public function getDeviceAll(){
		$data = $this->where()->select();
		if ($data) {
			return $data;
		}else{
			return false;
		}
		
	}
	
	public function getDevicelist(){
		$data = $this->where(array('status'=>1))->select();
		if ($data) {
			return $data;
		}else{
			return false;
		}
		
	}
	
	public function getExclusiveDevice($app_type,$map=''){

		$data = $this->where($map)->select();
		if ($data) {
			if($app_type){
				$app_type_data = explode(",",$app_type);
				foreach ($data as $key => $value) {
					
					if($value['app']){
						$app = explode(",",$value['app']);
						
						
						foreach ($app_type_data as  $v) {
							if(in_array($v,$app)){
								unset($data[$key]);
								continue;
							}
						}
					}
				}
			}
			return $data;
		}else{
			return false;
		}
		
	}	
	

	public function getDeviceInfo($id,$map=null){

		$data = $this->field($map)->where(array('id'=>$id))->find();
		if ($data) {
			return $data->toArray();
		}else{
			return false;
		}
	}


	static function get_device_network_info($id){


		$info = self::get(['id'=>$id]);
		$network_info = json_decode($info->system_info,true)['netcards'];
		if(!$network_info){
			return array();
		}else{
			return $network_info;
		}
	}


	//获取操作系统
	static function get_system_type($id){

		$info = self::field('system_info')->where(array('id'=>$id))->find();
		$system_info = json_decode($info->system_info);
		$client_system = $system_info->systemversion;


		if(strtolower(substr($client_system,0,1))=='w') {
			return 'Windows';
		}else{
			return 'Linux';
		}
	}


	//检查是否能删除cdp下的设备
	static function check_can_del_device($id){

		$count = ComputeVirtual::getCount($id,'id');
		if($count)
			return false;
		else
			return true;

	}

	static function del_device_data($id){

		$task_ids = CdpTask::all(function($query) use($id){
			$query->where('device_id', $id)->field('id');
		});
		if(!$task_ids){
			return true;
		}
		$task_ids = $task_ids->toArray();
		$flag = 1;
		foreach($task_ids as $item){
			if(!CdpTask::del_task($item['id'])){
				$flag = 0;
			}
		}
//		$cdp_snap = new CdpSnap();
//		$flag = 1;
//		foreach($task_ids as $item){
//			if(!$cdp_snap->del_snap_by_task_id($item['id'])){
//				$flag = 0;
//			}else{
//				CdpTask::destroy(["id"=>$item['id']]);
//			}
//		}
		if($flag)
			return true;
		else
			return false;

	}

	static function checkAuth($device_id){

		$uid = $_SESSION['qinfo']['user_auth']['uid'];
		if($uid == 3){
			return true;
		}
		$user_auth_id = db('device_auth')
				->where(array('uid'=>$uid))
				->select();
		if(in_array($device_id,$user_auth_id)){
			return true;
		}else{
			return false;
		}
	}

	//获取 unique_id
	static public function get_unipue_id($id){

		$device_info = self::field(array('unique_id'))->where(array('id'=>$id))->find();
		$unique_id = $device_info['unique_id'];
		return $unique_id;
	}

	static public function getDeviceId($key,$value){

		$device = self::where(array($key=>$value))->find();
		return $device['id'];
	}

	/**
	 * 统计 设备下的信息
	 *
	 * 任务数
	 * 任务状态
	 * 快照数
	 * 虚机数    （在线个数）
	 * 演练虚机数（在线个数）
	 * 接管虚机数 （在线个数）
	 * @param $id
	 */
	static public function getDeviceMoreInfo($id){

		if(!$id){
			return array();
		}
        $data = array();
		$newest_task_id = CdpTask::getNewestTaskId($id);
       if (count($newest_task_id)!=1){
           $newest_task_id = 0;
       }else{
           $newest_task_id = $newest_task_id[0]['id'];
           $snap_time = CdpSnap::getNewestSnapTime($newest_task_id);
           $newest_snap_time = (count($snap_time)==1)?$snap_time[0]['create_time'] : 0;

           $newest_snap_time = date("Y-m-d",strtotime($newest_snap_time));
       }

		$soft_type  = get_soft_type();
        $data['newest_task_id'] = $newest_task_id;
        $data['newest_snap_time'] = $newest_snap_time;

        $data['current_time'] = date('Y-m-d',time());
        $device_info = Device::where(array('id'=>$id))->column('status');
        $data['device_status'] = current($device_info);
		//任务数
		$data['task_count'] = CdpTask::where(array('device_id'=>$id))->count('id');
		$current_task = CdpTask::get(array('device_id'=>$id,"type"=>"current"));
		if($current_task){
            $data['task_id'] = $current_task['id'];
			$data['have_current_task'] = 1;
            $data['task_status'] = $current_task->status;   //  任务状态
			$data['part_status'] = CdpTask::task_is_finished($current_task->id)?1:0;   //分区状态
            $data['clone_is_running'] = CdpTask::clone_is_running($current_task->id);
		}else{
			$data['have_current_task'] =  0;
		}
		// 快照数
		$data['snap_count'] = CdpSnap::getSnapCountByDeviceId($id);
        //检查当日是否有快照


		//虚机数
		$virtual_info = ComputeVirtual::where(array('device_id'=>$id))->field('id,type,status')->select();
		if($virtual_info){
			$virtual_info = $virtual_info->toArray();
			$data['virtual_count'] = count($virtual_info);
			$data['take_over_count'] = 0;
			$data['exercise_count'] = 0;
			$data['status_on'] = 0;
			$data['take_over_on'] = 0;
			$data['exercise_on'] = 0;
			foreach($virtual_info as $key=>$value){

				if($value['status']==1){

					$data['status_on']++;
				}
				if($value['type']==1){
					if($value['status']==1){
						$data['exercise_on']++;
					}
					$data['exercise_count']++;

				}else{

					if($value['status']==1){
						$data['take_over_on']++;
					}
					$data['take_over_count']++;
				}
			}
		}

 		$data['soft_type'] = $soft_type;
		return $data;
	}


	static function check_device_is_on($id){

		$status = self::where(array('id'=>$id))->column('status');
		if(is_array($status)&&current($status)==1){
			return true;
		}else{
			return false;
		}
	}


}
