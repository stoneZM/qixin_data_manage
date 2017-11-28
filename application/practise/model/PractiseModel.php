<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/20
 * Time: 9:35
 */

namespace app\practise\model;
use app\cdp\model\CdpSnap;
use app\device\model\Device;
use app\remote\model\Base;


class PractiseModel extends Base
{

     static function get_virtual_log($device_id,$is_paginate=true){

        $device_ip = Device::where(array('id'=>$device_id))->column('ip');
        $device_ip = current($device_ip);
        $vhost_model = db('vhost_info');

        if($is_paginate){
            $list = $vhost_model
                ->where(array('vhost_source_ip'=>$device_ip,'vhost_snap_id'=>array('neq',0)))
                ->paginate(20);
        }else{
            $list = $vhost_model
                ->where(array('vhost_source_ip'=>$device_ip,'vhost_snap_id'=>array('neq',0)))
                ->select();
        }
         if($list){
             foreach ($list as $key => $vo) {
                 $snap_time = CdpSnap::where(array('id'=>$vo['vhost_snap_id']))->column('update_time');
                 $vo['snap_time'] = date('Y-m-d H:i:s',current($snap_time));
                 if(!$is_paginate){
                     $vo['is_active'] = $vo['is_active']? "正常" : "不正常";
                     $vo['is_normal'] = $vo['is_normal']? '正常': "不正常";
                 }
                 $list[$key] = $vo;
             }
         }
         return $list;
    }

    /**
     *  将时间转换为24小时制
     */
    static public function switch_time($time){

        $hour = sprintf("%02d",(int)($time/3600));
        $min = sprintf("%02d",(int)($time/60)%60);
        return $hour . ' : ' . $min;
    }

    static public function get_day_str($day){
        $days = explode(',',$day);
        $chars = array('一','二','三','四','五','六','日');
        $day_str = '';
        foreach($days as $item){
            $day_str .= '周'.$chars[$item-1].' ; ';
        }
        return $day_str;
    }

    static public function  num2char($day){

        $days = explode(',',$day);
        $days_str = array();
        for($i=1;$i<8;$i++){
            if(in_array($i,$days)){
                $days_str[$i] = 'checked';
            }
        }
        return $days_str;
    }


    /***********************************发送消息*********************************************/
    /*
     *  const HEADER = 1;
        const ISSTRATEGYUSED = 2;
        const ISNEWSTRATEGY = 3;
        const VHOSTSOURCEIP = 4;
        const WEEKDAY = 5;
        const TIME = 6;
     */

   static public function send_create_message($is_used,$host_ip,$week,$time,$is_add=1){


       $message_str = "w2p_config_auto_exercise_strategy";
       $data['ISSTRATEGYUSED'] = $is_used;
       $data['ISNEWSTRATEGY'] = $is_add;
       $data['VHOSTSOURCEIP'] = $host_ip;
       $data['WEEKDAY'] = $week;
       $data['TIME'] = $time;


       $result = \Netmessage::send_message($message_str, $data);
       if($result['code'] == 1){
           return true;
       }else{
           return false;
       }

   }



   //W2P_Create_Vhost_Finished
    /**
     *  const VHOSTID = 2;
     *  const VHOSTNAME = 3;
     * const VHOSTSOURCEIP = 4;
     *  const VHOSTSTATUS = 5;
     */
    static public function send_create_finished_message($host_id,$source_ip,$host_name,$host_status,$snap_id=0){

        $message_str = "W2P_Create_Vhost_Finished";
        $data['VHOSTID'] = $host_id;
        $data['VHOSTSOURCEIP'] = $source_ip;
        $data['VHOSTNAME'] = $host_name;
        $data['VHOSTSTATUS'] = $host_status;    // 0 => 刚创建  1=> 已存在
        $data['VHOSTSNAPID'] = $snap_id;

        $result = \Netmessage::send_message($message_str, $data);
        if($result['code'] == 1){
            return true;
        }else{
            return false;
        }

    }


    //w2p_destroy_vhost_finished
    /**
     *  const VHOSTNAME = 2;
     */
    static public function send_destroy_message($host_name){

        $message_str = "w2p_destroy_vhost_finished";
        $data['VHOSTNAME'] = $host_name;

        $result = \Netmessage::send_message($message_str, $data);
        if($result['code'] == 1){
            return true;
        }else{
            return false;
        }

    }





}