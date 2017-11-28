<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\move\controller;
use app\common\controller\Admin;
use app\common\model\Log;
use app\move\model\Move;

class Manage extends Admin {


	public function _initialize() {
		parent::_initialize();
		
		$this->move = model('move');
		$this->device = model('device/device');
	}
	/**
	 * CDP列表
	 */
	public function index() {


//		$exclusivedevice = $this->device->getExclusiveDevice('cdp,sb',array('status'=>1));

		$sourcedevice = $this->device->getExclusiveDevice('move',array('status'=>1,'attribute'=>1));
		if($sourcedevice){
			foreach ($sourcedevice as $source_key =>&$vo_source) {
				if($vo_source['harddisk_info']){
					$vo_source['harddisk_info'] = 	json_decode($vo_source['harddisk_info'],true);
				}else{
					unset($sourcedevice[$source_key]);
				}
			}
		}

		$targetdevice = $this->device->getExclusiveDevice('move',array('status'=>1,'attribute'=>2));
		if($targetdevice){
			foreach ($targetdevice as $target_key =>&$vo_target) {
				if($vo_target['harddisk_info']){
					$vo_target['harddisk_info'] = 	json_decode($vo_target['harddisk_info'],true);
				}else{
					unset($targetdevice[$target_key]);
				}
			}
		}
		
		$list = $this->move->where('')->order('id desc')->paginate(25);

		if($list){
			foreach ($list as $key => &$vo) {
			
			}
		}

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->setMeta(lang('move_task'));
		$this->assign('sourcedevice',$sourcedevice);
		$this->assign('targetdevice',$targetdevice);
		$this->assign($data);
		return $this->fetch();
	}
	
	public function get_harddisk()
	{
		$source_id =$_POST['source_id'];
		if(!$source_id){
			return $this->error('参数出错');
		}
		$ids =$_POST['ids'];
		if(!$ids){
			return $this->error('请选择原机磁盘');
		}
		$ids = explode(",",$ids);
		$device_Info = $this->device->getDeviceInfo($source_id);
		if($device_Info['harddisk_info']){
			$device_Info['harddisk_info'] = json_decode($device_Info['harddisk_info'],true);
		}else{
			return $this->error('未找到磁盘信息');
		}
		
		foreach ($ids as $h_key =>&$h_value) {
			$hd = explode("_",$h_value);
			$hdd[] = $hd[0];	
		}
	
		$harddisk_array=array_unique($hdd);
		foreach ($harddisk_array as $hd_key =>&$hd_value) {
			$harddisk_data = $device_Info['harddisk_info'][$hd_value];
			$result_harddisk[$hd_key]['aliasname'] = $harddisk_data['aliasname'];
			$result_harddisk[$hd_key]['harddiskindex'] = $harddisk_data['harddiskindex'];
			$result_harddisk[$hd_key]['totalsize'] = format_bytes($harddisk_data['totalsize']);
		}
		return json($result_harddisk);
	}

	/**
	 *
     */
	public function add_task() {
		
	
		$source_device = input('source_device');
		if(!$source_device){
			return $this->error(lang('please_select').lang('source_device'));	
		}
		$partition = input('partition/a');
		if(!$partition){
			return $this->error(lang('please_select').lang('migration_partition'));	
		}
		
		
		$target_device = input('target_device');
		if(!$target_device){
			return $this->error(lang('please_select').lang('target_device'));	
		}
		
		$target_hdd = input('target_hdd');
		if(!$target_hdd){
			return $this->error(lang('please_select').lang('data_migration_relation'));	
		}

		$source_info = Move::info_filter($this->device->getDeviceInfo($source_device));

		//检查cdp是否有当前任务存在
		$device_info = db("device")->where(array("ip"=>$source_info['ip']))->find();
		$cdp_task = db('cdp_task')->where(array('type'=>'current','device_id'=>$device_info['id']))->field(array('id'))->find();
		if($cdp_task){
			return $this->error(lang('cdp_task_is_exist').",".lang('please_stop')."!");
		}


		$source_info =  Move::info_filter($this->device->getDeviceInfo($source_device));
		$target_info =  Move::info_filter($this->device->getDeviceInfo($target_device));
		
		
		$check_map['source_ip'] = $source_info['ip'];
		$check_map['target_ip'] = $target_info['ip'];
		$check_map['status'] = 1;
		
		
		$check_move = $this->move->where($check_map)->find();
		if($check_move){
			return $this->error(lang('the_task_already_exists'));	
		}
		
		$config_data['partition'] = implode(',',$partition);
		$config_data['disk_versus'] = $target_hdd;
		$savedata['source_ip']= $source_info['ip'];
		$savedata['source']= json_encode($source_info);
		$savedata['target_ip']= $target_info['ip'];
		$savedata['target']= json_encode($target_info);
		$savedata['config']= json_encode($config_data);
		$savedata['status']= 0;
		$savedata['create_time']= time();

		$res_move = $this->move->insert($savedata);
		
		if($res_move){
			return $this->success(lang('add').lang('success'));	
		}else{
			return $this->error(lang('add').lang('fail'));	
		}
	}


