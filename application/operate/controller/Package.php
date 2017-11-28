<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------


namespace app\operate\controller;
use app\common\controller\Admin;
use think\Db;
use think\Exception;
use think\Request;

class Package extends Admin
{
	//系统版本列表
	public function index() {

		$list = db('package')->where($map)->order('id desc')->paginate(10);
		$list_data = $list ->toArray();
		if($list_data['data']){
			foreach ($list_data['data'] as $key => &$vo) {
				if ($vo['file_id']) {
					$file_data =  get_goods_file($vo['file_id']);
					$vo['file_url'] = $file_data['url'];
				} else {
					$vo['file_url'] = '';  
				}
				if($vo['module']){
					$tmp_module = explode(",",$vo['module']);
					
					foreach ($tmp_module as $t_key => &$t_vo) {
						$tmp_module_array = explode("_",$t_vo);
						$goods_key = $tmp_module_array[0];
						$goods_version_key = $tmp_module_array[1];
						$goods_data =  db('goods')->where(array('id' => $goods_key))->find();
						$goods_data['version'] = db('goods_version')->where(array('id' => $goods_version_key))->find();
						
						$vo['module_data'][$t_key] = $goods_data;
					}	
				}
			}
		}
		$data = array(
			'list' => $list_data['data'],
			'page' => $list->render(),
			'type' => $type,
		);
		$this->assign('good_data',$good_data);
		$this->assign($data);
		$this->setMeta(lang('custom_install'));
		return $this->fetch();
	}
	
