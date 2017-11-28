<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------

namespace addons\Saleinfo;
use app\common\controller\Addons;

/**
 * 系统环境信息插件
 * @author thinkphp
 */

class Saleinfo extends Addons {

	public $info = array(
		'name'        => 'Saleinfo',
		'title'       => '软件销售信息',
		'description' => '用于显示销售的信息',
		'status'      => 1,
		'author'      => 'sundawei',
		'version'     => '0.1',
	);

	public function install() {
		return true;
	}

	public function uninstall() {
		return true;
	}

	//实现的AdminIndex钩子方法
	public function AdminIndex($param) {
		$config = $this->getConfig();
		$this->assign('addons_config', $config);
		if ($config['display']) {
			$this->template('widget');
		}
	}
}