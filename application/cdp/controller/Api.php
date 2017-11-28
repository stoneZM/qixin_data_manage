<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/16
 * Time: 16:28
 */
namespace app\cdp\controller;
use app\cdp\model\CdpSnap;
use app\cdp\model\Compute;
use app\common\controller\Admin;
use app\device\model\ComputeVirtual;
use app\device\model\VirtualManager;
use app\practise\model\PractiseModel;


class Api extends Admin{

    protected $code = 0;  // 99=>'虚拟机已存在'
    protected $msg = 'fail';
    protected $data = array();
    protected $virtual_id = 0;
    protected $device_ip = '';

    public  function _initialize(){
        return true;
    }

    public function create(){



        $ip = input('ip');
        $type = (input('type')==1)?:0;  // 0 代表不选取当前点，1 代表当前点
        $snap_id = input('snap_id')?:0;

        $device_field = array('id','ip','alias','system_info');
        $device_info = db('device')->where(array('ip'=>$ip))->field($device_field)->find();
        $this->device_ip = $device_info['ip'];


        if(!$device_info){
            $this->code=0;
            $this->msg= lang('device_does_not_exist');
            return $this->getError();
        }

        //获取该设备下的最新任务
        $field = array('id');
        $where['device_id'] = array('eq',$device_info['id']);
        $where['type'] = array('neq','move');
        $task_id = db('cdp_task')->where($where)->field($field)->order('id desc')->find();

        $newest_task_id = $task_id['id'];

        /*获取该任务下最新的快照节点*/
        $snap_info = $this->get_snap($newest_task_id,$type,$snap_id);

        if(!$snap_info){
            return $this->getError();
        }

        $snap_id = current($snap_info)['id'];

        $spath = CdpSnap::get_snap_filepath($snap_info);
        //如果没有虚拟机的存在，则创建虚拟机
        $snap = CdpSnap::get_temp_vmdk_record($snap_info);
        $config_data = db('cdp_config')->where(array('device_id'=>$device_info['id'],'type'=>1))->find();


        if(!$config_data){
            $this->msg = '无可用的配置文件';
            return $this->getError();
        }

        //2012 Datacenter
        $system_info = json_decode($device_info['system_info'],true);
        $compute_info = db('compute')->field('ip')
                                     ->where(array('id'=>$config_data['compute_id']))
                                     ->find();

        // 构造数据
        $date = date('Ymd-His',time());
        $config['compute_ip'] = $compute_info['ip'];
        $config['host_name'] = 'Server-'.$date.'-'.rand(100,1000);

        $config['cpu_kernel'] = $config_data['cpu'];
        $config['memory'] = $config_data['memory']*1024;
        $config['netdata'] = json_decode($config_data['net_data'],true);
        $config['device_id'] = $device_info['id'];
        $config['alias'] = $device_info['alias'];
        $config['ip'] = $device_info['ip'];
        $config['comput_id'] = $config_data['compute_id'];
        $config['module'] = 'cdp';
        $config['system_type'] = $config_data['system_type'];
        $config['vir_type'] = 1;
        $config['task_id'] = $newest_task_id;
        $config['group_id'] = current($snap_info)['group_id'];
        $config['storage_id'] = current($snap_info)['storage_id'];
        $config['is_sata'] = strpos($system_info['systemversion'],"2012")?1:0;

        $config['task_id'] = $newest_task_id;
        $config['disktype'] = json_decode($device_info['harddisk_info'],true)[0]['disktype']?:0;


        $virtual_id = $this->add_virtual($snap,$spath,$config);
        if($virtual_id){

            //如果成功创建虚拟机，
            PractiseModel::send_create_finished_message($virtual_id,$config['ip'],$config['host_name'],0,$snap_id);
            $this->code = 1;
            $this->msg = '创建成功';
            return $this->getError();

        }else{

            return $this->getError();
        }
    }




    public function start(){


        $id  = input('id');

        if(!$id){
            $this->msg = '参数错误';
            return $this->getError();
        }

        $check_data = db('compute_virtual')->where(array('id'=>$id))->find();

        if(!$check_data){
            $this->msg = '虚拟机不存在';
            return $this->getError();
        }

        $name = $check_data['name'];
        $comput_id = $check_data['comput_id'];
        //节点ip
        $ip = Compute::get_ip($comput_id);

       if(VirtualManager::start_virtual($name,$ip)){
           db('compute_virtual')->where(array('id'=>$id))->update(array('status'=>1));
           $this->code = 1;
           $this->msg = '开启成功';
           return $this->getError();
       }else{
           $this->msg = "开机失败";
           return $this->getError();
       }
    }

    public function stop(){

        $id  = input('id');
        if(!$id){
            $this->msg = '参数错误';
            return $this->getError();
        }
        $check_data = db('compute_virtual')->where(array('id'=>$id))->find();
        if(!$check_data){
            $this->msg = '虚拟机不存在';
            return $this->getError();
        }
        $name = $check_data['name'];
        $comput_id = $check_data['comput_id'];
        //节点ip
        $ip = Compute::get_ip($comput_id);
        if(VirtualManager::stop_virtual($name,$ip)){
            db('compute_virtual')->where(array('id'=>$id))->update(array('status'=>0));
            $this->code = 1;
            $this->msg = '关机成功';
            return $this->getError();
        }else{
            $this->msg = "关机失败";
            return $this->getError();
        }

    }


