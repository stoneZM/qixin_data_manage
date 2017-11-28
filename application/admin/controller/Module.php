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

class Module extends Admin {

	public function _initialize() {
		parent::_initialize();
		$this->moduleModel = model('Module');
	}

	/**
	 * 模型管理首页
	 * @author huajie <banhuajie@163.com>
	 */
	public function index() {
		
		$aType = input('type', 'installed');
        $this->assign('type', $aType);
		
        /*刷新模块列表时清空缓存*/
        $aRefresh = input('refresh', 0);
//        if ($aRefresh == 1) {
////            cache('admin_modules', null);
//            $this->moduleModel->reload();
////            cache('admin_modules', null);
//        } else if ($aRefresh == 2) {
////            cache('admin_modules', null);
//            $this->moduleModel->cleanModulesCache();
//        }
        /*刷新模块列表时清空缓存 end*/
//
//        $modules = cache('admin_modules');
//        if ($modules == false) {
//			$modules = $this->moduleModel->getAll();
//			$config_updata = config('systemconfig.is_updata');
//			if($config_updata === null){
//				$is_os_updata = true;
//			}else if($config_updata === 1){
//				$is_os_updata = true;
//			}else{
//				$is_os_updata = false;
//			}
//			if ( model('Cloud')->checkurl() && $is_os_updata) {
//				$modules = model('Cloud')->getVersionInfoList($modules);
//			}
//            cache('admin_modules', $modules);
//        }
		$modules = $this->moduleModel->getAll();
		$available_module = get_license()['config_info']['module'];
		if($modules){
			foreach ($modules as $key => $m) {
				// 更具license 过滤模块
				if(!in_array($m['name'],$available_module)){
					 unset($modules[$key]);
					 continue;
				}
				switch ($aType) {
					case 'all':
						break;
					case 'installed':
						if ($m['can_uninstall'] && $m['is_setup']) {
						} else unset($modules[$key]);
						break;
					case 'uninstalled':
						if ($m['can_uninstall'] && $m['is_setup'] == 0) {
						} else unset($modules[$key]);
						break;
					case 'core':
						if ($m['can_uninstall'] == 0) {
						} else unset($modules[$key]);
						break;
				}
			}
		}

        $this->assign('modules', $modules);
		$this->setMeta(lang('module_list'));
		return $this->fetch();
	}


