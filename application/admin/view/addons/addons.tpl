<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace addons\[name];
use common\controller\Addon;

/**
* [title]插件
* @author [author]
*/
class [name] extends Addon{

	public $info = array(
		'name'=>'[name]',
		'title'=>'[title]',
		'description'=>'[description]',
		'status'=>[status],
		'author'=>'[author]',
		'version'=>'[version]'
	);

	//插件安装
	public function install(){
		return true;
	}

	public function uninstall(){
		return true;
	}

	[hook]
}