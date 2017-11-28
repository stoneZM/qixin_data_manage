<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/24
 * Time: 16:11
 */

namespace app\cdp\model;
use app\device\model\ComputeVirtual;
use think\Model;
use app\device\model\Libvirt;

class Compute extends Model
{

    protected $resultSetType = 'collection';

    // 获取计算池 内存和cpu信息 如果没有，则写入
    static function getComputeIfo($id){

        $data = self::get($id);
        if(!$data){
            return array("max_cpu"=>2 ,"total_mem" =>2048);
        }
        $compute_info = json_decode($data['compute_info'],true);
        if(!$compute_info['base_info']){
            $base_info = Libvirt::get_compute_info($data['ip']);
            $compute_info["base_info"] =  $base_info;
            self::update(['id'=>$data['id'],'compute_info'=>json_encode($compute_info)]);
            return $base_info;
        }
        return $compute_info['base_info'];
    }

    /**
     *   获取计算池列表
     */
    static function getComputeList(){

        $list = self::getAll();
        foreach($list as $k=>&$v){
            $compute_info = json_decode($v['compute_info'],true);
            $base_info = $compute_info['base_info'];
            $v['max_cpu'] = $base_info['max_cpu'];
            $v['total_mem'] = $base_info['total_mem'];
            $v['free_mem'] = $base_info['free_mem'];
        }
        return $list;
    }


    static function getAll(){

        $list = self::all(['status'=>1]);

        if($list){
            return $list->toArray();
        }else{
            return array();
        }

    }

    //更具虚拟机id获取ip
    static public function get_ip($id){
        $comput_info = db('compute')->field(array('ip'))->where(array('id'=>$id))->find();
        return $comput_info['ip'];
    }


    /******************发送消息******************************/
    static public function send_update_compute_message($id,$is_update=1){
        if ($is_update){
            $message_str = 'w2p_update_libvirt_computer_info';
        }else{
            $message_str = 'w2p_add_libvirt_computer';
        }
        $data['computerId'] = $id;
        $result = \Netmessage::send_message($message_str, $data);
        if ($result['code'] == 1) {
            return true;
        } else {
            return false;
        }
    }

}