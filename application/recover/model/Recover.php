<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/25
 * Time: 10:18
 */

namespace app\recover\model;
use think\Model;

class Recover extends Model
{


    //信息过滤
    static public function info_filter($data){

        $res_data['id'] = $data['id'];
        $res_data['alias'] = $data['alias'];
        $res_data['ip'] = $data['ip'];
        $res_data['unique_id'] = $data['unique_id'];
        $res_data['type'] = $data['type'];
        $res_data['harddisk_info'] = json_decode($data['harddisk_info'],true);

        return $res_data;
    }

    //将快照以磁盘id分类
    static public function handle_snap_list($snap_list){

        $new_data = array();
        foreach($snap_list as $k=>$v){
            $new_data[$v['harddisk_id']][] = $v;
        }
        return $new_data;
    }

    static public function handle_harddisk_info($info){

        $info = json_decode($info,true);
        $disks = array();
        foreach ($info as $k=>$v) {
            $disk['aliasname'] = $v['aliasname'];
            $disk['harddiskindex'] = $v['harddiskindex'];
            $disk['physicalname'] = $v['physicalname'];
            $disk['totalsize'] = format_bytes($v['totalsize']);
            $disks[] = $disk;
        }
        return $disks;

    }



    /**
     *  获取设备下的所有任务id
     *  SELECT task.id,COUNT(task.id) AS snap_count
    FROM qinfo_cdp_task AS task
    INNER JOIN qinfo_cdp_snap AS snap
    ON task.`id`=snap.`task_id`
    WHERE task.`device_id`=4
    GROUP BY task.`id`;
     *
     */
    static public function get_task($device_id){

//        $field = array('id','create_time');
        $map['device_id'] = $device_id;
        $map['type'] = array('neq','move');
        $prefix = config('database.prefix');
        $sql = "SELECT  task.id,task.create_time,COUNT(task.id) AS snap_count
			 FROM ".$prefix."cdp_task AS task
			 INNER JOIN ".$prefix."cdp_snap AS snap
			 ON task.`id`=snap.`task_id`
			 WHERE task.`device_id`=".$device_id."
			 GROUP BY task.`id`;";
//        $tasks = db('cdp_task')->where(array('device_id'=>$device_id))->field($field)->select();
        $tasks = db()->query($sql);

        return $tasks;
    }


    static public function getHarddiskSize($harddisk_info){
        $harddisk_info = json_decode($harddisk_info,true);
        $diskSize = array();
        foreach($harddisk_info as $k=>$v){
            $key = "harddiskindex_".$v['harddiskindex'];
            $diskSize[$key] = format_bytes($v['totalsize']);
        }
        return $diskSize;
    }


    /********************发送消息*****************************/

    static public function send_status_cdp_message($data){

//		w2p_start_recover 启动
//		w2p_retry_recover  重试
        if ($data['status'] == 1 || $data['status'] == 4)
            $message_str = 'w2p_start_recover';
        else if($data['status'] == 0)
            $message_str = 'w2p_stop_recover';

        $message_data['recoverTaskId'] =$data['id'];
        $message_data['nIsTemp'] = $data['is_temp'];

        $result = \Netmessage::send_message($message_str, $message_data);

        if ($result['code'] == 1) {
            return true;
        } else {
            return false;
        }
    }


    static public function send_get_recover_info_message($id){

        $message_data['recoverTaskId'] = $id;
        $result = \Netmessage::send_message('w2p_get_recovering_info', $message_data);
        return $result;

    }
}