<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/18
 * Time: 13:35
 */

namespace app\remote\model;


class RemoteSnap extends Base
{

    private   $snap_data = '';
    private   $storage_id = 0;
    protected $datetime_format = false;
    protected $snap_type3_count; // 类型是3的快照数
    protected $unique_id ;





    public function get_snap($unique_id,$date,$task_id){

        $this->unique_id = $unique_id;

        if(!$task_id){
            return array();
        }

        $map['unique_id'] = array('eq',$unique_id);
        $map['type'] = array('between','0,4');
        $map['virtual_id'] = array('eq',0);
        $map['task_id'] = $task_id;

        $this->snap_data = $this->where($map)
            ->order('create_time asc')
            ->select()->toArray();

        $this->snap_type3_count = $this->where(array('unique_id'=>$unique_id,'type'=>3))->count();


        $this->snap_data = $this->handle_snap_list($this->snap_data);

        return $this->snap_data;
    }

    public function get_snap_task_id($unique_id){

        $task_id_list = RemoteSnap::where(array('unique_id'=>$unique_id))
            ->distinct(true)
            ->order('task_id')
            ->column('task_id');

        return $task_id_list;

    }


    /**
     *  根据任务和子任务 id 删除snap
     * @param $task_id
     * @param $sub_task_id
     */


    public function del_temp_snap($virtual_id){

        $this->snap_data = $this->field('file_name,harddisk_id,group_id,unique_id')
            ->where(array('virtual_id'=>$virtual_id))
            ->select()
            ->toArray();
	 if($this->destroy(['virtual_id'=>$virtual_id])){
            $this->send_del_snap_message('123');
        }

    }


    //处理snap数据
    public function handle_snap_list($list){

        $data = array();

        $last_type3_snap_id = $this->get_last_type3_snap($this->unique_id);
        foreach($list as $k=>$v){
            $group_id = $v['group_id'];

            $data[$group_id]['disk'][] = $v;
            $data[$group_id]['type'] = $v['type'];
            $data[$group_id]['create_time'] = strtotime($v['create_time']);
            $data[$group_id]['group_id'] = $v['group_id'];
            $data[$group_id]['unique_id'] = $v['unique_id'];
//            $data[$group_id]['sub_task_id'] = $v['sub_task_id'];
//            $snap_id = $this->unique_id.'_'.$group_id;
            $virtual_id = $this->get_virtual_id($group_id);
            // 判断是否是最后一个类型为3的快照点

            if($last_type3_snap_id == $v['id']){
                $data[$group_id]['is_last'] = 1;
            }

            if($virtual = db('compute_virtual')->where(array('id'=>$virtual_id))->field(array('id','name','type'))->find()){
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
        }

        $data = $this->construct_data($data);
        return $data;
    }

    private function get_virtual_id($group_id){

      $info = $this
          ->where(array('group_id'=>$group_id,'virtual_id'=>array('neq',0)))
          ->field('virtual_id')
          ->find();

        if($info){
            return $info->virtual_id;
        }else{
            return 0;
        }

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
        if(is_high_system()&&$last_snap_type==2){ //高级版本则显示颗粒等信息
            foreach($tasks as $k=>$v){

                if($flag==$count-3 || $flag==$count-2){

                    $off_set = $count-$flag;
                    $next = array_slice($tasks,1-$off_set,1);

                    $v['begin_time'] = $v['create_time'];
                    $v['end_time'] = $next[0]['create_time']-1;
                    $v['available'] = 1;

                }else{
                    $v['available'] = 0;
                }
//                $v['keli'] = $this->getKeli($v['task_id'],$v['group_id']);
                $data[] = $v;
                $flag += 1;
            }

        }else{

            //不是高级版本则不显示颗粒等信息
            foreach($tasks as $k=>$v){
                $v['available'] = 0;
                $data[] = $v;
                $flag += 1;
            }
        }

        return $data;
    }

    //插入新的数据
    static public function insert_temp_snap($snap_list,$virtual_id){

        foreach($snap_list as $k=>$v){
            unset($v['id']);
            $v['virtual_id'] = $virtual_id;
            $v['create_time'] = time();
            db('remote_snap')->insert($v);
        }

    }


    /**
     *  发送删除快照的消息
     */
    protected function send_del_snap_message($unique_id){

        $res = $this->regroup_snap_data();
	  foreach($res as $key=>$v){
            $this->send_message($v,$unique_id);
        }
    }

    private function send_message($snap_path,$unique_id){

        $message_str = "w2m_delete_ahdr_file";
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
            $res = $this->field('group_id,unique_id')->where(array('id'=>$id))->find()->toArray();
            $snap_id = $res['unique_id']."_".$res['group_id'];

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

        $res = $this->where(array('virtual_id'=>$virtual_id))->field(array('task_id,sub_task_id'))->find()->toArray();
        return $res;
    }

    /**
     *   获取类型三的最后一条数据
     */
    private function get_last_type3_snap($unique_id){

        $res = $this->field('id')
            ->where(array('unique_id'=>$unique_id,'type'=>4))
            ->order("create_time desc")
            ->find();
        if($res){
            $res = $res->toArray();
            return $res['id'];
        }
        return 0;
    }


    /**
     *   删除文件
     */
    static function send_delete_file_message($file_path){


        $message_str = "w2p_delete_file";
        $data['spath'] = $file_path;
        $result = \Netmessage::send_message($message_str, $data);
        if($result['code']==1){
            return true;
        } else{
            return false;
        }
    }

}