    /**
     * 编辑模块
     */
    public function edit()
    {
        if (IS_POST) {
            $aName = input('name', '');
            $id = input('id', 0);
            $module['name'] = empty($aName) ? $this->error(lang('identifying').lang('cannot_be_empty')) : $aName;
			
            $aAlias = input('alias', '');
            $module['alias'] = empty($aAlias) ? $this->error(lang('module_name').lang('cannot_be_empty')) : $aAlias;
            $aIcon = input('icon', '');
            $module['icon'] = empty($aIcon) ? $this->error(lang('icon').lang('cannot_be_empty')) : $aIcon;
            $aSummary = input('summary', '');
            $module['summary'] = empty($aSummary) ? $this->error(lang('description').lang('cannot_be_empty')) : $aSummary;
            $module['title'] = input('name', '');
            $module['menu_hide'] = input('menu_hide', 0);
            $aToken = input('token', '');
            $aToken = trim($aToken);
            if ($aToken != '') {
                if ($this->moduleModel->setToken($module['name'], $aToken)) {
                    $tokenStr = 'Token'.lang('write_in').lang('success');
                } else {
                    $tokenStr = 'Token'.lang('write_in').lang('fail');
                }
            }
            if ($this->moduleModel->save($module,array('id'=>$id)) === false) {
                $this->error(lang('edit').lang('fail') . $tokenStr);
            } else {
                $this->moduleModel->cleanModuleCache($aName);
                $this->moduleModel->cleanModulesCache();
                $this->success(lang('edit').lang('success') . $tokenStr,url('index'));
            }
        } else {
            $aName = input('name', '');
			
            $module = $this->moduleModel->getModule($aName);
            $module['token'] = $this->moduleModel->getToken($module['name']);
            !isset($module['menu_hide']) && $module['menu_hide'] = 0;
			$this->assign('info',$module);
			$this->setMeta(lang('edit').lang('module'));
			return $this->fetch();
        }

    }
    public function import_module()
    {
        if (IS_POST) {
           
		   
			$file_id = input('file_id');
			
			if(!$file_id){
			   $this->error(lang('please_upload_module_installation_package'));
			}
			
			$file_info = db('file')->where(array('id' => $file_id))->find();
			$file_url = '.'.$file_info['url'];
			$localPath = config('CLOUD_PATH').'Install/'.time().'/';
			//开始安装
			$this->unzipFile($file_url, $localPath);
		   
			$filenames = scandir($localPath);
			$dirpath = ($localPath);
			foreach ($filenames as $filename) {
				if ($filename == '.' || $filename == '..') {
					continue;
				}
				$module_name = $filename;
				$file_path = $dirpath  . $filename;
			}
			
			//todo 进行文件合法性检测，防止错误安装。
			if (!file_exists($localPath . $module_name . '/' . 'Info/info.php')) {
				$this->deleteAll($localPath);
				//todo 进行版本检测
				$this->error('文件验证失败，无法执行导入，请检查文件结构');
				return false;
			}else{
				$updata_module = require($localPath . $module_name . '/' . 'Info/info.php');
			}
			
			if (file_exists(APP_PATH . $module_name)) {
				//todo 进行版本检测
				$this->error('本地已存在同名模块，导入被强制终止。请卸载并删除本地模块文件之后重试');
				return false;
			}
			$this->unzipFile($file_url, APP_PATH);
			$this->deleteAll($localPath);
      		$this->moduleModel->reload();
			$this->success(lang('import').lang('success'), url('index'));
        } else {
		    $this->setMeta(lang('import') .lang('module'));
			return $this->fetch();
		   
        }
    }	