    public function delete(){


        $id  = input('id');

        if(!$id){
           $this->msg = '参数错误';
            return $this->getError();
        }

        $check_data = db('compute_virtual')->where(array('id'=>$id))->find();

        if(!$check_data){
            $this->msg = '虚拟机不存在';
            return $this->getError();
        }

        $name = $check_data['name'];
        $comput_id = $check_data['comput_id'];
        //节点ip
        $ip = Compute::get_ip($comput_id);

        //先关机
        VirtualManager::stop_virtual($name,$ip);

        if(!VirtualManager::is_virtual_exist($name,$ip)){ //如果真实的虚机不存在，则直接删除记录
            $res = db('compute_virtual')->where(array('id'=>$check_data['id']))->delete();
            db('cdp_snap')->where(array('virtual_id'=>$id))->delete();
        }else{
            //否则先删除真实的虚机
            if(VirtualManager::del_virtual($name,$ip)){
                //删除temp数据
                $this->cdp_snap = new CdpSnap();
                $this->cdp_snap->del_temp_snap($id);
                $res = db('compute_virtual')->where(array('id'=>$check_data['id']))->delete();
                $this->cdp_snap->where(array('virtual_id'=>$id))->delete();
            }
        }
        //sql删除结果判断
        if($res){
            PractiseModel:: send_destroy_message($name);
            $this->code = 1;
            $this->msg = "操作成功";
            return $this->getError();
        }else{
            $this->msg = "删除失败";
            return $this->getError();
        }

    }


    private function add_virtual($snap,$spath,&$config){

        //获取磁盘的路劲
        foreach($snap as $k=>$v){
            $data['vmdk_path'][] = $v['file_name'];
            $data['snaps'][] = $v['id'];
        }

        $data['name'] = $config['host_name'];
        $data['cpu_kernel'] = $config['cpu_kernel'];
        $data['memory'] = $config['memory'];
        $data['net_data'] = $config['netdata'];
        $data['virtual_info'] = json_encode($data);
        $data['device_id'] = $config['device_id'];
        $data['comput_id'] = $config['comput_id'];
        //获取远程端口号
        if($port = ComputeVirtual::get_remote_port($data['comput_id'])){
            $data['remote_port'] = $port;
        }else{
            $this->msg = '无端口号可用';
            return false;
        }

        $data['source_device_ip'] = $config['ip'];
        $data['source_device_name'] = $config['alias'];
        $data['module'] = $config['module'];
        $data['status'] = 0;
        $data['snap_id'] = $config['task_id'].'_'.$config['group_id'];
        $data['type'] = $config['vir_type'];
        $data['system_type'] = $config['system_type'];
        $data['is_sata'] = $config['is_sata'];
        $data['task_id'] = $config['task_id'];
        $data['disjtype'] = $config['disktype'];
        $data['create_time'] = time();

        if(VirtualManager::libvirt_create_virtual($data))
        {

            if($insertId = db('compute_virtual')->insert($data,false,true)){
                //插入新的temp数据
                CdpSnap::insert_temp_snap($snap,$insertId);

                VirtualManager::send_create_virtual_messgae($spath,$config['storage_id'],$data['system_type']);

//                VirtualManager::start_virtual($config['host_name'],$config['compute_ip']);

                return $insertId;
            }
            else {
                return false;
            }
        }
    }

    /**
     * @param $task_id
     * @param $type    是否可在实施点创建虚机
     */

    private function get_snap($task_id,$type,$snap_id=0){

        $model = db('cdp_snap');
        $where = array();
        if ($snap_id !== 0){
            $group_id = $model->where(array('id'=>$snap_id))->column('group_id');
            if(!empty($group_id)&&count($group_id)>0){
                $group_id = current($group_id);
                $where['group_id'] = array('eq',$group_id);

            }else{
                $this->msg = '快照id无效';
                return false;
            }

        }else {

            $where['task_id'] = array('eq', $task_id);
            $where['virtual_id'] = array('eq', 0);
            $where['keli_id'] = 0;
            if ($type) {

                $where['type'] = array('eq', 2);
                $list = $model->where($where)->select();
                if (!$list) {
                    $this->msg = '无快照可选';
                    return false;
                } else {
                    return $list;
                }

            }
            $list = $model->where($where)
                ->field('group_id')
                ->order('id desc')
                ->group('group_id')
                ->limit(0, 2)
                ->select();
            if (count($list) != 2) {
                $this->msg = "无可选快照";
                return false;
            }
            $last_2_group_id = $list[1];
            $where['group_id'] = $last_2_group_id['group_id'];
        }

        $where['virtual_id'] = array('neq',0);
        $snap_info = $model->where($where)->field('virtual_id')->find();

        if($snap_info){  // 检查此快照点是否存在虚拟机
            $this->msg = '已存在虚拟机';
            $this->send_virtual_exist_message($snap_info['virtual_id']);
            return false;
        }

        $where['virtual_id'] = array('eq',0);
        $snaps = $model->where($where)->select();
        if(!$snaps){
            $this->msg = "无可选快照";
            return false;
        }

        return $snaps;

    }

    protected function  getError(){

        $res['code'] = $this->code;
        $res['msg'] = $this->msg;

        return json_encode($res);

    }

    //如果存在虚拟机 发送消息
    private function send_virtual_exist_message($virtual_id){

        $virtual = db('compute_virtual')
            ->field('name')
            ->where(array('id'=>$virtual_id))
            ->find();
        PractiseModel::send_create_finished_message($virtual_id,$this->device_ip,$virtual['name'],1);

    }

}