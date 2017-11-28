<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\device\controller;
use app\cdp\model\Cdp;
use app\cdp\model\Compute;
use app\common\controller\Admin;
use app\device\model\ComputeVirtual;
use app\device\model\Device;
use app\device\model\Libvirt;
use app\device\model\Storage;
use app\device\model\XmlMannager;
use app\device\model\VirtualManager;
use app\remote\model\Remote;
use app\remote\model\RemoteSnap;
use phpDocumentor\Reflection\File;
use app\cdp\model\CdpSnap;
use think\Log;


class Manage extends Admin
{
	protected $device;
	protected $cdp_snap;


	public function _initialize() {
		parent::_initialize();
		$this->device = model('Device');
		$this->device_group = model('DeviceGroup');
		$this->storage = model('Storage');
	}


	/**
	 * 设备列表
	 */
	public function device($group_id = 1) {


		$device_group = db('device_group')->where('')->select();
		foreach ($device_group as &$g_v) {
			if($g_v['group_id'] == $group_id ){
				$g_v['tabs'] = 'active';
			}
		}
		$this->assign('device_group',$device_group);
		$this->setMeta(lang('device_manage'));
		return $this->fetch();
	}



	/**
	 * 设备信息
	 */
	public function device_detail() {

		if (IS_POST) {
			$id  = input('id');


			if(!$id){
				return $this->error(lang('parameter_error'));
			}
			$info = db('device_monitor')->where(array('device_id'=>$id))->find();
			if(!$info['status']){
				return $this->error(lang('set_monitor_configuration'));
			}
			$snmp = new \Snmpwrapper($info['ip'],$info['snmp_name']);
			$s_type  = input('type','all');
			$s_course_type_field  = input('course_type','index');
			$s_course_type_sort  = input('course_sort','asc');
			$s_net_type_field  = input('net_type','index');
			$s_net_type_sort  = input('net_sort','asc');
			$old_system_uptime  = input('system_uptime','');


			if($s_type!='all'){
				$s_type = explode(",",$s_type);
			}
			//$system_snmp = $snmp->getwalkoid('1.3.6.1.2.1.25.4.2.1.4');//进程所在的路径表头
			//$system_snmp = $snmp->getwalk('1.3.6.1.2.1.25.4.2.1.6');//进程的类型表头
			//$system_snmp = $snmp->getwalk('1.3.6.1.2.1.25.4.2.1.1');//进程占CPU的大小表头
			//$system_snmp = $snmp->getwalk('1.3.6.1.2.1.25.5.1.1.2');//进程占内存的大小表头


			/*system*/
			$system_data = $snmp->get_system();//系统内容;
			if($system_data){
				$monitor_data['system'] = $system_data;
				$system_type = $system_data['systype'];
				$system_uptime = $system_data['uptime'];
				$monitor_data['system_uptime'] = $system_uptime;
				if(!$old_system_uptime){
					$old_system_uptime = $system_uptime;
				}
			}

			if($s_type == 'all' || in_array('storage',$s_type)){
				/*storage*/
				$storage_data = $snmp->get_win_storage();
				if($storage_data){
					$monitor_data['storage'] = $storage_data;
				}
			}

			if($s_type == 'all' || in_array('memory',$s_type)){
				/*memory*/
				$memory_data = $snmp->get_win_memory();

				if($memory_data){
					$monitor_data['memory'] = $memory_data;
				}
			}

			if($s_type == 'all' || in_array('cpu',$s_type)){
				/*cpu*/
				$cup_data = $snmp->get_win_cpu();
				if($cup_data){
					$monitor_data['cpu'] = $cup_data['cpu_record'];
					$monitor_data['cpu_all'] = $cup_data['cpu_usage'];
				}
			}

			if($s_type == 'all' || in_array('course',$s_type)){
				/*ip*/
				$new_course_data = $snmp->get_win_course();
				if($new_course_data){
					$course_data = format_course($new_course_data,$system_uptime,$old_system_uptime);
					array_values($course_data);
					$course_data = list_sort_by($course_data,$s_course_type_field,$s_course_type_sort);
					$monitor_data['course'] = $course_data;
				}
			}
			if($s_type == 'all' || in_array('net',$s_type)){
				/*net*/
				$net_data = $snmp->get_win_net();
				if($net_data){
					array_values($net_data);
					$net_data = list_sort_by($net_data,$s_net_type_field,$s_net_type_sort);
					$monitor_data['net'] = $net_data;
				}
			}

			return json($monitor_data);
		}else{
			$id  = input('id');
			if(!$id){
				return $this->error(lang('parameter_error'));
			}
			$info = db('device')->where(array('id'=>$id))->find();
			if(!$info){
				return $this->error(lang('device_does_not_exist'));
			}


			/*$hardware_info['ip'] = $info['ip'];
			$hardware_info['mac'] = $info['mac'];
			$hardware_info['cpu'] = $info['cpu'];
			$hardware_info['memory'] = $info['memory'];

			$system_info['sysname'] = $info['client_computer_name'];
			$system_info['systype'] = $info['client_systype'];
			$system_info['sysversion'] = $info['client_sysversion'];
			$system_info['sysuser'] = $info['client_user'];
			$system_info['sysworkgroup'] = $info['client_workgroup'];

			$software_info[0]['softname'] = 'DR';
			$software_info[0]['softversion'] = $info['client_version'];
			$software_info[0]['softsteupdate'] = $info['client_steup_date_time'];
			$software_info[0]['softstatus'] = 1;

			$software_info[1]['softname'] = 'CB';
			$software_info[1]['softversion'] = $info['client_version'];
			$software_info[1]['softsteupdate'] = $info['client_steup_date_time'];
			$software_info[1]['softstatus'] = 0;


			$software_info[2]['softname'] = 'MV';
			$software_info[2]['softversion'] = $info['client_version'];
			$software_info[2]['softsteupdate'] = $info['client_steup_date_time'];
			$software_info[2]['softstatus'] = 0;


			$s_data['hardware_info'] = json_encode($hardware_info);
			$s_data['software_info'] = json_encode($software_info);
			$s_data['system_info'] = json_encode($system_info);


			$res = db('device')->where(array('id'=>$id))->update($s_data);
			*/


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
			if($info['software_info']){
				$software_info = json_decode($info['software_info'],true);

				$software_count = count($software_info);

				if($software_count > 3){
					$software_class = '2';
				}elseif($software_count == 3){
					$software_class = '4';
				}elseif($software_count == 2){
					$software_class = '6';
				}elseif($software_count == 1){
					$software_class = '12';
				}

				$this->assign('software_class',$software_class);
				$this->assign('software_count',$software_count);
				$this->assign('software_info',$software_info);
			}

			if($info['harddisk_info']){
				$harddisk_list = json_decode($info['harddisk_info'],true);
				$this->assign('harddisk_list',$harddisk_list);
			}


			$device_monitor = db('device_monitor')->where(array('device_id'=>$id))->find();
			if($device_monitor['snmp_config']){
				$snmp_type = explode(",",$device_monitor['snmp_config']);
				$is_memory =  in_array('memory',$snmp_type);
				$is_cpu =  in_array('cpu',$snmp_type);
				$is_storage =  in_array('storage',$snmp_type);
				$is_course =  in_array('course',$snmp_type);
				$is_net =  in_array('net',$snmp_type);
				$info['is_memory'] = $is_memory;
				$info['is_cpu'] = $is_cpu;
				$info['is_storage'] = $is_storage;
				$info['is_course'] = $is_course;
				$info['is_net'] = $is_net;
				if($is_memory && $is_cpu){
					$info['snmp_memory_class'] = 'col-lg-4';
					$info['snmp_cpu_class'] = 'col-lg-8';
				}elseif($is_cpu){
					$info['snmp_cpu_class'] = 'col-lg-12';
				}elseif($is_memory){
					$info['snmp_memory_class'] = 'col-lg-12';
				}
			}
			$this->assign('device_monitor',$device_monitor);
			
			$device_log = get_log_list(array('record_id'=>$info['id'],'model'=>'device'));
			$this->assign('device_log',$device_log);

			//检查是否在CDP模块

			$have_in_cdp = Cdp::get(['device_id'=>$id])?1:0;


			$this->assign('have_in_cdp',$have_in_cdp);
			$this->assign('device_data',$info);
			$this->setMeta(lang('device').lang('details'));
			return $this->fetch();

		}
	}

