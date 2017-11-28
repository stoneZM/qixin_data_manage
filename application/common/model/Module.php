<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Db;
/**
* 设置模型
*/
class Module extends Base{
	
	protected $name = "module";
	protected $tokenFile = '/Info/token.ini';
    protected $moduleName = '';

	public $keyList = array(
		array('name'=>'id', 'title'=>'ID', 'type'=>'text', 'help'=>'', 'option'=>''),
		
	);
    /**获取全部的模块信息
     * @return array|mixed
     */
    public function getAll($is_installed = '')
    {
		
        $module = cache('module_all'.$is_installed);
        if ($module === false) {
            $dir = $this->getFile(APP_PATH);
            foreach ($dir as $subdir) {
                if (file_exists(APP_PATH . '/' . $subdir . '/Info/info.php') && $subdir != '.' && $subdir != '..') {
                    $info = $this->getModule($subdir);
                    if ($is_installed == 1 && $info['is_setup'] == 0) {
                        continue;
                    }
                    $this->moduleName = $info['name'];
                    //如果token存在的话
                    if (file_exists($this->getRelativePath($this->tokenFile))) {
                        $info['token'] = file_get_contents($this->getRelativePath($this->tokenFile));
                    }

                    $module[] = $info;

                }
            }
            cache('module_all'.$is_installed, $module);
        }
        return $module;
    }
    /**
     * 重新通过文件来同步模块
     */
    public function reload()
    {
        $modules = db('module')->select();
        foreach ($modules as $m) {
            if (file_exists(APP_PATH . '/' . $m['name'] . '/Info/info.php')) {
                $info = array_merge($m, $this->getInfo($m['name']));
                $this->update($info);
            }
        }
        $this->cleanModulesCache();
    }

    /**重置单个模块信息
     * @param $name
     */
    public function reloadModule($name)
    {
        $module = db('module')->where(array('name' => $name))->find();
        if (empty($module)) {
            $this->error = lang('module_information_does_not_exist');
            return false;
        } else {
            if (file_exists(APP_PATH . '/' . $module['name'] . '/Info/info.php')) {
                $info = array_merge($module, $this->getInfo($module['name']));
                $this->update($info);
                $this->cleanModuleCache($name);
                return true;
            }
        }
    }

    /**检查是否可以访问模块，被用于控制器初始化
     * @param $name
     */
    public function checkCanVisit($name)
    {
        $modules = $this->getAll();

        foreach ($modules as $m) {
            if (isset($m['is_setup']) && $m['is_setup'] == 0 && $m['name'] == ucfirst($name)) {
                header("Content-Type: text/html; charset=utf-8");
                exit(lang('the_module_you_are_visiting_is_not_installed_and_is_not_accessible_please_ask_the_administrator_to_install_it_in_the_background_cloud_market'));
            }
        }

    }

    /**检查模块是否已经安装
     * @param $name
     * @return bool
     */
    public function checkInstalled($name)
    {
        $modules = $this->getAll();

        foreach ($modules as $m) {
            if ($m['name'] == $name && $m['is_setup']) {
                return true;
            }
        }
        return false;
    }

    /**
     * 清理全部模块的缓存
     */
    public function  cleanModulesCache()
    {
        $modules = $this->getAll();
		if($modules){
			foreach ($modules as $m) {
            	$this->cleanModuleCache($m['name']);
        	}
		}
        
        cache('module_all', null);
        cache('admin_modules', null);
        cache('ALL_MESSAGE_SESSION',null);
        cache('ALL_MESSAGE_TPLS',null);
    }

    /**清理某个模块的缓存
     * @param $name 模块名
     */
    public function cleanModuleCache($name)
    {
        cache('common_module_' . strtolower($name), null);

    }

