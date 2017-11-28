<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/26
 * Time: 13:11
 */

namespace app\cdp\model;
use app\admin\controller\Log;
use think\Model;

class CdpSnap extends Model
{

    protected $resultSetType = "collection";
    private   $snap_data = '';
    private   $storage_id = 0;
    protected $datetime_format = false;
    public    $snap_type3_count; // 类型是3的快照数
    protected $task_id ;
    public $begin_time = '';    //一组快照的开始时间
    public $end_time = '';      //一组快照的结束时间
    protected $last_date_time = '';


    public function get_snap($task_id,$date){

        $this->task_id = $task_id;
        $begin_time = strtotime($date);
        $end_time = $begin_time + 86400 - 1;


        if($date){
           $map['create_time'] = array('between',"$begin_time,$end_time");
            //last_date_time
            $this->last_date_time = $end_time+1;
        }
        $map['task_id'] = array('eq',$task_id);
        $map['virtual_id'] = array('eq',0);
        $map['keli_id'] = array('eq',0);


        $this->snap_data = $this->where($map)
                                 ->order('id asc')
                                 ->select()
                                 ->toArray();

        $this->snap_type3_count = ($this->get_last_type3_snap($task_id)==-1)?0:1;


        $this->snap_data = $this->handle_snap_list($this->snap_data);

        return $this->snap_data;
    }


    /**
     *  根据任务和子任务 id 删除snap
     * @param $task_id
     * @param $sub_task_id
     */
    public function del($task_id,$sub_task_id){

        $this->snap_data = $this->field('id,harddisk_id,file_name,group_id,storage_id,create_time,update_time')
                          ->where(array('task_id'=>$task_id,'sub_task_id'=>$sub_task_id,'virtual_id'=>0,'keli_id'=>0))
                          ->order('id asc')
                          ->select();
        if($this->snap_data){
            $this->snap_data = $this->snap_data->toArray();
        }else{
            return false;
        }
        if(count($this->snap_data) == 0)
            return false;

        $this->storage_id =  current($this->snap_data)['storage_id'];
        $this->begin_time = $this->snap_data[0]['update_time'];
        $this->end_time = end($this->snap_data)['update_time'];

        if (!$this->check_have_virtual($this->snap_data))
            return false;

        $data = array();
        foreach($this->snap_data as $item){
            $data[] = $item["id"];
        }
//        $this->send_del_snap_message();
        if($this->destroy($data)){
            $this->send_del_snap_message();
            return true;
        }else{
            return false;
        }
    }

    public function del_snap_by_task_id($task_id){

       $this->snap_data = $this->field('id,harddisk_id,file_name,group_id,storage_id,create_time,update_time')
           ->where(array('task_id'=>$task_id))
           ->order('id asc')
           ->select()->toArray();
       if(count($this->snap_data) == 0){
           return true;
       }
       $this->storage_id = current($this->snap_data)['storage_id'];

       $data = array();
       foreach($this->snap_data as $item){
           $data[] = $item["id"];
       }
//       $this->send_del_snap_message();

       if($this->destroy($data)){
           $this->send_del_snap_message();
           return true;
       }else{
           return false;
       }
   }

    public function del_temp_snap($virtual_id){

        $this->snap_data = $this->field('id,harddisk_id,file_name,group_id,storage_id')
            ->where(array('virtual_id'=>$virtual_id))
            ->select()
            ->toArray();
//      $this->send_del_snap_message();

        if($this->destroy(['virtual_id'=>$virtual_id])){
            $this->send_del_snap_message();
        }

    }



    //处理snap数据
    public function handle_snap_list($list){

        $data = array();

        $last_type3_group_id = $this->get_last_type3_snap($this->task_id);
        foreach($list as $k=>$v){

            $group_id = $v['group_id'];
            $task_id = $v['task_id'];
            $data[$group_id]['disk'][] = $v;
            $data[$group_id]['type'] = $v['type'];

            if($data[$group_id]['type'] == 3){
                $data[$group_id]['flag'] = 1;

            }

            $data[$group_id]['create_time'] = strtotime($v['update_time'])?:strtotime($v['create_time']);
            $data[$group_id]['update_time'] = strtotime($v['create_time']);
            $data[$group_id]['group_id'] = $v['group_id'];
            $data[$group_id]['task_id'] = $v['task_id'];
            $data[$group_id]['sub_task_id'] = $v['sub_task_id'];
            $snap_id = $task_id.'_'.$group_id;

            // 判断是否是最后一个类型为4的快照点
            if($last_type3_group_id == $v['group_id']){
                $data[$group_id]['is_last'] = 1;
            }

            if($virtual = db('compute_virtual')->where(array('snap_id'=>$snap_id))->field(array('id','name','type'))->find()){
                $data[$group_id]['vir_type'] = $virtual['type'];
                $data[$group_id]['vir_title'] = $virtual['name'];
                $data[$group_id]['virtual_id'] = $virtual['id'];
//				$data[$group_id]['virtual'] = $virtual;
//				$data[$group_id]['virtual_count'] = count($virtual);
            }
            else{
//				$data[$v['group_id']]['virtual_count'] = 0;
                $data[$v['group_id']]['vir_type'] = -1;
            }
        }

        //将系统磁盘排在第一位
        foreach($data as $k=>$v){
            $data[$k]['disk'] = $this->arr_multisort($v['disk'],'have_os',SORT_DESC);
            if($data[$k]['disk'][0]['have_os'] == 1){
                $data[$k]['have_os'] = 1;
            }
            if($data[$k]['flag'] == 1){
                $data[$k]['type'] = 3;
            }
        }

        $data = $this->construct_data($data);
        return $data;
    }

