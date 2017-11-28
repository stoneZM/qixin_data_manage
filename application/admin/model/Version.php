<?php

namespace app\admin\model;
use think\Db;
class Version extends \think\Model
{

    /**检查是否有新的更新
     * @return bool
     */
    public function checkUpdate()
    {
        $result = cache('admin_update');
        if ($result === false) {

            if ($this->getNextVersion() == '') {
                $result = 0;
            } else {
                $result = 1;
            }
            cache('admin_update', $result, 600);
        }
        return $result;

    }
    public function cleanCheckUpdateCache(){
        cache('admin_update',null);
    }

    /**获取当前的版本号
     * @return string
     */
    public function getCurrentVersion()
    {
        $version = $Think.QINFOCMS_VERSION;
        $version = Db::name('version')->where(array('name' => $version))->find();
        return $version;
    }

    public function getVersions(){
        $version=cache('admin_versions');

        if($version===false){
            $this->refreshVersions();
            $version =Db::name('version')->order('number desc')->select();
            cache('admin_versions',$version);
        }
        return $version;
    }

    /**设置当前版本号
     * @param $name 版本号
     * @return int|void
     */
    public function setCurrentVersion($name)
    {
    }

    /**
     * 重新从服务器获取所有的版本信息并更新本地
     */
    public function refreshVersions()
    {
		
		$versions_url = appstoreU('goods/update/versions');
        $content = @file_get_contents($versions_url);
		$versions = json_decode($content, true);
		if($versions){
			$version_db = Db::name('version');
			foreach ($versions as $key => $v) {
				$version = $version_db->where('name',$v['name'])->find();
				if (!$version) {
					$version_db->insert($v);
				} else {
					unset($v['update_time']);
					$version = $v + $version;
					$version_db->update($version);
				}
			}
			$version_db->where(array('name' => array('not in', getSubByKey($versions, 'name'))))->delete();
		}else{
			return false;
		}
    }

    public function getNextVersion()
    {
        $versions = Db::name('version')->order('number asc')->select();
        $currentVersion = $this->getCurrentVersion();
        foreach ($versions as $v) {
            if (version_compare($v['name'], $currentVersion['name']) == 1) {
                return $v;
            }
        }
    }
}
