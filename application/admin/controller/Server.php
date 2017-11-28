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

class Server extends Admin {

	private $connection = false;


	public function _initialize() {
		parent::_initialize();
		$this->model = model('Server');
		$this->server_command = model('ServerCommand');
	}

	/**
	 * 主机管理
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function index() {
		$map          = array('status' => 1);
		$list = db('server')->where($map)->order('id desc')->select();
		$data = array(
			'list'        => $list,
		);
		$this->assign('tabs','index');
		$this->assign($data);
		$this->setMeta(lang('host_list'));
		return $this->fetch();
	}
	/**
	 * 新增主机
	 */
	public function add() {
		if (IS_POST) {
			$data   = $this->request->post();
			
			if(!$data['host']){
				return $this->error(lang('please_fill_in').lang('host_name'));
			}
			if(!$data['port']){
				return $this->error(lang('please_fill_in').lang('host_port'));
			}
			if(!$data['uname']){
				return $this->error(lang('please_fill_in').lang('host_account'));
			}
			$data['update_time'] = time();
			$data['create_time'] = time();
			if ($data) {
				$id = $this->model->save($data);
				if ($id) {			
					return $this->success(lang('add').lang('success'), url('index'));
				} else {
					return $this->error(lang('update').lang('fail'));
				}
			} else {
				return $this->error($this->model->getError());
			}
		} else {
			$this->setMeta(lang('add').lang('host'));
			$this->assign('info', null);
			return $this->fetch('edit');
		}
	}

	/**
	 * 编辑主机
	 */
	public function edit($id = 0) {
		if (IS_POST) {
			$data   = $this->request->post();
			if(!$data['host']){
				return $this->error(lang('please_fill_in').lang('host_name'));
			}
			if(!$data['port']){
				return $this->error(lang('please_fill_in').lang('host_port'));
			}
			if(!$data['uname']){
				return $this->error(lang('please_fill_in').lang('host_account'));
			}
			$data['update_time'] = time();
			if ($data) {
				$result = $this->model->save($data, array('id' => $data['id']));
				if (false !== $result) {
					return $this->success(lang('update').lang('success'), url('index'));
				} else {
					return $this->error($this->model->getError(), '');
				}
			} else {
				return $this->error($this->model->getError());
			}
		} else {
			$info = array();
			/* 获取数据 */
			$info = db('server')->field(true)->find($id);

			if (false === $info) {
				return $this->error(lang('get_information_error'));
			}
			$this->assign('info', $info);
			$this->setMeta(lang('edit').lang('host'));
			return $this->fetch();
		}
	}
	
	//删除主机
	public function del() {
		$id = $this->getArrayParam('id');
		if (empty($id)) {
			return $this->error(lang('illegal_operation'));
		}
		$result = db('server')->where(array('id' => array('IN', $id)))->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}
	

	//获取数据
	private function get_info_id($id) {
		$info = db('server')->field('host,port,uname,password')->where(array('status' => 1, 'id' => $id))->find();
		return $info;
	}
	
	public function ssh2() {

			$host_list = db('server')->where(array('status' => 1))->select();
			if ($host_list) {
				$this->assign('host_list', $host_list);
			}
			
			$command_list = db('server_command')->where(array('status' => 1))->select();
			if ($command_list) {
				$this->assign('command_list', $command_list);
			}
			$this->setMeta(lang('ssh').lang('remote_control'));
			$id   = input('id');
			$this->assign('id', $id);
			$this->assign('tabs','ssh2');
			return $this->fetch();
	}
	
	public function link() {
		$id   = input('id');
		if($id){
			$info = $this->get_info_id($id);
			$ssh = new \Ssh($info);//实例化对象
			if($ssh->code == 1){
				$data['output'] = $this->write('> '.lang('The_host_authentication_is_successful').':'.$info['host'],'success');
				return json($data);
			}else{
				$data['error'] = $this->write($ssh->get_error(),'red');
				return json($data);
			}
		}else{
			$data['error'] = $this->write(lang('Host_does_not_exist'),'red');
			return json($data);
		}
	}
	public function sendcmd() {
		$id   = input('id');
		$content   = input('content');
		
		if($id){
			$info = $this->get_info_id($id);
			$ssh = new \Ssh($info);//实例化对象
			if($ssh->code == 1){
				$cmd = $content;
				$data = $ssh->cmdlong($cmd);
				$data['title'] = $this->write('> '.lang('execute').'【'.$cmd.'】'.lang('command'),'yellow');
				if($data['error']){
					$data['error'] = $this->write($this->format_error($data['error']),'red');
				}
				if($data['output']){
					$data['output'] = $this->format_table($data['output']);
				}
				return json($data);
			}else{
				$data['error'] = $this->write($ssh->get_error(),'red');
				return json($data);
			}
		}else{
			$data['error'] = $this->write(lang('Host_does_not_exist'),'red');
			return json($data);
		}
	}	
	
