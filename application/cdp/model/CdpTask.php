<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/18
 * Time: 17:37
 */

namespace app\cdp\model;
use app\device\model\ComputeVirtual;
use app\device\model\Device;
use app\remote\model\Remote;
use think\model;

class CdpTask extends model
{

    protected $resultSetType = 'collection';



    static function  getSourceTaskByDeviceId($device_id,$no_current=true){

           $map['device_id'] = $device_id;
           if($no_current){
               $map['type'] = array('eq','cdp');
           }else{
               $map['type'] = array('neq','move');
           }

          $task = self::all(function($query) use ($map){

            $query->where($map)->order('create_time','desc');
         });

         if($task)
             return $task->toArray();
         else
             return array();
    }


    // 统计一台设备下任务数
    static function getCount($id,$field=' '){

        return self::where(array('device_id'=>$id))->field($field)->count();

    }

    static function getTaskByTime($device_id,$time=''){

        $time = $time ? : date('Y-m-d',time());
        $begin_time = strtotime($time);
        $end_time = $begin_time + 86400 - 1;
        $map['create_time'] = array('between',"$begin_time,$end_time");
        $map['device_id'] = array('eq',$device_id);
        $task_ids =  self::where($map)->column('id');
        return $task_ids;
    }

    static function getNewestTaskId($device_id){

        $map['device_id'] = array('eq',$device_id);
        $map['type'] = array('neq','move');

        return self::where($map)
                    ->order('create_time desc')
                    ->field('id')
                    ->limit(1)->select()->toArray();
    }


    //处理磁盘和分区信息
    static function handle_disk_part_info($info){
        $data = array();
        foreach($info as $k=>$v){

            $element = explode(',',$v);
            $item['key'] = $element[0];
            $item['harddiskindex'] = (int)$element[1];
            $item['partitionindex'] = (int)$element[2];
            $item['totalsize'] = $element[3];
            $item['status'] = 0;
            $item['driverletter'] = $element[4];
            $data[] = $item;
        }
        return json_encode($data);
    }

    //处理时间
    static function conver_time($time,$type){
        switch($type){
            case 'sec':
                return $time;
                break;
            case 'min':
                return $time*60;
                break;
            case 'hour':
                return $time*60*60;
                break;
            case 'day':
                return $time*60*60*24;
                break;
        }
    }

    //处理时间
    static function handle_time($task){

        $snap_inter = self::get_time_type($task['snap_time']);
        $synchr_inter = self::get_time_type($task['synchr_time']);

        $task['snap_inter_type'] = $snap_inter['type'];
        $task['snap_time'] = $snap_inter['time'];
        $task['synchr_interval_type'] = $synchr_inter['type'];
        $task['synchr_time'] = $synchr_inter['time'];
        return $task;
    }

    static function update_type($id){

        if($id){
            self::where(array('id'=>$id))->update(array('type'=>"cdp"));
        }

    }

    //修复 存在多个当前任务的情况
    static function modify_cdp_type($device_id){

        if($device_id){
            $task_ids = self::where(array('device_id'=>$device_id,'type'=>'current'))
                            ->order('id desc')
                            ->column('id');
            if(count($task_ids)>1){
                array_shift($task_ids);
                $map['id'] = array('in',$task_ids);
                self::where($map)->update(array('type'=>'cdp'));
            }
        }
    }


    /**
     *  检查存储容量
     */



    //获取时间的type
    static function get_time_type($time){

        if($time<60){
            return array('time'=>$time,'type'=>'sec');
        }
        if($time<3600){
            if($time%60==0)
                $new_time = $time/60;
            else
                $new_time = sprintf("%.1f",$time/60);
            return array('time'=>$new_time,'type'=>'min');
        }

        if($time<3600*24){
            if($time%3600==0)
                $new_time = $time/3600;
            else
                $new_time = sprintf("%.1f",$time/3600);
            return array('time'=>$new_time,'type'=>'hour');
        }

        if($time%(3600*24)==0)
            $new_time = $time/(3600*24);
        else
            $new_time = sprintf("%.1f",$time/(3600*24));
        return array('time'=>$new_time,'type'=>'day');
    }

    //获取分区信息
    static function get_work_partition($harddisk_info){

        $harddisk_info = json_decode($harddisk_info,true);
        $work_partition = array();

        foreach($harddisk_info as $k=>$v){
            if(count($v['partitions'])==0){
                continue;
            }
            $part_info = $v['partitions'];
            foreach($part_info as $key=>$value){
                $data['harddiskindex'] = $value['harddiskindex'];
                $data['partitionindex'] = $value['partitionindex'];
                $data['totalsize'] = $value['totalsize'];
                $data['driverletter'] = $value['driverletter'];
                $data['key'] = $value['harddiskindex'].'_'.$value['partitionindex'];
                $data['status'] = 0;
                $work_partition[] = $data;
            }
        }
        return json_encode($work_partition);
    }


    //检查是否能能创建任务
    static function check_can_add_task($device_id){


        // 如果有迁移任务的存在，则不能添加任务
        $table_prefix = config('database.prefix');
        $sql = "show tables like '".$table_prefix."move';";
        $res = db()->query($sql);
        if(!$res){
            return true;
        }
        $device_info = db('device')->where(array('id'=>$device_id))->field('ip')->find();
        $moving_info = db("move")->where(array('status'=>1,'source_ip'=>$device_info['ip']))->field('id')->find();
        if($moving_info){
            return false;
        }else{
            return true;
        }
    }




