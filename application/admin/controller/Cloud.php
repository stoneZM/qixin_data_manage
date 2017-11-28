<?php

namespace app\admin\controller;
use app\common\controller\Admin;

class Cloud extends Admin
{

    public function index()
    {
		
		$license_data = get_license();
		$this->assign('sn', $license_data['cdkey']);
		$this->setMeta(lang('cloud_market'));
		return $this->fetch();
    }

    /**
     * 获取升级列表
     */
    public function getVersionList()
    {
        $aToken = input('token', '');
        $versionList = model('Cloud')->getUpdateList($aToken);
        $this->assign('token', $aToken);
        $this->assign('versionList', $versionList);
        return $this->fetch();
    }


    /**
     * 系统自动更新，开始
     */
    public function update()
    {
        $aRefresh=input('refresh',0);
        if($aRefresh){
            cache('admin_versions',null);
        }
        $versionModel = model('Version');
        $version=$versionModel->getVersions();
        $currentVersion = $versionModel->getCurrentVersion();
        foreach ($version as $key => $vo) {
            $versionCompare = version_compare($currentVersion['name'], $vo['name']);
            if ($versionCompare > -1) {
                if ($versionCompare == 0) {
                    $version[$key]['class'] = 'active';
                    $version[$key]['word'] = '【当前版本】';
                } else {
                    $version[$key]['class'] = 'default';
                    $version[$key]['word'] = '【历史版本】';
                }
            } else {
                $version[$key]['class'] = 'success';
                $version[$key]['word'] = '【可升级】';
            }
        }
        $this->assign('cloud', cloud_url());
        $this->assign('currentVersion', $currentVersion['name']);
        $this->assign('version', $version);
        $this->assign('nextVersion', $versionModel->getNextVersion());
        $this->disableCheckUpdate();
		$this->setMeta(lang('auto_update'));
		return $this->fetch();	
    }

    private function disableCheckUpdate()
    {
        $this->assign('update', false);
    }

    /**
     * 获取文件列表
     */
    public function getFileList()
    {
        $aVersion = input('version', '');
        if ($aVersion == '') {
            $this->error('升级失败，请确认版本');
        }
        $versionModel = model('Version');
        $nextVersion = $versionModel->getNextVersion();
        if ($aVersion != $nextVersion['name']) {
            $this->error('此版本不允许当前版本升级，请不要跳过中间版本');
        }
        $this->assign('path', config('UPDATE_PATH') . $nextVersion['name']);
        /*版本正确性检测↑*/
        $currentVersion = $versionModel->getCurrentVersion();
        $this->assign('currentVersion', $currentVersion);
        $this->assign('nextVersion', $nextVersion);
        $this->disableCheckUpdate();
        $this->setMeta('自动升级');
		echo $this->fetch();
		
        $this->writeMessage('开始下载原版文件包');

        set_time_limit(0);

        $old_file_path = config('UPDATE_PATH') . $nextVersion['name'] . '/old';
        $new_file_path = config('UPDATE_PATH') . $nextVersion['name'] . '/new';
        if (!$this->createFolder($old_file_path)) {
            $this->write('创建目录失败' . $old_file_path . '请检查权限', 'danger');
            return;
        }
        if (!$this->createFolder($new_file_path)) {
            $this->write('创建目录失败' . $new_file_path . '请检查权限', 'danger');
            return;
        }
		$current_url = appstoreU('goods/Update/download', array('number' => $nextVersion['number'], 'type' => 'old'));
		$next_url = appstoreU('goods/Update/download', array('number' => $nextVersion['number'], 'type' => 'new'));
		
        $this->downloadFile($current_url, config('UPDATE_PATH') . $nextVersion['name'] . '/old.zip');
        $this->unzipFile(config('UPDATE_PATH') . $nextVersion['name'] . '/old.zip', $old_file_path);

        $this->writeMessage('开始下载升级文件包。<br/>');
        $this->downloadFile($next_url, config('UPDATE_PATH') . $nextVersion['name'] . '/new.zip');
		
		
        $this->unzipFile(config('UPDATE_PATH') . $nextVersion['name'] . '/new.zip', $new_file_path);
        $files = $this->treeDirectory($new_file_path, $new_file_path);
        foreach ($files as $v) {
            $this->writeFile($v);
        }
        $this->writeScript('enable()');
        cache('nextVersion', $nextVersion);
        cache('currentVersion', $currentVersion);
    }

