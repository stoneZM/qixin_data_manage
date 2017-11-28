<?php
/**
 * Created by PhpStorm.
 * User: stone
 * Date: 2017/7/20
 * Time: 13:46
 */

namespace app\device\model;

use app\device\model\Compute;
use think\Log;

class VirtualManager
{

    protected $ip = '';   //计算池的ip
    protected $error = '';

    //开启虚拟机
    static function start_virtual($domName, $ip)
    {

        $url = get_libvirt_url($ip);
        $lv = new Libvirt($url);

        $is_running = $lv->domain_is_running($domName);

        if ($is_running) return true;
        $start = $lv->domain_start($domName);
        if ($start)
            return true;
        else
            return false;

    }

    //关闭虚拟机
    static function stop_virtual($domName, $ip)
    {

        $url = get_libvirt_url($ip);
        $lv = new Libvirt($url);
        if (!$lv) return false;

        $is_running = $lv->domain_is_running($domName);
        if (!$is_running) return true;

        $destroy = $lv->domain_destroy($domName);
        if ($destroy)
            return true;
        else
            return false;
    }

    /**关闭所有虚拟机
     * @param $compute_id
     * @throws \think\Exception
     */
    static public function stop_all_virtual($compute_id){

        $compute_info = db('compute')->field(array('ip'))->where(array('id'=>$compute_id))->find();
        $virtual = db('compute_virtual');
        $virtual_info =  $virtual->where(array('comput_id'=>$compute_id,"status"=>1))
            ->field('name')
            ->select();
        $data['status'] = 0;
        $virtual->where(array('comput_id'=>$compute_id,"status"=>1))->update($data);
        if ($virtual_info){
            foreach ($virtual_info as $k=>$v){
                VirtualManager::stop_virtual($v['name'],$compute_info['ip']);
            }
        }
    }

    /**
     *  获取虚拟机状态
     */
    static function get_virtual_status($domName, $ip)
    {

        $url = get_libvirt_url($ip);
        $lv = new Libvirt($url);
        return $lv->domain_is_running($domName);
    }

    /**
     * 删除虚拟机
     */
    static function del_virtual($domName, $ip)
    {

        $url = get_libvirt_url($ip);
        $lv = new Libvirt($url);
        $res = $lv->domain_undefine($domName);
        if ($res)
            return true;
        else
            return false;
    }

    /**
     *判断虚拟机是否存在
     */
    static function is_virtual_exist($domName, $ip)
    {

        $url = get_libvirt_url($ip);
        $lv = new Libvirt($url);
        $doms = $lv->get_domains();

        if ( $doms && in_array($domName, $doms))
            return true;
        else {
            return false;
        }
    }


    /**
     * 创建虚拟机
     * @param 虚拟机名称
     * @param cpu数量
     * @param 内存数量
     * @param 网卡信息
     * @param 磁盘信息
     * @param 端口
     * @param 磁盘类型
     * @param other info => 系统类型
     * @return bool
     */
    static function libvirt_create_virtual($data){

        //根据计算池的ip获取计算节点
        $comput_id =   $data['comput_id'];

        $compute_info = db('compute')->field(array('ip'))->where(array('id'=>$comput_id))->find();
        $ip = $compute_info['ip'];


        $url = get_libvirt_url($ip);
        $lv = new Libvirt($url);

        $port = $data['remote_port'];
        $feature = array('apic', 'acpi', 'pae');
        $disk = null;

        $other_info['system_type'] = $data['system_type'];
        $other_info['is_sata'] = $data['is_sata'];

        if(!empty($data['vmdk_path'])){
            $disk['path'] = $data['vmdk_path'];
        }

        $clock_offset = 'UTC';
        $tmp = $lv->domain_new($data['name'],$data['cpu_kernel'], $feature, $data['memory'], $data['memory'], $clock_offset, $data['net_data'], $disk,$port,$data['disktype'],true,$other_info);
        if (!$tmp){
            return false;
        }
        else{
            return true;
        }
    }


    static function send_create_virtual_messgae($paths,$storageId=0,$system_type=1){


        $message_str = "w2p_create_ahdr_file";

        if ($storageId){
            $storage = db('storage')->where(array('id'=>$storageId))->field(array('unique_id'))->find();
            $data['sStorageId'] = $storage['unique_id'];
        }else{
            $storage = db('storage')->field(array('unique_id'))->select();
            foreach($storage as $key=>$value){
                if ($value['unique_id']){
                    $data['sStorageId'] = $value['unique_id'];
                    break;
                }
            }
        }
//		$data['sStorageId'] = $storageId[0]['unique_id'];
        $data['spath'] = $paths;
        $data['nSysType'] = $system_type;
        $result = \Netmessage::send_message($message_str, $data);

        if ($result['code'] == 1) {
            return true;
        } else {
            return false;
        }
    }




}