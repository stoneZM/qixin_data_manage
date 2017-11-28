<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------



namespace app\mp\addons\Donate;
use app\common\controller\MpAddons;

/**
 * 开发团队信息插件
 * @author thinkphp
 */

class Donate extends MpAddons{

    public $info = array(
		'name'=>'微捐赠',
        'bzname'=>'Donate',
        'desc'=>'微信捐赠插件',
        'author'=>'孙大伟',
        'version'=>'0.1',
    );


    public function install(){
		
		
		$install_sql_path = QINFO_MPADDON_PATH .  DS . strtolower($this->info['bzname']). DS .'install.sql';
		if (is_file($install_sql_path)) {
			execute_sql_file($install_sql_path);
		}
        return true;
    }

    public function uninstall(){
       $install_sql_path = QINFO_MPADDON_PATH .  DS . strtolower($this->info['bzname']). DS .'uninstall.sql';
		if (is_file($install_sql_path)) {
			execute_sql_file($install_sql_path);
		}
		return true;
    }

    //实现的AdminIndex钩子方法
    public function AdminIndex($param){
        $config = $this->getConfig();
        $this->assign('addons_config', $config);
        if($config['display']){
            $this->template('widget');
        }
    }
}