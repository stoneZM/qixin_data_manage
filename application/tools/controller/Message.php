<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\tools\controller;
use app\common\controller\Admin;

class Message extends Admin {


	public function _initialize() {
		parent::_initialize();
		
		$this->message = model('Message');
	}
	/**
	 * 插件列表
	 */
	public function index() {
	
		$list = $this->message->order('typecode asc, name asc, id desc')->paginate(25);
		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->setMeta(lang('message_manage'));
		$this->assign($data);
		return $this->fetch();
	}
	
	
	/**
	 * 通信类型反射
	 */
	public function get_mtype_read($key) {
		$data = array(
			'W2S' => 'S2W',
			'W2I' => 'I2W',
			'W2C' => 'C2W',
		);
		if($key){
			return $data[$key];
		}else{
			return $data;
		}
	}
	/**
	 * 通信类型
	 */
	public function get_mtype($key='') {
		$data = array(
			'W2S' => '网站 => 服务端(W2S)',
			'W2I' => '网站 => 索引端(W2I)',
			'W2C' => '网站 => 客户端(W2C)',
		);
		if($key){
			return $data[$key];
		}else{
			return $data;
		}
	}
	/**
	 * 字段类型列表
	 */
	public function get_fields_mtype($key='') {
		$data = array(
			/*'Net_message_PacketHeader' => '头信息',*/
			'TYPE_STRING' => '字符串',
			'TYPE_UINT32' => '数字32位',
			'TYPE_UINT64' => '数字64位',
		);
		if($key){
			return $data[$key];
		}else{
			return $data;
		}
	}
	
	
	//创建向导首页
	public function add() {
		if (IS_POST) {
			$data = input();
			if(!$data['title']){
				return $this->error(lang('name').lang('cannot_be_empty'));
			}
			if(!$data['name']){
				return $this->error(lang('mapping_name').lang('cannot_be_empty'));
			}
			if(!$data['typecode']){
				return $this->error(lang('message_code').lang('cannot_be_empty'));
			}
			if(!$data['mtype']){
				return $this->error(lang('type').lang('cannot_be_empty'));
			}
			if(!$data['mapping']){
				return $this->error(lang('mapping_class').lang('cannot_be_empty'));
			}
			$data['name'] = strtolower($data['name']);
			
			if(!$data['id']){
				$checkdata = $this->message->where(array('name' => $data['name'],'mtype' => $data['mtype']))->find();
				if($checkdata){
					return $this->error('当前通信类型中的【'.$data['name'].'】已经被使用,请更换');
				}
				
			}else{
				$checkdata = $this->message->where(array('name' => $data['name'],'id' => array('neq',$data['id'])))->find();
				if($checkdata){
					return $this->error('当前通信类型中的【'.$data['name'].'】已经被使用,请更换');
				}
			}
			
			$save_data['title'] = $data['title'];
			$save_data['name'] = $data['name'];
			$save_data['alias_name'] = strtolower($data['mtype']).'_'.$data['name'];
			$save_data['typecode'] = $data['typecode'];
			$save_data['mapping'] = $data['mapping'];
			$save_data['mtype'] = $data['mtype'];
			$save_data['description'] = $data['description'];
			$save_data['status'] = $data['status'];
			
			if(isset($data['write'])){
				$write = $data['write'];
	
				$wk = 0 ;
				foreach ($write as &$f) {
					if(!$f['name']){
						continue;
					}
					$write_data[$wk] = $f;
					$write_data[$wk]['number'] = $wk+1;
					$wk ++ ;
				}
				if(!empty($write_data)){
					$config['write'] = $write_data;
					$save_data['write_name'] = strtoupper($data['mtype']).'_'.$data['name'].'_write';
				}else{
					$config['write'] = '';
					$save_data['write_name'] = '';
				}
			}else{
				$save_data['write_name'] = '';
			}
			
			
			if(isset($data['read'])){
				$read = $data['read'];
				$rk = 0 ;
				foreach ($read as &$t) {
					if(!$t['name']){
						continue;
					}
					$read_data[$rk] = $t;
					$read_data[$rk]['number'] = $rk+1;
					$rk ++ ;
				}
				
				if(!empty($read_data)){
					$config['read'] = $read_data;
					$save_data['read_name'] = strtoupper($data['mtype']).'_'.$data['name'].'_read';
				}else{
					$config['read'] = '';
					$save_data['read_name'] ='';
				}
				if($data['id']){
					$save_data['id'] =  $data['id'];
				}
			}else{
				$save_data['read_name'] ='';
			}
			
			if(isset($config)){
				if(empty($config['write'])) unset($config['write']);
				if(empty($config['read'])) unset($config['read']);
				
				if(!$config){
					$save_data['config'] = '';
				}else{
					$save_data['config'] = json_encode($config);	
				}
				
			}
			
			if($data['id']){
				$result = $this->message->save($save_data, array('id' => $data['id']));
			}else{
				$result = $this->message->save($save_data);
			}
			if ($result) {
				
				if($data['id']){
					$msg = lang('edit').lang('success');
				}else{
					$msg = lang('add').lang('success');
				}
				return $this->success($msg, url('tools/message/index'));
			} else {
				return $this->error(lang('operation').lang('fail'));
			}
		} else {
			$id = input('id');
			$data = $this->message->where(array('id' => $id))->find();
			$config = $data['config'];
			if($config){
				$conf_data =  json_decode($config,true);
				if(isset($conf_data['write'])){
					$write_data = json_encode($conf_data['write']);
					$this->assign('write_data',$write_data);
				}
				if(isset($conf_data['read'])){
					$read_data = json_encode($conf_data['read']);
					$this->assign('read_data',$read_data);
				}
			}
			$this->assign('data',$data);
			$this->assign('mtype_data',$this->get_mtype());
			$this->assign('field_data',$this->get_fields_mtype());
			
			if($id){
				$this->setMeta(lang('edit').lang('message'));
			}else{
				$this->setMeta(lang('add').lang('message'));
			}
			return $this->fetch();
		}
	}
	
	
	public function msgchecked() {
		if (IS_POST) {
			$data = input();
			if(!$data['name']){
				$this->error(lang('parameter_error'));
			}
			/*$data['nWebId'] =2;
			$data['NType'] =0;
			$data['sUserId'] ='0050562ac0be';
			$data['nOperator'] =1;*/
			$result = \Netmessage::send_message($data['name'], $data['data']);
			if ($result) {
				$return_data  = check_message_data($result['nState']);
				if($return_data == '0'){
					$this->success(lang('Test_has_been_sent'));
				}else{
					$this->error($return_data);
				}
			} else {
				$this->error(\Netmessage::getError());
			}	
		}else{
			$id = input('id');
			$data = $this->message->where(array('id' => $id))->find();
			$config = $data['config'];
			if($config){
				$conf_data =  json_decode($config,true);
				if(isset($conf_data['write'])){
					$write_data = $conf_data['write'];
					
					foreach ($write_data as $key=> &$f) {
						if($f['name'] == 'header'){
							unset($write_data[$key]);
						}
					}
					$this->assign('write_data',$write_data);
				}
			}
			$this->assign('data',$data);
			$this->setMeta(lang('message').lang('test'));
			return $this->fetch();
		}
	}
	
