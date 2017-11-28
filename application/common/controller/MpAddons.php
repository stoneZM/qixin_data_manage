<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\common\controller;

/**
 * 插件类
 * @author yangweijie <yangweijiester@gmail.com>
 */
class MpAddons extends Base {

	public $info             = array();
	public $addon_path       = '';
	public $config_file      = '';
	public $custom_config    = '';
	public $admin_list       = array();
	public $custom_adminlist = '';
	public $access_url       = array();

	public function _initialize() {
		$mc = $this->getAddonsName();

		$this->addon_path = APP_PATH . "mp/addons/{$mc}/";
		if (is_file($this->addon_path . 'config.php')) {
			$this->config_file = $this->addon_path . 'config.php';
		}
	}

	public function template($template='') {
		$mc                         = $this->getAddonsName();
		$ac                         = input('ac', '', 'trim,strtolower');
		$op                         = input('op', '', 'trim,strtolower');
		$parse_str                  = \think\Config::get('parse_str');
		$parse_str['__ADDONROOT__'] = APP_PATH . "/application/mp/addons/{$mc}";
		\think\Config::set('parse_str', $parse_str);

		if ($template) {
			$template = $template;
		} else {
			$template = $op . "/" . $ac;
		}

		$this->view->engine(
			array('view_path' => "application/mp/addons/" . $mc . "/view/")
		);
		echo $this->fetch($template);
	}

	final public function getAddonsName() {
		$mc = input('mc', '', 'trim,strtolower');
		if ($mc) {
			return $mc;
		} else {
			$class = get_class($this);
			return strtolower(substr($class, strrpos($class, '\\') + 1));
		}
	}

	final public function checkInfo() {
		$info_check_keys = array('bzname', 'name', 'desc', 'author', 'version');
		foreach ($info_check_keys as $value) {
			if (!array_key_exists($value, $this->info)) {
				return false;
			}

		}
		return true;
	}

	public function getConfig() {

		static $_config = array();
		if (empty($name)) {
			$name = $this->getAddonsName();
		}
		if (isset($_config[$name])) {
			return $_config[$name];
		}
		$config        = array();
		$map['bzname']   = $name;
		$map['status'] = 1;
		$config        = db('MpAddons')->where($map)->value('config');
		if ($config) {
			$config = json_decode($config, true);
		} else {
			$config = include $this->config_file;
		}
		$_config[$name] = $config;
		return $config;
	}

}