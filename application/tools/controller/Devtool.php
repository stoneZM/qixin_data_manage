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

class Devtool extends Admin {


	public function _initialize() {
		parent::_initialize();
		$this->refreshSession();
	}
    private function refreshSession()
    {
		if(session('module')){
			$name = session('module');
		}else{
			$name = 'index';
		}
        $module = model('Module')->getModule($name);
		$this->module = $module;
        $this->assign('module', $module);
    }

    public function module()
    {
        $modules = model('Module')->getAll();
        foreach ($modules as $key => $v) {
            if ($v['is_setup'] && $v['can_uninstall']==1) {
                continue;
            }
            unset($modules[$key]);
        }
        $this->assign('modules', $modules);
        session('guide_menus',null);
        session('guide_default_rule',null);
		session('guide_default_group',null);
        session('guide_auth_rule',null);
        session('guide_action',null);
        session('guide_action_limit', null);
        session('guide_sql_tables', null);
        session('guide_sql_rows',null);
		$this->setMeta('模块打包向导');
		return $this->view->fetch();
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
				
					$file = "$file";
					//赋值给数组
					$arr_file[] = $file;	
				
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
	
	
	
    public function module1()
    {
        if (input('module', '') != '') {
           session('module',input('module', ''));
        }
		$menutype = input('menutype', '');
		$Whitelist = array('cms','mp','cdp','move','recover');
		
		$menuWhitelist = array('cdp','move','recover','remote');
        $this->refreshSession();
		if($menutype == 1){
			$menus = $this->getSubMenus();
		}else{
			$menus = $this->getSubMenus(0);
		}
        $all_menus = db('Menu')->where(array('module' => $this->module['name']))->select();
        $this->assign('menus', $menus);
        $controller_name = $this->module['name'];
        $path = APP_PATH . $controller_name . '/controller/';
		$dir = $this->getFile($path);
        if ($dir) {
			foreach ($all_menus as &$v) {
				$v['url'] = strtolower($v['url']);
			}
			unset($v);
			$all_menus_url = getSubByKey($all_menus, 'url');
			
            foreach ($dir as $subdir) {
				$sub_path = APP_PATH . $controller_name . '/controller/'. $subdir;
                if (file_exists($sub_path) && $subdir != '.' && $subdir != '..') {
					$temp = substr($subdir, strrpos($subdir, '.')+1); 
					if ($temp == 'php')   
					{
						$classname = substr($subdir,0,strripos($subdir,'.'));  //获取文件名
						require_once($sub_path);
						$controller_class = $controller_name.'/'.$classname;
						if($controller_class !== 'index/Index' && $controller_name != 'index' &&  !in_array($controller_name, $Whitelist)){
							$controller = controller($controller_class);
							$methods = $this->get_class_all_methods($controller);
							if($methods){
								foreach ($methods as $m) {
									if (!in_array(strtolower($this->module['name'] . '/' .strtolower($classname) . '/' . $m['name']), $all_menus_url)) {
										$havent_created[] = $m;
									}
								}
							}
						}
					}
                }	
            }
            $this->assign('havent_created', $havent_created);
            $this->assign('created', 1);
        }
        return $this->view->fetch();
    }
	
	
	
	
    public function module2()
    {
        $menus = input('menus', '');
        if ($menus != ''){
			 session('guide_menus',$menus);
		}
        $rules = db('Auth_rule')->where(array('module' => $this->module['name'], 'status' => 1))->select();
		if(empty($rules)){
			$rules = '';
		}
		
		$Authgroup = db('Auth_group')->where(array('status' => 1))->select();
		$this->assign('authgroup', $Authgroup);
        $this->assign('rules', $rules);
       	return $this->view->fetch();
    }


    public function module3()
    {
		$default = input('default/a', '');
        if ($default != '') {
			session('guide_default_rule',json_encode($default));
        }
		
		$default_group = input('default_group/a', '');
        if ($default_group != '') {
			session('guide_default_group',json_encode($default_group));
        }
		
		
        $auth_rule = input('auth_rule');
        if ($auth_rule != '') {
			session('guide_auth_rule',$auth_rule);
        }
		
        $action = db('Action')->where(array('module' => $this->module['name'], 'status' => 1))->select();
		if(empty($action)){
			$action = '';
		}
		$this->assign('action', $action);
        $action_limit = db('ActionLimit')->where(array('module' => $this->module['name'], 'status' => 1))->select();
		if(empty($action_limit)){
			$action_limit = '';
		}
        $this->assign('action_limit', $action_limit);
		
		return $this->view->fetch();
    }

    public function module4()
    {
        $action = input('action', '');
        if ($action) {
			session('guide_action',$action);
        }
        $action_limit = input('action_limit', '');
        if ($action_limit) {
			session('guide_action_limit',$action_limit);
        }

        $Db = \think\Db::connect();
        $list = $Db->query('SHOW TABLE STATUS');
		
        $list = array_map('array_change_key_case', $list);
        $db_prefix =config('database.prefix');
        $p = $db_prefix . strtolower($this->module['name']);
		$all_table = $list;
        foreach ($list as $key => $v) {
            if (stripos(trim($v['name']), trim($p)) === false) {
                unset($list[$key]);
            } else {
                $this->sql = '';
                $this->backup_table($v['name'], 1);
                $sql_table .= $this->sql;
                $sql_drop_table .= $this->backup_drop_table($v['name']);
                if ($v['rows'] > 0) {
                    $this->sql = '';
                    $this->backup_table($v['name'], 2);
                    $sql_rows .= $this->sql;
                    $has_data[] = $v;
                }
				
				$check_table[] = $v['name'];
				
            }
        }
		foreach ($all_table as $key_a=>  &$v) {
			 if($check_table && in_array($v['name'],$check_table)){
				 $v['checked'] = 'checked';
			 }else{
				 $v['checked'] = '';
			 }
        }
		$this->assign('all_table', $all_table);		
        $this->assign('tables', $list);
        $this->assign('sql_tables', $sql_table);
        $this->assign('sql_drop_tables', $sql_drop_table);
        $this->assign('sql_rows', $sql_rows);
        $this->assign('has_data', $has_data);
		return $this->view->fetch();
    }

    public function module5()
    {
        $sql_table = input('sql_tables', '');
        $sql_drop_table = input('sql_drop_table', '');
        $sql_rows = input('sql_rows', '');
        if ($sql_table) {
			session('guide_sql_tables',$sql_table);
        }else{
			session('guide_sql_tables',null);
		}
        if ($sql_table) {
			session('guide_sql_drop_table',$sql_drop_table);
        }else{
			session('guide_sql_drop_table',null);
		}
        if ($sql_rows) {
			session('guide_sql_rows',$sql_rows);
        }else{
			session('guide_sql_rows',null);
		}
        $guide = $this->getGuideContent();
        $this->assign('guide', $guide);
        $install = $this->getInstallContent();
        $this->assign('install', $install);
        $this->assign('cleanData', $sql_drop_table);
		return $this->view->fetch();
    }

    public function replace()
    {

        if (chmod(APP_PATH . $this->module['name'] . '/Info', 0777)) {
			
            $dir = 'Application/' . $this->module['name'] . '/Info';
			
			
			if(file_exists($dir . '/install.sql')){
				if (!rename($dir . '/install.sql', $dir . '/install.sql.bk')) {
					$info .= '备份install.sql失败';
				}
			}
			
			if(file_exists($dir . '/guide.json')){
				if (!rename($dir . '/guide.json', $dir . '/guide.json.bk')) {
					$info .= '备份guide_json失败';
				}
			}
			if(file_exists($dir . '/cleanData.sql')){
				if (!rename($dir . '/cleanData.sql', $dir . '/cleanData.sql.bk')) {
					$info .= '备份cleanData.sql失败';
				}
			}
            if (!file_put_contents($dir . '/guide.json', json_encode($this->getGuideContent()))) {
                $info .= '写入guide.json失败';
            }
            if (!file_put_contents($dir . '/install.sql', $this->getInstallContent())) {
                $info .='写入install.sql失败';
            }
            if (!file_put_contents($dir . '/cleanData.sql', session('guide_sql_drop_table'))) {
                $info .= '写入cleanData.sql失败';
            }
        } else {
            $this->error('替换失败，权限不足');
        }
		if($info){
			$info = '('.$info.')';
		}
        $this->success('完成替换'.$info);
    }


    public function download()
    {

        $zip = 'uploads/tmp/' . $this->module['name'] . '.zip';
        $file_name = $this->module['name'] . '.zip';
        $archive = new \PclZip($zip);
        file_put_contents('uploads/tmp/guide.json', json_encode($this->getGuideContent()));
        file_put_contents('uploads/tmp/install.sql', $this->getInstallContent());
        file_put_contents('uploads/tmp/cleanData.sql',session('guide_sql_drop_table'));
        $v_list = $archive->create('uploads/tmp/guide.json,uploads/tmp/install.sql,uploads/tmp/cleanData.sql',
            PCLZIP_OPT_REMOVE_PATH, 'uploads/tmp',
            PCLZIP_OPT_ADD_PATH, 'Application/' . $this->module['name'] . '/Info');
        if ($v_list == 0) {
            die("Error : " . $archive->errorInfo(true));
        }
        header("Content-Description: File Transfer");
        header('Content-type: ' . 'zip');
        header('Content-Length:' . filesize($zip));
        if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
            header('Content-Disposition: attachment; filename="' . rawurlencode($file_name) . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
        }
        readfile($zip);

    }

    public function backup_rows()
    {
        $tables = input('tables/a','');
		if($tables){
		foreach ($tables as $v) {
            $this->backup_table($v, 2);
        }	
		}
        echo $this->sql;
    }
	
    public function backup_tables()
    {
        $default = input('default/a');
		if($default){
			$tables_sql = '';
			$drop_table_sql='';
			$insert_sql = '';
			$db = \think\Db::connect();
			
			
			$tablelist = $db->query('SHOW TABLE STATUS');
			$tablelist = array_map('array_change_key_case', $tablelist); 
			
			foreach ($default as $v) {
				$this->sql = '';
                $this->backup_table($v, 1);
                $tables_sql .= $this->sql;
				
				$this->sql = '';
                $this->backup_table($v, 2);
                $insert_sql .= $this->sql;
				
				$this->sql = '';
				$drop_table_sql .= $this->backup_drop_table($v);	
				foreach ($tablelist as $tkey => $t) {
					if (strtolower(trim($t['name'])) == strtolower(trim($v))) {
						$tablestructure[] = $t;
					}
				}	
			}

		}
		
		$data['tables_sql'] = $tables_sql;
		$data['insert_sql'] = $insert_sql;
		$data['drop_table_sql'] = $drop_table_sql;
		$data['tablestructure'] = $tablestructure;
		return json($data); 
    }
	
	

    private function write($sql)
    {
        $this->sql .= $sql;
    }

    private function backup_drop_table($name)
    {
        return "DROP TABLE IF EXISTS `{$name}`;\n";
    }

    /**
     * @param int $type 备份类型，1:table,2:row,3:all
     */
    private function backup_table($table, $type = 1, $start = 0)
    {
        $db = \think\Db::connect();
        switch ($type) {
            case 1:
                if (0 == $start) {
                    $result = $db->query("SHOW CREATE TABLE `{$table}`");
                    $sql = "\n";
                    $sql .= "-- -----------------------------\n";
                    $sql .= "-- 表结构 `{$table}`\n";
                    $sql .= "-- -----------------------------\n";
                    //$sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                    $sql .= str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', trim($result[0]['Create Table']) . ";\n\n");
                    if (false === $this->write($sql)) {
                        return false;
                    }
                }

                //数据总数
                $result = $db->query("SELECT COUNT(*) AS count FROM `{$table}`");
                $count = $result['0']['count'];


                break;
            case 2:
                

                //备份数据记录
                $result = $db->query("SELECT * FROM `{$table}` LIMIT {$start}, 1000");
				if($result){
					//写入数据注释
					if (0 == $start) {
						$sql = "-- -----------------------------\n";
						$sql .= "-- 表内记录 `{$table}`\n";
						$sql .= "-- -----------------------------\n";
						
					}
					$this->write($sql);
				}
				
				
                foreach ($result as $row) {
                    $row = array_map('addslashes', $row);
                    $sql = "INSERT INTO `{$table}` VALUES ('" . str_replace(array("\r", "\n"), array('\r', '\n'), implode("', '", $row)) . "');\n";
                    if (false === $this->write($sql)) {
                        return false;
                    }
                }

                break;
            case 3:
                if (0 == $start) {
                    $result = $db->query("SHOW CREATE TABLE `{$table}`");
                    $sql = "\n";
                    $sql .= "-- -----------------------------\n";
                    $sql .= "-- Table structure for `{$table}`\n";
                    $sql .= "-- -----------------------------\n";
                    $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                    $sql .= trim($result[0]['Create Table']) . ";\n\n";
                    if (false === $this->write($sql)) {
                        return false;
                    }
                }

                //数据总数
                $result = $db->query("SELECT COUNT(*) AS count FROM `{$table}`");
                $count = $result['0']['count'];

                //备份表数据
                if ($count) {
                    //写入数据注释
                    if (0 == $start) {
                        $sql = "-- -----------------------------\n";
                        $sql .= "-- Records of `{$table}`\n";
                        $sql .= "-- -----------------------------\n";
                        $this->write($sql);
                    }

                    //备份数据记录
                    $result = $db->query("SELECT * FROM `{$table}` LIMIT {$start}, 1000");
                    foreach ($result as $row) {
                        $row = array_map('addslashes', $row);
                        $sql = "INSERT INTO `{$table}` VALUES ('" . str_replace(array("\r", "\n"), array('\r', '\n'), implode("', '", $row)) . "');\n";
                        if (false === $this->write($sql)) {
                            return false;
                        }
                    }

                    //还有更多数据
                    if ($count > $start + 1000) {
                        return array($start + 1000, $count);
                    }
                }

                break;
        }
    }

    private function get_class_all_methods($class)
    {
        $r = new \ReflectionClass($class);
        foreach ($r->getmethods() as $key => $methodobj) {
            if ($methodobj->isPublic() && $methodobj->class == $r->getName() && !in_array($methodobj->getName(), array('_initialize'))) {
                $methods[$key]['type'] = 'public';
                $methods[$key]['name'] = $methodobj->name;
                $methods[$key]['class'] = $methodobj->class;
				$methods[$key]['classname'] = $r->getShortName();
            }
        }
        return $methods;
    }

    private function getSubMenus($pid='')
    {
		if(is_integer($pid)){
			$menus = db('Menu')->where(array('module' => $this->module['name'], 'pid' => $pid))->select();
		}else{
			$menus = db('Menu')->where(array('module' => $this->module['name']))->order('id asc')->select();
			if ($menus){
				$c_pid = $menus[0]['id'];
				foreach ($menus as $key => &$m) {
					
					if($m['pid'] == $c_pid){
						unset($menus[$key]);
					}	
				}
			}
		}

        if ($menus == null) {
            return;
        } else {
            foreach ($menus as &$m) {
                $m['_'] = $this->getSubMenus($m['id']);
            }
        }
        return $menus;
    }

    /**
     * @param $guide
     * @return mixed
     */
    private function getGuideContent($guide = '')
    {
        $guide['menu'] = session('guide_menus');
        $guide['default_rule'] = session('guide_default_rule');
		$guide['default_group'] = session('guide_default_group');
        $guide['auth_rule'] = session('guide_auth_rule');
        $guide['action'] = session('guide_action');
        $guide['action_limit'] = session('guide_action_limit');
        return $guide;
    }

    /**
     * @return string
     */
    private function getInstallContent()
    {
        $install = session('guide_sql_tables') . session('guide_sql_rows');
        return $install;
    }

	
}