	/**
	 * json设备列表
	 */
	public function get_device_lists($group_id = 1) {

		$map['group_id'] = $group_id;
		$map['status'] = array('lt',3);

        $uid = session('user_auth.uid');
        $device_ids = db('device_auth')->where(array('uid'=>$uid))->find();
        $device_ids = explode(',',$device_ids['device_id']);
        $map['id'] = array('in',$device_ids);

		$list = db('device')->where($map)->order('id desc')->select();

		foreach ($list as &$v) {
			$v['url'] = url('device/manage/device_detail',array('id'=>$v['id']));
			$v['system_type'] = Device::get_system_type($v['id']);
			if($v['attribute'] == 1){
				$v['attribute_name'] = lang('protected_device');
			}elseif($v['attribute'] == 2){
				$v['attribute_name'] = lang('move_device');
			}
		}
		return  json($list);
	}

	/**
	 * 获取设备数据
	 */
	public function get_device() {
		$id  = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));
		}
		$map['id'] = $id;
		$data = db('device')->where($map)->find();
		return  json($data);
	}


	/**
	 * 获取设备数据
	 */
	public function get_device_monitor() {
		$device_id  = input('deviceid');
		if(!$device_id){
			return $this->error(lang('parameter_error'));
		}
		$map['device_id'] = $device_id;
		$data = db('device_monitor')->where($map)->find();

		if($data){
			if($data['snmp_config']){
				$data['snmp_config'] = explode(",",$data['snmp_config']);
			}
		}else{
			$maps['id'] = $device_id;
			$device_data = db('device')->where($maps)->find();
			if($device_data){
				$data['id'] = $device_data['id'];
				$data['ip'] = $device_data['ip'];
			}else{
				return $this->error(lang('parameter_error'));
			}
		}
		return $this->success('','',$data);
	}




	/**
	 * 添加设备
	 */
	public function device_add() {
		$data  = input();
		if(!$data['device_ip']){
			return $this->error(lang('ip').lang('cannot_be_empty'));
		}

		$check_data = db('device')->where(array('ip'=>$data['device_ip']))->find();
		if($check_data){
			return $this->error(lang('current_ip_devices_already_exist'));
		}

		$info['ip'] = $data['device_ip'];
		$info['alias'] = $data['device_alias'];
		$info['group_id'] = $data['device_group'];
//		$info['auto_template_memory'] = $data['auto_template_memory'];
//		$info['auto_template_cpu'] = $data['auto_template_cpu'];
		$info['status'] = 0;

		$res = db('device')->insert($info);
		if($res){
			return $this->success(lang('add').lang('success'));
		}else{
			return $this->error(lang('add').lang('fail'));
		}
	}
	/**
	 * 修改设备
	 */
	public function device_edit() {
		$data  = input();
		if(!$data['id']){
			return $this->error(lang('parameter_error'));
		}
		$info['alias'] = $data['device_alias'];
		$info['group_id'] = $data['device_group'];
		$info['auto_template_memory'] = $data['auto_template_memory'];
		$info['auto_template_cpu'] = $data['auto_template_cpu'];
		$map['id'] = $data['id'];
		$res = db('device')->where($map)->update($info);

		if($res){
			//记录行为
			$action_data = db('device')->where(array('id'=>$data['id']))->find();
			$extended_data['device_ip'] = $action_data['ip'];
			$extended_data['device_id'] = $action_data['id'];
			action_log('edit_device', 'device', $data['id'], session('user_auth.uid'),$extended_data);	
			return $this->success(lang('edit').lang('success'));
		}else{
			return $this->error(lang('edit').lang('fail'));
		}
	}
	/**
	 * 修改设备
	 */
	public function device_del() {
		$id  = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));
		}
		$check_data = db('device')->where(array('id'=>$id))->find();
		if(!$check_data){
			return $this->error(lang('device_does_not_exist'));
		}
		if($check_data['status'] == 1){
			return $this->error(lang('device_operation_delete_prohibited'));
		}
		$res = db('device')->where(array('id'=>$check_data['id']))->delete();
		if($res){
			return $this->success(lang('delete').lang('success'));
		}else{
			return $this->error(lang('delete').lang('fail'));
		}
	}

	public function get_server_info($host, $community, $objectid) {
		$a = snmpget($host, $community, $objectid);
		$tmp = explode(":", $a);
		if (count($tmp) > 1) {
		$a = trim($tmp[1]);
		}
		return $a;
	}
	/**
	 * 修改监控配置
	 */
	public function snmp_edit() {


		$id  = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));
		}

		$snmp_name = input('snmp_name');
		$snmp_port = input('snmp_port');
		$snmp_ip = input('snmp_ip');
		$snmp_status = input('snmp_status');
		$snmp_config = input('snmp_config/a');

		if(!$snmp_name){
			return $this->error(lang('community_password').lang('cannot_be_empty'));
		}
		if(!$snmp_port){
			return $this->error(lang('port').lang('cannot_be_empty'));
		}
		if(!$snmp_ip){
			return $this->error(lang('ip').lang('cannot_be_empty'));
		}
		if($snmp_config){
			$snmp_config = implode(",",$snmp_config);
		}

		$info['snmp_name'] = $snmp_name;
		$info['snmp_port'] = $snmp_port;
		$info['snmp_config'] = $snmp_config;
		$info['ip'] = $snmp_ip;
		$info['device_id'] = $id;
		$info['status'] = $snmp_status;

		$check_data = db('device_monitor')->where(array('device_id'=>$id))->find();

		if($check_data){
			$map['device_id'] = $id;
			$res = db('device_monitor')->where($map)->update($info);
		}else{
			$res = db('device_monitor')->insert($info);
		}
		if($res){
			return $this->success(lang('edit').lang('success'));
		}else{
			return $this->error(lang('edit').lang('fail'));
		}
	}



	/**
	 * 获取设备数据
	 */
	public function test_link() {
		$data  = input();
		if(!$data['host_ip']){
			return $this->error(lang('name').lang('cannot_be_empty'));
		}
		if(!$data['host_port']){
			return $this->error(lang('port').lang('cannot_be_empty'));
		}
		if(!$data['host_name']){
			return $this->error(lang('username').lang('cannot_be_empty'));
		}

		$info['host'] = $data['host_ip'];
		$info['port'] = $data['host_port'];
		$info['uname'] = $data['host_name'];
		$info['password'] = $data['host_password'];
		$ssh = new \Ssh($info);//实例化对象
		if($ssh->code == 1){
			if($data['install'] == 1){

				/*
				$data = $ssh->cmdlong('uname');
				if($data['error']){
					$data['error'] = $this->format_error($data['error']);
				}
				if($data['output']){
					$data['output'] = $this->format_table($data['output']);
				}*/

				$data['output'] = '功能开发中...';
				return json($data);

			}else{
				$data['code'] = 1;
				$data['msg'] = '【'.$info['host'].'】'.lang('connect_success');
				return json($data);
			}
		}else{
			return $this->error($ssh->get_error());
		}
	}


	/**
	 * 分组列表
	 */
	public function device_group() {
		$order = "group_id asc";
		$list  =$this->device_group->where('')->order($order)->paginate(15);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('device_group'));
		return $this->fetch();
	}
	/**
	 * 分组增加
	 */
	public function group_add() {
		if (IS_POST) {
			$data = $this->request->post();
			if(!$data['group_name']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			$info['group_name'] = $data['group_name'];

			$reuslt = db('device_group')->insert($info);
			if (false !== $reuslt) {
				return $this->success(lang('add').lang('success'),url('manage/device_group'));
			} else {
				return $this->error(lang('edit').lang('fail'), '');
			}
		} else {
			$this->setMeta(lang('add').lang('grouping'));
			return $this->fetch('group_edit');
		}
	}
	/**
	 * 分组修改
	 */
	public function group_edit() {
		if (IS_POST) {
			$data = $this->request->post();
			if(!$data['group_name']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			$info['group_name'] = $data['group_name'];
			$reuslt = db('device_group')->where(array('group_id'=>$data['group_id']))->update($info);
			if (false !== $reuslt) {
				return $this->success(lang('edit').lang('success'),url('manage/device_group'));
			} else {
				return $this->error(lang('edit').lang('fail'), '');
			}
		} else {

			$group_id = input('group_id');
			$info = db('device_group')->where(array('group_id'=>$group_id))->find();
			$this->assign('info',$info);
			$this->setMeta(lang('edit').lang('grouping'));
			return $this->fetch();
		}
	}

	/**
	 * 分组删除
	 */
	public function group_del() {
		$group_id  = input('group_id');
		if(!$group_id){
			return $this->error(lang('parameter_error'));
		}
		$check_data = db('device_group')->where(array('group_id'=>$group_id))->find();
		if(!$check_data){
			return $this->error(lang('device_does_not_exist'));
		}
		if($check_data['group_id'] == 1){
			return $this->error(lang('system_grouping_prohibit_deleting'));
		}
		db('device')->where(array('group_id'=>$check_data['group_id']))->update(array('group_id'=>1));
		$reuslt = db('device_group')->where(array('group_id'=>$check_data['group_id']))->delete();
		if($reuslt){
			return $this->success(lang('delete').lang('success'));
		}else{
			return $this->error(lang('delete').lang('fail'));
		}
	}


	/**
	 * 存储池列表
	 */
	public function storage() {


		$order = "id asc";
		$list  =db('storage')->where('')->order($order)->paginate(15);
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		Storage::updateStorageSize();
		$this->assign($data);
		$this->setMeta(lang('storage_pool'));
		return $this->fetch();
	}




	/**
	 * 存储池新增
	 */
	public function storage_add() {

		if (IS_POST) {
			$data = $this->request->post();


			if(!$data['name']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			if(!$data['type']){
				return $this->error(lang('type').lang('cannot_be_empty'));
			}
			if($data['type']  == 1 ){
				if(!$data['ip']){
					return $this->error(lang('ip').lang('cannot_be_empty'));
				}
				if(!$data['port']){
					return $this->error(lang('port').lang('cannot_be_empty'));
				}
				if(!$data['secretkeys']){
					//return $this->error(lang('secretkey').lang('cannot_be_empty'));
				}
				$check_config_ip = db('storage')->where('')->select();
				if($check_config_ip){
					foreach ($check_config_ip as $key => $vo) {
						if($vo['config']){
							$info_config_c = json_decode($vo['config'],true);
							if($info_config_c['ip'] && $info_config_c['ip'] == $data['ip']){
								return $this->error(lang('ip').lang('already_exist'));
							}
						}
					}
				}
				$info_config['ip'] = $data['ip'];
				$info_config['port'] = $data['port'];
				$info_config['secretkeys'] = $data['secretkeys'];
			}else{
				if(!$data['accesskey']){
					return $this->error(lang('accesskey').lang('cannot_be_empty'));
				}
				if(!$data['secretkey']){
					return $this->error(lang('secretkey').lang('cannot_be_empty'));
				}
				$info_config['accesskey'] = $data['accesskey'];
				$info_config['secretkey'] = $data['secretkey'];

			}


			$license_data = get_license();

			if($data['type'] == 1 ){
				if(!$license_data['config_info']['storage']['storage_net']){
					return $this->error(lang('unauthorized_application'));
				}
				$auth_size = $license_data['config_info']['storage']['storage_net_size'];

			}elseif($data['type'] == 2 ){
				if(!$license_data['config_info']['storage']['storage_ali']){
					return $this->error(lang('unauthorized_application'));
				}
				$auth_size = $license_data['config_info']['storage']['storage_ali_size'];
			}elseif($data['type'] == 3 ){
				if(!$license_data['config_info']['storage']['storage_baidu']){
					return $this->error(lang('unauthorized_application'));
				}
				$auth_size = $license_data['config_info']['storage']['storage_baidu_size'];
			}elseif($data['type'] == 4 ){
				if(!$license_data['config_info']['storage']['storage_qiniu']){
					return $this->error(lang('unauthorized_application'));
				}
				$auth_size = $license_data['config_info']['storage']['storage_qiniu_size'];
			}

			$info['name'] = $data['name'];
			$info['type'] = $data['type'];
			$info['size'] = $auth_size;
			$info['config'] = json_encode($info_config);
			$info['status'] = $data['status'];
			$info['creation_time'] = time();
			$reuslt = db('storage')->insert($info,false,true);
			if (false !== $reuslt) {
				//记录行为
				$extended_data['storage_id'] = $reuslt;
				$extended_data['storage_name'] = $data['name'];
				$extended_data['storage_type'] = $data['type'];
				action_log('add_storage', 'storage', $reuslt, session('user_auth.uid'),$extended_data);
				
				return $this->success(lang('add').lang('success'),url('manage/storage'));
			} else {
				return $this->error(lang('add').lang('fail'), '');
			}
		} else {
			$storage_type_tmp  = get_storage_type();
			foreach ($storage_type_tmp as $key => $vo) {
				$storage_type[$key]['id'] = $key;
				$storage_type[$key]['name'] = $vo;
			}
			$this->assign('storage_type',$storage_type);
			$this->setMeta(lang('storage_add'));
			return $this->fetch('storage_edit');
		}
	}

	public function storage_edit() {
		if (IS_POST) {
			$data = $this->request->post();

			if(!$data['id']){
				return $this->error(lang('parameter_error'));
			}
			if(!$data['name']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			if(!$data['type']){
				return $this->error(lang('type').lang('cannot_be_empty'));
			}


			if($data['type']  == 1 ){
				if(!$data['ip']){
					return $this->error(lang('ip').lang('cannot_be_empty'));
				}
				if(!$data['port']){
					return $this->error(lang('port').lang('cannot_be_empty'));
				}
				if(!$data['secretkeys']){
					//return $this->error(lang('secretkey').lang('cannot_be_empty'));
				}
				$check_config_ip = db('storage')->where(array('id'=>array('neq',$data['id'])))->select();
				if($check_config_ip){
					foreach ($check_config_ip as $key => $vo) {
						if($vo['config']){
							$info_config_c = json_decode($vo['config'],true);
							if($info_config_c['ip'] && $info_config_c['ip'] == $data['ip']){
								return $this->error(lang('ip').lang('already_exist'));
							}
						}
					}
				}
				$info_config['ip'] = $data['ip'];
				$info_config['port'] = $data['port'];
				$info_config['secretkeys'] = $data['secretkeys'];
			}else{
				if(!$data['accesskey']){
					return $this->error(lang('accesskey').lang('cannot_be_empty'));
				}
				if(!$data['secretkey']){
					return $this->error(lang('secretkey').lang('cannot_be_empty'));
				}
				$info_config['accesskey'] = $data['accesskey'];
				$info_config['secretkey'] = $data['secretkey'];

			}

			$license_data = get_license();

			if($data['type'] == 1 ){
				if(!$license_data['config_info']['storage']['storage_net']){
					return $this->error(lang('unauthorized_application'));
				}
				$auth_size = $license_data['config_info']['storage']['storage_net_size'];

			}elseif($data['type'] == 2 ){
				if(!$license_data['config_info']['storage']['storage_ali']){
					return $this->error(lang('unauthorized_application'));
				}
				$auth_size = $license_data['config_info']['storage']['storage_ali_size'];
			}elseif($data['type'] == 3 ){
				if(!$license_data['config_info']['storage']['storage_baidu']){
					return $this->error(lang('unauthorized_application'));
				}
				$auth_size = $license_data['config_info']['storage']['storage_baidu_size'];
			}elseif($data['type'] == 4 ){
				if(!$license_data['config_info']['storage']['storage_qiniu']){
					return $this->error(lang('unauthorized_application'));
				}
				$auth_size = $license_data['config_info']['storage']['storage_qiniu_size'];
			}


			$info['name'] = $data['name'];
			$info['type'] = $data['type'];
			$info['size'] = $auth_size;
			$info['status'] = $data['status'];
			$info['config'] = json_encode($info_config);
			$info['update_time'] = time();
			$reuslt = db('storage')->where(array('id'=>$data['id']))->update($info);
			if (false !== $reuslt) {
				
				//记录行为
				$extended_data['storage_id'] = $data['id'];
				$extended_data['storage_name'] = $data['name'];
				$extended_data['storage_type'] = $data['type'];
				action_log('edit_storage', 'storage', $data['id'], session('user_auth.uid'),$extended_data);
				
				
				return $this->success(lang('edit').lang('success'),url('manage/storage_manage',array('id'=>$data['id'])));
			} else {
				return $this->error(lang('edit').lang('fail'), '');
			}
		} else {
			$id = input('id');
			if(!$id){
				return $this->error(lang('parameter_error'));
			}
			$info = db('storage')->where(array('id'=>$id))->find();

			$storage_type_tmp  = get_storage_type();
			foreach ($storage_type_tmp as $key => $vo) {
				if($key == $info['type']){
					$storage_type[$key]['id'] = $key;
					$storage_type[$key]['name'] = $vo;
				}
			}
			$info['config'] = json_decode($info['config'],true);

			$this->assign('info',$info);
			$this->assign('storage_type',$storage_type);
			$this->setMeta(lang('storage_add'));
			return $this->fetch('storage_edit');
		}
	}


	/**
	 * 存储池删除
	 */
	public function storage_del() {
		$id  = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));
		}
		$check_data = db('storage')->where(array('id'=>$id))->find();
		if(!$check_data){
			return $this->error(lang('storage_does_not_exist'));
		}
		if($check_data['is_primary'] == 1){
			return $this->error(lang('system_storage_prohibit_delete'));
		}

		if($check_data['status'] == 1){
		//判断是否能删除

		}


		$reuslt = db('storage')->where(array('id'=>$check_data['id']))->delete();
		if($reuslt){
			
			//记录行为
			$extended_data['storage_id'] = $id;
			$extended_data['storage_name'] = $check_data['name'];
			$extended_data['storage_type'] = $check_data['type'];
			action_log('del_storage', 'storage', $id, session('user_auth.uid'),$extended_data);
			return $this->success(lang('delete').lang('success'));
		}else{
			return $this->error(lang('delete').lang('fail'));
		}
	}

	/**
	 * 存储池状态
	 */
	public function storage_status() {
		$id  = input('id');
		$status  = input('status');

		if(!$id){
			return $this->error(lang('parameter_error'));
		}
		$check_data = db('storage')->where(array('id'=>$id))->find();
		if(!$check_data){
			return $this->error(lang('storage_does_not_exist'));
		}

		if($status == 0){
		//判断是否能禁用

		}
		$info['status'] = $status;
		$reuslt = db('storage')->where(array('id'=>$check_data['id']))->update($info);
		if($reuslt){
			//记录行为
			$extended_data['storage_id'] = $check_data['id'];
			$extended_data['storage_name'] = $check_data['name'];
			$extended_data['storage_type'] = $check_data['type'];
			action_log('status_storage', 'storage', $check_data['id'], session('user_auth.uid'),$extended_data);
			return $this->success(lang('update').lang('success'));
		}else{
			return $this->error(lang('update').lang('fail'));
		}
	}


	/**
	 * 存储空间列表
	 */
	public function storage_manage() {
		$id  = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));
		}
		$storage_data = db('storage')->where(array('id'=>$id))->find();
		if(!$storage_data){
			return $this->error(lang('storage_does_not_exist'));
		}

		if($storage_data['config']){
			$storage_data['config'] = json_decode($storage_data['config'],true);
		}



		if($storage_data['type']==4){
			$config_qiniu = array('accessKey'=>$storage_data['config']['accesskey'],'secretKey'=>$storage_data['config']['secretkey']);
			$storageqiniu  = new \app\device\model\StorageQiniu($config_qiniu);
			$bucket = $storageqiniu ->Allbucket();
			$bucket_list = $bucket[0];
			foreach ($bucket_list as $key => $vo) {
				$size = 0;
				$bucket_arr = $storageqiniu->get_buckets($vo)[0];
				foreach ($bucket_arr as $f_key => $f_vo) {
						$size +=$f_vo['fsize'];
				}
				$rbucket[$vo]['data'] = $bucket_arr;
				$rbucket[$vo]['size'] = $size;
			}
		}

		if($storage_data['data']){
			$storage_data['data'] = json_decode($storage_data['data'],true);
		}
		$storage_data['space_list'] = db('storage_path')->where(array('storage_id'=>$storage_data['id']))->order('id asc')->select();
		if($storage_data['space_list']){
			foreach ($storage_data['space_list'] as $key => &$vo) {

				if($storage_data['type']==4){
					$vo['used_size'] = $rbucket[$vo['name']]['size'];
				}
				$vo['usage_rate'] = bcdiv($vo['used_size'],$storage_data['size'],4)*100;

			}
		}
		
		$storage_log = get_log_list(array('record_id'=>$storage_data['id'],'model'=>'storage'));
		$this->assign('storage_log',$storage_log);
			
			
		$this->assign('storage_data',$storage_data);
		$this->setMeta(lang('storage_manage'));
		return $this->fetch();
	}

	/**
	 * 存储池新增
	 */
	public function storage_space_add() {


		$storage_id = input('storageid');
		if(!$storage_id){
			return $this->error(lang('parameter_error'));
		}
		$storage_data = db('storage')->where(array('id'=>$storage_id))->find();
		if(!$storage_data){
			return $this->error(lang('storage_does_not_exist'));
		}


		if (IS_POST) {

			$data = $this->request->post();
			if(!$data['name']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			if(!$data['storage_path']){
				return $this->error(lang('storage_path').lang('cannot_be_empty'));
			}

			if($storage_data['type'==1]){
				if (!preg_match('/^\//', trim($data['storage_path']))) {
					$data['storage_path'] = "/" . $data['storage_path'];
				}
			}
			$check_map['storage_id'] = $storage_id;
			$check_map['path'] = strtolower(trim($data['storage_path']));
			$check_storage=db('storage_path')->where($check_map)->find();
			if($check_storage){
				return $this->error(lang('storage_path_already_exists'));
			}

			$save_data['storage_id'] = $storage_id;
			$save_data['path'] = strtolower(trim($data['storage_path']));
			$save_data['name'] = $data['name'];
			$save_data['type'] = 1;
			$save_data['used_size'] = 0;
			$save_data['status'] = $data['status'];


			$reuslt=db('storage_path')->insert($save_data,false,true);

			if (false !== $reuslt) {
				
				//记录行为
				$extended_data['storage_id'] = $storage_id;
				$extended_data['storage_name'] = $storage_data['name'];
				$extended_data['storage_type'] = $storage_data['type'];
				$extended_data['storage_space_name'] = $data['name'];
				$extended_data['storage_space_id'] = $reuslt;
				action_log('add_storage_space', 'storage', $storage_id, session('user_auth.uid'),$extended_data);
				
				return $this->success(lang('add').lang('success'),url('manage/storage_manage',array('id'=>$storage_id)));
			} else {
				return $this->error(lang('add').lang('fail'), '');
			}
		} else {


			$this->assign('storage_data',$storage_data);
			$this->setMeta(lang('storage_space_add'));
			return $this->fetch('storage_space_edit');
		}
	}


	/**
	 * 存储空间删除
	 */
	public function storage_space_del() {
		$id  = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));
		}
		$check_data = db('storage_path')->where(array('id'=>$id))->find();
		if(!$check_data){
			return $this->error(lang('storage_does_not_exist'));
		}
		if($check_data['is_primary'] == 1){
			return $this->error(lang('system_storage_prohibit_delete'));
		}

		if($check_data['status'] == 1){
		//判断是否能删除

		}
		$reuslt = db('storage_path')->where(array('id'=>$check_data['id']))->delete();
		if($reuslt){
			
			
			
			$storage_data = db('storage')->where(array('id'=>$check_data['storage_id']))->find();
			
			//记录行为
			$extended_data['storage_id'] = $storage_data['id'];
			$extended_data['storage_name'] = $storage_data['name'];
			$extended_data['storage_type'] = $storage_data['type'];
			$extended_data['storage_space_name'] = $check_data['name'];
			$extended_data['storage_space_id'] = $check_data['id'];
			action_log('del_storage_space', 'storage', $storage_data['id'], session('user_auth.uid'),$extended_data);
			
			return $this->success(lang('delete').lang('success'));
		}else{
			return $this->error(lang('delete').lang('fail'));
		}
	}

	/**
	 * 存储空间状态
	 */
	public function storage_space_status() {
		$id  = input('id');
		$status  = input('status');

		if(!$id){
			return $this->error(lang('parameter_error'));
		}
		$check_data = db('storage_path')->where(array('id'=>$id))->find();
		if(!$check_data){
			return $this->error(lang('storage_does_not_exist'));
		}

		if($status == 0){
		//判断是否能禁用

		}
		$info['status'] = $status;
		$reuslt = db('storage_path')->where(array('id'=>$check_data['id']))->update($info);
		if($reuslt){
			
			$storage_data = db('storage')->where(array('id'=>$check_data['storage_id']))->find();
			//记录行为
			$extended_data['storage_id'] = $storage_data['id'];
			$extended_data['storage_name'] = $storage_data['name'];
			$extended_data['storage_type'] = $storage_data['type'];
			$extended_data['storage_space_name'] = $check_data['name'];
			$extended_data['storage_space_id'] = $check_data['id'];
			action_log('status_storage_space', 'storage', $storage_data['id'], session('user_auth.uid'),$extended_data);

			return $this->success(lang('update').lang('success'));
		}else{
			return $this->error(lang('update').lang('fail'));
		}
	}


	/**
	 * 计算池列表
	 */
	public function computenode() {

		$order = "id asc";
		$list  =db('compute')->where('')->order($order)->paginate(15);
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta(lang('compute_pool'));
		return $this->fetch();
	}

	/**
	 * 存储池新增
	 */
	public function compute_add() {

		if (IS_POST) {

			//检查是否有权限去添加计算池
			$license_data = get_license();
			$compute_size = $license_data['config_info']['compute_size'];
			$has_compute_size = db('compute')->count();

			if($has_compute_size+1 > $compute_size){
				return $this->error(lang('compute_size').lang('over'));
			}


			$data = $this->request->post();
			if(!$data['name']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			if(!$data['ip']){
				return $this->error(lang('ip').lang('cannot_be_empty'));
			}
//			if(!$data['port']){
//				return $this->error(lang('port').lang('cannot_be_empty'));
//			}


			$check_map['ip'] = $data['ip'];
			$check_compute=db('compute')->where($check_map)->find();
			if($check_compute){
				return $this->error(lang('pool_already_exists'));
			}

			$license_data = get_license();
			$auth_compute_count = $license_data['config_info']['compute_size'];
			$compute_count=db('compute')->where('')->count();
			if($compute_count >= $auth_compute_count){
				return $this->error(lang('authorization_limit'));
			}

			$save_data['name'] = $data['name'];
			$save_data['ip'] = $data['ip'];
			$save_data['port'] = $data['port'];
			$save_data['status'] = $data['status'];
			$save_data['creation_time'] = time();
			$save_data['is_primary'] = 0;


//			$compute_info = $this->get_compute_info($save_data['ip']);
//			$save_data['compute_info'] = json_encode($compute_info);


            $compute_id=db('compute')->insert($save_data,false,true);			
            Compute::send_update_compute_message($compute_id,0);
			if ($compute_id) {			
				//记录行为
				$extended_data['compute_id'] = $compute_id;
				$extended_data['compute_name'] = $save_data['name'];
				$extended_data['compute_ip'] = $save_data['ip'];
				action_log('add_compute', 'compute', $compute_id, session('user_auth.uid'),$extended_data);
				return $this->success(lang('add').lang('success'),url('manage/computenode'));
			} else {
				return $this->error(lang('add').lang('fail'), '');
			}
		} else {

			$this->setMeta(lang('compute_add'));
			return $this->fetch('compute_edit');
		}
	}


	//获取该节点下最大内存和网桥信息,CPU等信息
	private function get_compute_info($ip){

		$url = get_libvirt_url($ip);
		$lv = new Libvirt($url);
		$ci  = $lv->get_connect_information();
		$maxcpu = $ci['hypervisor_maxvcpus'];
		$memStat = $lv->get_mem_stats();

		$data['max_cpu'] = $maxcpu;
		$data['total_mem'] = $memStat['total'];
		$data['free_mem'] = $memStat['free'];
		return $data;

	}

	/**
	 * 存储池新增
	 */
	public function compute_edit() {


		$id = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));
		}

		$compute_data = db('compute')->where(array('id'=>$id))->find();
		if(!$compute_data){
			return $this->error(lang('compute_does_not_exist'));
		}
		if (IS_POST) {

			$data = $this->request->post();
			if(!$data['name']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			if(!$data['ip']){
				return $this->error(lang('ip').lang('cannot_be_empty'));
			}
//			if(!$data['port']){
//				return $this->error(lang('port').lang('cannot_be_empty'));
//			}


			$check_map['ip'] = $data['ip'];
			$check_map['id'] = array('not in',$id);
			$check_compute=db('compute')->where($check_map)->find();
			if($check_compute){
				return $this->error(lang('pool_already_exists'));
			}

			$save_data['name'] = $data['name'];
			$save_data['ip'] = $data['ip'];
			$save_data['port'] = $data['port'];
			$save_data['status'] = $data['status'];


			$reuslt=db('compute')->where(array('id'=>$id))->update($save_data);
            Compute::send_update_compute_message($id,0);
			if (false !== $reuslt) {
				
				//记录行为
				$extended_data['compute_id'] = $id;
				$extended_data['compute_name'] = $save_data['name'];
				$extended_data['compute_ip'] = $save_data['ip'];

				action_log('edit_compute', 'compute', $id, session('user_auth.uid'),$extended_data);
				
				return $this->success(lang('edit').lang('success'),url('manage/computenode'));
			} else {
				return $this->error(lang('edit').lang('fail'), '');
			}
		} else {


			$this->assign('info',$compute_data);
			$this->setMeta(lang('compute_edit'));
			return $this->fetch('compute_edit');
		}
	}



	/**
	 * 计算池删除
	 */
	public function compute_del() {
		$id  = input('id');
		if(!$id){
			return $this->error(lang('parameter_error'));
		}
		$check_data = db('compute')->where(array('id'=>$id))->find();
		if(!$check_data){
			return $this->error(lang('compute_does_not_exist'));
		}
		if($check_data['is_primary'] == 1){
			return $this->error(lang('system_computing_pool_delete'));
		}

//		if($check_data['status'] == 1){
//		  //判断是否能删除
//
//		}
		$reuslt = db('compute')->where(array('id'=>$check_data['id']))->delete();
		if($reuslt){
			
			//记录行为
			$extended_data['compute_id'] = $check_data['id'];
			$extended_data['compute_name'] = $check_data['name'];
			$extended_data['compute_ip'] = $check_data['ip'];
			action_log('del_compute', 'compute', $check_data['id'], session('user_auth.uid'),$extended_data);
			
			return $this->success(lang('delete').lang('success'));
		}else{
			return $this->error(lang('delete').lang('fail'));
		}
	}

    /**
     * 跟新计算池的网桥信息
     */
    public function update_compute(){
        $compute_id = input('id');
        if (!$compute_id){
            return $this->error(lang('parameter_error'));
        }
        $res = db('compute')->where(array('id'=>$compute_id))->find();
        if (!$res){
            return $this->error(lang('compute_does_not_exist'));
        }
        if(Compute::send_update_compute_message($compute_id))
        {
			
						//记录行为
			$extended_data['compute_id'] = $compute_id;
			$extended_data['compute_name'] = $res['name'];
			$extended_data['compute_ip'] = $res['ip'];
			action_log('update_compute', 'compute', $compute_id, session('user_auth.uid'),$extended_data);
			
			
			
            return $this->success(lang('update').lang('success'));
        }else{
            return $this->error(lang('update').lang('fail'));
        }
    }




	private function format_table($string) {
		if(strpos($string, '【------】') !== false){
			$string_array_data  = explode("【------】",$string);
			$content_title = $string_array_data[0];
			$content  = $string_array_data[1];
		}else{
			$content  = $string;
		}
		if(strpos($content, '<br>') !== false){
			$string_array = explode("<br>",$content);
		}else{
			$string_array = $content;
		}
		if($string_array){
			$string = '';
			if($content_title){
				$string .= '<p>'.$content_title.'</p>';
			}
			if(is_array($string_array)){
				foreach ($string_array as $key => $vo) {
					$string .= '<p>'.$vo.'</p>';
				}
			}else{
				$string .= '<p>'.$string_array.'</p>';
			}
			return $string;
		}else{
			return $string;
		}
	}
	private function format_error($string) {
		if(strpos($string, '【------】') !== false){
			$string_array_data  = explode("【------】",$string);
			$string_array_title = $string_array_data[0];
			$string_array  = explode("<br>",$string_array_data[1]);
		}else{
			$string_array  = explode("<br>",$string);
		}
		if($string_array){
			$string = '';
			if($string_array_title){
				$string .= '<p>'.$string_array_title.'</p>';
			}
			foreach ($string_array as $key => $vo) {
				$string .= '<p>'.$vo.'</p>';
			}
			return $string;
		}else{
			return $string;
		}
	}


	/**
	 * 计算池的详细信息
	 */
	public function compute_detail(){

		$id  = input('id');

		if(!$id){
			return $this->error(lang('parameter_error'));
		}

		//获取计算池基本信息
		$compute_data = db('compute')->where(array('id'=>$id))->find();

		//获取该计算池下的所有虚拟机
		$virtual_data = db('compute_virtual')->where(array('comput_id'=>$id))->order('id desc')->paginate(12);

		$data = array(
			'compute_info' => $compute_data,
			'virtual_data' => $virtual_data,
			'page' => $virtual_data->render()
		);


		$compute_log = get_log_list(array('record_id'=>$compute_data['id'],'model'=>'compute'));
		$this->assign('compute_log',$compute_log);
		
		$this->assign($data);
		$this->setMeta(lang('compute_detail'));
		return $this->fetch('compute_detail');
	}


	/**
	 *  改变计算池的使用状态
	 */
    public function compute_status(){

		$id  = input('id');
		$status  = input('status');

		if(!$id){
			return $this->error(lang('parameter_error'));
		}
		$check_data = db('compute')->where(array('id'=>$id))->find();
		if(!$check_data){
			return $this->error(lang('compute_does_not_exist'));
		}

		if($status == 0){
			//判断是否能禁用
		}
		$info['status'] = $status;
		$reuslt = db('compute')->where(array('id'=>$check_data['id']))->update($info);
		if($reuslt){
			
			//记录行为
			$extended_data['compute_id'] = $check_data['id'];
			$extended_data['compute_name'] = $check_data['name'];
			$extended_data['compute_ip'] = $check_data['ip'];
			action_log('status_compute', 'compute', $check_data['id'], session('user_auth.uid'),$extended_data);
			
			return $this->success(lang('update').lang('success'));
		}else{
			return $this->error(lang('update').lang('fail'));
		}

	}


	/**
	 *  虚拟机列表页
	 *
	 */

	public function virtual()
	{
		$device_id = input('device_id');
		if(!$device_id){
			return $this->error(lang("params").lang('error'));
		}
		$virtual_list = ComputeVirtual::getVirtualListByDeviceId($device_id);

		$data = array(
				'list'=>$virtual_list,
				'page'=>$virtual_list->render()
		);

		$this->assign($data);
	    $this->setMeta(lang('virtual_list'));
		return $this->fetch();
	}


	/**
	 *   计算池下的虚拟机详情
	 */

	 public function virtual_detail(){

		 $id = input('id');
		 //获取该计算池下的所有虚拟机
		 $virtual_data = db('compute_virtual')->where(array('id'=>$id))->find();
		 if(!$virtual_data){
			 return $this->error(lang('virtual_does_not_exist'));
		 }

		 $comput_id = $virtual_data['comput_id'];
		 $compute_info = db('compute')->where(array('id'=>$comput_id))->find();

		 $compute_info = json_decode($compute_info['compute_info'],true);

		 $max_cpu = $compute_info['max_cpu'] ? $compute_info['max_cpu'] : 5;

		 $virtual_info = json_decode($virtual_data['virtual_info'],true);

		 $has_installed_snaps = $virtual_info['snaps'];


		 //节点ip
		 $ip = $this->get_ip($comput_id);

		 //判断真实的虚机是否存在
		 if(VirtualManager::is_virtual_exist($virtual_data['name'],$ip)){
			$virtual_status = VirtualManager::get_virtual_status($virtual_data['name'],$ip);
		 }else{
			 $virtual_not_exist = 1;
		 }

		 $old_status = $virtual_data['status'];
		 $virtual_data['status'] = $virtual_status ? 1 : 0;
		 if ($old_status != $virtual_data['status']) {
		     $data['status'] = $virtual_data['status'];
		     db('compute_virtual')->where(array('id'=>$id))->update($data);
		 }
//
		 $is_liter = is_liter_system() ? 1 : 0;
//
		 if(!$is_liter) {

			 //获取磁盘组
			 $snap_id = explode('_', $virtual_data['snap_id']);
			 $task_id = $snap_id[0];
			 $group_id = $snap_id[1];
			 //获取此任务下的所有快照信息
			 $snap = db('cdp_snap');
			 $field = array('id', 'group_id', 'type', 'task_id', 'create_time', 'harddisk_id', 'have_os');
			 $snaps = $snap->where(array('task_id' => $task_id, 'virtual_id' => 0, 'keli_id' => 0, 'group_id' => array('neq', $group_id)))->field($field)->select();
			 $snaps = $this->handle_snap_list($snaps, $has_installed_snaps);
			 // $virtual_data['type'] = $virtual_data['type']==1 ? lang('exercise') : lang('take_over');
			 $net_data = $virtual_info['net_data'];
			 $bridge_info = $compute_info['bridges'] ?: array();
			 $this->assign('snaps', $snaps);
			 $this->assign('net_data', $net_data);
			 $this->assign('bridge_info', $bridge_info);
		 }

		 $this->assign('is_running',$virtual_data['status']);
         $this->assign('is_liter',$is_liter);
		 $this->assign('virtual_not_exist',$virtual_not_exist);
		 $this->assign('max_cpu',$max_cpu);
		 $this->assign('virtual_data',$virtual_data);
		 $this->assign('ip',$ip);

		 $this->setMeta(lang('virtual_detail'));
		 return $this->fetch('virtual_detail');
	 }


	//处理snap数据
	private function handle_snap_list($list,$has_installed_snaps){

		$data = array();
		foreach($list as $k=>$v){
			if(in_array($v['id'],$has_installed_snaps))
				continue;
			$hd_id = $v['harddisk_id'];
			$date = date('Y-m-d',$v['create_time']);
			$data[$date]['date'] = $date;
			$data[$date]['vmdk'][$hd_id][] = $v;
		}
		return $data;
	}



	public function add_disk(){

		$disk = $_POST['disk'];

		$virtual_id = input('virtual_id');
		$virtual_name = input('virtual_name');
		$ip = input('ip');

		if (!$disk) {
			return $this->error(lang('choose_disk!'));
		}
		$virtual = db('compute_virtual')->where(array('id'=>$virtual_id))->field(array('virtual_info,name,device_id'))->find();
		$virtual_info = json_decode($virtual['virtual_info'],true);
		$system_type = Device::get_system_type($virtual_info['device_id']);
		$system_type = ($system_type=='Windows'? 1 : 0);

		$snap_list = array();
    	foreach ($disk as $key => $value) {
    		$snap =  db('cdp_snap')->where(array('id'=>$value))->select();
    		$snap_list[] = $snap;
			$virtual_info['snaps'][] = $value;
    	}


		$url = get_libvirt_url($ip);
		$lv = new Libvirt($url);

		$xml = $this->getXML($lv,$virtual_name);
		$xmlMan = new XmlMannager($xml);

		$spath = array();

    	foreach ($snap_list as $key => $value) {

			$spath = array_merge($spath,$value);

    		$snap_list[$key] = $this->get_temp_vmdk_record($value);
			$this->insert_temp_snap($snap_list[$key],$virtual_id,$xmlMan);
    	}

		$storage_id = $spath[0]['storage_id'];
		$spath = CdpSnap::get_snap_filepath($spath);


		if($this->changeXML($lv,$virtual_name,$xmlMan->getNewXml())){
				db('compute_virtual')->where(array('id'=>$virtual_id))->update(array('virtual_info'=>json_encode($virtual_info)));

				VirtualManager::send_create_virtual_messgae($spath,$storage_id,$system_type);

				ComputeVirtual::editVirtualLog($virtual['name'],$virtual['device_id'],'磁盘',1);

				return $this->success(lang('add_disk').lang('success'));
		} else{

			ComputeVirtual::editVirtualLog($virtual['name'],$virtual['device_id'],'磁盘',0);
			return $this->error(lang('add_disk').lang('error').lang($xmlMan->getError()));
		}

	}

	private function changeXML($lv,$domainName,$newXML){

		$msg = $lv->domain_change_xml($domainName,$newXML);
		if($msg)
			return true;
		else
			return false;

	}

	private function getXML($lv,$domainName){

		$inactive = (!$lv->domain_is_running($domainName)) ? true : false;
		$xml = $lv->domain_get_xml($domainName, $inactive);

		return $xml;
	}

	// 根据快照点的数据，构造新的数据，主要用于构造tmp_vmdk
	private  function get_temp_vmdk_record($snap_list){

		foreach($snap_list as $k=>&$v){

			$file_name = $v['file_name'];
			$explode_file_name = explode('.',$file_name);
			$v['file_name'] = $explode_file_name[0].'_temp'.'.'.$explode_file_name[1];
			$v['parent_id'] = $v['id'];

		}
		return $snap_list;
	}

	//插入新的数据
	private function insert_temp_snap($snap_list,$virtual_id,$xmlMan){


		foreach($snap_list as $k=>$v){
			unset($v['id']);
			$v['virtual_id'] = $virtual_id;
			$v['create_time'] = time();
			db('cdp_snap')->insert($v);
			$xmlMan->addDisk($v['file_name']);
		}

	}

	public function virtual_edit_type(){

		$virtual_id = input('virtual_id');
		$virtual_name = input('virtual_name');
		$ip = input('ip');


		$type = input('bridge_name');
		if(!$type){
			return $this->error(lang('no_modify'));
		}


		$virtual = db('compute_virtual')->where(array('id'=>$virtual_id))->field(array('virtual_info,device_id'))->find();
		//		检查原机是否在线,如果在线则不能转接管
		if(Device::check_device_is_on($virtual['device_id'])){
			return $this->error(lang('source_device_on_action_forbid '));
		}

		$virtual_info = json_decode($virtual['virtual_info'],true);

		if(!$virtual_info['net_data']){
			return $this->error(lang('can_not_edit'));
		}

		foreach($virtual_info['net_data'] as $k=>&$v){
			$v['type'] = $type;
		}


		$url = get_libvirt_url($ip);
		$lv = new Libvirt($url);

		//如果正在运行，则关闭虚拟机
		$is_running = VirtualManager::get_virtual_status($virtual_name,$ip);
		if($is_running){
			VirtualManager::stop_virtual($virtual_name,$ip);
		}

		$xml = $this->getXML($lv,$virtual_name);
		$xmlMan = new XmlMannager($xml);

		$xmlMan->editNetwork($type);

		if($this->changeXML($lv,$virtual_name,$xmlMan->getNewXml())){
			db('compute_virtual')->where(array('id'=>$virtual_id))
					->update(array('virtual_info'=>json_encode($virtual_info),'type'=>0));

			//如果正在运行，则重新开启虚拟机
			if($is_running){
				VirtualManager::start_virtual($virtual_name,$ip);
			}

			ComputeVirtual::editVirtualLog($virtual['name'],$virtual['id'],'类型',1);
			return $this->success(lang('edit').lang('success'));
		} else{
			ComputeVirtual::editVirtualLog($virtual['name'],$virtual['id'],'类型',0);
			return $this->error(lang('edit').lang('error').lang($xmlMan->getError()));
		}

	}
	/**
	 *  更改虚拟机的运行状态
	 */
	public function virtual_status(){

		$id  = input('id');
		$status  = input('status');

        if($id){
            if(!$id){
                return $this->error(lang('parameter_error'));
            }
            if(is_numeric($id)){
                return $this->change_virtual_status($id,$status,1);
            }
            $id = rtrim($id,';');
            $id = explode(';',$id);
        }else{
            $device_id = input('device_id');
            if($device_id){
                $id = ComputeVirtual::where(array('device_id'=>$device_id,'status'=>1))->column('id');
                if(count($id) == 0){
                    return json(array('code'=>0,'error_msg'=>'操作无效,无运行的虚拟机!'));
                }
            }
        }


		$fail_count = 0;
		$success_count = 0;
		foreach($id as $key=>$value){
			if(!$this->change_virtual_status($value,$status,0)){
				$fail_count++;
			}else{
				$success_count++;
			}
		}
		$code = ($fail_count==0)?1:0;
        $error_msg = $fail_count ." 个失败! ". $success_count + "个成功!";
		return json(array('code'=>$code,'f_count'=>$fail_count,'s_count'=>$success_count));
	}

	private function change_virtual_status($id,$status,$flag){

		$check_data = db('compute_virtual')->where(array('id'=>$id))->find();
		if(!$check_data){
			if($flag){
				return $this->error(lang('virtual_does_not_exist'));
			}else{
				return false;
			}
		}
		$info['status'] = $status;
		$name = $check_data['name'];

		$comput_id = $check_data['comput_id'];
		//节点ip
		$ip = $this->get_ip($comput_id);
		$action = $status ? 'start_virtual': 'stop_virtual';

		if ($status ==1){  //开机
			$res = VirtualManager::start_virtual($name,$ip);
			$tip_str = 'start_virtual';
		}else{
			$res = VirtualManager::stop_virtual($name,$ip);
			$tip_str = 'stop_virtual';
		}
		if($res){
			$change_db = db('compute_virtual')->where(array('id'=>$check_data['id']))->update($info);
		}else{
			ComputeVirtual::statusVirtualLog($name,$check_data['device_id'],$action,0);
			if($flag){
				return $this->error(lang($tip_str).lang('fail'));
			}else{
				return false;
			}
		}
		if($change_db){
			ComputeVirtual::statusVirtualLog($name,$check_data['device_id'],$action,1);
			if($flag){
				return $this->success(lang($tip_str).lang('success'));
			}else{
				return true;
			}
		}else{
			ComputeVirtual::statusVirtualLog($name,$check_data['device_id'],$action,0);
			if($flag){
				return $this->error(lang($tip_str).lang('fail'));
			}else{
				return true;
			}
		}
	}

	/**
	 * 删除虚拟机
	 */
	public function virtual_del(){

		$id  = input('id');
        if($id){
            $from = input("from")?:0;
            if(is_numeric($id)){
                if(!$id){
                    return $this->error(lang('parameter_error'));
                }
                return $this->delete_virtual($id,$from,1);
            }
            $id = rtrim($id,';');
            $id = explode(';',$id);
        }else{
            $device_id = input('device_id');
            if($device_id){
              if(ComputeVirtual::where(array('device_id'=>$device_id,'status'=>1))->find()){
                  return json(array('code'=>0,'error_msg'=>'请先关闭该设备下的所有虚拟机!'));
              }
                $id = ComputeVirtual::where(array('device_id'=>$device_id))->column('id');
                if(count($id) == 0){
                    return json(array('code'=>0,'error_msg'=>'操作无效,该设备下无虚拟机!'));
                }
            }else{

            }
        }
		$fail_count = 0;
		$success_count = 0;
		foreach($id as $key=>$value){
			if(!$this->delete_virtual($value,$from,0)){
				$fail_count++;
			}else{
				$success_count++;
			}
		}
		$code = ($fail_count==0)?1:0;
        $error_msg = $fail_count ."个删除失败! ". $success_count + "个删除成功!";
		return json(array('code'=>$code,'f_count'=>$fail_count,'s_count'=>$success_count,'error_msg'=>$error_msg));

	}


	private function delete_virtual($id,$from,$flag){


		$check_data = db('compute_virtual')->where(array('id'=>$id))->find();
		if(!$check_data){
			if($flag)
				return $this->error(lang('virtual_does_not_exist'));
			else
				return false;
		}
		if($check_data['status'] == 1){
			if($flag)
				return $this->error(lang('virtual_operation_delete_prohibited'));
			else
				return false;
		}
		$name = $check_data['name'];
		$comput_id = $check_data['comput_id'];
		//节点ip
		$ip = $this->get_ip($comput_id);


		if(!VirtualManager::is_virtual_exist($name,$ip)){ //如果真实的虚机不存在，则直接删除记录
			$res = db('compute_virtual')->where(array('id'=>$check_data['id']))->delete();

			//type 为 3的则操作 remote_snap 表
			$table_name = ($check_data['type']==3)?"remote_snap":'cdp_snap';

			db($table_name)->where(array('virtual_id'=>$id))->delete();

		}else{

			if($virtual_status = VirtualManager::get_virtual_status($name,$ip)){
				$old_status = $check_data['status'];
				$virtual_data['status'] = $virtual_status ? 1 : 0;
				if ($old_status != $virtual_data['status']) {
					$data['status'] = $virtual_data['status'];
					db('compute_virtual')->where(array('id'=>$id))->update($data);
				}

				if($flag)
					return $this->error(lang('virtual_operation_delete_prohibited'));
				else
					return false;
			}

			//否则先删除真实的虚机
			if(VirtualManager::del_virtual($name,$ip)){

				if($check_data['type']==3){
					
					$remote_snap = new RemoteSnap();
					$remote_snap->del_temp_snap($id);
					//删除虚拟机表中数据
					db('compute_virtual')->where(array('id'=>$check_data['id']))->delete();


				}else{

					//删除temp数据
					$this->cdp_snap = new CdpSnap();
					$this->cdp_snap->del_temp_snap($id);

					$res = db('compute_virtual')->where(array('id'=>$check_data['id']))->delete();
					$this->cdp_snap->where(array('virtual_id'=>$id))->delete();
					// 更改小颗粒的状态
					$keli_model = db('cdp_keli');
					if($keli_model->where(array('virtual_id'=>$check_data['id']))->find()){
						$keli_model->where(array('virtual_id'=>$check_data['id']))->update(array('status'=>1));
					}
				}

			}else{
				if($flag)
					return $this->error(lang('delete').lang('fail'));
				else
					return false;
			}
		}
		//sql删除结果判断
		if($res){
			if ($from){
				ComputeVirtual::delVirtualLog($check_data['name'],$check_data['device_id'],1);
				if($flag)
					return $this->success(lang('delete').lang('success'));
				else
					return true;
			}else{
				ComputeVirtual::delVirtualLog($check_data['name'],$check_data['device_id'],1);
				if($flag)
					return ['code'=>1];
				else
					return true;
			}
		}else{
			ComputeVirtual::delVirtualLog($check_data['name'],$check_data['device_id'],0);
			if($flag)
				return ['code'=>0];
			else
				return false;
		}

	}


	//更具虚拟机id获取ip
	private function get_ip($id){
		$comput_info = db('compute')->field(array('ip'))->where(array('id'=>$id))->find();
		return $comput_info['ip'];
	}




	/**
	 *   虚拟机配置的修改
	 */
	public function virtual_edit(){

		if(IS_POST){

			$id = input('id');
			$ip = input('ip');

			$url = get_libvirt_url($ip);
			$lv = new Libvirt($url);

			$name = input('virtual_name');
			$old_cpu = input('old_cpu_count');
			$new_cpu = input('cpu_count');
			$old_memory = input('old_memory');
			$memory = input('memory');
			$memory = $memory * 1024;
			$db = db('compute_virtual');
			$info = $db->field('device_id')->where(array('id'=>$id))->find();

			if($old_cpu != $new_cpu || $old_memory != $memory){
				$change_res = $this->changeAction($lv,$name);
				if(!$change_res){

					ComputeVirtual::editVirtualLog($name,$info['device_id'],'内存与cpu',0);
					return $this->error(lang('edit').lang('error'));

				}else{

					$data['cpu_kernel'] = $new_cpu;
					$data['memory'] = $memory;
					$db->where(array('id'=>$id))->update($data);
					ComputeVirtual::editVirtualLog($name,$info['device_id'],'内存与cpu',1);
					return $this->success(lang('edit').lang('success'));
				}
			}else{
				return $this->error(lang('no_change'));
			}
		}
	}

	/**
	 *  修改设置
	 */
	public function changeAction($lv,$name){

		$cpus = input('cpu_count');
		$oldCpus = input('old_cpu_count');
		$pmemory = input('memory');

		$pmemory = $pmemory * 1024;


		if ($cpus && $oldCpus) {
			$inactive = (!$lv->domain_is_running($name)) ? true : false;
			$xml = $lv->domain_get_xml($name, $inactive);
			$xmlMan = new XmlMannager($xml);
			$xmlMan->eidtCPU($cpus) ;
			$xmlMan->editMem($pmemory);
			$msg = $lv->domain_change_xml($name,$xmlMan->getNewXml());
		    if($msg)
				return true;
			else
				return false;
		}
	}



	//生成mac地址
	public function generate_mac_addr(){


        $device_id = input('device_id');
		$card_index = input('index');
		if($card_index !== ''){
			$card_index = explode(',',$card_index)?:array($card_index);
		}else{
			$card_index = array();
		}
		foreach($card_index as $key=>$v){
			if($v==999)
				unset($card_index[$key]);
		}

        $netcards = Device::get_device_network_info($device_id);

		$cards_count = count($netcards);
		$seed = 1;
		$prefix = '52:54:00';
		$mac_addr = $prefix.':'.
		$this->macbyte(($seed * rand()) % 256).':'.
		$this->macbyte(($seed * rand()) % 256).':'.
		$this->macbyte(($seed * rand()) % 256);
		if(count($card_index)<$cards_count){

			foreach($card_index as $v){
				unset($netcards[$v]);
			}
			$keys = array_keys($netcards);
			$card = current($netcards);
			$key = current($keys);
			return ['ip'=>$card['ip'],'mac_addr'=>$card['mac'],'index'=>$key];
		}else{
			return ["ip"=>' ','mac_addr'=>$mac_addr,'index'=>999];
		}


	}

	function macbyte($val) {
		if ($val < 16)
			return '0'.dechex($val);
		return dechex($val);
	}

	public function vnc(){

		$id = input('id');
		$virtual_data = db('compute_virtual')->where(array('id'=>$id))->find();
		if(!$virtual_data){
			return $this->error(lang('virtual_does_not_exist'));
		}
		$comput_id = $virtual_data['comput_id'];
		//节点ip
		$ip = $this->get_ip($comput_id);

      	$virtual_status = VirtualManager::get_virtual_status($virtual_data['name'],$ip);

		if(!$id){
		  return  $this->error(lang('parameter_error'));
		}
		$manager_data=db('compute_virtual')->where(array('id'=>$id))->find();

		if(!$virtual_status || $manager_data['status'] != 1 ){
		  // $data['status'] = $virtual_status?0:1;
		  // if ($manager_data['status'] != $data['status']) {
		  // 		db('compute_virtual')->where(array('id'=>$id))->update($data);
		  // 	}
		  return $this->error(lang('virtual_not_run'));
		}


		if(!$manager_data){
		  return $this->error(lang('virtual_not_exist'));
		}


//		$computing_data = get_computing_info($manager_data['vir_computing_id']);

//		$token = $manager_data['source_device_ip'].'-'.$manager_data['remote_port'];
		$token = '127.0.0.1-'.$manager_data['remote_port'];

		$serverhost = config('vnc_web_host');
		$serverport = config('vnc_web_port');

		$this->assign('serverhost',$serverhost);//novnc_ip
		$this->assign('serverport',$serverport);//novnc_port


		$this->assign('encrypt',0);//novnc_port
		$this->assign('true_color',1);//novnc_port
		$this->assign('password','123456');//novnc_port
		$this->assign('token',$token);//novnc_port

		$this->assign('ip',$ip);
		$this->assign('vnc_host',$_SERVER['SERVER_NAME']);//C('VNC_HOST')
//		$this->assign('storage_host',C('STORAGE_HOST'));//C('STORAGE_HOST')
//		$this->assign('computing_data',$computing_data);
		$this->assign('manager_data',$manager_data);
		return $this->fetch('vnc');
	}


	//获取主机的一些信息
	/**
	 */
	public function get_connect_information(){

		$ip = input('ip')?:'192.168.2.22';

		$url = get_libvirt_url($ip);
		$lv = new Libvirt($url);
		$connect_info = $lv->get_cpu_stats();

		$this->assign('connect_info',$connect_info);

	}



	/**
	 *  发消息
	 */
   //更新软件
	public function update(){


		$device_id = input('id');
		$softName= input('name');

		$device_info = db('device')->where(array('id'=>$device_id))->find();
		$device_uniqueid = $device_info['unique_id'];

		$data['deviceUniqueId'] =$device_uniqueid;
		$data['name'] = $softName;

		$result = \Netmessage::send_message('W2P_Update_Module', $data);

		if ($result['code'] == 1) {

			return $this->success(lang('update').lang('success'));

		} else {

			return $this->error(lang(\Netmessage::getError()));
		}

	}


	//卸载软件
	public function uninstall(){

		$device_id = input('id');
		$softName= input('name');
		$device_info = db('device')->where(array('id'=>$device_id))->find();
		$device_uniqueid = $device_info['unique_id'];

		$data['deviceuniqueid'] =$device_uniqueid;
		$data['name'] = $softName;

		$result = \Netmessage::send_message('w2p_remove_module', $data);
		if ($result['code'] == 1) {
			return $this->success(lang('uninstall').lang('success'));
		} else {
			return $this->error(lang(\Netmessage::getError()));
		}
	}


	//停止或开启软件
   public function soft_status(){

	   $device_id = input('id');
	   $softName= input('name');
	   $status = input('status');
	   $message_name = $status==1?'w2p_start_module':'w2p_stop_module';
       $tip_str = $status==1?'start':'stop';
	   $device_info = db('device')->where(array('id'=>$device_id))->find();
	   $device_uniqueid = $device_info['unique_id'];
	   $data['deviceuniqueid'] =$device_uniqueid;
	   $data['name'] = $softName;
	   $result = \Netmessage::send_message($message_name, $data);

	    if ($result['code'] == 1) {

		    return $this->success(lang($tip_str).lang('success'));
	   } else {

		     return $this->error(lang(\Netmessage::getError()));
	   }

   }

	//重启软件
	public function soft_restart(){

		$device_id = input('id');
		$softName= input('name');
		$device_info = db('device')->where(array('id'=>$device_id))->find();
		$device_uniqueid = $device_info['unique_id'];

		$data['deviceuniqueid'] =$device_uniqueid;
		$data['name'] = $softName;

		$result = \Netmessage::send_message('w2p_restart_module', $data);

		if ($result['code'] == 1) {
			return $this->success(lang('uninstall').lang('success'));
		} else {
			return $this->error(lang(\Netmessage::getError()));
		}

	}


}