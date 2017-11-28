<?php
/**
 * 云市场的本地操作Model
 * User: Yixiao Chen
 * Date: 2015/6/30 0030
 * Time: 下午 3:00
 */

namespace app\admin\model;

class Cloud
{

    protected $curlModel;

    public function checkUpdateByList()
    {

    }
    /**验证远程地址
     * @param $token
     * @return bool
     */
    public function checkurl()
    {
		$url = cloud_url();
		$return = $this->curl($url);
        if ($return === false) {
            return false;
        }else{
            return true;
        }
    }
    /**通过Token获取版本信息
     * @param $token
     * @return bool
     */
    public function getVersion($token)
    {
		$url = appstoreU('goods/install/getVersion', array('token' => $token));
		$return = $this->curl($url);
        if ($return === false) {
            return false;
        }
        $goods = json_decode($return, true);
        if ($goods['status'] == 1) {
            return $goods['version'];
        } else {
            $this->error = $goods['info'];
            return false;
        }
    }

    /**获取升级列表
     * @param $token 通过token获取升级列表
     */
    public function getUpdateList($token)
    {
        $new_versions = array();
		
		$url = appstoreU('goods/install/getupdatelist', array('token' => $token));
        $return = $this->curl($url);
        $versions = json_decode($return, true);
        if ($versions['status'] == 1) {
            return $versions['updateList'];
        } else {
            $this->error = '更新版本列表不存在';
            return false;
        }
    }

    /**打开远程连接
     * @param $url 链接地址
     * @return bool
     */
    private function curl($url)
    {
        $this->curlModel = model('Curl');
        $return = $this->curlModel->curl($url);
        if ($return === false) {
            $this->error = '数据连接错误';
            return false;
        }
        return $return;
    }

    /**获取版本信息
     * @param $list
     */
    public function getVersionInfoList($list)
    {
		if($list){
			foreach ($list as &$vo) {
				if (!empty($vo['token'])){
					$vo = $this->getVersionInfo($vo);
				}
			}
		}
        return $list;
    }

    public function getVersionInfo($data)
    {
        $data['version_info'] = $this->getVersion($data['token']);
        $data['update_list'] = $this->getUpdateList($data['token']);
        return $data;
    }
}