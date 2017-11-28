<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return array(

	'__domain__'=>array(
        'm'       =>  'mobile',
    ),
	

	'__pattern__'    => array(
		'name' => '\w+',
	),

	'login'          => 'user/login/index',
	'register'       => 'user/login/register',
	'logout'         => 'user/login/logout',
	'uc'             => 'user/index/index',
	
	'admin/login'    => 'admin/index/login',
	'admin/logout'   => 'admin/index/logout',
	
	'interface/:id'    => 'Mp/Api/index',
	'mpaddon/:addon/index' => 'mp/addons/config',
	'mpaddon/:addon/rule' => 'mp/addons/rule',
	'mpaddon/:addon/entry/:act' => 'mp/addons/entry',
	'mpaddon/:addon/setting' => 'mp/addons/setting',
	'mpaddon/:addon/preview/:act' => 'mp/addons/preview',
	
	
	'mpaddon/:mc/mobile/:ac' => 'mp/mobiles/execute',
	'mpaddon/:mc/:op/:ac' => 'mp/addons/mpexecute',
	
	'plugins/:_plugins/:_controller/:_action' => 'weixin/plugins/execute',
	
	// 变量传入index模块的控制器和操作方法
	'addons/:mc/:ac' => 'index/addons/execute', // 静态地址和动态地址结合
	'usera/:mc/:ac'  => 'user/addons/execute', // 静态地址和动态地址结合
	'admina/:mc/:ac' => 'admin/addons/execute', // 静态地址和动态地址结合
	
	
	
);