	public function fastcmd() {
		$id   = input('id');
		$command_id   = input('command_id');
		if($id){
			$info = $this->get_info_id($id);
			$ssh = new \Ssh($info);//实例化对象
			if($ssh->code == 1){
				$command_data = db('server_command')->where(array('status' => 1,'id' => $command_id))->find();
				if(!$command_data['content']){
					$data['error'] = $this->write(lang('execute_command_is_empty'),'red');
					return json($data);
				}
				$cmd = $command_data['content'];
				
				if($command_data['types'] == 1){
					$data =  $ssh->cmdlong($cmd);
				}else{
					$data =  $ssh->cmd($cmd);
				}
				
				$data['title'] = $this->write('> '.lang('execute').'【'.$cmd.'】'.lang('command'),'yellow');
				if($data['error']){
					$data['error'] = $this->write($this->format_error($data['error']),'red');
				}
				if($data['output']){
					$data['output'] = $this->format_table($data['output']);
				}
				return json($data);
			}else{
				$data['error'] = $this->write($ssh->get_error(),'red');
				return json($data);
			}
		}else{
			$data['error'] = $this->write(lang('Host_does_not_exist'),'red');
			return json($data);
		}
	}
	

	/**
	 * 命令管理
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function command() {
		$map          = array('status' => 1);
		$list = db('server_command')->where($map)->order('id asc')->select();
		$data = array(
			'list'        => $list,
		);
		$this->assign('tabs','command');
		$this->assign($data);
		$this->setMeta(lang('quick_command'));
		return $this->fetch();
	}
	/**
	 * 新增命令
	 */
	public function command_add() {
		if (IS_POST) {
			$data   = $this->request->post();
			if(!$data['name']){
				return $this->error(lang('please_fill_in').lang('name'));
			}
			if(!$data['content']){
				return $this->error(lang('please_fill_in').lang('command'));
			}	
			$data['update_time'] = time();
			$data['create_time'] = time();
			if ($data) {
				$id = $this->server_command->save($data);
				if ($id) {			
					return $this->success(lang('add').lang('success'), url('command'));
				} else {
					return $this->error(lang('add').lang('fail'));
				}
			} else {
				return $this->error($this->server_command->getError());
			}
		} else {
			$this->setMeta(lang('add').lang('command'));
			$this->assign('info', null);
			return $this->fetch('command_edit');
		}
	}

	/**
	 * 编辑命令
	 */
	public function command_edit($id = 0) {
		if (IS_POST) {
			$data   = $this->request->post();
			if(!$data['name']){
				return $this->error(lang('please_fill_in').lang('name'));
			}
			if(!$data['content']){
				return $this->error(lang('please_fill_in').lang('command'));
			}
			$data['update_time'] = time();
			if ($data) {
				$result = $this->server_command->save($data, array('id' => $data['id']));
				if (false !== $result) {
					return $this->success(lang('update').lang('success'), url('command'));
				} else {
					return $this->error($this->server_command->getError(), '');
				}
			} else {
				return $this->error($this->server_command->getError());
			}
		} else {
			$info = array();
			/* 获取数据 */
			$info = db('server_command')->field(true)->find($id);
			if (false === $info) {
				return $this->error(lang('get_information_error'));
			}
			$this->assign('info', $info);
			$this->setMeta(lang('edit').lang('command'));
			return $this->fetch();
		}
	}
	
	//删除主机
	public function command_del() {
		$id = $this->getArrayParam('id');
		if (empty($id)) {
			return $this->error(lang('illegal_operation'));
		}
		$result = db('server_command')->where(array('id' => array('IN', $id)))->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
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
			$string = '<ul class="clearfix" style="padding:0;marage:0">';
			if($content_title){
				$string .= '<li class="col-lg-12 info-box-text" style="margin-bottom:10px; font-size:14px;padding-left:0;;color:yellow">'.$content_title.'</li>'; 
			}
			
			if(is_array($string_array)){
				foreach ($string_array as $key => $vo) { 
					$string .= '<li class="col-lg-12" style="font-size:14px;padding-left:0">'.$vo.'</li>'; 
				}
			}else{
				$string .= '<li class="col-lg-12" style="font-size:14px;padding-left:0">'.$string_array.'</li>'; 
			}
			$string .='</ul>'; 
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
			$string = '<ul class="clearfix" style="padding:0;marage:0">';
			if($string_array_title){
				$string .= '<li class="col-lg-12" style="margin-bottom:10px; font-size:14px;padding-left:0;color:yellow">'.$string_array_title.'</li>'; 
			}
			foreach ($string_array as $key => $vo) { 
				$string .= '<li class="col-lg-12" style="font-size:14px;padding-left:0">'.$vo.'</li>'; 
			}
			$string .='</ul>'; 
			return $string;
		}else{
			return $string;
		}
	}
	
	
    private function setValue($val)
    {
        $js = "progress.setValue($val)";
        $this->writeScript($js);
    }

    private function showProgress()
    {
        $js = "progress.show();";
        $this->writeScript($js);
    }

    private function hideProgress()
    {
        $js = "progress.hide();";
        $this->writeScript($js);
    }
	
	
	
    private function writeMessage($str)
    {
        $js = "writeMessage('$str')";
        $this->writeScript($js);
    }

    private function writeScript($str)
    {
        echo "<script>$str</script>";
        ob_flush();
        flush();
    }

    private function write($str, $type = 'info', $br = '<br>')
    {
        return '<span class="text-'.$type.'">'.$str.'</span>'.$br;
    }
}