	//添加版本
	public function package_add() {
		
		if (IS_POST) {
			$data = input();
			
			if(!$data['title']){
				return $this->error(lang('title').lang('cannot_be_empty'));
			}
			if(!$data['system_version']){
				return $this->error(lang('system_version').lang('cannot_be_empty'));
			}
			
			$system_version_data = db('goods_update')->where(array('number'=>$data['system_version'],'status'=>1))->find();
			if(!$system_version_data){
				return $this->error(lang('system_file_does_not_exist'));
			}
			
			if(!$system_version_data['install_id']){
				return $this->error(lang('system').lang('installation_package_does_not_exist'));
			}
			
			
			$agent_info = db('agent')->where(array('status'=>1,'id'=>$data['agent_id']))->field('id,name,alias')->find();
			if(!$agent_info){
				return $this->error(lang('agent_info_does_not_exist'));
			}
			
			
			
			if($data['module']){
				foreach ($data['module'] as $key=> &$module_vo) {
					if($module_vo){
						$module_data = db('goods')->where(array('id'=>$key))->find();
						if($module_data['status'] !=1){
							return $this->error($module_data['etitle'].lang('already_off_the_shelf'));
						}
						$module_version = db('goods_version')->where(array('id'=>$module_vo))->order('create_time desc')->find();
						if(!$module_version['file_id']){
							return $this->error($module_data['etitle'].lang('module').lang('installation_package_does_not_exist'));
						}
						$smodule[]  = $key.'_'.$module_vo;
					}
				}	
			}
			if($smodule){
				$smodule = implode(",",$smodule);
			}
			$savedata['title'] = $data['title'];
			$savedata['license_id'] = $data['license_id'];
			$savedata['name'] = $system_version_data['name'];
			$savedata['number'] = $system_version_data['number'];
			$savedata['status'] = $data['status'];
			$savedata['module'] = $smodule;
			$savedata['create_time'] = time();
			$savedata['update_time'] = time();
			$savedata['generate'] = 0;
			
			
			$c_confg['site_title'] = $data['site_title'];
			$c_confg['site_alias'] = $data['site_alias'];
			$c_confg['site_url'] = $data['site_url'];
			$c_confg['site_corporate_name'] = $data['site_corporate_name'];
			$c_confg['site_copyright'] = $data['site_copyright'];
			$c_confg['is_updata'] = $data['is_updata'];
			$c_confg['updata_url'] = $data['updata_url'];
			$c_confg['ico_id'] = $data['ico_id'];
			$c_confg['custom_id'] = $data['custom_id'];
			
			$savedata['agent_info'] = json_encode($agent_info);
			$savedata['config'] = json_encode($c_confg);
			
			$result = db('package')->insert($savedata);
			if ($result) {
				return $this->success(lang('add').lang('success'), url('operate/package/index'));
			} else {
				return $this->error(lang('add').lang('fail'));
			}
		} else {
			
			
			$map['status'] = 1;
			$system_list = db('goods_update')->where($map)->field(array('number','name','title','create_time','install_id'))->order('number desc')->select();
			foreach ($system_list as &$sys_vo) {
				if (!$sys_vo['install_id']) {
					$sys_vo['disabled'] = 'disabled';
				}else{
					$sys_vo['disabled'] = '';
				}
			}
			$module_list = db('goods')->where(array('status'=>1,'entity'=>2))->order('create_time asc')->select();
			if($module_list){
				foreach ($module_list as &$module_vo) {
					$module_version = db('goods_version')->where(array('status'=>1,'goods_id'=>$module_vo['id']))->order('create_time desc')->select();
					if($module_version){
							foreach ($module_version as &$version_vo) {
								if (!$version_vo['file_id']) {
									$version_vo['disabled'] = 'disabled';
								}else{
									$version_vo['disabled'] = '';
								}	
							}
						$module_vo['version']	 = $module_version;
					}	
				}
			}
			
			$license_data = db('license')->where(array('status'=>1,'type'=>1))->order('id asc')->select();
			$this->assign('license_data',$license_data);
			
			
			
			$agent_data = db('agent')->where(array('status'=>1))->order('id asc')->select();
			$this->assign('agent_data',$agent_data);
			$this->assign('module_list',$module_list);
			$this->assign('system_list',$system_list);

			$data['status'] = 1;
			
			$this->assign('data',$data);
			$this->setMeta(lang('add').lang('custom_install'));
			return $this->fetch('package_edit');
		}
	}
	//编辑版本
	public function package_edit() {
		
		$id = input('id','');
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		if (IS_POST) {
			
			$data = input();
			
			
			
			
			if(!$data['title']){
				return $this->error(lang('title').lang('cannot_be_empty'));
			}
			if(!$data['system_version']){
				return $this->error(lang('system_version').lang('cannot_be_empty'));
			}
			
			$system_version_data = db('goods_update')->where(array('number'=>$data['system_version'],'status'=>1))->find();
			if(!$system_version_data){
				return $this->error(lang('system_file_does_not_exist'));
			}
			
			if(!$system_version_data['install_id']){
				return $this->error(lang('system').lang('installation_package_does_not_exist'));
			}
			
			$agent_info = db('agent')->where(array('status'=>1,'id'=>$data['agent_id']))->field('id,name,alias')->find();
			if(!$agent_info){
				return $this->error(lang('agent_info_does_not_exist'));
			}
			
			if($data['module']){
				foreach ($data['module'] as $key=> &$module_vo) {
					if($module_vo){
						$module_data = db('goods')->where(array('id'=>$key))->find();
						if($module_data['status'] !=1){
							return $this->error($module_data['etitle'].lang('already_off_the_shelf'));
						}
						$module_version = db('goods_version')->where(array('id'=>$module_vo))->order('create_time desc')->find();
						if(!$module_version['file_id']){
							return $this->error($module_data['etitle'].lang('module').lang('installation_package_does_not_exist'));
						}
						$smodule[]  = $key.'_'.$module_vo;
					}
				}	
			}
			if($smodule){
				$smodule = implode(",",$smodule);
			}
			$savedata['title'] = $data['title'];
			$savedata['license_id'] = $data['license_id'];
			$savedata['name'] = $system_version_data['name'];
			$savedata['number'] = $system_version_data['number'];
			$savedata['status'] = $data['status'];
			$savedata['module'] = $smodule;
			$savedata['update_time'] = time();
			$savedata['generate'] = 0;
			
			$c_confg['site_title'] = $data['site_title'];
			$c_confg['site_alias'] = $data['site_alias'];
			$c_confg['site_url'] = $data['site_url'];
			$c_confg['site_corporate_name'] = $data['site_corporate_name'];
			$c_confg['site_copyright'] = $data['site_copyright'];
			$c_confg['is_updata'] = $data['is_updata'];
			$c_confg['updata_url'] = $data['updata_url'];
			$c_confg['ico_id'] = $data['ico_id'];
			$c_confg['custom_id'] = $data['custom_id'];

			$savedata['agent_info'] = json_encode($agent_info);
			$savedata['config'] = json_encode($c_confg);
			
			$result = db('package')->where(array('id' => $id))->update($savedata);
			if ($result) {
				return $this->success(lang('edit').lang('success'), url('operate/package/index'));
			} else {
				return $this->error(lang('edit').lang('fail'));
			}
		} else {

			$data = db('package')->where(array('id' => $id))->find();
			
			if($data['module']){
				$smodule = explode(",",$data['module']);
				unset($data['module']);
				foreach ($smodule as $m_key=> &$m) {
					$tmp = explode("_",$m);
					$data['module'][$tmp[0]] = $tmp[1];
				}
			}
			
			
			$map['status'] = 1;
			$system_list = db('goods_update')->where($map)->field(array('number','name','title','create_time','install_id'))->order('number desc')->select();
			foreach ($system_list as &$sys_vo) {
				if (!$sys_vo['install_id']) {
					$sys_vo['disabled'] = 'disabled';
				}else{
					$sys_vo['disabled'] = '';
				}
			}
			$module_list = db('goods')->where(array('status'=>1,'entity'=>2))->order('create_time asc')->select();
			if($module_list){
				foreach ($module_list as &$module_vo) {
					$module_version = db('goods_version')->where(array('status'=>1,'goods_id'=>$module_vo['id']))->order('create_time desc')->select();
					if($module_version){
							foreach ($module_version as &$version_vo) {
								if (!$version_vo['file_id']) {
									$version_vo['disabled'] = 'disabled';
								}else{
									$version_vo['disabled'] = '';
								}	
							}
						$module_vo['version']	 = $module_version;
					}	
				}
			}
			$license_data = db('license')->where(array('status'=>1,'type'=>1))->order('id asc')->select();
			$this->assign('license_data',$license_data);
			$agent_data = db('agent')->where(array('status'=>1))->order('id asc')->select();
			$agent_info =  json_decode($data['agent_info'],true);
			$data['config'] =  json_decode($data['config'],true);
			$data['ico_id'] = $data['config']['ico_id'];
			$data['custom_id'] = $data['config']['custom_id'];
			$this->assign('agent_info',$agent_info);
			$this->assign('agent_data',$agent_data);
			$this->assign('module_list',$module_list);
			$this->assign('system_list',$system_list);

			$this->assign('id',$id);
			$this->assign('data',$data);
			$this->setMeta(lang('edit').lang('custom_install'));
			return $this->fetch('package_edit');
		}
	}
	
