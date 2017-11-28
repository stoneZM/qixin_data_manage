<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

use think\Exception;
use think\Log;
use think\Config;
use think\Db;
use think\Loader;

class Generate
{
    private $module;
    private $name;
    private $dir;
    private $namespaceSuffix;
    private $nameLower;
    private $data;
    // 控制器黑名单
    private $blacklistName = [
        'AdminGroup',
        'AdminNode',
        'AdminNodeLoad',
        'AdminRole',
        'AdminUser',
        'Pub',
        'Demo',
        'Generate',
        'Index',
        'LogLogin',
        'Ueditor',
        'Upload',
        'WebLog',
        'NodeMap',
        'Error',
    ];
    // 数据表黑名单
    private $blacklistTable = [
        'action',
        'action_limit',
        'action_log',
        'addons',
        'attachment',
        'attribute',
        'auth_extend',
        'auth_group',
        'auth_group_access',
        'auth_rule',
		'category',
		'channel',
		'config',
		'district',
		'document',
		'file',
		'hooks',
		'link',
		'member',
		'member_extend',
		'member_extend_group',
		'member_extend_setting',
		'menu',
		'model',
		'page',
		'picture',
		'rewrite',
		'seo_rule',
		'sync_login',
		
    ];

    public function run($data, $option = 'all')
    {
        // 检查方法是否存在
        $action = 'build' . ucfirst($option);
        if (!method_exists($this, $action)) {
            throw new Exception('选项不存在：' . $option, 404);
        }
        // 载入默认配置
        $defaultConfigFile = APP_PATH . 'tools' . DS . 'extra' . DS . 'generate.php';
        if (file_exists($defaultConfigFile)) {
            $data = array_merge(include $defaultConfigFile, $data);
        }
        // 检查目录是否可写
        $pathCheck = APP_PATH . $data['module'] . DS;
		
		if(!is_dir($pathCheck)){
			mkdir($pathCheck);
			chmod($pathCheck,0755);
		}
        if (!self::checkWritable($pathCheck)) {
            throw new Exception("目录没有权限不可写，请执行一下命令修改权限：<br>chmod -R 755 " . realpath($pathCheck), 403);
        }
		
        // 将菜单全部转为小写
        if (isset($data['menu']) && $data['menu']) {
            foreach ($data['menu'] as &$menu) {
                $menu = strtolower($menu);
            }
        }
        $this->data = $data;
        $this->module = $data['module'];
        $controllers = explode(".", $data['controller']);
        $this->name = array_pop($controllers);
        $this->nameLower = Loader::parseName($this->name);

        // 分级控制器目录和命名空间后缀
        if ($controllers) {
            $this->dir = strtolower(implode(DS, $controllers) . DS);
            $this->namespaceSuffix = "\\" . strtolower(implode("\\", $controllers));
        } else {
            $this->dir = "";
            $this->namespaceSuffix = "";
        }
        // 数据表表名
        $tableName = str_replace(DS, '_', $this->dir) . $this->nameLower;
		if($data['module'] == 'admin' || $data['module'] == 'tools'){
			// 判断是否在黑名单中
			if (in_array($data['controller'], $this->blacklistName)) {
				throw new Exception('该控制器不允许创建');
			}
		}
        // 判断是否在数据表黑名单中
        if (isset($data['table']) && $data['table'] && in_array($tableName, $this->blacklistTable)) {
            throw new Exception('该数据表不允许创建');
        }

        // 创建目录
        $dir_list = ["view" . DS . $this->dir . $this->nameLower];
		if($data['module'] == 'admin' || $data['module'] == 'tools'){
			if (isset($data['model']) && $data['model']) {
			    $dir_list[] = "model" . DS . $this->dir;
			}
			if (isset($data['validate']) && $data['validate']) {
			    $dir_list[] = "validate" . DS . $this->dir;
			}
			if ($this->dir) {
				$dir_list[] = "controller" . DS . $this->dir;
			}
		}else{
			if($this->dir){
				if (isset($data['model']) && $data['model']) {
					$dir_list[] = "model" . DS . $this->dir;
				}
				if (isset($data['validate']) && $data['validate']) {
					$dir_list[] = "validate" . DS . $this->dir;
				}
				$dir_list[] =  "controller". DS . $this->dir;
			}else{
				if (isset($data['model']) && $data['model']) {
					$dir_list[] = "model" . DS . $this->dir;
				}
				if (isset($data['validate']) && $data['validate']) {
					$dir_list[] = "validate" . DS . $this->dir;
				}
				$dir_list[] =  "controller". DS;
				
			}
		}
        $this->buildDir($dir_list);
        if ($action != 'buildDir') {
            // 文件路径
            $pathView = APP_PATH . $this->module . DS . "view" . DS . $this->dir . $this->nameLower . DS;
            $pathTemplate = APP_PATH . 'tools' . DS . "view" . DS . "generate" . DS . "template" . DS;
            $fileName = APP_PATH . $this->module . DS . "%NAME%" . DS . $this->dir . $this->name . ".php";
            $code = $this->parseCode();
            // 执行方法
            $this->$action($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        }
    }

    /**
     * 检查当前模块目录是否可写
     * @return bool
     */
    public static function checkWritable($path = null)
    {
        if (null === $path) {
            $path = APP_PATH . 'tools' . DS;
        }
        try {
            $testFile = $path . "bulid.test";
            if (!file_put_contents($testFile, "test")) {
                return false;
            }
            // 解除锁定
            unlink($testFile);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 生成所有文件
     */
    private function buildAll($pathView, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        // 创建文件
        $this->buildIndex($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        if (isset($data['menu']) && in_array('recyclebin', $data['menu'])) {
            $this->buildForm($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
            $this->buildTh($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
            $this->buildTd($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
            $this->buildRecycleBin($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        }
        $this->buildEdit($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        $this->buildController($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        if (isset($data['validate']) && $data['validate']) {
            $this->buildValidate($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        }
        if (isset($data['model']) && $data['model']) {
            $this->buildModel($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        }
        if (isset($data['create_table']) && $data['create_table']) {
            $this->buildTable($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        }
        // 建立配置文件
        if (isset($data['create_config']) && $data['create_config']) {
            $this->buildConfig($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
        }
    }

    /**
     * 创建目录
     */
    private function buildDir($dir_list)
    {
        foreach ($dir_list as $dir) {
            $path = APP_PATH . $this->module . DS . $dir;
            if (!is_dir($path)) {
                // 创建目录
                mkdir($path, 0755, true);
            }
        }
    }

    /**
     * 创建 edit.html 文件
     */
    private function buildEdit($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $template = file_get_contents($pathTemplate . "edit.tpl");
        $file = $path . "edit.html";

        return file_put_contents($file, str_replace(
            ["[ROWS]", "[SET_VALUE]", "[SCRIPT]"],
            [$code['edit'], implode("\n", array_merge($code['set_checked'], $code['set_selected'])), implode("", $code['script_edit'])],
            $template));
    }

    /**
     * 创建form.html文件
     */
    private function buildForm($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $content = implode("\n", $code['search']);
        $file = $path . "form.html";

        return file_put_contents($file, $content);
    }

    /**
     * 创建th.html文件
     */
    private function buildTh($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $content = implode("\n", $code['th']);
        $file = $path . "th.html";

        return file_put_contents($file, $content);
    }

    /**
     * 创建td.html文件
     */
    private function buildTd($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $content = implode("\n", $code['td']);
        $file = $path . "td.html";

        return file_put_contents($file, $content);
    }

    /**
     * 创建 recyclebin.html 文件
     */
    private function buildRecycleBin($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        // 首页菜单选择了回收站才创建回收站
        $file = $path . "recyclebin.html";

        $content = '{extend name="template/recyclebin" /}';
        if ($code['search_selected']) {
            $content .= "\n" . '{block name="script"}' . implode("", $code['script_search']) . "\n"
                . '<script>' . "\n"
                . tab(1) . '$(function () {' . "\n"
                . $code['search_selected']
                . tab(1) . '})' . "\n"
                . '</script>' . "\n"
                . '{/block}' . "\n";
        }

        // 默认直接继承模板
        return file_put_contents($file, $content);
    }

    /**
     * 创建 index.html 文件
     */
    private function buildIndex($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
		
		
        $script = '';
        if ($code['search_selected']) {
            $script = '{block name="script"}' . implode("", $code['script_search']) . "\n"
                . '<script>' . "\n"
                . tab(1) . '$(function () {' . "\n"
                . $code['search_selected']
                . tab(1) . '})' . "\n"
                . '</script>' . "\n"
                . '{/block}' . "\n";
        }
        // 菜单全选的默认直接继承模板
        $menuArr = isset($this->data['menu']) ? $this->data['menu'] : [];
        $menu = '';
        if ($menuArr) {
            $menu = '{tp:menu menu="' . implode(",", $menuArr) . '" /}';
        }
        $tdMenu = '';
        if (in_array("resume", $menuArr) || in_array("forbid", $menuArr)) {
            $tdMenu .= tab(4) . '{$vo.status|show_status=$vo.id}' . "\n";
        }
        $tdMenu .= tab(4) . '{tp:menu menu=\'sedit\' /}' . "\n";
        // 有回收站
        if (in_array('recyclebin', $menuArr)) {
            $form = '{include file="'.$this->data['controller'].'/form" /}';
            $th = '{include file="'.$this->data['controller'].'/th" /}';
            $td = '{include file="'.$this->data['controller'].'/td" /}';
            $tdMenu .= tab(4) . '{tp:menu menu=\'sdelete\' /}';
        } else {
            $form = implode("\n" . tab(1), $code['search']);
            $th = implode("\n" . tab(3), $code['th']);
            $td = implode("\n" . tab(3), $code['td']);
            $tdMenu .= tab(4) . '{tp:menu menu=\'sdeleteforever\' /}';
        }

        $template = file_get_contents($pathTemplate . "index.tpl");
        $file = $path . "index.html";

        return file_put_contents($file, str_replace(
                ["[FORM]", "[MENU]", "[TH]", "[TD]", "[TD_MENU]", "[SCRIPT]"],
                [$form, $menu, $th, $td, $tdMenu, $script],
                $template
            )
        );
    }

    /**
     * 创建控制器文件
     */
    private function buildController($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $template = file_get_contents($pathTemplate . "Controller.tpl");
        $file = str_replace('%NAME%', 'controller', $fileName);
		// 一定别忘记表名前缀
        $tableName = isset($this->data['table_name']) && $this->data['table_name'] ? $this->data['table_name'] : $tableName;
        return file_put_contents($file, str_replace(
                ["[TITLE]", "[NAME]", "[FILTER]", "[NAMESPACE]","[MODULE]", "[TABLE]"],
                [$this->data['title'], $this->name, $code['filter'], $this->namespaceSuffix, $this->data['module'],$tableName],
                $template
            )
        );
    }

    /**
     * 创建模型文件
     */
    private function buildModel($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        // 直接生成空模板
        $template = file_get_contents($pathTemplate . "Model.tpl");
        $file = str_replace('%NAME%', 'model', $fileName);
		// 一定别忘记表名前缀
        $tableName = isset($this->data['table_name']) && $this->data['table_name'] ? $this->data['table_name'] : $tableName;
        $autoTimestamp = '';
        if (isset($this->data['auto_timestamp']) && $this->data['auto_timestamp']) {
            $autoTimestamp = '// 开启自动写入时间戳字段' . "\n"
                . tab(1) . 'protected $autoWriteTimestamp = \'int\';';
        }

        return file_put_contents($file, str_replace(
                ["[TITLE]", "[NAME]", "[NAMESPACE]", "[TABLE]", "[AUTO_TIMESTAMP]","[MODULE]"],
                [$this->data['title'], $this->name, $this->namespaceSuffix, $tableName, $autoTimestamp, $this->data['module']],
                $template
            )
        );
    }

    /**
     * 创建验证器
     */
    private function buildValidate($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $template = file_get_contents($pathTemplate . "Validate.tpl");
        $file = str_replace('%NAME%', 'validate', $fileName);

        return file_put_contents($file, str_replace(
                ["[TITLE]", "[NAME]", "[NAMESPACE]", "[RULE]","[MODULE]"],
                [$this->data['title'], $this->name, $this->namespaceSuffix, $code['validate'], $this->data['module']],
                $template
            )
        );
    }

    /**
     * 创建数据表
     */
    private function buildTable($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
		
        // 一定别忘记表名前缀
        $tableName = isset($this->data['table_name']) && $this->data['table_name'] ?
            Config::get("database.prefix") . $this->data['table_name'] :
            Config::get("database.prefix") . $tableName;
        // 在 MySQL 中，DROP TABLE 语句自动提交事务，因此在此事务内的任何更改都不会被回滚，不能使用事务
        // http://php.net/manual/zh/pdo.rollback.php
        $tableExist = false;
        // 判断表是否存在
        $ret = Db::query("SHOW TABLES LIKE '{$tableName}'");
        // 表存在
        if ($ret && isset($ret[0])) {
            //不是强制建表但表存在时直接return
            if (!isset($this->data['create_table_force']) || !$this->data['create_table_force']) {
                return true;
            }
            Db::execute("RENAME TABLE {$tableName} to {$tableName}_build_bak");
            $tableExist = true;
        }
        $auto_create_field = ['id', 'status', 'isdelete', 'create_time', 'update_time'];
        // 强制建表和不存在原表执行建表操作
        $fieldAttr = [];
        $key = [];
        if (in_array('id', $auto_create_field)) {
            $fieldAttr[] = tab(1) . "`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '{$this->data['title']}主键'";
        }
        foreach ($this->data['field'] as $field) {
            if (!in_array($field['name'], $auto_create_field)) {
                // 字段属性
                $fieldAttr[] = tab(1) . "`{$field['name']}` {$field['type']}"
                    . ($field['extra'] ? ' ' . $field['extra'] : '')
                    . (isset($field['not_null']) && $field['not_null'] ? ' NOT NULL' : '')
                    . (strtolower($field['default']) == 'null' ? '' : " DEFAULT '{$field['default']}'")
                    . ($field['comment'] === '' ? '' : " COMMENT '{$field['comment']}'");
            }
            // 索引
            if (isset($field['key']) && $field['key'] && $field['name'] != 'id') {
                $key[] = tab(1) . "KEY `{$field['name']}` (`{$field['name']}`)";
            }
        }

        if (isset($this->data['menu'])) {
            // 自动生成status字段，防止resume,forbid方法报错，如果不需要请到数据库自己删除
            if (in_array("resume", $this->data['menu']) || in_array("forbid", $this->data['menu'])) {
                $fieldAttr[] = tab(1) . "`status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态，1-正常 | 0-禁用'";
            }
            // 自动生成 isdelete 软删除字段，防止 delete,recycle,deleteForever 方法报错，如果不需要请到数据库自己删除
            if (in_array("delete", $this->data['menu']) || in_array("recyclebin", $this->data['menu'])) {
                // 修改官方软件删除使用记录时间戳的方式，效率较低，改为枚举类型的 tinyint(1)，相应的traits见 thinkphp/library/traits/model/FakeDelete.php，使用方法和官方一样
                // 软件删除详细介绍见：http://www.kancloud.cn/manual/thinkphp5/189658
                $fieldAttr[] = tab(1) . "`isdelete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '删除状态，1-删除 | 0-正常'";
            }
        }

        // 如果创建模型则自动生成create_time，update_time字段
        if (isset($this->data['auto_timestamp']) && $this->data['auto_timestamp']) {
            // 自动生成 create_time 字段，相应自动生成的模型也开启自动写入create_time和update_time时间，并且将类型指定为int类型
            // 时间戳使用方法见：http://www.kancloud.cn/manual/thinkphp5/138668
            $fieldAttr[] = tab(1) . "`create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'";
            $fieldAttr[] = tab(1) . "`update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'";
        }
        // 默认自动创建主键为id
        $fieldAttr[] = tab(1) . "PRIMARY KEY (`id`)";

        // 会删除之前的表，会清空数据，重新创建表，谨慎操作
        $sql_drop = "DROP TABLE IF EXISTS `{$tableName}`";
        // 默认字符编码为utf8，表引擎默认InnoDB，其他都是默认
        $sql_create = "CREATE TABLE `{$tableName}` (\n"
            . implode(",\n", array_merge($fieldAttr, $key))
            . "\n)ENGINE=" . (isset($this->data['table_engine']) ? $this->data['table_engine'] : 'InnoDB')
            . " DEFAULT CHARSET=utf8 COMMENT '{$this->data['title']}'";

        // 写入执行的SQL到日志中，如果不是想要的表结构，请到日志中搜索BUILD_SQL，找到执行的SQL到数据库GUI软件中修改执行，修改表结构
        Log::write("BUILD_SQL：\n{$sql_drop};\n{$sql_create};", Log::SQL);
        // execute和query方法都不支持传入分号 (;)，不支持一次执行多条 SQL
        try {
            Db::execute($sql_drop);
            Db::execute($sql_create);
            Db::execute("DROP TABLE IF EXISTS `{$tableName}_build_bak`");
        } catch (\Exception $e) {
            // 模拟事务操作，滚回原表
            if ($tableExist) {
                Db::execute("RENAME TABLE {$tableName}_build_bak to {$tableName}");
            }

            throw new Exception($e->getMessage());
        }
    }

    /**
     * 创建配置文件
     */
    private function buildConfig($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $content = '<?php' . "\n\n"
            . 'return ' . var_export($data, true) . ";\n";
        $file = $path . "config.php";

        return file_put_contents($file, $content);
    }


    /**
     * 创建文件的代码
     * @return array
     * return [
     * 'search'          => $search,
     * 'th'              => $th,
     * 'td'              => $td,
     * 'edit'            => $editField,
     * 'set_checked'     => $setChecked,
     * 'set_selected'    => $setSelected,
     * 'search_selected' => $searchSelected,
     * 'filter'          => $filter,
     * 'validate'        => $validate,
     * ];
     */
    private function parseCode()
    {
        // 生成 form.html 文件的代码
        $search = ['<form class="mb-20" method="get" action="{:\\\\think\\\\Url::build($Request.action)}">'];
        // 生成 th.html 文件的代码
        $th = ['<th width="25"><input type="checkbox"></th>'];
        // 生成 td.html 文件的代码
        $td = ['<td><input type="checkbox" name="id[]" value="{$vo.id}"></td>'];
        // 生成 edit.html 文件的代码
        $editField = '';
        // radio类型的表单控件编辑状态使用javascript赋值
        $setChecked = [];
        // select类型的表单控件编辑状态使用javascript赋值
        $setSelected = [];
        // 搜索时被选中的值
        $searchSelected = '';
        // 控制器过滤器
        $filter = '';
        // 生成验证器文件的代码
        $validate = '';
        // DatePicker脚本引入
        $scriptSearch = [];
        $scriptEdit = [];
        if (isset($this->data['form']) && $this->data['form']) {
            foreach ($this->data['form'] as $form) {
                $options = $this->parseOption($form['option']);
                // 表单搜索
                if (isset($form['search']) && $form['search']) {
                    // 表单搜索
                    switch ($form['search_type']) {
                        case 'select':
                            // td
                            $td[] = '<td>{$vo.' . $form['name'] . ($form['name'] == "status" ? '|get_status' : '') . '}</td>';
                            // 默认选中
                            $searchSelected .= tab(2) . '$("[name=\'' . $form['name'] . '\']").find("[value=\'{$Request.param.' . $form['name'] . '}\']").attr("selected", true);' . "\n";
                            $search[] = tab(1) . '<div class="select-box" style="width:250px">';
                            $search[] = tab(2) . '<select name="' . $form['name'] . '" class="select">';
                            $search = array_merge($search, $this->getOption($options, $form, true, 3));
                            $search[] = tab(2) . '</select>';
                            $search[] = tab(1) . '</div>';
                            break;
                        case 'date':
                            // td
                            $td[] = '<td>{$vo.' . $form['name'] . ($form['name'] == "status" ? '|get_status' : '') . '}</td>';
                            $search[] = tab(1) . '<input type="text" class="input-text Wdate" style="width:250px" '
                                . 'placeholder="' . $form['title'] . '" name="' . $form['name'] . '" '
                                . 'value="{$Request.param.' . $form['name'] . '}" '
                                . '{literal} onfocus="WdatePicker({dateFmt:\'yyyy-MM-dd\'})" {/literal} '
                                . '>';
                            $scriptSearch['date'] = "\n" . '<script type="text/javascript" src="__LIB__/My97DatePicker/WdatePicker.js"></script>';
                            break;
                        default:
                            // td
                            $td[] = '<td>{$vo.' . $form['name'] . '|high_light=$Request.param.' . $form['name'] . "}</td>";
                            $filter .= tab(2) . 'if ($this->request->param("' . $form['name'] . '")) {' . "\n"
                                . tab(3) . '$map[\'' . $form['name'] . '\'] = ["like", "%" . $this->request->param("' . $form['name'] . '") . "%"];' . "\n"
                                . tab(2) . '}' . "\n";
                            $search[] = tab(1) . '<input type="text" class="input-text" style="width:250px" '
                                . 'placeholder="' . $form['title'] . '" name="' . $form['name'] . '" '
                                . 'value="{$Request.param.' . $form['name'] . '}" '
                                . '>';
                            break;
                    }
                } else {
                    // td
                    $td[] = '<td>{$vo.' . $form['name'] . ($form['name'] == "status" ? '|get_status' : '') . '}</td>';
                }
                // th
                if (isset($form['sort']) && $form['sort']) {
                    // 带有表单排序的需使用表单排序方法
                    $th[] = '<th width="">' . "{:sort_by('{$form['title']}','{$form['name']}')}</th>";
                } else {
                    $th[] = '<th width="">' . $form['title'] . "</th>";
                }
                // 像id这种白名单字段不需要自动生成到编辑页
                if (!in_array($form['name'], ['id', 'isdelete', 'create_time', 'update_time'])) {
                    // 使用 Validform 插件前端验证数据格式，生成在表单控件上的验证规则
                    $validateForm = '';
                    if (isset($form['validate']) && $form['validate']['datatype']) {
                        $v = $form['validate'];
                        $defaultDesc = in_array($form['type'], ['checkbox', 'radio', 'select', 'date']) ? '选择' : '填写';
                        $validateForm = ' datatype="' . $v['datatype'] . '"'
                            . (' nullmsg="' . ($v['nullmsg'] ? $v['nullmsg'] : '请' . $defaultDesc . $form['title']) . '"')
                            . ($v['errormsg'] ? ' errormsg="' . $v['errormsg'] . '"' : '')
                            . (isset($form['require']) && $form['require'] ? '' : ' ignore="ignore"');
                        $validate .= tab(2) . '"' . $form['name'] . '|' . $form['title'] . '" => "'
                            . (isset($form['require']) && $form['require'] ? 'require' : '') . '",' . "\n";
                    }
                    $editField .= tab(2) . '<div class="row cl">' . "\n"
                        . tab(3) . '<label class="form-label col-xs-3 col-sm-3">'
                        . (isset($form['require']) && $form['require'] ? '<span class="c-red">*</span>' : '')
                        . $form['title'] . '：</label>' . "\n"
                        . tab(3) . '<div class="formControls col-xs-6 col-sm-6'
                        . (in_array($form['type'], ['radio', 'checkbox']) ? ' skin-minimal' : '')
                        . '">' . "\n";
                    switch ($form['type']) {
                        case "radio":
                        case "checkbox":
                            if ($form['type'] == "radio") {
                                // radio类型的控件进行编辑状态赋值，checkbox类型控件请自行根据情况赋值
                                $setChecked[] = tab(2) . '$("[name=\'' . $form['name'] . '\'][value=\'{$vo.' . $form['name'] . ' ?? \'' . $form['default'] . '\'}\']").attr("checked", true);';
                            } else {
                                $setChecked[] = tab(2) . 'var checks = \'' . $form['default'] . '\'.split(",");' . "\n"
                                    . tab(2) . 'if (checks.length > 0){' . "\n"
                                    . tab(3) . 'for (var i in checks){' . "\n"
                                    . tab(4) . '$("[name=\'' . $form['name'] . '[]\'][value=\'"+checks[i]+"\']").attr("checked", true);' . "\n"
                                    . tab(3) . '}' . "\n"
                                    . tab(2) . '}';
                            }

                            // 默认只生成一个空的示例控件，请根据情况自行复制编辑
                            $name = $form['name'] . ($form['type'] == "checkbox" ? '[]' : '');

                            switch ($options[0]) {
                                case 'string':
                                    $editField .= $this->getCheckbox($form, $name, $validateForm, $options[1], '', 0);
                                    break;
                                case 'var':
                                    $editField .= tab(4) . '{foreach name="$Think.config.conf.' . $options[1] . '" item=\'v\' key=\'k\'}' . "\n"
                                        . $this->getCheckbox($form, $name, $validateForm, '{$v}', '{$k}', '{$k}')
                                        . tab(4) . '{/foreach}' . "\n";
                                    break;
                                case 'array':
                                    foreach ($options[1] as $option) {
                                        $editField .= $this->getCheckbox($form, $name, $validateForm, $option[1], $option[0], $option[0]);
                                    }
                                    break;
                            }
                            break;
                        case "select":
                            // select类型的控件进行编辑状态赋值
                            $setSelected[] = tab(2) . '$("[name=\'' . $form['name'] . '\']").find("[value=\'{$vo.' . $form['name'] . ' ?? \'' . $form['default'] . '\'}\']").attr("selected", true);';
                            $editField .= tab(4) . '<div class="select-box">' . "\n"
                                . tab(5) . '<select name="' . $form['name'] . '" class="select"' . $validateForm . '>' . "\n"
                                . implode("\n", $this->getOption($options, $form, false, 6)) . "\n"
                                . tab(5) . '</select>' . "\n"
                                . tab(4) . '</div>' . "\n";
                            break;
                        case "textarea":
                            // 默认生成的textarea加入了输入字符长度实时统计，H-ui.admin官方的textarealength方法有问题，请使用 tpadmin 框架修改后的源码，也可拷贝 H-ui.js 里相应的方法
                            // 如果不需要字符长度实时统计，请在生成代码中删除textarea上的onKeyUp事件和下面p标签那行
                            $editField .= tab(4) . '<textarea class="textarea" placeholder="" name="' . $form['name'] . '" '
                                . 'onKeyUp="textarealength(this, 100)"' . $validateForm . '>'
                                . '{$vo.' . $form['name'] . ' ?? \'' . $form['default'] . '\'}'
                                . '</textarea>' . "\n"
                                . tab(4) . '<p class="textarea-numberbar"><em class="textarea-length">0</em>/100</p>' . "\n";
                            break;
                        case "date":
                            $editField .= tab(4) . '<input type="text" class="input-text Wdate" '
                                . 'placeholder="' . $form['title'] . '" name="' . $form['name'] . '" '
                                . 'value="' . '{$vo.' . $form['name'] . ' ?? \'' . $form['default'] . '\'}' . '" '
                                . '{literal} onfocus="WdatePicker({dateFmt:\'yyyy-MM-dd\'})" {/literal} '
                                . $validateForm . '>' . "\n";
                            $scriptEdit['date'] = "\n" . '<script type="text/javascript" src="__LIB__/My97DatePicker/WdatePicker.js"></script>';
                            break;
                        case "text":
                        case "password":
                        case "number":
                        default:
                            $editField .= tab(4) . '<input type="' . $form['type'] . '" class="input-text" '
                                . 'placeholder="' . $form['title'] . '" name="' . $form['name'] . '" '
                                . 'value="' . '{$vo.' . $form['name'] . ' ?? \'' . $form['default'] . '\'}' . '" '
                                . $validateForm . '>' . "\n";
                            break;
                    }
                    $editField .= tab(3) . '</div>' . "\n"
                        . tab(3) . '<div class="col-xs-3 col-sm-3"></div>' . "\n"
                        . tab(2) . '</div>' . "\n";
                }
            }
        }
        if ($search) {
            $search[] = tab(1) . '<button type="submit" class="btn btn-success"><i class="Hui-iconfont">&#xe665;</i> 搜索</button>';
        }
        $search[] = '</form>';

        if ($filter) {
            $filter = 'protected function filter(&$map)' . "\n"
                . tab(1) . '{' . "\n"
                . $filter
                . tab(1) . '}';
        }
        // 自动屏蔽查询条件isdelete字段
        if (!isset($this->data['menu']) ||
            (isset($this->data['menu']) &&
                !in_array("delete", $this->data['menu']) &&
                !in_array("recyclebin", $this->data['menu'])
            )
        ) {
            $filter = 'protected static $isdelete = false;' . "\n\n" . tab(1) . $filter;
        }
        if ($validate) {
            $validate = 'protected $rule = [' . "\n" . $validate . '    ];';
        }

        return [
            'search'          => $search,
            'th'              => $th,
            'td'              => $td,
            'edit'            => $editField,
            'set_checked'     => $setChecked,
            'set_selected'    => $setSelected,
            'search_selected' => $searchSelected,
            'filter'          => $filter,
            'validate'        => $validate,
            'script_edit'     => $scriptEdit,
            'script_search'   => $scriptSearch,
        ];
    }

    /**
     * 生成复选框、单选框
     */
    private function getCheckbox($form, $name, $validateForm, $title, $value = '', $key = 0, $tab = 4)
    {
        return tab($tab) . '<div class="radio-box">' . "\n"
        . tab($tab + 1) . '<input type="' . $form['type'] . '" name="' . $name . '" '
        . 'id="' . $form['name'] . '-' . $key . '" value="' . $value . '"' . $validateForm . '>' . "\n"
        . tab($tab + 1) . '<label for="' . $form['name'] . '-' . $key . '">' . $title . '</label>' . "\n"
        . tab($tab) . '</div>' . "\n";
    }

    /**
     * 获取下拉框的option
     */
    private function getOption($options, $form, $empty = true, $tab = 3)
    {
        switch ($options[0]) {
            case 'string':
                return [tab($tab) . '<option value="">' . $options[1] . '</option>'];
                break;
            case 'var':
                $ret = [];
                if ($empty) {
                    $ret[] = tab($tab) . '<option value="">所有' . $form['title'] . '</option>';
                }
                $ret[] = tab($tab) . '{foreach name="$Think.config.conf.' . $options[1] . '" item=\'v\' key=\'k\'}';
                $ret[] = tab($tab + 1) . '<option value="{$k}">{$v}</option>';
                $ret[] = tab($tab) . '{/foreach}';

                return $ret;
                break;
            case 'array':
                $ret = [];
                foreach ($options[1] as $option) {
                    $ret[] = tab($tab) . '<option value="' . $option[0] . '">' . $option[1] . '</option>';
                }

                return $ret;
                break;
        }
    }

    /**
     * 格式化选项值
     */
    private function parseOption($option, $string = false)
    {
        if (!$option) return ['string', $option];
        // {vo.item} 这种格式传入的变量
        if (preg_match('/^\{(.*?)\}$/', $option, $match)) {
            return ['var', $match[1]];
        } else {
            if ($string) {
                return ['string', $option];
            }
            // key:val#key2:val2#val3#... 这种格式
            $ret = [];
            $arrVal = explode('#', $option);
            foreach ($arrVal as $val) {
                $keyVal = explode(':', $val, 2);
                if (count($keyVal) == 1) {
                    $ret[] = ['', $keyVal[0]];
                } else {
                    $ret[] = [$keyVal[0], $keyVal[1]];
                }
            }

            return ['array', $ret];
        }
    }
}