    /**
     * 比对代码
     */
    public function compare()
    {
        $this->assignVersionInfo();
        $currentVersion = cache('currentVersion');
        $nextVersion = cache('nextVersion');
        $old_file_path = config('UPDATE_PATH') . $nextVersion['name'] . '/old';
        $new_file_path = config('UPDATE_PATH') . $nextVersion['name'] . '/new';
        $compared_with_old = $this->diff($old_file_path);
        $compared_with_new = $this->diff($new_file_path);
        $compared = $compared_with_old + $compared_with_new;

        $this->assign('path', config('UPDATE_PATH') . $currentVersion['name']);
        $this->assign('compared', $compared);
        $this->disableCheckUpdate();
        echo $this->fetch();
        $this->enable = 1;
        foreach ($compared as $key => $v) {
            $this->writeFile("{$this->convert($key, $v)}");
        }
        if ($this->enable) {
            $this->writeScript('enable()');
        }
    }

    /**
     * 覆盖代码
     */
    public function cover()
    {
        $this->assignVersionInfo();
        $nextVersion = cache('nextVersion');
        $old_file_path = config('UPDATE_PATH') . $nextVersion['name'] . '/old';
        $new_file_path = config('UPDATE_PATH') . $nextVersion['name'] . '/new';
        $sub = date('Ymd-His');
        $backup_path = config('UPDATE_PATH') . $nextVersion['name'] . '/backup/' . $sub;
        $this->assign('backup_path', $backup_path);
		
		$need_back_old = $this->treeDirectory($old_file_path, $old_file_path);
        $need_back = $this->treeDirectory($new_file_path, $new_file_path);
        $this->disableCheckUpdate();
        echo $this->fetch();
        //备份文件
        $this->createFolder($backup_path);
        if (!file_exists($backup_path)) {
            $this->write('备份文件夹'.$backup_path.'创建失败，请检查文件夹权限。请确保文件夹'.config('CLOUD_PATH').'具备写入权限。升级暂时中止，请赋予权限后再次刷新本页面', 'danger');
            exit;
        } else {
            $this->write('创建备份文件夹' . $backup_path . '成功', 'success');
        }
		
        foreach ($need_back_old as $v) {
            $current_file = text($v);
            if ($current_file == '/update.sql') {
                continue;
            }
            $from = realpath('.' . $current_file);
            //替换斜杠，防止linux无法识别
            $des = realpath(str_replace('./', '', $backup_path)) . str_replace('/', DIRECTORY_SEPARATOR, $current_file);
            $des_dir = substr($des, 0, strrpos($des, DIRECTORY_SEPARATOR));
            $this->createFolder($des_dir);
            if (@copy($from, $des)) {
                @chmod($des, 0777);
                $this->write(str_replace('\\', '\\\\', '备份文件'.$current_file.'到'.str_replace('./', '', $backup_path) . $current_file.'……成功'), 'success');
            } else {
                $this->write(str_replace('\\', '\\\\', '备份文件'.$current_file.'到'.str_replace('./', '', $backup_path) . $current_file.'……失败，自动更新终止')	, 'danger');
            }
        }
        $this->write('文件全部备份完成');
        //覆盖文件
		
        foreach ($need_back as $v) {

            $from = realpath($new_file_path . text($v));
            $des = realpath('.' . str_replace('/', DIRECTORY_SEPARATOR, text($v)));

            if (!$des) {
                $des = str_replace('/', DIRECTORY_SEPARATOR, dirname(realpath('./index.php')) . text($v));
            }
            $des_dir = substr($des, 0, strrpos($des, DIRECTORY_SEPARATOR));
            if (!is_dir($des_dir)) {
                $this->createFolder($des_dir);
            }
            if (file_exists($des)) {
                unlink($des);
            }
            if (copy($from, $des)) {
                chmod($des, 0777);
                $this->writeFile(str_replace('\\', '\\\\', '覆盖文件' . $des) . '……成功');
            } else {
                $this->writeFile(str_replace('\\', '\\\\', '覆盖文件' . $des) . '……失败');
            }
        }
        $this->write('文件全部覆盖完成。');
        $this->writeScript('enable()');
    }


    /**
     * 升级数据库
     */
    public function updb()
    {
        $nextVersion = cache('nextVersion');
        $new_file_path = config('UPDATE_PATH') . $nextVersion['name'];
        $sql_path = $new_file_path . '/new/update.sql';
        $sql = @file_get_contents($sql_path);
        if (IS_POST) {
            if (!file_exists($sql_path)) {
                $this->error('数据库升级脚本不存在。');
            } else {
                $result = model('Module')->executeSqlFile($sql_path);
                if ($result) {
                    $this->success('脚本升级成功。');
                } else {
                    $this->error('脚本升级失败。');
                }
            }
        } else {
            $this->assignVersionInfo();
            $this->assign('path', $new_file_path);
            if (file_exists($sql_path)) {
                $this->assign('sql', $sql);
            }
            $this->disableCheckUpdate();
            return $this->fetch();
        }

    }

