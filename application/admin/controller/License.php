<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

//------------------------
// 演示示例
//-------------------------

namespace app\admin\controller;
use app\cdp\model\Cdp;
use app\common\controller\Admin;

class License extends Admin
{
	/**
	* 授权码新增
	*/
	public function index() {



		if (IS_POST) {
			
			$data = $this->request->post();


			if(!$data['cdkey']){
				return $this->error(lang('cd_key').lang('cannot_be_empty'));
			}
			
			if(!$data['reg_agency']){
				return $this->error(lang('reg_agency').lang('cannot_be_empty'));
			}
			
			if(!$data['reg_phone']){
				return $this->error(lang('reg_phone').lang('cannot_be_empty'));
			}
			
			if(!$data['reg_email']){
				return $this->error(lang('reg_email').lang('cannot_be_empty'));
			}
			
			$reuslt_data['cdkey'] = trim($data['cdkey']);
			
			$reuslt_data['use_info']['reg_agency'] = $data['reg_agency'];
			$reuslt_data['use_info']['reg_phone'] = $data['reg_phone'];
			$reuslt_data['use_info']['reg_email'] = $data['reg_email'];
			$reuslt_data['use_info']['reg_contacts'] = $data['reg_contacts'];
			$reuslt_data['use_info']['reg_area'] = $data['reg_area'];
			
			
			
			/*@exec("wmic CPU get ProcessorID", $disk_data);
			$hardware_data['macaddr'] = 'F4-8E-38-99-2F-A6';
			$hardware_data['productname'] = '00ffaac2699d';
			$hardware_data['uuid'] = '00ffaac2699d';
			$hardware_data['disksn'] = $disk_data[1];
			$hardware_code = licensecrypto(json_encode($hardware_data),'encode');*/
			
			$hardware_data = get_hardware();
			if(!$hardware_data || !$hardware_data['macaddr'] || !$hardware_data['uuid'] || !$hardware_data['disksn']){
				return $this->error(lang('data_acquisition_failure'));
			}
			$reuslt_data['hardware_info'] = $hardware_data;
			
			$r_data['data'] = licensecrypto(json_encode($reuslt_data),'encode');
			$r_data['msg'] = lang('authorization_code_success');
		
			
			if (false !== $r_data) {
				return json($r_data);
			} else {
				return $this->error(lang('authorization_code_failed'), '');
			}
		} else {

			update_hardware_info();
			$license_data = get_license();
			$this->assign('license_data',$license_data);
			$this->setMeta(lang('license'));
			return $this->fetch();
		}
	}
	
	
	
	
	/**
	* 授权码激活
	*/
	public function license() {
		
		if (IS_POST) {

			$data = $this->request->post();
			if(!$data['license_code']){
				return $this->error(lang('license_code').lang('cannot_be_empty'));
			}
			$data['license_code']=str_replace("\r\n","",$data['license_code']);
			$license_info = json_decode(licensecrypto($data['license_code']),true);
			if(!$license_info['cdkey']){
				return $this->error(lang('authorization_code_error'));
			}
			
			if(!$license_info['reviewer']){
				return $this->error(lang('authorization_code_not_authenticated'));
			}


			if(!self::check_new_licence($data['license_code'])){

				return $this->error("cdp节点已超限,请自行删除后重试");
			}

			$r_data = set_license($data['license_code']);
			if (false !== $r_data) {
				
				$db = \think\Db::connect();
				updata_system_menu($db,config('database.prefix'));
				//config('system_type',$license_info['version_type']);
//				model('Config')->where(array('name' => 'system_type'))->setField('value', $license_info['version_type']);
				cache('db_config_data', null);
				if($license_info['type']==1){
					remove_license_png();
				}else{
					set_license_png($license_info['sn']);
				}
				return $this->success(lang('operation').lang('success'), url('index'));
			} else {
				return $this->error(lang('operation').lang('fail'), '');
			}
		} else {

			update_hardware_info();
			$license_data = get_license();
			$this->assign('license_data',$license_data);
			$this->setMeta(lang('license'));
			return $this->fetch();
		}
	}


//	判断已有节点和新授权节点个数比较
		static  function check_new_licence($license)
		{
			$license_data = json_decode(licensecrypto($license),true);
			$config = $license_data['config_info'];
			$cdp_size = $config['cdp_size'];
			$have_cdp = db('cdp')->where(array('status'=>1))->count();

			if($have_cdp>$cdp_size){
				return false;
			}else{
				return true;
			}

		}
}