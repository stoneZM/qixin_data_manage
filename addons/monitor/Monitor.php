<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------


namespace addons\monitor;
use app\common\controller\Addons;

/**
 * 系统环境信息插件
 * @author thinkphp
 */

class monitor extends Addons{

    public $info = array(
        'name'=>'Monitor',
        'title'=>'服务器监控',
        'description'=>'服务器监控基础信息',
        'status'=>1,
        'author'=>'sundawei',
        'version'=>'0.1'
    );

    public function install(){
        return true;
    }

    public function uninstall(){
        return true;
    }
	
    //实现的AdminIndex钩子方法
    public function AdminIndex($param){
		
		
		$Monitor = model('monitor');
		
		
        $config = $this->getConfig();
        $this->assign('addons_config', $config);
		$map['status'] = array('egt',0);
		$maps['is_read'] = array('eq',0);
		$is_constantly = true; // 是否开启实时信息, false - 关闭, true - 开启
		
        if($config['display']){
            $this->template('widget');
        }
    }
}