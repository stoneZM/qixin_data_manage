<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\move\model;
use think\Model;

class Move extends Model {

	protected $auto   = array('status', 'update_time');
	protected $insert = array('create_time');

	protected function setStatusAttr($value) {
		return $value ? $value : 0;
	}

	protected function setIsinstallAttr($value) {
		return $value ? $value : 0;
	}

	protected function getStatusTextAttr($value, $data) {
		return $data['status'] ? "启用" : "禁用";
	}

	protected function getUninstallAttr($value, $data) {
		return 0;
	}

	static public function get_partition_info($device_id,$partition){


		$partition_info = explode(',',$partition);
		$disk_index = array();
		$partition_index = array();
		foreach ($partition_info as $k => $v) {
			$index = explode('_',$v);
			$disk_index[] = $index[0];
			$partition_index[$index[0]][] = $index[1];
		}
		$disk_index = array_unique($disk_index);
		$info = array();
		$harddisk_info = db('device')->where(array('id'=>$device_id))->field(array('harddisk_info'))->find();
		$partition_info = array();
		$harddisk_list = json_decode($harddisk_info['harddisk_info']);

		foreach ($harddisk_list as $k => $v) {
			$diskInfo = $v;
			if (in_array($diskInfo->harddiskindex,$disk_index)) {
				foreach ($diskInfo->partitions as $key => $value) {
					$partition_info = $value;
					$index = $partition_index[$diskInfo->harddiskindex];
					if (in_array($partition_info->partitionindex,$index)) {
						$data['harddiskindex'] = $diskInfo->harddiskindex;
						$data['key'] = $diskInfo->harddiskindex.'_'.$partition_info->partitionindex;
						$data['driverletter'] = $partition_info->driverletter;
						$data['partitionindex'] = $partition_info->partitionindex;
						$data['status']=0;
						$data['totalsize'] = (string)$partition_info->totalsize;
						$info[] = $data;
					}
				}
			}
		}

		return json_encode($info);

	}


	static public function add_cdp_task($device_id,$partition){

		$data['device_id'] = $device_id;
		$data['status'] = 0;
		$data['type'] = "move";
		$data['work_partition'] = Move::get_partition_info($device_id,$partition);
		$harddisk_info = db('device')->where(array('id'=>$device_id))->field(array('harddisk_info'))->find();
		$data['harddisk_info'] =  $harddisk_info['harddisk_info'];
		$data['storage_id'] = 0;
		$data['backup_speed'] = 0;
		$data['snap_num'] = 64;
		$data['snap_time'] = 172800;
		$data['synchr_time'] = 1800;
		$data['sub_snap_num'] = 8 ;
		$data['create_time'] = time();
		$model = db('cdp_task');
		if ($model->insert($data)) {
			return $model->getLastInsID();
		}else{
			return false;
		}

	}

	//信息过滤
	static public function info_filter($data){
		$res_data['id'] = $data['id'];
		$res_data['alias'] = $data['alias'];
		$res_data['ip'] = $data['ip'];
		$res_data['unique_id'] = $data['unique_id'];
		$res_data['type'] = $data['type'];
		$res_data['harddisk_info'] = json_decode($data['harddisk_info'],true);

		return $res_data;
	}



	/********************发送消息****************************/

	static function send_status_cdp_message($status,$data){

//		w2p_start_move 启动
//		w2p_retry_move  重试
		if ($status == 0)
			$message_str = 'w2p_stop_migrate';
		else if($status == 1)
			$message_str = 'w2p_start_migrate';

		$message_data['moveTaskId'] =$data['move_task_id'];
		$message_data['taskId'] =$data['task_id'];
		$message_data['sourceUniqueId'] = $data['source_unipue_id'];
		$message_data['targetUniqueId'] = $data['target_unipue_id'];
		$message_data['selectedPartitions'] = $data['partition'];
		$message_data['harddiskVersus'] = $data['disk_versus'];

		$result = \Netmessage::send_message($message_str, $message_data);


		if ($result['code'] == 1) {
			return true;
		} else {
			return false;
		}

	}


	static function send_async_message($unique_id){
		//deviceUniqueId

		$message_str = 'w2p_clone_syn';
		$message_data['deviceUniqueId'] = $unique_id;

		$result = \Netmessage::send_message($message_str, $message_data);
		if($result['code']==1){
			return true;
		}
		return false;
	}

	static function send_get_migrating_info_message($id){

//		$target_info = json_decode($info['target'],true);
//		$message_data['targetUniqueId'] = $target_info['unique_id'];
		$message_data['moveTaskId'] = $id;
		$result = \Netmessage::send_message('w2p_get_migrating_info', $message_data);
		return $result;
	}

}