	public function updatemessage() {
		$MessageModel = D('Message');
		$data      = $MessageModel->create();
		if ($data) {
			if ($data['id']) {
				$flag = $MessageModel->save($data);
				if ($flag !== false) {
					S('hooks', null);
					$this->success(lang('update').lang('success'), Cookie('__forward__'));
				} else {
					$this->error(lang('update').lang('fail'));
				}
			} else {
				$flag = $MessageModel->add($data);
				if ($flag) {
					S('hooks', null);
					$this->success(lang('add').lang('success'), Cookie('__forward__'));
				} else {
					$this->error(lang('add').lang('fail'));
				}
			}
		} else {
			$this->error($MessageModel->getError());
		}
	}
	
	
    /**
     * 创建控制器文件
     * @return int
     */
    private function buildMessage($fileName,$data)
    {
        
		$pathTemplate = APP_PATH . "tools" . DS . "view" . DS . "message" . DS . "template" . DS . "Message.tpl";
        $template = file_get_contents($pathTemplate);
        return file_put_contents($fileName, str_replace(
                ["[NAME]", "[FIELDS]", "[CLASS]", "[GETFIELD]"],
                [$data['name'], $data['fields'], $data['class'], $data['get_field']],
                $template)
        );
    }
	
	public function get_fields_string($data='') {
		
		if(!$data){
			return false;
		}
		$fields_string ='';
		$class_string ='';
		$get_field_string ='';
		foreach ($data as &$w) {
			$fields_string .='    public $'.$w['name']. ";\n";
			
			$class_string .='    $f = new \DrSlump\Protobuf\Field();'. "\n";
			$class_string .='    $f->number    = '.$w['number'].';'. "\n";
			$class_string .='    $f->type      =  \DrSlump\Protobuf::'.$w['type'].';'. "\n";
			$class_string .='    $f->name      = "'.$w['name'].'";'. "\n";
			$class_string .='    $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;'. "\n";
			$class_string .='    $descriptor->addField($f);'. "\n\n";
			
			$get_field_string .='    public function get'.$w['name'].'()'. "\n";
			$get_field_string .='    {'. "\n";
			$get_field_string .='        return $this->_get('.$w['number'].');'. "\n";
			$get_field_string .='    }'. "\n\n";
		}
		$data['fields'] = $fields_string;
		$data['class'] = $class_string;
		$data['get_field'] = $get_field_string;
		
		return $data;
	}

	
	