    //判断cdp任务是否全部完成
    static function task_is_finished($id){

        $info = self::field('work_partition')->where(array('id'=>$id))->find();
        $work_partition = json_decode($info['work_partition'],true)?:array();
        $hasFinishedFlag = 1;  //判断任务是否全部完成的标志
        foreach($work_partition as $k=>$v){
            if ($v['status'] != 3) {
                $hasFinishedFlag = 0;
                break;
            }
        }
        if($hasFinishedFlag){
            return true;
        }else{
            return false;
        }
    }
    static public function clone_is_running($id){
        $hasnoRunningFlag = 0;  //判断是否有分区在备份
        $info = self::field('work_partition')->where(array('id'=>$id))->find();
        $work_partition = json_decode($info['work_partition'],true)?:array();
        foreach($work_partition as $k=>$v){
            if ($v['status'] == 1) {
                $hasnoRunningFlag = 1;
                break;
            }
        }
        return $hasnoRunningFlag;
    }




    //新建任务时，检查是否要设置 snappoing_path
    static function new_task_set_snappoint($task_id,$device_id){

        $device_info = Device::field('ip')->where(array('id'=>$device_id))->find();
        $ip = $device_info['ip'];
        $prefix = config('database.prefix');
        $table_name = $prefix.'remote_task';

        if(self::check_table_exist($table_name)){
           //如果存在 对应的实时任务
            $map = array('from'=>$ip,'is_realtime'=>1);
            $remote_task = Remote::get($map);
            if($remote_task){

                self::where(array('id'=>$task_id))->update(array('snappoint_path'=>$remote_task['to']));

            }
        }
    }



    //设置 task 的 snappoint_path 表明是分发任务是否是实时任务
    static function set_snappoint($device_id,$target_ips,$type='set'){

        $map = array('device_id'=>$device_id,'type'=>'current');
        $task = self::field('id')->where($map)->find();
        $updateField['snappoint_path'] = $target_ips;

        self::where($map)->update($updateField);
        $unique_id = Device::get_unipue_id($device_id);
        self::send_task_message($unique_id,$task->id,2);
        return $task->id;

    }

    static function check_table_exist($table_name){

        $db_model = db();
        $sql = "show tables like '".$table_name."' ;";
        return $db_model->query($sql);
    }

    //创建task 和 修改task 的时候发消息
    static function send_task_message($unique_id,$task_id,$type){

        $data['deviceuniqueid'] =$unique_id;
        $data['cdpTaskId'] = $task_id;

        if($type==1)
            $message_str = 'w2p_add_cdp';
        else if($type == 2)
            $message_str = 'w2p_modify_cdp';

        $result = \Netmessage::send_message($message_str, $data);

        if ($result['code'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    static function check_can_del($id){

        $virtual_count = ComputeVirtual::getCountByTaskId($id,'id');
        if($virtual_count>0)
            return false;
        return true;

    }


    static function send_delete_task_message($task_id){

        $message_str = "w2p_delete_dir";
        $data['nTaskId'] = $task_id;
        $result = \Netmessage::send_message($message_str, $data);
        if($result['code'] == 1)
            return true;
        else
            return false;
    }

    //删除任务
    static function del_task($id){

        if(CdpTask::send_delete_task_message($id)){
            if(CdpTask::destroy(['id'=>$id])){
                //删除快照点
                CdpSnap::destroy(['task_id'=>$id]);
                return true;

            } else{
                return false;
            }
        }else{
                return false;
        }

    }

    static function stop_remote_task($task_id){

        $snappoint_path = self::where(array('id'=>$task_id))->column('snappoint_path');
        if(!empty(current($snappoint_path))){
           $table_name = config('database.prefix')."remote_task";
           $sql = "SHOW TABLES LIKE '".$table_name ."'";
            if(db()->query($sql)){
                db('remote_task')->where(array('to'=>current($snappoint_path)))->update(array('status'=>3));
            }
        }
    }


    /***************日志信息**********************/
    static function addCdpTaskLog($device_id,$result,$desc=''){

        $extend['color'] = $result?"green":'red';
        $extend['result'] = $result?"成功":'失败';
        $extend['desc'] = $desc;
        action_log('add_cdp_task', 'cdp', $device_id, session('user_auth.uid'),$extend);
    }

    static function delCdpTaskLog($device_id,$result,$task_time,$desc=''){

        $extend['color'] = $result?"green":'red';
        $extend['result'] = $result?"成功":'失败';
        $extend['task_time'] = $task_time;
        $extend['desc'] = $desc;
        action_log('del_cdp_task', 'cdp', $device_id, session('user_auth.uid'),$extend);
    }


    static function editCdpTaskLog($device_id,$result,$task_time,$desc=''){

        $extend['color'] = $result?"green":'red';
        $extend['result'] = $result?"成功":'失败';
        $extend['task_time'] = $task_time;
        $extend['desc'] = $desc;
        action_log('edit_cdp_task', 'cdp', $device_id, session('user_auth.uid'),$extend);
    }

    static function statusCdpTaskLog($device_id,$action,$result,$task_time,$desc=''){

        $extend['color'] = $result?"green":'red';
        $extend['result'] = $result?"成功":'失败';
        $extend['task_time'] = $task_time;
        $extend['desc'] = $desc;
        action_log($action, 'cdp', $device_id, session('user_auth.uid'),$extend);
    }
}