    // 对snap的type进行排序，type为0是为原始点 type为1是快照点，type为2是实施点
    private function arr_multisort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){
        if(is_array($arrays)){
            foreach ($arrays as $array){
                if(is_array($array)){
                    $key_arrays[] = $array[$sort_key];
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
        array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
        return $arrays;
    }


    //重新构造数据
    private function construct_data($tasks){

        $data = array();
        $count = count($tasks);
        $flag = 0;
        $last_snap_type = end($tasks)['type'];


        // 根据系统类型，判断是否获可生成颗粒
        if(is_high_system()){ //高级版本则显示颗粒等信息

            foreach($tasks as $k=>$v){
//                if(($last_snap_type==2)&&($flag==$count-3 || $flag==$count-2)){
                if(($last_snap_type==2&&$flag==$count-2) || $v['type']==2){
                    $v['begin_time'] = $v['update_time'];
                    if ($v['type'] == 2){
                        $v['end_time'] = time();
                    }else{
                        $v['end_time'] = $v['create_time']-1;
                    }
                    $v['available'] = 1;
                }else{
                    $v['available'] = 0;
                }
                $v['keli'] = $this->getKeli($v['task_id'],$v['group_id']);
                $data[] = $v;
                $flag += 1;
            }

            return $this->handle_keli_available($this->last_date_time,$data);

        }else{
            //不是高级版本则不显示颗粒等信息
            foreach($tasks as $k=>$v){
                $v['available'] = 0;
                $data[] = $v;
                $flag += 1;
            }
            return $data;
        }
    }


    /**
     * 处理当前点独立在某一天的情况
     * @param $time
     */
    private function handle_keli_available($time,$data){


        if(!$time){
            return $data;
        }
        $begin_time = $time;
        $end_time = $begin_time + 86400 - 1;
        $map['create_time'] = array('between',"$begin_time,$end_time");
        $map['task_id'] = array('eq', $this->task_id);
        $map['virtual_id'] = array('eq', 0);
        $map['keli_id'] = array('eq', 0);

        $snap_count = $this->field('id')->where($map)->count();

        //如果没有快照或者大于3个不做处理
        if($snap_count==0||$snap_count>=3){
            return $data;
        }
        //如果小于三个，查看是否有当前点
        $map['type'] = 2;
        $current_snap = $this->field('id,create_time,update_time')->where($map)->order('id asc')->select();
        // 如果不存在，则不做处理
        if(!$current_snap){
            return $data;
        }
        $current_snap = $current_snap->toArray();
        $end_time = strtotime(current($current_snap)['create_time']);
        //否则存在并且个数只有1或2，则返回个数
        if($snap_count==2){

            $last_snap = array_pop($data);
//            $last_snap['available'] = 1;
            $last_snap['available'] = 0;
            $last_snap['begin_time'] = $last_snap['update_time'];
            $last_snap['end_time'] = $end_time-1;

            array_push($data,$last_snap);
            return $data;

        }
        if($snap_count==1){


            if(count($data)==1){
                if($data[0]['type']==0){
                    return $data;
                }
                $data[0]['available'] = 1;
                $data[0]['begin_time'] = $data[0]['update_time'];
                $data[0]['end_time'] = $end_time-1;
                return $data;

            }else{

                $last_snap = array_pop($data);
//                $last_two_snap = array_pop($data);
//                $last_two_snap['available'] = 1;
//                $last_two_snap['begin_time'] = $last_two_snap['update_time'];
//                $last_two_snap['end_time'] =  $last_two_snap['create_time']-1;
//                $last_two_snap['keli'] = $this->getKeli($last_two_snap['task_id'],$last_two_snap['group_id']);
//                array_push($data,$last_two_snap);
                $last_snap['available'] = 1;
                $last_snap['begin_time'] = $last_snap['update_time'];
                $last_snap['end_time'] = $end_time-1;
//                $last_snap['keli'] = $this->getKeli($last_snap['task_id'],$last_snap['group_id']);
                array_push($data,$last_snap);
            }

            return $data;
        }

        return $data;
    }



    /**
     *  获取某个快照处的所有已创建的颗粒
     */
    private function getKeli($task_id,$group_id){

        $keli = db('cdp_keli')->where(array('task_id'=>$task_id,'group_id'=>$group_id))->order('keli_time asc')->select();

        $virtual_model = db('compute_virtual');
        $field = array('type','name');
        foreach($keli as $k=>&$v){

            if( $v['status'] == 2 ){
                $virtual_info = $virtual_model->where(array('id'=>$v['virtual_id']))->field($field)->find();
                $v['virtual_type'] = ($virtual_info['type'] == 1) ? 'T' : 'R';
                $v['virtual_name'] = $virtual_info['name'];
            }
        }
        return $keli;
    }



    /**
     *  发送删除快照的消息
     */
    protected function send_del_snap_message(){

        $res = $this->regroup_snap_data();
        $unique_id = $this->get_unique_id($this->storage_id);
        foreach($res as $key=>$v){
            $this->send_message($v,$unique_id);
        }
    }

    private function send_message($snap_path,$unique_id){

        $message_str = "w2p_delete_ahdr_file";
		$data['sStorageId'] = $unique_id;
        $data['spath'] = $snap_path;

        \Netmessage::send_message($message_str, $data);

    }

    function get_unique_id($storage_id){

        if ($storage_id){
            $storage = db('storage')
                       ->where(array('id'=>$storage_id))
                       ->field(array('unique_id'))
                       ->find();
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
        return $data['sStorageId'];
    }


    /**
     *  检查是否有虚拟机存在
     *  true : 不存在虚机
     *  false : 存在虚机
     */
    function check_have_virtual($id){

        if(!$id){
           $this->error = lang('params').lang('error');
           return false;
        }

        if(is_array($id)){
            $res = true;
            foreach($id as $v){
                $res = $this->check_have_virtual($v['id']);
                if($res)
                   continue;
               else
                   break;
            }
            return $res;
        }else{
            $res = $this->field('group_id,task_id')->where(array('id'=>$id,'virtual_id'=>0,'keli_id'=>0))->find()->toArray();
            $snap_id = $res['task_id']."_".$res['group_id'];

            if(db('compute_virtual')->field("id")->where(array('snap_id'=>$snap_id))->find()){
                $this->error = lang('have_virtual_forbid_del');
                return false;
            }
            return true;
        }
    }


    /**
     *   重组 删除快照点消息体内容
     */

    private function regroup_snap_data(){

        $temp_data = array();
        $file_path = $this->get_disk_path();
        foreach($this->snap_data as $k=>$v){
            $file_name = array_slice(explode('/',$v['file_name']),-1)[0];

            $temp_data[$v['harddisk_id']]['file_name'] .= $file_name.";";
        }

        $res_data = array();
        foreach($temp_data as $k=>$v){
           $res_data[$k] = $file_path[$k].';'.$v['file_name'];
        }
        return $res_data;
    }

    private function get_disk_path(){


        $group_id = reset($this->snap_data)['group_id'];
        $file_path = array();
        foreach($this->snap_data as $ke=>$v){

            if($group_id!==$v['group_id']){
                break;
            }
            $temp = explode('/',$v['file_name']);
            array_pop($temp);
            $file_path[$v['harddisk_id']]  = implode('/',$temp);
        }
        return $file_path;
    }


    /**
     *  根据 virtual_id 获取 task_id 和 sub_task_id
     */
    public function get_tid_by_vid($virtual_id){

        $res = $this
            ->where(array('virtual_id'=>$virtual_id))
            ->field(array('task_id,sub_task_id'))
            ->find()
            ->toArray();
        return $res;
    }

    /**
     *   获取类型4的最后一条数据
     */
    private function get_last_type3_snap($task_id,$group_id=null){


        $map['task_id'] = array('eq',$task_id);
        $map['virtual_id'] = array('eq',0);
        $map['keli_id'] = array('eq',0);
        $map['type'] = array('eq',4);
        if($group_id){
            $map['group_id'] = array('neq',$group_id);
        }

       $res = $this->field('id,group_id')
                   ->where($map)
                   ->order("update_time desc")
                   ->find();

       if($res){
           $is_all_type_4 = $this->field('id')
                 ->where(array('group_id'=>$res->group_id,'virtual_id'=>0,'keli_id'=>0,'type'=>3))
                 ->count();
           if(!$is_all_type_4){
               return $res->group_id;
           }else{
             return  $this->get_last_type3_snap($task_id,$res->group_id);
           }
       }
       return -1;
    }


    // 获取 发送创建虚机消息的所用的snap路径
    static function get_snap_filepath($snaps){

        $spath = array();
        foreach ($snaps as $k=>$v) {
            $spath[] = $v['file_name'];
        }
        $spath = implode(';',$spath);

        return $spath;
    }

    //根据给定日期获取前后两天时间
    static function getNextAndPreDayTime($time){

        $current_time = time();
        $time_unix = strtotime($time);
        $duration = $current_time - $time_unix;
        $data['pre'] = date("Y-m-d",strtotime( $time."-1 day" ));
        $data['next'] = date("Y-m-d",strtotime( $time."+1 day" ));
       if($duration>86400){  //表明是过去日期
           $data['pre_disable'] = false;
           $data['next_disable'] = false;
       }elseif ($duration<0 && $duration < -86400){ //表明是未来日期
           $data['pre_disable'] = true;
           $data['next_disable'] = true;
       }elseif ($duration>-86400){
           $data['pre_disable'] = false;
           $data['next_disable'] = true;
       } else{  //今日日期
           $data['pre_disable'] = false;
           $data['next_disable'] = true;
       }
        return $data;
    }


    // 根据快照点的数据，构造新的数据，主要用于构造tmp_vmdk
    static  function get_temp_vmdk_record($snap_list){

        foreach($snap_list as $k=>&$v){

            $file_name = $v['file_name'];
            $explode_file_name = explode('.',$file_name);
            $v['file_name'] = $explode_file_name[0].'_temp'.'.'.$explode_file_name[1];
            $v['parent_id'] = $v['id'];
        }
        return $snap_list;
    }


    // 根据设备id 获取 快照数
    static function getSnapCountByDeviceId($deivce_id){

        $cdp_task_ids = CdpTask::field('id')
            ->where(array('device_id'=>$deivce_id))
            ->select();
        if($cdp_task_ids){
            $cdp_task_ids = $cdp_task_ids->toArray();
        }
        $total_count = 0;
        foreach($cdp_task_ids as $key=>$value){
            $total_count += self::where(array('task_id'=>$value['id'],'virtual_id'=>0,'keli_id'=>0))
                ->group('group_id')
                ->count('id');
        }
        return $total_count;
    }

    static function getSnapCountByTime($task_id,$time = ''){

        $time = $time?:date("Y-m-d",time());
        $begin_time = strtotime($time);
        $end_time = $begin_time + 86400 - 1;

        $map['create_time'] = array('between',"$begin_time,$end_time");
        $map['virtual_id'] = array('eq',0);
        $map['keli_id'] = array('eq',0);
        $map['task_id'] = array('eq',$task_id);

       $count =  self::where($map)->group('group_id')->count(array('id'));
        return $count ;
    }

    static function getNewestSnapTime($task_id){

        $map['task_id'] = array('eq',$task_id);
        $map['virtual_id'] = array('eq',0);
        $map['keli_id'] = array('eq',0);

        return self::where($map)
              ->field('create_time')
              ->order('create_time')
              ->limit(1)
              ->select()->toArray();
    }

    //插入新的数据
    static public function insert_temp_snap($snap_list,$virtual_id){

        foreach($snap_list as $k=>$v){
            unset($v['id']);
            $v['virtual_id'] = $virtual_id;
            $v['create_time'] = time();
            db('cdp_snap')->insert($v);
        }
    }


    /************************日志************************************/

    static function  createSnapLog($device_id,$result,$desc=''){
        $extend['color'] = $result?"green":"red";
        $extend['result'] = $result?"成功":'失败';
        $extend['desc'] = $desc;
        action_log('create_snap', 'cdp', $device_id, session('user_auth.uid'),$extend);
    }

    static function delSnapLog($device_id,$result,$task_time,$begin_time,$end_time,$desc=''){

        $extend['color'] = $result?"green":"red";
        $extend['result'] = $result?"成功":'失败';
        $extend['desc'] = $desc;
        $extend['task_time'] = $task_time;
        $extend['begin_time'] = $begin_time;
        $extend['end_time'] = $end_time;
        action_log('del_snap', 'cdp', $device_id, session('user_auth.uid'),$extend);

    }

}
