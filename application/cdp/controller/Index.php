<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\cdp\controller;
use app\cdp\model\Cdp;
use app\cdp\model\CdpConfig;
use app\cdp\model\CdpKeli;
use app\cdp\model\CdpSnap;
use app\cdp\model\CdpTask;
use app\common\controller\Admin;
use app\device\model\Device;
use app\device\model\ComputeVirtual;
use app\device\model\Libvirt;
use app\device\model\VirtualManager;
use app\cdp\model\Compute;
use app\cdp\behavior\Virtual;
use app\practise\model\PractiseModel;


class Index extends Admin {


	protected $cdp;

	public function _initialize() {

		parent::_initialize();
		$this->cdp = model('cdp');
		$this->device = model('device/device');

	}


	/**
	 * CDP列表
	 */
	public function index() {

        $map['status'] = array('eq',1);
        $map['attribute'] = array('eq',1);
        $uid = session('user_auth.uid');
        $device_ids = db('device_auth')->where(array('uid'=>$uid))->find();
        $device_ids = explode(',',$device_ids['device_id']);
        if ($uid!=1){
            $map['id'] = array('in',$device_ids);
        }



        $exclusivedevice = $this->device->getExclusiveDevice('cdp,sb',$map);

		$list = $this->cdp->getList();

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);

		$this->setMeta(lang('cdp_manage'));
		$this->assign('exclusivedevice',$exclusivedevice);
		$this->assign($data);