	//删除自定义版本
	public function package_del($id) {
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		$result = db('package')->where(array('id' => $id))->delete();
		if ($result) {
			return $this->success(lang('delete').lang('success'));
		} else {
			return $this->error(lang('delete').lang('fail'));
		}
	}
	//生成打包自定义版本zip
	public function package_generate($id) {
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		$data = db('package')->where(array('id' => $id))->find();
		if (!$data) {
			return $this->success(lang('record_does_not_exist'));
		}
		$generate_info['id']= $data['id'];
		$generate_info['title']= $data['title'];

		
		$system_info = db('goods_update')->where(array('number' => $data['number']))->field(array('number','name','title','install_id'))->find();
		if(!$system_info || !$system_info['install_id']){
			return $this->error(lang('system_file_does_not_exist'));
		}
		
		$system_file_data = get_goods_file($system_info['install_id']);
		if(!$system_file_data || !$system_file_data['url'] || !file_exists('.'.$system_file_data['url'])){
			return $this->error(lang('system').lang('file_does_not_exist'));
		}
		$system_info['file_url'] = '.'.$system_file_data['url'];
		$generate_info['system'] = $system_info;
		if($data['module']){
			$smodule = explode(",",$data['module']);
	
			foreach ($smodule as $m_key=> &$m) {
				$tmp_module = explode("_",$m);
				$goods_info = db('goods')->where(array('status'=>1,'id' => $tmp_module[0]))->field(array('id','title','etitle','status'))->find();
				
				if(!$goods_info){
					return $this->error($goods_info['etitle'].lang('already_off_the_shelf'));
				}else{
					$goods_version_info = db('goods_version')->where(array('status'=>1,'id' => $tmp_module[1]))->field(array('id','title','goods_id','status','file_id','token'))->find();
					if(!$goods_version_info || !$goods_version_info['file_id']){
						return $this->error($goods_info['etitle'].lang('module').lang('installation_package_does_not_exist'));
					}else{
						
						$goods_info['version_id'] = $goods_version_info['id'];
						$goods_info['version_number'] = $goods_version_info['title'];
						$goods_info['file_id'] = $goods_version_info['file_id'];
						$goods_info['token'] = $goods_version_info['token'];
						
						
						$module_file_data = get_goods_file($goods_info['file_id']);
						if(!$module_file_data || !$module_file_data['url'] || !file_exists('.'.$module_file_data['url'])){
							return $this->error(lang('module').lang('file_does_not_exist'));
						}
						$goods_info['file_url'] = '.'.$module_file_data['url'];
					}
				}
				$generate_info['module'][$goods_info['etitle']] = $goods_info;
				
			}
		}
		
	
		
		
		$generate_info['agent_info'] = json_decode($data['agent_info'],true);
		
		$generate_info['config'] = json_decode($data['config'],true);
		
		if($data['license_id']){
			$license_data = db('license')->where(array('id'=>$data['license_id']))->find();
			$generate_info['license_data'] = $license_data;
		}
		if($generate_info['config']['ico_id']){
			$icon_file_data = get_cover($generate_info['config']['ico_id']);
			if(!$icon_file_data || !$icon_file_data['path'] || !file_exists('.'.$module_file_data['path'])){
				return $this->error(lang('ico_file').lang('file_does_not_exist'));
			}
			$generate_info['config']['ico_url'] = '.'.$icon_file_data['path'];
		}
		
		if($generate_info['config']['custom_id']){
			$custom_file_data = get_goods_file($generate_info['config']['custom_id']);
			if(!$custom_file_data || !$custom_file_data['url'] || !file_exists('.'.$custom_file_data['url'])){
				return $this->error(lang('custom_package').lang('file_does_not_exist'));
			}
			$generate_info['config']['custom_url'] = '.'.$custom_file_data['url'];
		}
		
		cache('generate_'.$generate_info['id'], $generate_info);
		
		return $this->redirect('/operate/package/generate/id/'.$generate_info['id']);
		//$this->generate_file($generate_info);
		
	}