    public function manual_upgrade()
    {
        if (IS_POST) {
           
		   
			$file_id = input('file_id');
			
			$aid = input('id');
			
			if(!$aid){
			 	return $this->error(lang('parameter_error'));
			}
			
			if(!$file_id){
			   $this->error(lang('please_upload_module_installation_package'));
			}
			
			$current_module = $this->moduleModel->getModuleById($aid);
			
			if($current_module['is_setup']==0){
			   $this->error('请安装模块后再进行手动升级');
			}
			
			$file_info = db('file')->where(array('id' => $file_id))->find();
			$file_url = '.'.$file_info['url'];
			$localPath = config('CLOUD_PATH').'Update/'.time().'/';
			//开始安装
			$this->unzipFile($file_url, $localPath);
		   
			$filenames = scandir($localPath);
			$dirpath = ($localPath);
			foreach ($filenames as $filename) {
				if ($filename == '.' || $filename == '..') {
					continue;
				}
				$module_name = $filename;
				$file_path = $dirpath  . $filename;
			}
			
			if(!$module_name || $module_name != $current_module['name']){
				$this->deleteAll($localPath);
				$this->error('上传包不符合当前模块');
			}
			//todo 进行文件合法性检测，防止错误安装。
			if (!file_exists($localPath . $module_name . '/' . 'Info/info.php')) {
				$this->deleteAll($localPath);
				//todo 进行版本检测
				$this->error('文件验证失败，无法执行导入，请检查文件结构');
				return false;
			}else{
				$updata_module = require($localPath . $module_name . '/' . 'Info/info.php');
			}
			
			$versioncompare = version_compare($updata_module['version'], $current_module['version']);
			if($versioncompare == -1){
				$this->deleteAll($localPath);
				$this->error('上传的模块('.$current_module['alias'].')版本小于当前模块版本，升级被终止。');
			}elseif($versioncompare == 0){
				$this->deleteAll($localPath);
				$this->error('上传的模块('.$current_module['alias'].')版本和当前模块一样，升级被终止。');
			}
			
			if($updata_module['previous_version'] != $current_module['version']){
				$this->deleteAll($localPath);
				$this->error('不可跨级升级，请先升级到【'.$updata_module['previous_version'].'】版本');
			}
			$this->unzipFile($file_url, APP_PATH);
			
			$sql_path = $localPath . $module_name . '/update.sql';
			if (file_exists($sql_path)) {
                $result = model('Module')->executeSqlFile($sql_path);
            }
			
			$this->deleteAll($localPath);
      		$this->moduleModel->reload();
			$this->success(lang('manual_upgrade').lang('success'), url('index'));
        } else {
		    $this->setMeta(lang('manual_upgrade').'-'.lang('import') .lang('module'));
			return $this->fetch();
		   
        }
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
	
    /**
     * @param $localFile
     * @param $localPath
     */
    private function unzipFile($localFile, $localPath)
    {
        $archive = new \PclZip($localFile);
      //  $this->write('&nbsp;&nbsp;&nbsp;>开始解压文件......');
        $list = $archive->extract(PCLZIP_OPT_PATH, $localPath, PCLZIP_OPT_SET_CHMOD, 0777);
        if ($list === 0) {
		  $this->error('解压失败，'. $archive->errorInfo(true));
          exit;
        }
       // unlink($localFile);
       // $this->write('&nbsp;&nbsp;&nbsp;>解压成功。', 'success');
    }
	
    public function install()
    {
        $aName = input('name', '');
        $module = $this->moduleModel->getModule($aName);

        if (IS_POST) {
            //执行guide中的内容
            $res = $this->moduleModel->install($module['id']);
            if ($res == true) {
                $this->success(lang('install').lang('success'), url('/admin/module/index',array('type'=>'installed')));
            } else {
                $this->error(lang('install').lang('fail') . $this->moduleModel->error);
            }
        } else {
            $module['mode'] = 'install';
            $module['add_nav'] = '1';
			$this->assign('info',$module);
		    $this->setMeta(lang('module') .' - '.lang('install'));
			return $this->fetch();
		   
        }
    }
	
    public function uninstall()
    {
        $aId = input('id', 0);
        $module = $this->moduleModel->getModuleById($aId);
        if (IS_POST) {
            $aWithoutData = input('withoutData', 1);//是否保留数据
            $res = $this->moduleModel->uninstall($aId, $aWithoutData);
            if ($res == true) {
                if (file_exists(APP_PATH . '/' . $module['name'] . '/Info/uninstall.php')) {
                    require_once(APP_PATH . '/' . $module['name'] . '/Info/uninstall.php');
                }
                cache('admin_modules', null);
                $this->success(lang('uninstall').lang('success'),url('/admin/module/index',array('type'=>'installed')));
            } else {
                $this->error(lang('uninstall').lang('fail') . $this->moduleModel->error);
            }

        }else{
			$module['withoutData'] = 1;
			$this->assign('info',$module);
		    $this->setMeta(lang('module') .' - '.lang('uninstall'));
			return $this->fetch();
		}
    }
	
    public function del()
    {

			$aId = input('id', 0);
			$module = $this->moduleModel->getModuleById($aId);
			
			if($module['can_uninstall'] == 0){
				$this->error('系统模块，禁止删除');
			}
			if($module['is_setup'] == 1){
				$this->error('模块运行中，请卸载后进行物理文件删除');
			}
			$result = db('module')->where(array('id'=>$aId))->delete();
			cache('admin_modules', null);
			if(!$result){
				$this->error(lang('delete').lang('fail'));
			}else{
				
				
				$localPath = APP_PATH . $module['name'];
				$this->deleteAll($localPath);
				
				$this->moduleModel->reload();
				
				
				$this->success(lang('delete').lang('success'));
			}
    }
}