	    return $this->fetch();
	}


	/**
	 *  向cdp中添加设备
	 */
	public function add_device() {


		$devicedata = input('deviceinfo/a');

		if(!$devicedata){
			return $this->error(lang('please_select').lang('device'));	
		}

		if(!$this->cdp->add($devicedata)){
			Cdp::addCdpDeviceLog($devicedata,0,$this->cdp->getError());
			return $this->error($this->cdp->getError());
		}
			Cdp::addCdpDeviceLog($devicedata,1);
		return $this->success(lang('add').lang('success'));	
	}

	/**
	 *  删除设备
	 */
	public function del_device() {


		$id = input('id');
		$del_data = input('del_backup_data')?:0;

		if($del_data){
			if(!Device::check_can_del_device($id))
				return $this->error(lang('have_virtual_cannot_del'));
		}

		if(!$id){
			return $this->error(lang('parameter_error'));	
		}
		sleep(1);
		if(!$this->cdp->del($id,$del_data)){
			Cdp::delCdpDeviceLog($id,0,$this->cdp->getError());
			return $this->error($this->cdp->getError());
		}else{
			Cdp::delCdpDeviceLog($id,1);
			return $this->success(lang('delete').lang('success'));
		}
	}




	public function detail() {

		$id = input('id');
		$type = input('type');  //返回到哪个页面   3=>/device_detail 页面

		if(!$id){
			return $this->error(lang('parameter_error'));
		}
		$info = $this->device->getDeviceInfo($id);

		if(!$info){
			return $this->error(lang('device_does_not_exist'));
		}

		CdpTask::modify_cdp_type($id);

		if($info['hardware_info']){
			$hardware_info = json_decode($info['hardware_info'],true);
			if($hardware_info['macaddr']){
				$hardware_info['mac_desc'] = get_device_macdesc($hardware_info['macaddr']);
			}
			$this->assign('hardware_info',$hardware_info);
		}
		if($info['system_info']){
			$system_info = json_decode($info['system_info'],true);
			$this->assign('system_info',$system_info);
		}

		if($info['harddisk_info']){
			$harddisk_list = json_decode($info['harddisk_info'],true);
			foreach ($harddisk_list as $key => &$vo_h) {
				$vo_h['partition_number'] = count($vo_h['partitions']);
				if($vo_h['partition_number'] < 5 ){
					$vo_h['completion'] = 5-$vo_h['partition_number'];
				}
			}
			$harddisk_count = count($harddisk_list);
			if($harddisk_count > 3){
				$harddisk_class = '2';
			}elseif($harddisk_count == 3){
				$harddisk_class = '4';
			}elseif($harddisk_count == 2){
				$harddisk_class = '6';
			}elseif($harddisk_count == 1){
				$harddisk_class = '12';
			}

			$this->assign('harddisk_class',$harddisk_class);
			$this->assign('harddisk_list',$harddisk_list);
		}


		$auot_config = is_high_system()?1:0;  //高级版可配置自动化
		$is_liter = is_liter_system()?1:0;   //是否是轻量版

		$this->assign('auto_config',$auot_config);
		$this->assign('is_liter',$is_liter);

		$task_list_data = db('cdp_task')->where(array('device_id'=>$id))->order('id desc')->select();

		if($task_list_data){
			foreach ($task_list_data as $key => &$task) {

				if($task['type'] == "current" ){
					 $task['current'] = 1;
				 }else{
					 $task['current'] = 0;
				}
				if($task['type'] == 'current')
					$current_task = $task;
				$task_list[date('Y-m-d',$task['create_time'])][]=$task;//分组
			}
		}

		$device_info = db('device')->field(array('status'))->where(array('id'=>$id))->find();
		$device_status = $device_info['status'];


		$this->assign('device_status',$device_status);
		$this->assign('task_list',$task_list);
//		$current_task = $task_list_data[0];

		//判断任务是否都已完成
		if($current_task['work_partition']){
			$task_have_finished = $this->taskHaveFinished($current_task['work_partition']);
		}

		$this->assign('task_have_finished',$task_have_finished);
		$this->assign('current_task',$current_task);

		$this->assign('current_task_id',$current_task['id']);
		$this->setMeta(lang('task_manage').'('.$info['ip'].')');
		$this->assign('device_data',$info);

		//获取存储路径
		$storage = db('storage')->where(array('type'=>1,'status'=>1))->select();
		$storage_path_list = array();
		foreach($storage as $item){
			$path = db('storage_path')->where(array('storage_id'=>$item['id'],'status'=>1))->select();
			if(!$path) continue;
			$item['storage_path'] = $path;
			$storage_path_list[] = $item;
		}


//		$this->assign('device_log',$device_log);

		$this->assign('storage_path_list',$storage_path_list);
		if($type==3){
			$back_url = '/device/manage/device_detail/id/'.$id;
		}else{
			$back_url = '/cdp/index/index';
		}
		$this->assign('back_url',$back_url);
		$this->assign('device_id',$id);
		return  $this->fetch('detail');
	}


	//判断任务是否都已完成
	private function taskHaveFinished($work_partition){
		//获取每个分区的状态
		$work_partition = json_decode($work_partition,true);

		$partition = array();
		$hasFinishedFlag = 1;  //判断任务是否全部完成的标志
		foreach($work_partition as $k=>$v){

			$item['key'] = $v['key'];
			$item['status'] = $v['status'];
			$partition[] = $item;
			if ($v['status'] != 3) {
				$hasFinishedFlag = 0;
				break;
			}
		}
		return $hasFinishedFlag;
	}


	public function get_task_snap() {

		$id = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));	
		}
		$task_data = db('cdp_task')->where(array('id'=>$id))->find();

		$is_current_task = $task_data['type']=="current"?1:0;

		if($task_data && $task_data['work_partition']){
			$work_partition = json_decode($task_data['work_partition'],true);
			foreach ($work_partition as $key => &$vo) {
				
				$vo['totalsize'] = format_bytes($vo['totalsize']);
				
			}	
		}
		$sanp_list_data = db('cdp_snap')
				->where(array('task_id'=>$id,'virtual_id'=>0,'keli_id'=>0))
				->field('id,create_time,task_id')
				->group('group_id')
				->order('id asc')
				->select();
		$sanp_count = count($sanp_list_data);

		foreach ($sanp_list_data as $key => &$vo) {

			$time = date('Ymd',$vo['create_time']);
			if($key == 0 ){
				$first_time = date('Y-m-d',$vo['create_time']);
				$sanp_list[$time]['className']='label bg-light-blue';
				
			}elseif($key == ($sanp_count-1)){

				$sanp_list[$time]['className']='label bg-red';

			}else{

				$sanp_list[$time]['className']='label bg-green';

			}

			//统计每天快照数量

			if(!array_key_exists('count',$sanp_list[$time])){
				$sanp_list[$time]['count'] = 0;
			}
			$sanp_list[$time]['count']++;


			$sanp_list[$time]['title']=lang('snapshot')." ( ".$sanp_list[$time]['count']." ) ";
			$sanp_list[$time]['id']=date('Y-m-d',$vo['create_time']);
			$sanp_list[$time]['start']=date('Y-m-d',$vo['create_time']);
			$sanp_list[$time]['end']=date('Y-m-d',$vo['create_time']);
			$sanp_list[$time]['allday']=false;
			$sanp_list[$time]['task_id']=$vo['task_id'];
//			$sanp_list[date('Ymd',$vo['create_time'])]['data'][]=$vo;
		}

		if($sanp_list){
			$sanp_list = array_values($sanp_list);
		}

		$data['work_list'] = $work_partition;
		$have_fisnished = 1;
		foreach($work_partition as $key=>$v){
			if($v['status'] != 3){
				$have_fisnished = 0;
				break;
			}
		}

		$data['have_finished'] = $have_fisnished;
		$data['engine_status'] = $task_data['engine_status'];
		
		$data['sanp_list']['first_time'] = $first_time;
		$data['sanp_list']['data'] = $sanp_list;
		$data['is_current'] = $is_current_task;
		
		return json($data);
	}


	public function get_log(){

		$type = input('type');
		$id = input('id');
		if($type=="cdp_detail"){
			//日志信息
			$logs = get_log_list(array('record_id'=>$id,'model'=>'cdp'));
		}
		if($type=='cdp_index'){
			$logs = get_log_list(array('model'=>"cdp_device"));
		}
		$time = array();
		$data = array();
		if(!$logs){
			return json(array('code'=>0));
		}
		foreach($logs as $k=>$v) {
			$time[] = $k;
			$data[] = $v;
		}
		sleep(1);
		return json(array('code'=>1,'data'=>$data,'time'=>$time));

	}




	function snap_detail(){

		$task_id = input('task_id');
		$time = input('time');

		$snap_model = new CdpSnap();
		
		$cdp_task = CdpTask::get(function($query) use ($task_id){
			$query->where('id',$task_id)->field('device_id,snap_time');
		});
		if($cdp_task){
			$cdp_task = $cdp_task->toArray();
		}

		if(!$task_id){
			return $this->error(lang('parameter_error'));
		}

		$snap_list = $snap_model->get_snap($task_id,$time);

		/* 获取软件级别 */
		// 0->轻量版  1-> 基础版 2-> 高级版
		/*如果是轻量版的 则只有演练模式*/
		$soft_type = get_soft_type();

		$origin_can_del = $snap_model->snap_type3_count ? 1 : 0;

		$this->assign('snap_list',$snap_list);
		$this->assign('back_id',$cdp_task['device_id']);
		$this->setMeta(lang('snap_list'));

		//获取计算节点
		$computing_list = Compute::getComputeList();
		$this->assign('computing_list',$computing_list);

		$first_id = $computing_list[0]['id'];
		$compute_info = Compute::getComputeIfo($first_id);
		$compute_info['total_mem'] = intval($compute_info["total_mem"]/(512*1024)) * 512*1024;
		$this->assign('max_cpu',$compute_info['max_cpu']);
		$this->assign("total_mem",$compute_info['total_mem']);

		//获取设备信息
		$device_info = db('device')
				->where(array('id'=>$cdp_task['device_id']))
				->field(array('system_info'))->find();

		$hardware_info = json_decode($device_info['system_info'],true);

		$client_system_type = Device::get_system_type($cdp_task['device_id']);

        //获取前后日期
        $pre_next_date = CdpSnap::getNextAndPreDayTime($time);


		$this->assign('origin_can_del',$origin_can_del);
		$this->assign('soft_type',$soft_type);
		$this->assign('hardware_info',$hardware_info);
		$this->assign('client_system',$client_system_type);
		$this->assign('task_id',$task_id);
        $this->assign('current_time',$time);
        $this->assign('other_date',$pre_next_date);

		return $this->fetch();
	}



	/**
	 *   删除快照点
	 */
	public function snap_del(){


		$task_id = input('task_id');
		$sub_task_id = input('sub_task_id');
		$task_info = CdpTask::field('device_id,create_time')->find();
		if($task_info){
			$task_info = $task_info->toArray();
		}else{
			return $this->success(lang('delete').lang('fail'));
		}
		if(!$task_id || !$sub_task_id){
			return $this->error(lang('params').lang('error'));
		}

		$snap_model = new CdpSnap();
		if($snap_model->del($task_id,$sub_task_id)){
			CdpSnap::delSnapLog($task_info['device_id'],1,$task_info['create_time'],$snap_model->begin_time,$snap_model->end_time);
			return $this->success(lang('delete').lang('success'));
		}else{
			CdpSnap::delSnapLog($task_info['device_id'],0,$task_info['create_time'],$snap_model->begin_time,$snap_model->end_time);
			return $this->error(lang("del").lang('fail'));
		}

	}




	//处理compute数据
	private function handle_compute_list($list){

		foreach($list as $k=>&$v){
			$compute_info = json_decode($v['compute_info']);
			$v['max_cpu'] = $compute_info->max_cpu;
		}
		return $list;
	}




	// 获取机器的一些信息，如：最大内存，空闲内存等
	public function get_system_info(){

		$ip = input('ip');
		$url = get_libvirt_url($ip);
		$lv = new Libvirt($url);

		$mem_stat = $lv->get_mem_stats();

        echo json_encode($mem_stat);

	}

	/**
	 *  生成颗粒
	 */
    public function add_keli(){

		$group_id = input('snap_group_id');
		$keli_time = 'keli_time_'.$group_id;
		$data['group_id'] = $group_id;
		$data['task_id'] = input('task_id');
		$data['status'] = 0;
		$data['keli_time'] = input($keli_time);
		$data['create_time'] = time();
		$model = db('cdp_keli');
		$task_info = CdpTask::field('device_id,create_time')->where(array('id'=>$data['task_id']))->find();

		if($task_info){
			$task_info = $task_info->toArray();
		}else{
			return $this->success(lang('create').lang('fail'));
		}

		if($insert_id = $model->insert($data,false,true)){

			if($this->send_create_keli_message($insert_id)){
				CdpKeli::addKeliLog($task_info['device_id'],$task_info['create_time'],1);
				return $this->success(lang('create').lang('success'));
			}else{
				$model->where(array('id'=>$insert_id))->delete();
				CdpKeli::addKeliLog($task_info['device_id'],$task_info['create_time'],0,"失败原因:消息服务器连接失败");
				return $this->error(lang('send_message').lang('fail'));
			}
		}else{
			CdpKeli::addKeliLog($task_info['device_id'],$task_info['create_time'],0,"失败原因:数据库操作失败");
			return $this->error(lang('create').lang('fail'));
		}

	}

	/**
	 *    增加虚拟机
	 */
   public function add_virtual(){

	   if(IS_POST){

	   	   $task_id = input('task_id');
		   $group_id = input('snap_group_id');
		   $keli_id = input('keli_id');


		   if($group_id == -1 && $keli_id != -1){
			   $filtrate_key = 'keli_id';
			   $filtrate_value = $keli_id;
		   }else if($group_id != -1 && $keli_id == -1){
			    $filtrate_key = 'group_id';
			    $filtrate_value = $group_id;
		   }else{
			   return $this->error(lang('param_error'));
		   }

		   $snap = db('cdp_snap')
		   			->where(array($filtrate_key=>$filtrate_value,'virtual_id'=>0,'task_id'=>$task_id))
				    ->order('harddisk_id asc')
				    ->select();


 			// 原机在线不能创建虚拟机
		   $data['type'] = input('vir_type');
		   $data['device_id'] = input('device_id');
           if( 0 == $data['type']){
               if(Device::check_device_is_on($data['device_id'])){
                   return $this->error('原机在线,不能创建接管虚拟机!');
               }
           }

		   if(!\think\Hook::listen("before_create_virtual",$snap,'',true)){
			   return $this->error(lang('snap_merge_not_completed'));
		   }

		   $spath = CdpSnap::get_snap_filepath($snap);

		   $snap = CdpSnap::get_temp_vmdk_record($snap);

           $storage_id = $snap[0]['storage_id'];
		   //获取磁盘的路劲
		   foreach($snap as $k=>$v){
			   $data['vmdk_path'][] = $v['file_name'];
			   $data['snaps'][] = $v['id'];
		   }

		   if(!$snap)
			   return $this->error(lang('selected_disk_have_os_system'));


		   $data['name'] = input('host_name');
		   $data['cpu_kernel'] = input('cpu_kernel');
		   $data['memory'] = input('memory');
           $data['net_data'] = input('netdata/a');
		   $data['virtual_info'] = json_encode($data);

		   $data['task_id'] = $task_id;

		   $data['comput_id'] = input('comput_id');
		   //获取远程端口号
		   $data['remote_port'] = ComputeVirtual::get_remote_port($data['comput_id']);

		   //获取源设备信息
		   $device = db('device')->where(array('id'=>$data['device_id']))->find();


		   //2012 Datacenter
		   $system_info = json_decode($device['system_info'],true);

		   $data['is_sata'] = strpos($system_info['systemversion'],"2012")?1:0;

		   $data['source_device_ip'] = $device['ip'];
		   $data['source_device_name'] = $device['alias'];
           $data['disktype'] = json_decode($device['harddisk_info'],true)[0]['disktype']?:0;
		   $data['module'] = 'cdp';

		   $data['status'] = 0;
		   $data['snap_id'] = $task_id.'_'.$group_id;
		   $data['system_type'] = input('system_type')=='Windows'? 1 : 0;
		   $data['create_time'] = time();



            if(is_liter_system()){  // 如果是轻量版

				// 修改内存和cpu为计算池的一般
				$compute_info = Compute::getComputeIfo($data['comput_id']);

				$data['cpu_kernel'] = intval($compute_info['max_cpu']/2)?:1;
				$data['memory'] = intval($compute_info["total_mem"]/2)?:1024;

               if($insertId = $this->add_virtual_liter($data,$snap,$spath,$storage_id,$data['system_type'])){
				   ComputeVirtual::addVirtualLog($data['name'],$data['device_id'], $data['type'],1);
				   return  $this->success(lang('create').lang('success'),url("/device/manage/virtual_detail/id/$insertId"));

			      }else{
				   ComputeVirtual::addVirtualLog($data['name'],$data['device_id'],$data['type'],0,'失败原因:未知');
				   return  $this->error(lang('create') . lang('fail'));
			   }

            }else{

               if($insertId = $this->add_virtual_common($data,$snap,$spath,$storage_id,$group_id,$keli_id,$data['system_type'])){
				   ComputeVirtual::addVirtualLog($data['name'],$data['device_id'],$data['type'],1);
				   return $this->success(lang('create').lang('success'),url("/device/manage/virtual_detail/id/$insertId"));

			   }else{

				   ComputeVirtual::addVirtualLog($data['name'],$data['device_id'],0,$data['type'],'失败原因:未知');
				   return $this->error(lang('create') . lang('fail'));
			   }
            }

	   }
   }

    //轻量版新增虚拟机
   private function add_virtual_liter($data,$snap,$spath,$storage_id,$system_type){

       //先关闭所有虚拟机
       ComputeVirtual::stop_all_virtual($data['comput_id']);

       if(VirtualManager::libvirt_create_virtual($data))
       {
           if($insertId = db('compute_virtual')->insert($data,false,true)){
               //插入新的temp数据
               CdpSnap::insert_temp_snap($snap,$insertId);

               VirtualManager::send_create_virtual_messgae($spath,$storage_id,$system_type);

               return $insertId;
           }else {
               return false;
           }
       }
   }


    //普通版新增虚拟机
    private function add_virtual_common($data,$snap,$spath,$storage_id,$group_id,$keli_id,$system_type){

        if(VirtualManager::libvirt_create_virtual($data))
        {
            if($insertId = db('compute_virtual')->insert($data,false,true)){
                //插入新的temp数据
                CdpSnap::insert_temp_snap($snap,$insertId);
                if($group_id == -1 && $keli_id != -1){
                    $keli_data['virtual_id'] = $insertId;
                    $keli_data['status'] = 2;
                    db('cdp_keli')->where(array('id'=>$keli_id))->update($keli_data);
                }
                VirtualManager::send_create_virtual_messgae($spath,$storage_id,$system_type);
                return $insertId;
            }
            else {
                return false;
            }
        }
    }


		/**
		 * 获取task
		 */
	public function get_task(){

		$id = input('id');
		$task = db('cdp_task')->where(array('id'=>$id))->find();
		if($task){
			if($task['stop_time'])
			{
				$stop_time = explode('-',$task['stop_time']);
				$task['begin_time'] =$stop_time[0];
				$task['end_time'] = $stop_time[1];
			}
			$task = CdpTask::handle_time($task);
			return json($task);
		} else{
			return $this->error(lang('fail'));
		}

	}

		/**
		 *   增加网卡
		 */
	public function add_network(){

	}

		/**
		 *   编辑任务
		 */
	public function task_edit(){

		if(IS_POST){

			    $task_id = input('task_id');
                $snap_time=input('snap_time');
                $inter_type=input('snap_inter_type');
                if($snap_time && $inter_type){
                    $data['snap_time'] = CdpTask::conver_time($snap_time,$inter_type);
                }else{
                    return $this->error(lang('fill_snap_time'));
                }

                $synchr_time=input('synchr_time');
                $synchr_inter_type=input('synchr_interval_type');
                if($synchr_time&&$synchr_inter_type){
                    $data['synchr_time'] = CdpTask::conver_time($synchr_time,$synchr_inter_type);
                }else{
                    return $this->error(lang('fill_synchr_time'));
                }
                if($data['synchr_time'] > $data['snap_time'])
                    return $this->error(lang('snapshot_greater_synchronization'));

                if(!is_liter_system()){

                    $data['backup_speed'] = input('backup_speed')?input('backup_speed'):0;
                    $data['snap_num'] = input('snap_num');

                    if(!($data['snap_num'])){
                        return $this->error(lang('fill_snap_number'));
                    }
                    $sub_snap_num=input('sub_snap_num');
					if($sub_snap_num){
                        $data['sub_snap_num'] = $sub_snap_num;
                    }else{
                        return $this->error(lang('fill_sub_snap_num'));
                    }

                    $begin=input('begin');
                    $end=input('end');
                    // 停止区间验证
                    if(($begin&&$end) || (!$begin&&!$end)){
                        if($begin&&$end){
                            $data['stop_time'] = $begin."-".$end;
                        }
                    }else{
                        return $this->error(lang('Stop interval format is error'));
                    }
                }

				$cdp_task = db('cdp_task');
				$task = $cdp_task->field('create_time,device_id')->where(array('id'=>$task_id))->find();

			    $unique_id = $this->get_unipue_id($task['device_id']);

					//更新数据库
				$result= $cdp_task->where(array('id'=>$task_id))->update($data);
			    if($result !== false){
					CdpTask::send_task_message($unique_id,$task_id,2);
					//记录日志
					CdpTask::editCdpTaskLog($task['device_id'],1,$task['create_time']);
					return $this->success(lang('edit_task').lang('success'));

				}else{

					CdpTask::editCdpTaskLog($task['device_id'],0,$task['create_time'],'失败原因: 数据库操作失败');
					return $this->error(lang('edit_task').lang('fail'));
				}
		}
	}

	/**
	 * 启动 或 暂停任务
	 */
	public function task_status(){



		$id = input('id');

		$status = input('status');

		if(!$id||!isset($status)){
			return $this->error(lang('parameter_error'));
		}
		$task = db('cdp_task')->where(array('id'=>$id))->find();
		if(!$task){
			return $this->error(lang('task').lang('not_exist'));
		}

		//检查设备是否在线
		if(!Device::check_device_is_on($task['device_id'])){
			return $this->error(lang('device_not_on_action_forbid'));
		}

		 if($status==1) {$status_str = 'start_up';$action='start_cdp_task';}
      	 if($status==2) {$status_str = 'task_pause';$action='pause_cdp_task';}
		 if($status==3) {$status_str = 'stop';$action='stop_cdp_task';}


		$data['status'] = $status;

		$device_id = $task['device_id'];
		$unqiue_id = $this->get_unipue_id($device_id);
        $task_id = $id;

		//发送消息
		if($this->send_status_cdp_message($task_id,$unqiue_id,$data['status'])){
			if($status == 3){
				$updateField['type'] = "cdp";

				//终止任务的时候,如果有实时分发任务，则终止分发任务
				CdpTask::stop_remote_task($id);

				db('cdp_task')->where(array('id'=>$id))->update($updateField);
			}
			CdpTask::statusCdpTaskLog($device_id,$action,1,$task['create_time']);
		    return $this->success(lang('task').lang($status_str).lang('success'));

		}else{

			CdpTask::statusCdpTaskLog($device_id,$action,0,$task['create_time'],'失败原因: 消息服务器连接错误');
			return $this->error(lang('task').lang($status_str).lang('fail'));
		}

	}



	private function send_create_keli_message($keli_id){

		$data['nId'] = $keli_id;
		$result = \Netmessage::send_message('W2P_Create_Small_Snap',$data);
		if($result['code'] == 1){
			return true;
		}else{
			return false;
		}

	}


	//发送消息
	private function send_status_cdp_message($task_id,$unique_id,$status){

//		w2p_pause_cdp 暂停
//		w2p_stop_cdp  终止
//		w2p_start_cdp 开启
		if ($status == 1)
			$message_str = 'w2p_start_cdp';
		else if($status == 2)
        	$message_str = 'w2p_pause_cdp';
		else if($status == 3)
			$message_str = 'w2p_stop_cdp';

		$data['deviceuniqueid'] =$unique_id;
		$data['cdpTaskId'] = $task_id;
		$result = \Netmessage::send_message($message_str, $data);

		if ($result['code'] == 1) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 *  启动任务后不断请求备份信息
	 */
	public function get_clone_state(){


		// 如果任务的状态为开启 ，则不断请求备份信息
		$task_id = input('id');
		$task_info = db('cdp_task')->field(array('status','device_id','engine_status','work_partition'))->where(array('id'=>$task_id))->find();
		$data = array();

        //获取每个分区的状态
		$work_partition = json_decode($task_info['work_partition'],true);

		$partition = array();
		$hasFinishedFlag = 1;  //判断任务是否全部完成的标志
		$hasPauseFlag = 0; 		//判断任务是否有暂停
        $hasnoRunningFlag = 0;   // 判断是否有正在进行中的任务

		foreach($work_partition as $k=>$v){
		
			$item['key'] = $v['key'];
			$item['status'] = $v['status'];
			$partition[] = $item;
			if ($v['status'] != 3) {
				$hasFinishedFlag = 0;
			}

			//如果有任务暂停
			if($v['status'] == 2){
				$hasPauseFlag = 1;
			}

			if($v['status'] == 1){
				$hasnoRunningFlag = 1;
			}

		}

		//判断任务clone是否全部完成
		if ($hasFinishedFlag) {
			echo json_encode(array('code'=>3,'message'=>lang('clone_has_finished'),'engine_status'=>$task_info['engine_status'],'partition_status'=>$partition));
			exit();
		}
		//没有正在进行中的任务，并且任务都暂停了
		if ($hasnoRunningFlag && $hasPauseFlag) {
			echo json_encode(array('code'=>4,'message'=>lang('clone_has_pause'),'engine_status'=>$task_info['engine_status'],'partition_status'=>$partition));
			exit();
		}

		if($task_info['status'] != 1){
			echo json_encode(array('code'=>0,'message'=>lang('task_not_run'),'engine_status'=>$task_info['engine_status'],'partition_status'=>$partition));
			exit();
		}

		if($task_info['engine_status'] != 1){
			echo json_encode(array('code'=>9,'message'=>lang('engine_not_run'),'engine_status'=>$task_info['engine_status']));
			exit();
		}
        

		//发消息 请求数据
		$unique_id = $this->get_unipue_id($task_info['device_id']);
		$data['deviceuniqueid'] =$unique_id;

//        $result['code'] = 1;
//        $result['progress'] = 100;
		$result = \Netmessage::send_message('w2p_update_cloning_info', $data);
		$result['engine_status'] = $task_info['engine_status'];
		$result['partition_status'] = $partition;
		echo json_encode($result);

	}



	/**
	 *   增加任务
	 */
	public function task_add(){

		if(IS_POST){


			if(!CdpTask::check_can_add_task(input('device_id'))){
				return $this->error(lang('moving_is_running').lang("can_not_add_task"));
			}

			$check_storage = self::checkStorageNetCapacity(true);
			if($check_storage['storage_warning_code']==0){
			 	if($check_storage['used_percent']>0.99)
				$msgStr = '存储容量不足,请先增加存储!';
				return $this->error($msgStr);
			}

			if(input('device_status') != 1)
				return $this->error(lang('device_is_not_running'));

			$data['harddisk_info'] = input('harddisk_info');
			$data['storage_id'] = input('storage_id');
			if(!$data['storage_id']){
				return $this->error(lang('no_storage_available'));
			}

			$snap_time = input('snap_time');
			$inter_type = input('snap_inter_type');
			if ($snap_time && $inter_type){
				$data['snap_time'] = CdpTask::conver_time($snap_time, $inter_type);
			}else{
				return $this->error(lang('fill_snap_time'));
			}

			$synchr_time = input('synchr_time');
			$synchr_inter_type = input('synchr_interval_type');
			if ($synchr_time && $synchr_inter_type){
				$data['synchr_time'] = CdpTask::conver_time($synchr_time, $synchr_inter_type);
			}else{
				return $this->error(lang('fill_synchr_time'));
			}

			if ($data['synchr_time'] > $data['snap_time'])
				return $this->error(lang('snapshot_greater_synchronization'));

			if($info = input('partition/a')) {
					$data['work_partition'] = CdpTask::handle_disk_part_info($info);
				}else{
					return $this->error(lang('choose_disk_and_partition'));
				}


			if(is_liter_system()){
				//如果是liter版本
				$default_config = array("backup_speed" => 0 ,"snap_num" => 64 , "sub_snap_num" => 3);
				$data = array_merge($default_config,$data);

			}else {

				// 如果不是liter版本
				$data['harddisk_info'] = input('harddisk_info');
				$data['backup_speed'] = input('backup_speed') ? input('backup_speed') : 0;
				$data['snap_num'] = input('snap_num');

				if(empty($data['snap_num']))
					return $this->error(lang('Please fill in snap num'));

				$sub_snap_num=input('sub_snap_num');
				if($sub_snap_num)
					$data['sub_snap_num'] = $sub_snap_num;
			    else
					return $this->error(lang('fill_sub_snap_num'));


				$begin = input('begin');
				$end = input('end');
				// 停止区间验证
				if (($begin && $end) || (!$begin && !$end)) {
					if ($begin && $end) {
						//TODO::停止区间验证
						$data['stop_time'] = $begin . "-" . $end;
					}
				} else {
					return $this->error(lang('stop_interval_format_error'));
				}
			}
			$data['status'] = 0;
			$data['create_time'] = time();
			$data['device_id'] = input('device_id');
			$unique_id = $this->get_unipue_id($data['device_id']);
			$data['type'] = "current";
			$task_model = db('cdp_task');

			$task_info = CdpTask::field('id')->where(array('device_id'=>$data['device_id'],'type'=>'current'))->find();
			$old_task_id = $task_info->id;

			//插入数据库
			if($task_id = $task_model->insert($data,false,true)){

//				//设置是否是实时的
//				CdpTask::new_task_set_snappoint($task_id,$data['device_id']);

				//插入成功发送消息
				if(CdpTask::send_task_message($unique_id,$task_id,1)){

					//更改原始的cdp任务的状态
					CdpTask::update_type($old_task_id);

					//记录日志
					CdpTask::addCdpTaskLog($data['device_id'],1);

					return $this->success(lang('add_task').lang('success'));

				}else{

					CdpTask::addCdpTaskLog($data['device_id'],0,'失败原因:消息服务器连接失败');
					//消息发送失败，则删除新创建的数据
					$task_model->where(array('id'=>$task_id))->delete();
					return $this->error(lang('send_message').lang('fail'));
				}
			}else{ //插入数据失败的时候

				CdpTask::addCdpTaskLog($data['device_id'],0,'失败原因:数据库操作失败');
				return $this->error(lang('add_task').lang('fail'));

			}
		}
	}



	//获取 unique_id
	private function get_unipue_id($id){

		$device_info = db('device')->field(array('unique_id'))->where(array('id'=>$id))->find();
		$unique_id = $device_info['unique_id'];
		return $unique_id;
	}


   //验证停止区间
	private function verify_stop_time($begin,$end){

//		$begin_hour = explode(':',$begin)[0];
//		$begin_min = explode(':',$begin)[1];
//
//		$end_hour = explode(':',$end)[0];
//		$end_min = explode(':',$end)[1];
	}


	/**
	 * 删除历史任务
	 */
	public function del_task(){

		$id = input('id');
		if(!$id)
			return $this->error(lang('parameter_error'));
		$task = db('cdp_task')->where(array('id'=>$id))->find();

		if(!$task)
			return $this->error(lang('task').lang('not_exist'));

		if(!CdpTask::check_can_del($id)){
			return $this->error(lang("have_virtual").lang('cannot_del'));
		}

		if(CdpTask::send_delete_task_message($id)){

			if(db('cdp_task')->where(array('id'=>$id))->delete()){

				//删除快照点
				CdpSnap::destroy(['task_id'=>$id]);

				CdpTask::delCdpTaskLog($task['device_id'],1,$task['create_time']);
				return $this->success(lang('delete').lang('success'));

			} else{

				CdpTask::delCdpTaskLog($task['device_id'],0,$task['create_time'],'失败原因:数据库操作失败');
				return $this->error(lang('delete').lang('fail'));
			}
		}else{

			CdpTask::delCdpTaskLog($task['device_id'],0,$task['create_time'],'失败原因:消息服务器连接错误');
			return $this->error(lang('send_message').lang('fail'));
		}
	}

	/**
	 *  创建快照
	 */
	public function create_snap(){


		$id = input('id');

		$task = CdpTask::field('create_time,device_id')->where(array('id'=>$id))->find();

		if(!CdpTask::task_is_finished($id)){
			return $this->error(lang("cannot_create_snap"));
		}
		//检查设备是否在线
		if(!Device::check_device_is_on($task['device_id'])){
			return $this->error(lang('device_not_on_action_forbid'));
		}

		if($task){
			$task = $task->toArray();
		}else{
			return $this->error(lang('task').lang('not_exist'));
		}

		$extend['task_time'] = $task['create_time'];


		if($this->send_message($id,1)){

			CdpSnap::createSnapLog($task['device_id'],1);

			return $this->success(lang('creating_snap'));
		}else{

			CdpSnap::createSnapLog($task['device_id'],0,'失败原因:消息服务器连接错误');
			return $this->error(lang('create_snap').lang('fail'));
		}
	}

	/**
	 * 合并快照
	 */
	public function merge_snap(){

		$id = input('id');
		$this->send_message($id,2);

	}


	// 创建、合并快照 、 删除历史任务时发送的消息
	private  function send_message($id,$type){

		if($type == 1){
			$message_str = 'w2p_create_snap';

		}
		if($type == 2){
			$message_str = 'w2p_merge_snap';
		}
        if($type == 3){
			$message_str = 'w2p_delete_cdp';
		}


		$task_info  = db('cdp_task')->field('device_id')->where(array('id'=>$id))->find();
		$device_id = $task_info['device_id'];
		$unique_id = $this->get_unipue_id($device_id);

		$data['deviceuniqueid'] = $unique_id;
		$result = \Netmessage::send_message($message_str,$data);

		if ($result['code'] == 1)
			  return true;
		else
			 return false;

	}


	/**
	 * @return 自动化配置
	 */
	public function auto_conf(){

		$compute_model = db('compute');

		$device_id = input('device_id');

		$auto_config = db('cdp_config')->where(array('device_id'=>$device_id))->select();

		$compute = $compute_model->field(array('id','name','ip'))->select();

		$virtual_model = db('compute_virtual');

		$client_system_type = Device::get_system_type($device_id);

		$best_compute_count = 999;
		foreach($compute as $k=>$v){

			$count = $virtual_model->where(array('comput_id'=>$v['id']))->count();
			if($best_compute_count > $count){
				$best_compute_count = $count;
				$best_compute_id = $v['id'];
			}
		}

		$take_over_config['have_config'] = 0;
		$exercise_config['have_config'] = 0;
		if(count($auto_config)>0){
			$device_model = db('device');
			foreach ($auto_config as $k=>$v) {
				$device_info = $device_model->where(array('id'=>$v['device_id']))->field(array('alias','ip'))->find();
				$compute_info = $compute_model->where(array('id'=>$v['compute_id']))->field(array('name','ip'))->find();
				$v['network'] = count(json_decode($v['net_data'],true));
				$v['system_type_alias'] = $v['system_type']==0?'Linux':'Windows';
				$v['device_ip'] = $device_info['ip'];
				$v['device_name'] = $device_info['alias'];
				$v['compute_ip'] = $compute_info['ip'];
				$v['compute_name'] = $compute_info['name'];
				$v['day_alias'] = $this->num2char($v['day']);
				$v['type_alias'] = $v['type']==0 ? lang('take_over') : lang('exercise');
				$v['netdata'] = json_decode($v['net_data'],true);
				$v['accurate_time_alias'] = $this->switch_time($v['accurate_time']);
				if($v['type']==0){
					$take_over_config['have_config'] = 1;
					$take_over_config['config'] = $v;
				}
				if($v['type']==1){
					$exercise_config['have_config'] =1;
					$exercise_config['config']=$v;
				}
			}
		}

		$this->assign('client_system',$client_system_type);
		$this->assign('best_id',$best_compute_id);
		$this->assign('computing_list',$compute);
		$this->assign('device_id',$device_id);

		$this->assign('take_over',$take_over_config);
		$this->assign('exercise',$exercise_config);
		$this->setMeta(lang('auto_configure'));

		return $this->fetch();
	}

	private function  num2char($num){

		$chars = array('一','二','三','四','五','六','日');
		return $chars[$num-1];
	}

	/**
	 *  添加配置信息
	 */
	public function add_auto_config(){


		$data['device_id'] = input('device_id');
		$data['compute_id'] = input('compute_id');
		$data['day'] = input('week/a')?:array();
		$data['accurate_time'] = input('time')?:'';
		$data['type'] = input('type');
		$net_data = input('netdata/a')?:array();
		$data['net_data'] = json_encode($net_data);
		$data['cpu'] = input('cpu_kernel');
		$data['memory'] = input('memory');
		$data['system_type'] = input('system_type');
		$data['status'] = input('status');
		$data['username'] = input('username');
		$data['password'] = input('password');
		$data['create_time'] = time();


		$first_char = strtolower(substr($data['system_type'],0,1));
		if($first_char=='w'){
			$data['system_type'] = 1;
		}else{
			$data['system_type'] = 0;
		}

		$model = db('cdp_config');
		$count = $model->where(array('device_id'=>$data['device_id'],'type'=>$data['type']))->count();
		$device_info = db('device')->where(array('id'=>$data['device_id']))->field('ip')->find();

		$type = $data['type']==0?'接管':'演练';
		if($count>0){
			return $this->error(lang($type).lang('have_configured'));
		}

		if(count($data['day'])>0){
			$data['day'] = implode(',',$data['day']);
		}else{
			return $this->error(lang('choose_day'));
		}


		if($model->insert($data)){

			//如果是演练，怎发送消息
			if( $data['type'] == 1){
				/**
				 *  const ISSTRATEGYUSED = 2;
					const VHOSTSOURCEIP = 4;
					const WEEKDAY = 5;
					const TIME = 6;
				 *  const ISNEWSTRATEGY = 3;
				 */
				PractiseModel::send_create_message($data['status'],$device_info['ip'],$data['day'],$data['accurate_time'],1);
			}

			CdpConfig::addConfigLog($data['device_id'],1,$data['type'],$device_info['ip']);
			return $this->success(lang('add').lang('success'));
		}else{

			CdpConfig::addConfigLog($data['device_id'],1,$data['type'],$device_info['ip'],"失败原因:数据库操作失败");
			return $this->error(lang('add').lang('fail'));
		}

	}

	public function edit_auto_config(){


		$id = input('config_id');
		$data['compute_id'] = input('compute_id');
		$data['day'] = input('week/a')?:array();
		$data['accurate_time'] = input('time')?:'';
		$net_data = input('netdata/a');
		$data['net_data'] = json_encode($net_data);
		$data['cpu'] = input('cpu_kernel');
		$data['memory'] = input('memory');
		$data['status'] = input('status');
		$data['username'] = input('username');
		$data['password'] = input('password');
		$data['create_time'] = time();

		$model = db('cdp_config');
		$config = $model->field('type,device_id')->where(array('id'=>$id))->find();
		$device_info = db('device')->where(array('id'=>$config['device_id']))->field('ip')->find();

		if(count($data['day'])>0){
			$data['day'] = implode(',',$data['day']);
		}else{
			return $this->error(lang('choose_day'));
		}

		if($model->where(array('id'=>$id))->update($data)){

			if( $config['type'] == 1){

				PractiseModel::send_create_message($data['status'],$device_info['ip'],$data['day'],$data['accurate_time'],1);
			}

			CdpConfig::editConfigLog($config['device_id'],1,$config['type'],$device_info['ip']);
			return $this->success(lang('edit').lang('success'));

		}else{

			CdpConfig::editConfigLog($config['device_id'],0,$config['type'],$device_info['ip'],"失败原因:数据库操作失败");
			return $this->error(lang('edit').lang('fail'));
		}
	}

	/**
	 *  将时间转换为24小时制
	 */
	private function switch_time($time){

		$hour = sprintf("%02d",(int)($time/3600));
		$min = sprintf("%02d",0);
		return $hour . ' : ' . $min;
	}



}
