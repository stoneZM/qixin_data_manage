<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/28
 * Time: 11:07
 */

namespace app\device\model;
use app\cdp\model\Compute;
use think\Model;


define('MAX_PORT',5999);
define("MIN_PORT",5933);

class ComputeVirtual extends Model
{

    protected $resultSetType = "collection";


    // 获取一台设备下的虚机数量
    static function getCount($id,$field=''){
        return self::where(array('device_id'=>$id))->field($field)->count();
    }

    static function getCountByTaskId($id,$field=''){
        return self::where(array('task_id'=>$id))->field($field)->count();
    }

    // 获取在线的虚拟机个数
    static function getVirtualOnCount($id,$field=""){
        return self::where(array('device_id'=>$id,'status'=>1))->field($field)->count();
    }

    static function getVirtualListByDeviceId($device_id){

        $field = array('id,snap_id,task_id,device_id,name,type,status,remote_port,cpu_kernel,memory,create_time,source_device_ip,comput_id');

        $list = self::where(array('device_id'=>$device_id))->field($field)->order('id desc')->paginate(20);
        foreach($list as $key=>&$value){

            $compute_ip = Compute::get_ip($value['comput_id']);
            $value['compute_ip'] = $compute_ip;
        }

        return $list;

    }


    // 关闭一个计算池下的所有虚拟机
    static function stop_all_virtual($compute_id){

        $compute_info = db('compute')
                        ->field(array('ip'))
                        ->where(array('id'=>$compute_id))
                        ->find();
        $virtual_info =  self::field('name')
                             ->where(array('comput_id'=>$compute_id,"status"=>1))
                             ->select();
        self::where(array('comput_id'=>$compute_id,"status"=>1))->update(['status'=>0]);
        if ($virtual_info){
            $virtual_info = $virtual_info->toArray();
            foreach ($virtual_info as $k=>$v){
                VirtualManager::stop_virtual($v['name'],$compute_info['ip']);
            }
        }
    }


    // 获取 虚拟机下的 可用的端口号
    static function get_remote_port($compute_id){

        $remote_port_list = self::field(array('remote_port'))
                                ->where(array('comput_id'=>$compute_id))
                                ->order('remote_port ASC')
                                ->select();

        if($remote_port_list){
            $remote_port_list = $remote_port_list->toArray();
        }else{
            $remote_port_list = array();
        }

        $remote_ports = array();
        foreach($remote_port_list as $k=>$v){
            $remote_ports[] = $v['remote_port'];
        }

        if(empty($remote_ports)){
            $remote_port = MIN_PORT;
        }else{
            $max_port = end($remote_ports);
            $remote_port = $max_port+1;

            if($remote_port>MAX_PORT){
                $remote_port = self::get_diff_remote_port($remote_ports,MIN_PORT);
            }
        }
        return $remote_port;
    }

    private  static function get_diff_remote_port($port_list,$cur_port){

        if($cur_port>MAX_PORT){
            return false;
        }
        if(in_array($cur_port,$port_list)){
            return self::get_diff_remote_port($port_list,$cur_port+1);
        }
        return $cur_port;
    }

    /********************删除temp文件*******************************/




    /********************日志*******************************/
    public static function addVirtualLog($name,$device_id,$virtual_type,$result,$desc=''){

        $extend['virtual_name'] = $name;
        $extend['virtual_type'] = $virtual_type?"演练":"接管";
        $extend['color'] = $result?"green":"red";
        $extend['result'] = $result?"成功":"失败";
        $extend['desc'] = $desc;
        action_log('add_virtual','cdp', $device_id, session('user_auth.uid'),$extend);
    }

    public static function delVirtualLog($name,$device_id,$result,$desc=''){

        $extend['virtual_name'] = $name;
        $extend['color'] = $result?"green":"red";
        $extend['result'] = $result?"成功":"失败";
        $extend['desc'] = $desc;
        action_log('del_virtual','cdp', $device_id, session('user_auth.uid'),$extend);
    }

    public static function editVirtualLog($name,$device_id,$edit_cnt,$result,$desc=''){

        $extend['virtual_name'] = $name;
        $extend['edit_cnt'] = $edit_cnt;
        $extend['color'] = $result?"green":"red";
        $extend['result'] = $result?"成功":"失败";
        $extend['desc'] = $desc;
        action_log('edit_virtual','cdp', $device_id, session('user_auth.uid'),$extend);
    }

    public static function statusVirtualLog($name,$device_id,$action,$result,$desc=''){

        $extend['virtual_name'] = $name;
        $extend['color'] = $result?"green":"red";
        $extend['result'] = $result?"成功":"失败";
        $extend['desc'] = $desc;
        action_log($action,'cdp', $device_id, session('user_auth.uid'),$extend);
    }


}