	public function detail() {

		$id = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));	
		}
		$info = db('move')->where(array('id'=>$id))->find();

		if(!$info){
			return $this->error(lang('task_does_not_exist'));	
		}
		

		$source_info = json_decode($info['source'],true);
		$target_info = json_decode($info['target'],true);
		
		$device_source = $this->device->getDeviceInfo($source_info['id']);
		$device_target = $this->device->getDeviceInfo($target_info['id']);
		
		$source_info['status']  = $device_source['status'];
		$target_info['status']  = $device_target['status'];
		$move_config = json_decode($info['config'],true);
		
		$disk_versus = explode(',',$move_config['disk_versus']);
		foreach ($disk_versus as $key =>&$value) {
			$tempdisk = explode(':',$value);
			$harddiskversus[$key]['source'] = $source_info['harddisk_info'][$tempdisk[0]];
			$harddiskversus[$key]['target'] = $target_info['harddisk_info'][$tempdisk[1]];
		}
	
		$this->assign('harddiskversus',$harddiskversus);
		
		$this->assign('source_info',$source_info);
		$this->assign('target_info',$target_info);
		$this->assign('move_info',$info);
		$this->setMeta(lang('task_details'));
		return $this->fetch();
	}	

	/**
	 * 读取迁移信息
	 */
	public function get_move_state(){

		$id = input('id');

		if(!$id)
			return $this->error(lang('parameter_error'));
		$info = db('move')->where(array('id'=>$id))->find();
		if(!$info)
			return $this->error(lang('task').lang('not_exist'));
		
		
		if($info['status']==2){
			$data['code'] = 2;
			$data['msg'] = lang('task_completed');
			return json($data);
		}

		if($info['status']==3)
			return $this->error('task_fail');

		$result = Move::send_get_migrating_info_message($id);
		if ($result['code'] == 1) {

			$res_data['code'] = $result['code'];
			$res_data['progress'] = $result['migratinginfo']['progress'];
			$res_data['speed'] = $result['migratinginfo']['speed'];
			$res_data['useTime'] = $result['migratinginfo']['elapsedtime'];
			return json($res_data);
		} else {
			return $this->error('获取信息失败');
		}
	}

	/**
	 * 删除历史任务
	 */
	public function del_task(){

		$id = input('id');

		if(!$id)
			return $this->error(lang('parameter_error'));
		$task = db('move')->where(array('id'=>$id))->find();
		if(!$task)
			return $this->error(lang('task').lang('not_exist'));
//		if($task['status']==1)
//			return $this->error(lang('task_can_not_del'));
//		if($task['status'] != 0 &&  $task['status'] != 3)
//			return $this->error(lang('only_failed_and_failed_tasks_can_only_be_deleted'));
			
		if(db('move')->where(array('id'=>$id))->delete())
			return $this->success(lang('delete').lang('success'));
		else
			return $this->error(lang('delete').lang('fail'));
		
	}

	/**
	 * 启动 或 暂停任务
	 */
	public function task_status(){

		$id = input('id');
		$status = input('status');

		if(!$id){
			return $this->error(lang('parameter_error'));
		}
		$task = db('move')->where(array('id'=>$id))->find();
		if(!$task){
			return $this->error(lang('task').lang('not_exist'));
		}

		if($status==0) $status_str = 'pause';
		if($status==3) $status_str = 'retry';
		if($status==1) $status_str = 'startup';


//		if($task['status']!=0  && $task['status']!=3)
//			return $this->error(lang('current_status_does_not_start_tasks'));
		if($task['status'] == $status){
			return $this->error(lang('task').lang('have').lang($status_str));
		}

		$license  = get_license();
		$module = $license['config_info']['module'];
		$mv_size = $license['config_info']['mv_size'];
		

		if(!in_array('move',$module)){
			return $this->error(lang('have_no_permission_add_task'));
		}
        $count = db('move')->where('status =0 or status =2')->count();
		if($mv_size <= $count){
			return $this->error(lang('add_count_over_permit'));
		}
		
		$task_source = json_decode($task['source'],true);
		$task_target = json_decode($task['target'],true);
		$task_config = json_decode($task['config'],true);


		$data['move_task_id'] = $id;
		$data['source_unipue_id'] = $task_source['unique_id'];
		$data['target_unipue_id'] = $task_target['unique_id'];
		$data['partition'] = $task_config['partition'];
		$data['disk_versus'] = $task_config['disk_versus'];
		
		$cdp_task_id = Move::add_cdp_task($task_source['id'],$task_config['partition']);
		$data['task_id'] = $cdp_task_id;
		//发送消息
		if(Move::send_status_cdp_message($status,$data)){
			return $this->success(lang('task').lang($status_str).lang('success'));
		}else{
			db('cdp_task')->where(array('id'=>$cdp_task_id))->delete();
			return $this->error(lang('task').lang($status_str).lang('fail'));
		}

	}

	public function async(){

		$id = input("move_id");
		$from = input('from')?:0;

		$move_info = Move::where(array('id'=>$id))->find();

		$source_device_info = json_decode($move_info['source'],true);

		$unique_id = $source_device_info['unique_id'];

		if(Move::send_async_message($unique_id)){
			if($from){
				return $this->success('同步成功!');
			}
			return json(array('code'=>1));
		}else{
			if($from){
				return $this->error('同步失败!');
			}
			return json(array('code'=>0));
		}


	}

}