    /**卸载模块
     * @param $id 模块ID
     * @param int $withoutData 0.不清理数据 1.清理数据
     * @return bool
     */
    public function uninstall($id, $withoutData = 1)
    {
        $module = $this->find($id);
        if (!$module || $module['is_setup'] == 0) {
            $this->error = lang('module_does_not_exist_or_is_not_installed');
            return false;
        }
        $this->cleanMenus($module['name']);
        $this->cleanAuthRules($module['name']);
        $this->cleanAction($module['name']);
        $this->cleanActionLimit($module['name']);
        if ($withoutData == 0) {
            //如果不保留数据
            if (file_exists(APP_PATH . '/' . $module['name'] . '/Info/cleanData.sql')) {
                $uninstallSql = APP_PATH . '/' . $module['name'] . '/Info/cleanData.sql';
                $res = $this->executeSqlFile($uninstallSql);
                if ($res === false) {
                    $this->error = lang('cleanup_module_data_failed_error_message').'：' . $res['error_code'];
                    return false;
                }
            }
            //兼容老的卸载方式，执行一边uninstall.sql
            if (file_exists(APP_PATH . '/' . $module['name'] . '/Info/uninstall.sql')) {
                $uninstallSql = APP_PATH . '/' . $module['name'] . '/Info/uninstall.sql';
                $res = $this->executeSqlFile($uninstallSql);
                if ($res === false) {
                    $this->error = lang('cleanup_module_data_failed_error_message').'：' . $res['error_code'];
                    return false;
                }
            }
        }
        $save_data['is_setup'] = 0;
		$this->save($save_data,array('id'=>$module['id']));
        $this->cleanModulesCache();
        return true;
    }
    /**
     * 执行SQL文件
     * @access public
     * @param string $file 要执行的sql文件路径
     * @param boolean $stop 遇错是否停止  默认为true
     * @param string $db_charset 数据库编码 默认为utf-8
     * @return array
     */
    public function executeSqlFile($file, $stop = true, $db_charset = 'utf-8')
    {
		
		$error = true;
        if (!is_readable($file)) {
            $error = array(
                'error_code' => lang('SQL_file_is_not_readable'),
                'error_sql' => '',
            );
            return $error;
        }
		
		$sql = file_get_contents($file);
		$sql = str_replace("\r", "\n", $sql);
		$sql = explode(";\n", $sql);
		
		
		
		//替换表前缀
		$orginal = 'qinfo_';
		$replace_prefix = config('database.prefix');
		$sql = str_replace(" `{$orginal}", " `{$replace_prefix}", $sql);
	 	foreach ($sql as $value) {
		
			$value = trim($value);
			if(empty($value)) continue;
			if(false !== Db::execute($value)){	
			} else {
				$error[] = array(
					'error_code' => lang('fail'),
					'error_sql' => $value,
				);
				if ($stop) return $error;	
			}
		}
		
		return $error;
    } 
    /**通过模块名来获取模块信息
     * @param $name 模块名
     * @return array|mixed
     */
    public function getModule($name)
    {
		$module = $this->where(array('name' => strtolower($name)))->find();
        if ($module === false || $module == null) {
            $m = $this->getInfo($name);
            if ($m != array()) {
                if (intval($m['can_uninstall']) == 1) {
                    $m['is_setup'] = 0;//默认设为已安装，防止已安装的模块反复安装。
                } else {
                    $m['is_setup'] = 1;
                }
                $m['id'] = $this->insertGetId($m);
                $m['token'] = $this->getToken($m['name']);
                return $m;
            }
        } else {
			$module = $module->toArray();
            $module['token'] = $this->getToken($module['name']);
            return $module;
        }
    }

    /**获取模块的token
     * @param $name 模块名
     * @return string
     */
    public function getToken($name)
    {
        $this->moduleName = $name;
        if (file_exists($this->getRelativePath($this->tokenFile))) {
            $token = file_get_contents($this->getRelativePath($this->tokenFile));
        }
        return $token;
    }

    /**设置模块的token
     * @param $name 模块名
     * @param $token Token
     * @return string
     */
    public function setToken($name, $token)
    {
        $this->moduleName = $name;
        @chmod($this->getRelativePath($this->tokenFile), 0777);
        $result = file_put_contents($this->getRelativePath($this->tokenFile), $token);
        @chmod($this->getRelativePath($this->tokenFile), 0777);
        return $result;
    }

