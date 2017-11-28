<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\install\controller;

class Install extends \think\Controller {

    protected $status;

    public function _initialize() {
        $this->status = array(
            'index'    => 'info',
            'check'    => 'info',
            'config'   => 'info',
            'sql'      => 'info',
            'module'      => 'info',
            'complete' => 'info',
        );

        if (request()->action() != 'complete' && is_file(APP_PATH . 'install.lock')) {
            return $this->redirect('index/index/index');
        }
    }


    // 脚本执行安装程序
    public function install(){
        $admin[] = 'admin';
        $admin[] = "admin";
        $threeadmin[] = 123456;
        $threeadmin[] = 123456;
        $threeadmin[] = 123456;
        $result = $this->config($admin,$threeadmin);
        return json($result);

    }

    public function check() {
        session('error', false);

        //环境检测
        $env = check_env();

        //目录文件读写检测
        if (IS_WRITE) {
            $dirfile = check_dirfile();
            $this->assign('dirfile', $dirfile);
        }
        //函数检测
        $func = check_func();
        session('step', 1);
        $this->assign('env', $env);
        $this->assign('func', $func);
        $this->status['index'] = 'success';
        $this->status['check'] = 'primary';
        $this->assign('status', $this->status);
        return $this->fetch();
    }

    public function config($admin = null,$threeadmin = null) {

            $info = array();
            list($info['username'], $info['password'], $info['repassword'])
                = $admin;
            //缓存管理员信息
            session('admin_info', $info);

            $userinfo = array();
            list($userinfo['securityadmin'], $userinfo['useradmin'], $userinfo['auditoradmin'])
                = $threeadmin;
            //缓存管理员信息
            session('three_admin', $userinfo);


            $db = array('mysql','127.0.0.1','qinfo_os','root','mysqluser','3306','qinfo_');
            //检测数据库配置

            $DB = array();
            list($DB['type'], $DB['hostname'], $DB['database'], $DB['username'], $DB['password'],
                $DB['hostport'], $DB['prefix']) = $db;
            //缓存数据库配置
            session('db_config', $DB);

            //创建数据库
            $dbname = $DB['database'];
            unset($DB['database']);
            $db  = \think\Db::connect($DB);
            $sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8";
            if (!$db->execute($sql)) {

                $res['code'] = 0;
                $res['msg'] = $db->getError();
                return $res;
            } else {
               return $this->sql();
            }

    }


    public function sql() {

        session('error', false);
        $this->status['index']  = 'success';
        $this->status['check']  = 'success';
        $this->status['config'] = 'success';
        $this->status['sql']    = 'primary';

        if (session('update')) {
            $db = \think\Db::connect();
            //更新数据表
            update_tables($db, config('prefix'));
        } else {
            //连接数据库
            $dbconfig = session('db_config');
            $db       = \think\Db::connect($dbconfig);
            //创建数据表
            create_tables($db, $dbconfig['prefix']);
            //注册创始人帐号
            $admin = session('admin_info');
            $threeadmin = session('three_admin');
            register_administrator($db, $dbconfig['prefix'], $admin,$threeadmin);
            //更新系统类型
            updata_system_menu($db, $dbconfig['prefix']);

            updata_init_data($db, $dbconfig['prefix']);

            //创建配置文件
            $conf = write_config($dbconfig);
            session('config_file', $conf);
        }

        if (session('error')) {
            $res['code'] = 0;
            $res['msg'] = '安装失败';
            return $res;
        } else {
          return  $this->module();
        }
    }

//	public function sql() {
//
//		session('error', false);
//		$this->status['index']  = 'success';
//		$this->status['check']  = 'success';
//		$this->status['config'] = 'success';
//		$this->status['sql']    = 'primary';
//		$this->assign('status', $this->status);
//		echo $this->fetch();
//		if (session('update')) {
//			$db = \think\Db::connect();
//			//更新数据表
//			update_tables($db, config('prefix'));
//		} else {
//			//连接数据库
//			$dbconfig = session('db_config');
//			$db       = \think\Db::connect($dbconfig);
//			//创建数据表
//			create_tables($db, $dbconfig['prefix']);
//			//注册创始人帐号
//			$admin = session('admin_info');
//			$threeadmin = session('three_admin');
//			register_administrator($db, $dbconfig['prefix'], $admin,$threeadmin);
//			//更新系统类型
//			updata_system_menu($db, $dbconfig['prefix']);
//
//			updata_init_data($db, $dbconfig['prefix']);
//
//			//创建配置文件
//			$conf = write_config($dbconfig);
//			session('config_file', $conf);
//		}
//
//		if (session('error')) {
//			show_msg('失败');
//		} else {
//			echo '<script type="text/javascript">location.href = "'.url('install/index/module').'";</script>';
//		}
//	}

    public function module() {
        session('error', false);
        $this->status['index']  = 'success';
        $this->status['check']  = 'success';
        $this->status['config'] = 'success';
        $this->status['sql'] = 'success';
        $this->status['module']    = 'primary';
        $this->assign('status', $this->status);


        update_module();

        //创建配置文件
        $conf = write_install_lock();

        if (session('error')) {
            $res['code'] = 0;
            $res['msg'] = '安装失败';
            return $res;
        } else {
            $res['code'] = 1;
            $res['msg'] = '安装成功';
            return $res;
        }
    }


    public function complete() {
        $this->status['index']    = 'success';
        $this->status['check']    = 'success';
        $this->status['config']   = 'success';
        $this->status['sql']      = 'success';
        $this->status['module'] = 'success';
        $this->status['complete'] = 'primary';
        $this->assign('status', $this->status);
        $this->assign('status', $this->status);
        return $this->fetch();
    }
}