    /**
     * 升级完成
     */
    public function finish()
    {
        $nextVersion = cache('nextVersion');
        $versionModel = model('Version');
        $versionModel->where(array('name' => $nextVersion['name']))->setField('update_time', time());
        $this->assign('currentVersion', $versionModel->getCurrentVersion());
        $new_file_path = config('UPDATE_PATH') . $nextVersion['name'];
        $this->assign('path', $new_file_path);
        $this->disableCheckUpdate();
        $versionModel->cleanCheckUpdateCache();
		\think\Cache::clear();
		\think\Log::clear();
        return $this->fetch();
    }

    /**递归方式创建文件夹
     * @param $dir
     * @param int $mode
     * @return bool
     */
    private function createFolder($dir, $mode = 0777)
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) {
            return true;
        }
        if (!$this->createFolder(dirname($dir), $mode)) {
            return false;
        }
        return @mkdir($dir, $mode);
    }

    /**转换状态文字
     * @param $v
     * @return string
     */
    private function convert($file, $v)
    {

        $html = "<tr><td>$file</td><td>";
        switch ($v[0]) {
            case 'add':
                $html .= '<span class="text-warning"> <i class="icon-plus"></i> 新增，新版本新增的文件</span>';
                break;
            case 'modified':
                $html .= '<span class="text-danger" ><i class="icon-warning-sign"></i> 冲突，二次开发修改，未通过！</span>';
                break;
            case 'ok':
                $html .= '<span class="text-success"><i class="icon-check"></i> OK，和原版一样，通过</span>';
                break;
            case 'db':
                $html .= '<span class="text-info"><i class="icon-cube"></i> 数据库引导文件，通过</span>';
                break;
            case 'guide':
                $html .= '<span class="text-info"><i class="icon-cube"></i>引导脚本，通过</span>';
                break;
            case 'info':
                $html .= '<span class="text-info"><i class="icon-cube"></i> 版本信息文件，通过</span>';
                break;

        }
        $html .= '</td><td>';
        if ($v[1]) {
            $html .= '<span class="text-success"><i class="icon-check"></i> 文件写入权限检测通过</span>';
        } else {
            $html .= '<span class="text-danger"><i class="icon-warning-sign"></i>文件不具备写入权限，请在赋予该文件写入权限！</span>';
            $this->enable = 0;
        }
        $html .= '</td></tr>';
        return $html;

    }

    /**比较文件
     * @param $path
     * @return array
     */
    private function diff($path, $root = './', $ext_file = array('/update.sql' => array('db', 1), '/update.php' => array('guide', 1)))
    {
		
        $files = $this->treeDirectory($path, $path);
        $result = array();
        foreach ($files as $v) {
            $local_path = str_replace('//', '/', $root . text($v));
            $is_ext = false;
            foreach ($ext_file as $key => $ext) {
                if ($local_path == str_replace('//', '/', $root . $key)) {
                    $result[$v] = $ext;
                    $is_ext = true;
                    continue;
                }
            }
            chmod($path . text($v), 0777);
            @chmod($local_path, 0777);
            if ($is_ext)
                continue;
            $md5_source = md5_file($path . text($v));
            @$md5_local = md5_file($local_path);
            if (!$md5_local) {
                $result[$v] = array('add', 1);
            } else if ($md5_source != $md5_local) {
                $result[$v] = array('modified', is_writable($local_path));
            } else {
                $result[$v] = array('ok', is_writable($local_path));
            }
        }
        return $result;
    }

    private function getChmod($filepath)
    {
        return substr(base_convert(@fileperms($filepath), 10, 8), -4);
    }

    /**列出所有的文件
     * @param $dir
     * @param $root
     * @return array
     */
    private function treeDirectory($dir, $root)
    {
        $files = array();
        $dirpath = ($dir);
        $filenames = scandir($dir);
        foreach ($filenames as $filename) {
            if ($filename == '.' || $filename == '..') {
                continue;
            }
            $file = $dirpath . DIRECTORY_SEPARATOR . $filename;
            if (is_dir($file)) {
                $files = array_merge($files, $this->treeDirectory($file, $root));
            } else {
                $files[] = str_replace($root, '', str_replace('\\', '/', $dir . DIRECTORY_SEPARATOR . '<span class=text-success>' . $filename . '</span>'));
            }
        }

        return $files;
    }

    /**
     * 获取指定版本的信息
     */
    public function version()
    {
        $aName = input('name', '');
        $versionModel = model('Version');
        $version = $versionModel->where(array('name' => $aName))->find();
		
		$this->assign('data', $version);
        $this->assign('log', nl2br($version['log']));
       	$this->setMeta('自动升级');
		return $this->fetch();
    }

    private function assignUpdatingGoods($goods)
    {
        $cloudModel = model('Cloud');
		$moduleModel = model('Module');
        switch ($goods['entity']) {
            case 1:
                //todo 插件关联升级数据
                break;
            case 2:
                $goodsInfo = $moduleModel->getModule($goods['etitle']);
                $goodsInfo = $cloudModel->getVersionInfo($goodsInfo);
                break;
        }
        $this->assign('goodsInfo', $goodsInfo);
        return $goodsInfo;
    }

    /**
     * 升级云市场扩展
     */
    public function updateGoods()
    {

        $aToken = input('token', '');
        $cloudModel = model('Cloud');
        $version = $cloudModel->getVersion($aToken);

        if (!$version) {
            $this->error('您所升级的扩展并不存在，请检查token的正确性。');
        }
        $versionList = $cloudModel->getUpdateList($aToken);
        $this->assign('versionList', $versionList);
        $this->assign('token', $aToken);
        $this->assign('version', $version);
        cache('version', $version);
        cache('versionList', $versionList);
        cache('token', $aToken);
        $this->assignUpdatingGoods($version['goods']);


		$this->setMeta('扩展自动升级');
        return $this->fetch();
    }

    /**
     * 自动升级云市场扩展第一步，下载文件
     */
    public function updating1()
    {

        /*初始化各类信息*/
        $version = cache('version');
        $versionList = array_reverse(cache('versionList'));
        $token = cache('token');
        $this->assign('version', $version);
        $this->assign('versionList', $versionList);
        if (empty($version)) {
            $this->error('当前版本信息获取失败，请从扩展更新列表进入本页面。');
        }
        if (empty($versionList)) {
            $this->error('当前没有检测到新版本，请稍后重试。');
        }
        $this->assignUpdatingGoods($version['goods']);
        /*展示模板*/
        $path = config('CLOUD_PATH') . $this->switchEntity($version['goods']['entity']) . '/' . $version['goods']['etitle'] . '/' . $versionList[0]['title'];
		
		
	
        $pathOld = $path . '/old';
        $pathNew = $path . '/new';
        $this->assign('path', $path);
		$this->setMeta('扩展自动升级-下载文件');
        echo($this->fetch());
        set_time_limit(0);
        /*创建文件夹*/
        if (!$this->createFolder($pathOld)) {
            $this->write('创建原版文件夹失败，请检查权限。更新终止。' . $pathOld, 'danger');
            return;
        }
        if (!$this->createFolder($pathNew)) {
            $this->write('创建原版文件夹失败，请检查权限。更新终止。' . $pathNew, 'danger');
            return;
        }
		$current_url = appstoreU('goods/install/download', array('token' => $token, 'type' => 'current'));
		$next_url = appstoreU('goods/install/download', array('token' => $token, 'type' => 'next'));
        /*下载文件*/
        $this->write('开始下载原版' . $version['title'] . '文件。', 'info');
        $this->downloadFile($current_url, $path . '/old.zip');
        $this->write('开始下载新版' . $versionList[0]['title'] . '文件。', 'info');
        $this->downloadFile($next_url, $path . '/new.zip');
		
		
        /*解压缩文件夹*/
        $this->unzipFile($path . '/old.zip', $pathOld);

        $this->unzipFile($path . '/new.zip', $pathNew);

        $files = $this->treeDirectory($pathNew, $pathNew);
        foreach ($files as $v) {
            $this->writeFile($v);
        }
        $this->writeScript('enable()');
    }


    /**
     * 本地文件比较
     */
    public function updating2()
    {
        $version = cache('version');
        $versionList = array_reverse(cache('versionList'));
        $this->assign('version', $version);
        $this->assign('versionList', $versionList);
        if (empty($version)) {
            $this->error('当前版本信息获取失败，请从扩展更新列表进入本页面。');
        }
        if (empty($versionList)) {
            $this->error('当前没有检测到新版本，请稍后重试。');
        }
        $this->assignUpdatingGoods($version['goods']);
		$this->setMeta('扩展自动升级-比较本地文件');
		
		
        $path = config('CLOUD_PATH') . $this->switchEntity($version['goods']['entity']) . '/' . $version['goods']['etitle'] . '/' . $versionList[0]['title'];
        $pathOld = $path . '/old';
        $pathNew = $path . '/new';
        $old_file_path = $pathOld;
        $new_file_path = $pathNew;
        $compared_with_old = $this->diff($old_file_path, $this->switchDir($version['goods']['entity']), array($version['goods']['etitle'] . '/update.sql' => array('db', 1)));
        $compared_with_new = $this->diff($new_file_path, $this->switchDir($version['goods']['entity']), array($version['goods']['etitle'] . '/update.sql' => array('db', 1)));
        $compared = $compared_with_old + $compared_with_new;

        $this->assign('path', $path);
        $this->assign('compared', $compared);
        $this->disableCheckUpdate();
        echo($this->fetch());
        $this->enable = 1;
        foreach ($compared as $key => $v) {
            $this->writeFile("{$this->convert($key, $v)}");
        }
        if ($this->enable) {
            $this->writeScript('enable()');
        }

    }

    /**
     * 覆盖文件
     */
    public function updating3()
    {
        $version = cache('version');
        $versionList = array_reverse(cache('versionList'));
        $this->assign('version', $version);
        $this->assign('versionList', $versionList);
        if (empty($version)) {
            $this->error('当前版本信息获取失败，请从扩展更新列表进入本页面。');
        }
        if (empty($versionList)) {
            $this->error('当前没有检测到新版本，请稍后重试。');
        }
        $this->assignUpdatingGoods($version['goods']);
		$this->setMeta('扩展自动升级-升级代码');
        $path = config('CLOUD_PATH') . $this->switchEntity($version['goods']['entity']) . '/' . $version['goods']['etitle'] . '/' . $versionList[0]['title'];
        $pathOld = $path . '/old';
        $pathNew = $path . '/new';
        $old_file_path = $pathOld;
        $new_file_path = $pathNew;

        $sub = date('Ymd-His');
        $backup_path = $path . '/backup/' . $sub;
        $this->assign('backup_path', $path);
        $need_back = $this->treeDirectory($new_file_path, $new_file_path);
		$need_back_old = $this->treeDirectory($old_file_path, $old_file_path);
        $this->disableCheckUpdate();
		echo($this->fetch());
		
        //备份文件
        @mkdir($path . '/backup');
        @mkdir($backup_path);
        if (!file_exists($backup_path)) {
            $this->write('备份文件夹'.$backup_path.'创建失败，请检查文件夹权限。请确保文件夹'.config('CLOUD_PATH').'具备写入权限。升级暂时中止，请赋予权限后再次刷新本页面。', 'danger');
            exit;
        } else {
            $this->write('创建备份文件夹' . $backup_path .'成功。', 'success');
        }
		
        foreach ($need_back_old as $v) {
            if (text($v) == '') {
                continue;
            }
            $from = realpath($this->switchDir($version['goods']['entity']) . '/' . text($v));
            $des = realpath(str_replace('./', '', $backup_path)) . str_replace('/', DIRECTORY_SEPARATOR, text($v));
            $des_dir = substr($des, 0, strrpos($des, DIRECTORY_SEPARATOR));
            $this->createFolder($des_dir);
            @copy($from, $des);
            if (file_exists($des) === false) {
                $this->write('备份文件到文件' . str_replace('./', '', $backup_path) . text($v) . '失败，请检查文件夹权限', 'danger');
            } else {
                $this->write(str_replace(array('\\', '//'), array('\\\\', '/'), '备份文件'.$this->switchDir($version['goods']['entity']) . text($v).'到'.str_replace('./', '', $backup_path) . text($v).'……成功'), 'success');	
            }
        }
        $this->write('文件全部备份完成');
        //覆盖文件
        foreach ($need_back as $v) {

            $from = realpath($new_file_path . text($v));
            $des = str_replace(array('/', '.' . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $this->switchDir($version['goods']['entity']) . text($v));
            $des_dir = substr($des, 0, strrpos($des, DIRECTORY_SEPARATOR));
            if (!is_dir($des_dir)) {
                $this->createFolder($des_dir);
            }
            if (file_exists($des)) {
                unlink($des);
            }
            if (copy($from, $des)) {
                chmod($des, 0777);
                $this->writeFile(str_replace('\\', '\\\\', '覆盖文件' . $des) . '……成功');
            } else {
                $this->writeFile(str_replace('\\', '\\\\', '覆盖文件' . $des) . '……失败');
            }
        }
        $this->write('文件全部覆盖完成');
        $this->writeScript('enable()');
    }

    /**
     * 导入数据库改动
     */
    public function updating4()
    {
        $version = cache('version');
        $versionList = array_reverse(cache('versionList'));

        $this->assignUpdatingGoods($version['goods']);
        $path = config('CLOUD_PATH') . $this->switchEntity($version['goods']['entity']) . '/' . $version['goods']['etitle'] . '/' . $versionList[0]['title'];
        $pathOld = $path . '/old';
        $pathNew = $path . '/new' . '/' . $version['goods']['etitle'] . '/';
		
        $sql_path = $pathNew . '/update.sql';
        if (IS_POST) {
            if (!file_exists($sql_path)) {
                $this->error('数据库升级脚本不存在。');
            } else {
                $result = model('Module')->executeSqlFile($sql_path);
                if ($result === true) {
                    $this->success('脚本升级成功。');
                } else {
                    $this->error('脚本升级失败。');
                }
            }
        }
        $this->assign('version', $version);
        $this->assign('versionList', $versionList);
        if (empty($version)) {
            $this->error('当前版本信息获取失败，请从扩展更新列表进入本页面。');
        }
        if (empty($versionList)) {
            $this->error('当前没有检测到新版本，请稍后重试。');
        }

        $this->assign('path', $pathNew);
        if (file_exists($sql_path)) {
            $this->assign('sql', @file_get_contents($pathNew . '/update.sql'));
        }
		$this->setMeta('扩展自动升级-升级数据库');
        return $this->fetch();
    }


    /**
     * 完成，设置版本号和token
     */
    public function updating5()
    {
        $version = cache('version');
        $versionList = array_reverse(cache('versionList'));
        $this->assign('version', $version);
        $this->assign('versionList', $versionList);
        $newToken = $versionList[0]['token'];
        switch ($version['goods']['entity']) {
            case 1:
                //todo 插件设置Token
                break;
            case 2:
                $moduleModel = model('Module');
                $moduleModel->setToken($version['goods']['etitle'], $newToken);
                $moduleModel->reloadModule($version['goods']['etitle']);
                cache('version', $versionList[0]);
                $this->cleanModuleListCache();
                break;
        }

        $this->assignUpdatingGoods($version['goods']);
        $path = config('CLOUD_PATH') . $this->switchEntity($version['goods']['entity']) . '/' . $version['goods']['etitle'] . '/' . $versionList[0]['title'];
        $this->assign('token', $newToken);
        $this->assign('path', $path);
        return $this->fetch();
    }

    private function cleanModuleListCache()
    {
        cache('admin_modules', null);
    }


    /**
     * 安装程序
     */
    public function install()
    {
        $aToken = input('token', '');
        $aCookie = input('cookie', '');
		
        cache('cloud_cookie', $aCookie);
        set_time_limit(0);
		echo $this->fetch();
		if(!$aToken){
			$this->write('获取版本失败......', 'danger');
			return false;
		}
        $this->write('自动安装程序开始......<br/>如果您在安装过程中遇到问题，请先确保全站文件权限都为<span class="text-danger">777</span><br/>开始获取版本信息......', 'info');
        $this->write('&nbsp;&nbsp;&nbsp;>' . '连接远程服务器....', 'info');
		$url = appstoreU('goods/install/getVersion', array('token' => $aToken)); 
        //$this->writeMessage(file_get_contents(appstoreU('goods/install/getVersion', array('token' => $aToken))));
        $data = $this->curl($url);
        if ($data === 'false') {
            $this->write('&nbsp;&nbsp;&nbsp;>服务器登陆验证失败。安装意外终止', 'danger');
            return;
        }
        $data = json_decode($data, true);
        if (!$data['status']) {
            $this->write('解析服务器返回结果失败。安装意外终止' . $data['info'], 'danger');
        }
        $version = $data['version'];
		
		
        switch ($version['goods']['entity']) {
            case 1:
                /*$this->installPlugin($version, $aToken);*/
				
				$this->write('&nbsp;&nbsp;&nbsp;>' . '本功能正在开发中...', 'danger');
				$this->goBack();
				return;
                break;
            case 2:
                $this->installModule($version, $aToken);
                break;
        }
    }

    private function installPlugin($version, $token)
    {
        $plugin['name'] = $version['goods']['etitle'];
        $plugin['alias'] = $version['goods']['title'];
        $this->write('&nbsp;&nbsp;&nbsp;>' .'当前正在安装的是【插件】，插件名' . '【' . $plugin['alias'] . '】【' . $plugin['name'] . '】');
		
        if (file_exists(QINFO_ADDON_PATH . '/' . $plugin['name'])) {
            //todo 进行版本检测
            $this->write('&nbsp;&nbsp;&nbsp;>' . '本地已存在同名插件，安装被强制终止。请卸载并删除本地模块文件之后刷新本页面重试。3秒后回到上一页', 'danger');
            $this->goBack();
            return;
        }
        //下载文件

        $localPath = config('CLOUD_PATH') . $this->switchEntity($version['goods']['entity']) . '/';
        $this->createFolder($localPath);
        $localFile = $localPath . $plugin['name'] . '.zip';
		$current_url = appstoreU('goods/index/download', array('token' => $token));
        $this->downloadFile($current_url, $localFile);
        chmod($localFile, 0777);
        //开始安装
        $this->write('开始安装插件......');
        $this->unzipFile($localFile, QINFO_ADDON_PATH);
        $rs = model('Addons')->install($plugin['name']);
        if ($rs === true) {

            $tokenFile = QINFO_ADDON_PATH . $plugin['name'] . '/token.ini';
            if (file_put_contents($tokenFile, $token)) {
                $this->write('&nbsp;&nbsp;&nbsp;>' . '插件安装成功。即将跳转到本地主题页面', 'success');
                $jump = url('Addons/index');
                sleep(2);
                $this->writeScript("location.href='$jump';");
                return true;
            } else {
                $this->write('&nbsp;&nbsp;&nbsp;>插件安装成功，但未能成功写入token，请手动创建并复制token内容到'.$tokenFile.'下。token内容<br/>' . $token, 'warning');
                return true;
            }

        } else {
            $this->write('&nbsp;&nbsp;&nbsp;>插件安装失败', 'danger');
        }

        //todo 进行文件合法性检测，防止错误安装。

    }

    private function installModule($version, $token)
    {

        $module['name'] = $version['goods']['etitle'];
        $module['alias'] = $version['goods']['title'];
        $this->write('&nbsp;&nbsp;&nbsp;>当前正在安装的是【模块】，模块名' . '【' . $module['alias'] . '】【' . $module['name'] . '】');
        if (file_exists(APP_PATH . $version['goods']['etitle'])) {
            //todo 进行版本检测
            $this->write('&nbsp;&nbsp;&nbsp;>' . '本地已存在同名模块，安装被强制终止。请卸载并删除本地模块文件之后刷新本页面重试。3秒后回到上一页', 'danger');
            $this->goBack();
            return false;
        }
        //下载文件
        $localPath = config('CLOUD_PATH') . $this->switchEntity($version['goods']['entity']) . '/';
        $this->createFolder($localPath);
        $localFile = $localPath . $version['goods']['etitle'] . '.zip';
		$current_url = appstoreU('goods/index/download', array('token' => $token));
        $this->downloadFile($current_url, $localFile);
        //开始安装
        $this->unzipFile($localFile, APP_PATH);
        //todo 进行文件合法性检测，防止错误安装。
        if (!file_exists(APP_PATH . $version['goods']['etitle'] . '/' . 'Info/info.php')) {
            $this->write('文件验证失败，无法执行安装，请检查文件结构。');
            exit;
        }
        $moduleModel = model('Module');
        $moduleModel->reload();
        $module = $moduleModel->getModule($module['name']);
        $res = $moduleModel->install($module['id']);
        if ($res === true) {
            $this->write($moduleModel->getError());
            $this->write('&nbsp;&nbsp;&nbsp;>安装模块成功', 'success');
            $tokenFile = APP_PATH . $module['name'] . '/Info/token.ini';
            $this->cleanModuleListCache();
            if ($moduleModel->setToken($module['name'], $token)) {
                $this->write('模块安装成功，即将跳转到本地模块页面。', 'success');
                $jump = url('Module/index');
                sleep(2);
                $this->writeScript("location.href='$jump';");
            } else {
                $this->write('模块安装成功，但未能成功写入token，请手动创建并复制token内容到'.$tokenFile.'下。token内容<br/>' . $token, 'warning');
                return true;
            } 

        } else {
            $this->write('模块安装失败。错误信息：' . $moduleModel->getError(), 'warning');
        }

        return true;
    }

    private function downloadFile($url, $local)
    {
		
		$url =$url.'?t='.time().range(1,10);
		
        $file = fopen($url, "rb");
        if ($file) {
            //获取文件大小
            $filesize = -1;
            $headers = get_headers($url, 1);
            if ((!array_key_exists("Content-Length", $headers))) $filesize = 0;
            $filesize = $headers["Content-Length"];
            //不是所有的文件都会先返回大小的，有些动态页面不先返回总大小，这样就无法计算进度了
            if (file_exists($local)) {
                unlink($local);
            }
            if (isset($headers['Location'])) {
                $url = $headers['Location'];
            }
            if (is_array($filesize)) {
                $filesize = $filesize[1];
            }
            $filesize = intval($filesize);

            if ($filesize != -1) {
                $this->write('&nbsp;&nbsp;&nbsp;>文件总大小—' . number_format($filesize / 1024, 2) . 'KB');
                $this->write('&nbsp;&nbsp;&nbsp;>开始下载文件');
                // $this->showProgress();
            }
            /* $newf = fopen($local, "wb");
             $downlen = 0;
             $total = 0;
          /* if ($newf) {
                 while (!feof($file)) {
                     $data = fread($file, 1024 * 8);//默认获取8K
                     $downlen += strlen($data);//累计已经下载的字节数
                     fwrite($newf, $data, 1024 * 8);
                     $total += 1024 * 8;
                     if ($total > 1024 * 1024 * 2) {
                         $total = 0;
                         $this->setValue('"' . number_format($downlen / $filesize * 100, 2) . '%' . '"');
                         $this->replace('&nbsp;&nbsp;&nbsp;>已经下载' . number_format($downlen / $filesize * 100, 2) . '% - ' . number_format($downlen / 1024 / 1024, 2) . 'MB', 'success');
                     }
                 }
             }
             if ($file) {
                 fclose($file);
             }
             if ($newf) {
                 fclose($newf);
             }*/
            $this->getFile($url, $local);
            @chmod($local, 0777);
            if (filesize($local) == 0) {
                $this->replace('&nbsp;&nbsp;&nbsp;文件大小异常，下载失败。', 'danger');
                // $this->hideProgress();
                exit;
            }
            $this->replace('&nbsp;&nbsp;&nbsp;文件下载完成......', 'success');
            $this->hideProgress();
        } else {
            $this->write('&nbsp;&nbsp;&nbsp;>文件下载失败，请检查php配置[allow_url_open]是否为on', 'danger');
            exit;
        }
    }

    private function getFile($url, $path, $type = 0)
    {
        if (trim($url) == '') {
            return false;
        }

        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $content = ob_get_contents();
            ob_end_clean();
        }
        $size = strlen($content);
        //文件大小
        $fp2 = @fopen($path, 'a');
        fwrite($fp2, $content);
        fclose($fp2);
        unset($content, $url);
        return $path;
    }

    private function setValue($val)
    {
        $js = "progress.setValue($val)";
        $this->writeScript($js);
    }

    private function showProgress()
    {
        $js = "progress.show();";
        $this->writeScript($js);
    }

    private function hideProgress()
    {
        $js = "progress.hide();";
        $this->writeScript($js);
    }

    private function url($url)
    {
        return cloud_url() . $url;
    }

    private function writeMessage($str)
    {
        $js = "writeMessage('$str')";
        $this->writeScript($js);
    }

    private function writeFile($str)
    {
        $js = "writeFile('$str')";
        $this->writeScript($js);
    }

    private function replaceMessage($str)
    {
        $js = "replaceMessage('$str')";
        $this->writeScript($js);
    }

    private function goBack()
    {
        $this->writeScript("setTimeout(function(){history.go(-1);},3000);");
    }

    private function writeScript($str)
    {
        echo "<script>$str</script>";
        ob_flush();
        flush();
    }

    private function replace($str, $type = 'info', $br = '<br>')
    {
        $this->replaceMessage('<span class="text-'.$type.'">'.$str.'</span>'.$br);
    }

    private function write($str, $type = 'info', $br = '<br>')
    {
        $this->writeMessage('<span class="text-'.$type.'">'.$str.'</span>'.$br);
    }


    private function curl($url)
    {
        return model('Curl')->curl($url);
    }


    /**
     * @param $localFile
     * @param $localPath
     */
    private function unzipFile($localFile, $localPath)
    {
        $archive = new \PclZip($localFile);
        $this->write('&nbsp;&nbsp;&nbsp;>开始解压文件......');
        $list = $archive->extract(PCLZIP_OPT_PATH, $localPath, PCLZIP_OPT_SET_CHMOD, 0777);
        if ($list === 0) {
            $this->write('&nbsp;&nbsp;&nbsp;>解压失败。'. $archive->errorInfo(true));
            exit;
        }
        unlink($localFile);
        $this->write('&nbsp;&nbsp;&nbsp;>解压成功。', 'success');
    }

    private function assignVersionInfo()
    {
        $currentVersion = cache('currentVersion');
        $nextVersion = cache('nextVersion');
        $this->assign('nextVersion', $nextVersion);
        $this->assign('currentVersion', $currentVersion);
    }

    private function switchEntity($entity)
    {
        switch ($entity) {
            case 1:
                return 'Addons';
            case 2:
                return 'Module';
        }
    }

    private function switchDir($entity)
    {
        switch ($entity) {
            case 1:
                return QINFO_ADDON_PATH;
            case 2:
                return APP_PATH;
        }
    }
} 