    /**通过ID获取模块信息
     * @param $id
     * @return array|mixed
     */
    public function getModuleById($id)
    {
        $module = $this->where(array('id' => $id))->find();
        if ($module === false || $module == null) {
            $m = $this->getInfo($module['name']);
            if ($m != array()) {
                if ($m['can_uninstall']) {
                    $m['is_setup'] = 0;//默认设为已安装，防止已安装的模块反复安装。
                } else {
                    $m['is_setup'] = 1;
                }
                $m['id'] = $this->insertGetId($m);
                $m['token'] = $this->getToken($m['name']);
                return $m;
            }

        } else {
			$module = $module->toArray();
			
            $module['token'] = $this->getToken($module['name']);
            return $module;
        }
    }


    /**检查某个模块是否已经是安装的状态
     * @param $name
     * @return bool
     */
    public function isInstalled($name)
    {
        $module = $this->getModule($name);
        if ($module['is_setup']) {
            return true;
        } else {
            return false;
        }
    }

    /**安装某个模块
     * @param $id
     * @return bool
     */
    public function install($id)
    {
        $log = '';
        if ($id != 0) {
            $module = $this->find($id);
        } else {
            $aName = input('name', '');
            $module = $this->getModule($aName);
        }
        if ($module['is_setup'] == 1) {
            $this->error = lang('module_has_been_installed');
            return false;
        }
        if (file_exists(APP_PATH . '/' . $module['name'] . '/Info/guide.json')) {
            //如果存在guide.json
            $guide = file_get_contents(APP_PATH . '/' . $module['name'] . '/Info/guide.json');
            $data = json_decode($guide, true);

            //导入菜单项,menu
            $menu = json_decode($data['menu'], true);
            if (!empty($menu)) {
                $this->cleanMenus($module['name']);
                if ($this->addMenus($menu[0],$menu[0]['pid']) == true) {
                    $log .= '&nbsp;&nbsp;>'.lang('menu_installation_success').';<br/>';
                }
            }

            //导入前台权限,auth_rule
            $auth_rule = json_decode($data['auth_rule'], true);
            if (!empty($auth_rule)) {
                $this->cleanAuthRules($module['name']);
                if ($this->addAuthRule($auth_rule)) {
                    $log .= '&nbsp;&nbsp;>'.lang('permission_to_import_success').'。<br/>';
                }
                //设置默认的权限
                $default_rule = json_decode($data['default_rule'], true);
				if (!empty($default_rule)) {
					
					
					$default_group = json_decode($data['default_group'], true);
					
					if ($this->addDefaultRule($default_rule, $module['name'],$default_group)) {
						$log .= '&nbsp;&nbsp;>'.lang('default_permissions_set_successfully').'。<br/>';
					}
				}
            }

            //导入
            $action = json_decode($data['action'], true);
            if (!empty($action)) {
                $this->cleanAction($module['name']);
                if ($this->addAction($action)) {
                    $log .= '&nbsp;&nbsp;>'.lang('successful_import_behavior').'。<br/>';
                }
            }
            $action_limit = json_decode($data['action_limit'], true);
            if (!empty($action_limit)) {
                $this->cleanActionLimit($module['name']);
                if ($this->addActionLimit($action_limit)) {
                    $log .= '&nbsp;&nbsp;>'.lang('behavior_restrictions_import_success').'。<br/>';
                }
            }
            if (file_exists(APP_PATH . '/' . $module['name'] . '/Info/install.sql')) {
                $install_sql = APP_PATH . '/' . $module['name'] . '/Info/install.sql';
                if ($this->executeSqlFile($install_sql) === true) {
                    $log .= '&nbsp;&nbsp;>'.lang('module_data_to_add_success').'。';
                }
            }
        }
        $save_data['is_setup'] = 1;
        $rs = $this->save($save_data,array('id'=>$module['id']));
        if ($rs === false) {
            $this->error = lang('module_information_modification_failed');
            return false;
        }
        $this->cleanModulesCache();//清除全站缓存
        $this->error = $log;
        return true;
    }



    /*——————————————————————————私有域—————————————————————————————*/

