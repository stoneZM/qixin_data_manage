<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/25
 * Time: 10:18
 */

namespace app\mount\model;
use think\Model;

class Mount extends Model
{
    static public function send_mount_snap_message($data)
    {
        // $message_data['recoverTaskId'] = $data['task_id'];
        // $result = \Netmessage::send_message('w2p_get_recovering_info', $message_data);
        $message_str = 'w2p_mount_snap';
        $message_data['TaskId'] = $data['task_id'];
        $result = \Netmessage::send_message($message_str, $message_data);
        if ($result['code'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    static public function send_umount_snap_message($data)
    {
        $message_str = 'w2p_umount_snap';
        $message_data['TaskId'] = $data['task_id'];
        $result = \Netmessage::send_message($message_str, $message_data);
        if ($result['code'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    static public function info_filter($data){

        $res_data['id'] = $data['id'];
        $res_data['alias'] = $data['alias'];
        $res_data['ip'] = $data['ip'];
        $res_data['unique_id'] = $data['unique_id'];
        $res_data['type'] = $data['type'];
        $res_data['harddisk_info'] = json_decode($data['harddisk_info'],true);

        return $res_data;
    }

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

    static public function get_task($device_id){
        $map['device_id'] = $device_id;
        $map['type'] = array('neq','move');
        $prefix = config('database.prefix');
        $sql = "SELECT  task.id,task.create_time,COUNT(task.id) AS snap_count
			 FROM ".$prefix."cdp_task AS task
			 INNER JOIN ".$prefix."cdp_snap AS snap
			 ON task.`id`=snap.`task_id`
			 WHERE task.`device_id`=".$device_id."
			 GROUP BY task.`id`;";
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
}
