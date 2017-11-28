<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------
//------------------------
// 自动生成代码
//-------------------------

namespace app\tools\controller;
use app\common\controller\Admin;

use think\Config;
use think\Controller;
use think\Loader;
use think\Url;
use think\Db;

class Generate extends Admin
{
    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        $tables = Db::getTables();
        
        if ($this->request->param('table')) {
            $table = $this->request->param('table');
            $prefix = Config::get('database.prefix');
            $tableInfo = Db::getTableInfo($table);
            $controller = Loader::parseName(preg_replace('/^(' . $prefix . ')/', '', $table), 1);

            $this->assign('table_info', json_encode($tableInfo));
            $this->assign('controller', $controller);
        }

		$this->assign('tables', $tables);
		$this->setMeta(lang('module_generation'));
        return $this->fetch();
    }

    /**
     * 模拟终端
     */
    public function cmd()
    {
        echo "<p style='color: green'>代码开始生成中……</p>\n";
        $config = explode(".", $this->request->param('config', 'generate'));
        $configFile = ROOT_PATH . $config[0] . '.php';
        if (!file_exists($configFile)) {
            echo "<p style='color: red;font-weight: bold'>配置文件不存在：{$configFile}</p>\n";
            exit();
        }

        $data = include $configFile;
        $generate = new \Generate();
        $generate->run($data, $this->request->param('file', 'all'));
        echo "<p style='color: green;font-weight: bold'>代码生成成功！</p>\n";
        exit();
    }

    /**
     * 生成代码
     */
    public function run()
    {
        $generate = new \Generate();
        $data = $this->request->post();
        unset($data['file']);
        $generate->run($data, $this->request->post('file'));

        return ajax_return_adv('生成成功', '', false, '', '', ['action' => Url::build($data['controller'] . '/index')]);
    }
}