    /**获取模块的相对目录
     * @param $file
     * @return string
     */
    private function getRelativePath($file)
    {
        return APP_PATH . $this->moduleName . $file;
    }

    private function addDefaultRule($default_rule, $module_name,$authgroups = '')
    {
		
        foreach ($default_rule as $v) {
            $rule = db('AuthRule')->where(array('module' => $module_name, 'name' => $v))->find();
            if ($rule) {
                $default[] = $rule;
            }
        }
        $auth_id = getSubByKey($default, 'id');
        if ($auth_id) {
			if (!empty($authgroups)) {
				$map = 'id in ('.implode(",",$authgroups).')';
			}else{
				$map = '';
			}
            $groups = db('AuthGroup')->where($map)->select();
            foreach ($groups as $g) {
                $old = explode(',', $g['rules']);
                $new = array_merge($old, $auth_id);
                $g['rules'] = implode(',', $new);
                model('AuthGroup')->where('id',$g['id'])->update($g);
            }
        }
        return true;
    }

    private function addAction($action)
    {
        foreach ($action as $v) {
            unset($v['id']);
            model('Action')->insert($v);
        }
        return true;
    }

    private function addActionLimit($action)
    {
        foreach ($action as $v) {
            unset($v['id']);
            model('ActionLimit')->insert($v);
        }
        return true;
    }

    private function addAuthRule($auth_rule)
    {
        foreach ($auth_rule as $v) {
            unset($v['id']);
            model('AuthRule')->insert($v);
        }
        return true;
    }

    private function cleanActionLimit($module_name)
    {
        $db_prefix = config('database.prefix');
        $sql = "DELETE FROM `{$db_prefix}action_limit` where `module` = '" . $module_name . "'";
        Db::execute($sql);
    }

    private function cleanAction($module_name)
    {
        $db_prefix = config('database.prefix');
        $sql = "DELETE FROM `{$db_prefix}action` where `module` = '" . $module_name . "'";
        Db::execute($sql);
    }

    private function cleanAuthRules($module_name)
    {
        $db_prefix = config('database.prefix');
        $sql = "DELETE FROM `{$db_prefix}auth_rule` where `module` = '" . $module_name . "'";
        Db::execute($sql);
    }

    private function cleanMenus($module_name)
    {
		$db_prefix = config('database.prefix');
        $sql = "DELETE FROM `{$db_prefix}menu` where `url` like '" . $module_name . "/%'";
        Db::execute($sql);
    }

    private function addMenus($menu, $pid = 0)
    {
		$ins_menu = $menu;
        $ins_menu['pid'] = $pid;
        unset($ins_menu['id']);
		unset($ins_menu['_']);
        model('Menu')->insert($ins_menu);
		$id = model('Menu')->getLastInsID();
       // $menu['id'] = $id;
        if (!empty($menu['_']))
            foreach ($menu['_'] as $v) {
                $this->addMenus($v, $id);
            }
        return true;
    }


    private function getInfo($name)
    {
        if (file_exists(APP_PATH . '/' . $name . '/Info/info.php')) {
            $module = require(APP_PATH . '/' . $name . '/Info/info.php');
            return $module;
        } else {
            return array();
        }

    }

    /**
     * 获取文件列表
     */
    private function getFile($folder)
    {
        //打开目录
        $fp = opendir($folder);
        //阅读目录
        while (false != $file = readdir($fp)) {
            //列出所有文件并去掉'.'和'..'
            if ($file != '.' && $file != '..' ) {
                //$file="$folder/$file";
				if(is_dir($folder.$file)){
					$file = "$file";
					//赋值给数组
					$arr_file[] = $file;	
				}
            }
        }
        //输出结果
        if (is_array($arr_file)) {
            while (list($key, $value) = each($arr_file)) {
                $files[] = $value;
            }
        }
        //关闭目录
        closedir($fp);
        return $files;
    }

	public function info($id, $field = true){
		$module = $this->where(array('id'=>$id))->field($field)->find();
		if($module){
			$module = $module->toArray();
		}
		return $module;
	}
}