	public function generate($id=''){
		
		if (!$id) {
			return $this->error(lang('illegal_operation'));
		}
		
		$data = cache('generate_'.$id);
		
		set_time_limit(0);
		
		
		if(!$data){
			return $this->error(lang('illegal_operation'));
		}
		
		$generate_path = './data/generate/';
		$generate_path= $generate_path.$data['id'].'/';
		$generate_temp_path= $generate_path.'system/';
		
		
		$path = $generate_path.time().'.zip';
		$this->assign('path', $path);
		$this->assign('data', $data);
		$this->setMeta('生成安装包');
		
		echo $this->fetch();
		if (!$this->deleteAll($generate_temp_path)) {
            $this->write('删除历史文件失败' . $generate_temp_path . '请检查权限', 'danger');
            return;
        }else{
			$this->write('删除历史文件' . $generate_temp_path , 'info');
		}
	
		
		if (!$this->createFolder($generate_temp_path)) {
            $this->write('创建目录失败' . $generate_temp_path . '请检查权限', 'danger');
            return;
        }else{
			$this->write('创建打包目录' . $generate_temp_path , 'info');
		}
		
		$this->write('开始解压系统文件包。');
		$this->unzipFile($data['system']['file_url'], $generate_temp_path);
		
		if($data['module']){
			foreach ($data['module'] as $m_key=> &$m) {
				$this->write('开始解压'.$m['title'].'模块文件包。');
				$this->unzipFile($m['file_url'], $generate_temp_path.'application');
				$add_module_key[] = $m_key;
			}
			
			$add_module = implode(",",$add_module_key);
			
		}
		
		if($add_module){
			$this->write('替换模块安装脚本。');
			@file_put_contents($generate_temp_path.'data/module', $add_module);
		}
		
		if($data['config']['custom_url']){
			$this->write('开始解压定制文件包。');
			$this->unzipFile($data['config']['custom_url'], $generate_temp_path);
			
		}
		
		if($data['license_data']){
			$this->write('生成授权码文件。');
			@file_put_contents($generate_temp_path.'data/license/license', $data['license_data']['activation_code']);
		}
		
		if($data['config']['ico_url']){
			$this->write('开始替换ICO图标。');
			echo copy($data['config']['ico_url'],$generate_temp_path.'favicon.ico');
		}
		
		
		$custom_config = $data['config'];
		unset($custom_config['ico_id'],$custom_config['custom_id'],$custom_config['ico_url'],$custom_config['custom_url']);
		$this->write('开始生成自定义配置文件。');
		
		$custom_html = "<?php\n";
		$custom_html .= "return [\n";
		foreach ($custom_config as $name => $value) {
			$custom_html .="	'".$name."' => '".$value."', \n";	
		}
		$custom_html .="];";
		
		$custom_path =$generate_temp_path . 'application/extra/systemconfig.php';

		if(@file_put_contents($custom_path, $custom_html)){
			$this->write('自定义配置文件写入成功' . $custom_path , 'info');
		}else{
			$this->write('自定义配置文件写入失败' . $custom_path . '请检查权限', 'danger');
		}
		
		$this->write('开始压缩系统文件包。');
		$zip = $path;
        $archive = new \PclZip($zip);
		$v_list = $archive->create($generate_temp_path,PCLZIP_OPT_REMOVE_PATH, $generate_temp_path);	
        if ($v_list == 0) {
			$this->write($archive->errorInfo(true), 'danger');
			return;
        }else{
			$this->write('文件打包成功');
		}
		
		
		$savedata['url'] = str_replace("./data","/data",$path);
		$savedata['generate_time'] = time();
		$savedata['generate'] = 1;
		$data = db('package')->where(array('id' => $data['id']))->update($savedata);
		
		cache('generate_'.$data['id'],null);
		
		$this->writeScript('enable()');
	}
    /**递归方式创建文件夹
     * @param $dir
     * @param int $mode
     * @return bool
     */
    private function createFolder($dir, $mode = 0777)
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) {
            return true;
        }
        if (!$this->createFolder(dirname($dir), $mode)) {
            return false;
        }
        return @mkdir($dir, $mode);
    }
	

	private function deleteAll($directory){//自定义函数递归的函数整个目录  
		if(file_exists($directory)){//判断目录是否存在，如果不存在rmdir()函数会出错  
			if($dir_handle=@opendir($directory)){//打开目录返回目录资源，并判断是否成功  
				while($filename=readdir($dir_handle)){//遍历目录，读出目录中的文件或文件夹  
					if($filename!='.' && $filename!='..'){//一定要排除两个特殊的目录  
						$subFile=$directory."/".$filename;//将目录下的文件与当前目录相连  
						if(is_dir($subFile)){//如果是目录条件则成了  
							$this -> deleteAll($subFile);//递归调用自己删除子目录  
						}  
						if(is_file($subFile)){//如果是文件条件则成立  
							unlink($subFile);//直接删除这个文件  
						}  
					}  
				}  
				closedir($dir_handle);//关闭目录资源  
				rmdir($directory);//删除空目录  
			}  
		}
		return true; 
	} 
	
 	private function writeMessage($str)
    {
        $js = "writeMessage('$str')";
        $this->writeScript($js);
    }

    private function goBack()
    {
        $this->writeScript("setTimeout(function(){history.go(-1);},3000);");
    }

    private function writeScript($str)
    {
        echo "<script>$str</script>";
        ob_flush();
        flush();
    }

    private function write($str, $type = 'info', $br = '<br>')
    {
        $this->writeMessage('<span class="text-'.$type.'">'.$str.'</span>'.$br);
    }
	
    /**
     * @param $localFile
     * @param $localPath
     */
    private function unzipFile($localFile, $localPath)
    {
        $archive = new \PclZip($localFile);
        $this->write('&nbsp;&nbsp;&nbsp;>开始解压文件......');
        $list = $archive->extract(PCLZIP_OPT_PATH, $localPath, PCLZIP_OPT_SET_CHMOD, 0777);
        if ($list === 0) {
            $this->write('&nbsp;&nbsp;&nbsp;>解压失败。'. $archive->errorInfo(true));
            exit;
        }
        //unlink($localFile);
        $this->write('&nbsp;&nbsp;&nbsp;>解压成功。', 'success');
    }
}