	public function create_message($message_data) {
		
		if($message_data['config']){
			$config_data = json_decode($message_data['config'],true);
		}
		if(isset($config_data['write'])){
			$g_dir['write']['dir'] = QINFO_MESSAGE_GENER_PATH . $message_data['write_name'].".php";
			$g_dir['write']['data']['name'] = $message_data['write_name'];
			$write_arr = $this->get_fields_string($config_data['write']);
			$g_dir['write']['data']['fields'] = $write_arr['fields'];
			$g_dir['write']['data']['class'] = $write_arr['class'];
			$g_dir['write']['data']['get_field'] = $write_arr['get_field'];
			
		}
		if(isset($config_data['read'])){
			$g_dir['read']['dir'] = QINFO_MESSAGE_GENER_PATH . $message_data['read_name'].".php";
			$g_dir['read']['data']['name'] = $message_data['read_name'];
			$write_arr = $this->get_fields_string($config_data['read']);
			$g_dir['read']['data']['fields'] = $write_arr['fields'];
			$g_dir['read']['data']['class'] = $write_arr['class'];
			$g_dir['read']['data']['get_field'] = $write_arr['get_field'];
		}
		
		if(isset($g_dir)){
			
			foreach ($g_dir as &$t) {
				$this->buildMessage($t['dir'],$t['data']);
			}
		}
		return true;
	}
	
	//生成消息
	public function generate_map() {
		
		if (!is_writable(QINFO_MESSAGE_GENER_PATH)) {
			return $this->error(lang('You_cannot_use_this_function_because_you_do_not_create_directory_write_permissions'));
		}

		$message_data = $this->message->field('id,alias_name,typecode,write_name,read_name')->where(array('status' => 1))->select();
		foreach ($message_data as $key=>&$item) {
			
			$re_data[$item['alias_name']]['messagecode'] = $item['typecode'];
			
			if(isset($item['write_name']) && $item['write_name']){
				$re_data[$item['alias_name']]['messageclass'][] = $item['write_name'];
			}
			if(isset($item['read_name']) && $item['read_name']){
				$re_data[$item['alias_name']]['messageclass'][] = $item['read_name'];
			}
		}
		if(isset($re_data)){
			$fileName =  APP_PATH ."extra" . DS  . "map.php";
			$ret = file_put_contents($fileName, ("<?php \r\nreturn " . sqlarr2str($re_data) . ";"));
			if($ret){
				$this->success(lang('generate').lang('success'));
			}else{
				$this->success(lang('generate').lang('fail'));
			}
		}else{
			$this->error(lang('No_data_can_be_generated'));
		}
		
	}

	//生成消息
	public function generate($id = '') {
		
		if (!is_writable(QINFO_MESSAGE_GENER_PATH)) {
			return $this->error(lang('You_cannot_use_this_function_because_you_do_not_create_directory_write_permissions'));
		}
		if($id){
			$message_data = $this->message->where(array('id' => $id))->find();
			$this->create_message($message_data);
		}else{
			$message_data = $this->message->where(array('status' => 1))->select();
			foreach ($message_data as &$item) {
				$this->create_message($item);
			}
		}
		
		$this->generate_map();
		
	}
	/**
	 * 启用插件
	 */
	public function enable() {
		$id = input('id');
		$model  = model('Message');
		$result = $model::where(array('id' => $id))->update(array('status' => 1));
		if ($result) {
			return $this->success(lang('operation').lang('success'));
		} else {
			return $this->error(lang('operation').lang('fail'));
		}
	}

	/**
	 * 禁用插件
	 */
	public function disable() {
		$id = input('id');
		$model  = model('Message');
		$result = $model::where(array('id' => $id))->update(array('status' => 0));
		if ($result) {
			return $this->success(lang('operation').lang('success'));
		} else {
			return $this->error(lang('operation').lang('fail'));
		}
	}

	/**
	 * 设置插件页面
	 */
	public function config() {
		if (IS_POST) {
			# code...
		} else {
			$id = input('id', '', 'trim,intval');
			if (!$id) {
				return $this->error(lang('illegal_operation'));
			}
			$info = $this->addons->find($id);
			if (!empty($info)) {
				$class = get_addon_class($info['name']);

				$keyList = array();
				$data    = array(
					'keyList' => $keyList,
				);
				$this->assign($data);
				$this->setMeta($info['title'] . " - ".lang('Set_up'));
				return $this->fetch('common@public/edit');
			} else {
				return $this->error(lang('This_plugin_is_not_installed'));
			}
		}
	}
	
	//超级管理员删除钩子
	public function delmessage() {
		$id        = $this->getArrayParam('id');
		$map['id'] = array('IN', $id);
		$result    = model('Message')->where($map)->delete();
		if ($result !== false) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}


}