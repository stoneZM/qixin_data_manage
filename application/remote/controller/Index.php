<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/6
 * Time: 13:44
 */
namespace app\remote\controller;
use app\cdp\model\CdpSnap;
use app\cdp\model\CdpTask;
use app\cdp\model\Compute;
use app\common\controller\Admin;
use app\device\model\ComputeVirtual;
use app\device\model\Device;
use app\remote\model\Remote;
use app\remote\model\RemoteDevice;
use app\remote\model\RemoteSnap;
use app\device\model\VirtualManager;
use app\device\model\Libvirt;
use Symfony\Component\DomCrawler\Tests\Field\InputFormFieldTest;


class Index extends Admin
{

    protected $rm;       // Remote Model

   function _initialize()
   {
       parent::_initialize();
       $this->rm = new Remote();
       $this->device = new RemoteDevice();
   }

    public function device(){

        $this->setMeta(lang('remote').lang('device'));
    }

    public function index(){



        $source_list = $this->rm->getSource();

        $target_map = array('type'=>1);
        $target_list = $this->rm->getTargetDevice($target_map);


        $list = $this->rm->getList();

        $remote_device_list = $this->device->getList('','device_list');

        $remote_device = new RemoteDevice();
        $map['type'] = 0;
        $remote_list = $remote_device->getList($map,'remote_list');

        $have_remote_list = count($remote_list)?:-1;

        $type = input('type');

        if(!$type || $type==1){
            $tab1_active = "active";
        }
        if($type==2){
            $tab2_active = "active";
        }
        if ($type==3) {
            $tab3_active = "active";
        }


        //获取remote_auth
        $auth_info = db('remote_auth')->limit(0,1)->find();

        $this->assign('auth_info',$auth_info);
        $this->assign($remote_device_list);
        $this->assign('have_remote_list',$have_remote_list);
        $this->assign($remote_list);
        $this->assign($list);
        $this->assign('source_list',$source_list);
        $this->assign('device',$target_list);
        $this->assign('tab1_active',$tab1_active);
        $this->assign('tab2_active',$tab2_active);
        $this->assign('tab3_active',$tab3_active);
        $this->setMeta(lang('remote_manage'));
        return $this->fetch();
    }

    //获取存在当前任务的设备
    public function get_device()
    {
        $device = $this->rm->getSource('current');
        $new_array = array();
        foreach($device as $key=>$v){
            if(CdpTask::get(['type'=>'current','device_id'=>$v['id']])){
               $new_array[] = $v;
            }
        }
        if(count($new_array)>0){
            return json(array('code'=>1,'data'=>$new_array));
        }else{
            return json(array('code'=>0,'msg'=>lang('have_no_device_choose')));
        }

    }

    public function add_task(){


        $target_device = input('target_device/a');
        $ip_str = '';
        if($target_device){
            foreach($target_device as $v){
                $ip_str .= $v.";";
            }
            $ip_str = rtrim($ip_str,';');
        }
        $type = input('task_type');
        $this->rm->to = $ip_str;
        $this->rm->is_realtime = $type?:0;
        //实时任务
        $msg_flag = 1;
        if($type==1){

            $this->rm->from = input('current_source_device');
            $device_id = Device::getDeviceId("ip",$this->rm->from);
            $this->rm->task_id = CdpTask::set_snappoint($device_id,$ip_str);
            $this->rm->status = 1;
            $msg_flag = Remote::send_message($this->rm->task_id,$ip_str,0);

        }else{

            $this->rm->task_id = input('task_id');
            $this->rm->from = input('source_device');
        }

        if($msg_flag){
            if(!$this->rm->save()){
                return $this->error($this->rm->getError());
            }else{
                return $this->success(lang("add").lang('success'),url('remote/index/index?type=1'));
            }
        }else{
            return $this->error(lang('send_message').lang('fail'));
        }

    }

    public function start_task(){

        $id = input('id');
        $info = Remote::where(array('id'=>$id))->field('from,to,is_twice')->find();
        $ip_str = $info['to'];
        $is_twice = $info['is_twice'];
        $unique_id = 0;
        if($is_twice){
            $unique_id = RemoteDevice::where(array('ip'=>$info['from']))->column('unique_id');
            $unique_id = current($unique_id);
        }

        if(Remote::send_message($id,$ip_str,$is_twice,$unique_id)){

            $data['status'] = 1;
            Remote::where(array('id'=>$id))->update($data);
            return $this->success(lang('startup').lang('success'));

        }else{

           return $this->error(lang('startup').lang('fail'));
        }
    }

    //添加二次分发任务
    public function r_add_task(){

        $target_device = input('target_device/a');
        $ip_str = '';
        if($target_device){
            foreach($target_device as $v){
                $ip_str .= $v.";";
            }
            $ip_str = rtrim($ip_str,';');
        }
        $unique_id = input('unique_id');
        $this->rm->from = $this->device->getOne($unique_id)['ip'];
        $this->rm->to = $ip_str;
        $this->rm->is_realtime = 0;
        $this->rm->task_id = input('device_task_id');
        $this->rm->status = 0;
        $this->rm->is_twice = 1;

        if(!$this->rm->save()){
            return $this->error($this->rm->getError());
        }else{
            return $this->success(lang("add").lang('success'),url('remote/index/index?type=1'));
        }

    }


    //取消实时任务
    public function cancel_realtime(){

        $task_id = input('id');
        $task_info = $this->rm->get(['id'=>$task_id]);
        $source_ip = $task_info->from;
        $device_id = Device::getDeviceId('ip',$source_ip);

        if($task_info&&$device_id){
            $this->rm->where(array('id'=>$task_id))->update(['is_realtime'=>0]);
            CdpTask::set_snappoint($device_id,'');
            return $this->success(lang('cancel').lang('success'));
        }else{
            return $this->success(lang('cancel').lang('fail'));
        }
    }


    public function del(){

        $id = input('id');
        if(!$id){
            return $this->error(lang('params').lang('error'));
        }
        if($this->rm->del($id)){
            return $this->success(lang('delete').lang('success'),url('remote/index/index?type=1'));
        }else{
            return $this->error($this->rm->getError());
        }
    }


    public function get_device_task(){

        $id = input('id');

        if(!$id){
           return json(array('code'=>0,'msg'=>lang('params_error')));


        }
         $task =  $this->rm->getDeviceTask($id);
         return json(array('code'=>1,'data'=>$task)) ;

    }


    public function get_remote_task(){

        $unique_id = input('unique_id');
        $task_id_list = RemoteSnap::where(array('unique_id'=>$unique_id))
                                    ->distinct(true)
                                    ->column('task_id');

        if(!count($task_id_list)){
            return json(array('code'=>0,'msg'=>'暂无任务可选'));
        }
        return json(array('code'=>1,'data'=>$task_id_list));
    }

    public function detail(){

        $unique_id = input('uuid');
        $task_id = input('task_id')?:0;
        $remote_snap = new RemoteSnap();

        $task_ids = $remote_snap->get_snap_task_id($unique_id);

        if(!$task_id){
            if(count($task_ids)>0){
                $task_id = $task_ids[0];
            }else{
               $task_id = 0;
            }
        }

        $snaps = $remote_snap->get_snap($unique_id,'',$task_id);

        //获取计算节点
        $computing_list = Compute::getAll();

        $computing_list = $this->handle_compute_list($computing_list);

        //获取设备信息
        $system_info = RemoteDevice::getSystemInfo($unique_id);


        $this->assign('hardware_info',$system_info);
        $this->assign('unique_id',$unique_id);
        $this->assign('computing_list',$computing_list);
        $this->assign('task_ids',$task_ids);

        $this->assign('current_id',$task_id);

        $this->assign('snap_list',$snaps);
        $this->assign('meta_title',lang('data_manage'));
        return $this->fetch('detail');
    }


    //处理compute数据
    private function handle_compute_list($list){

        foreach($list as $k=>&$v){
            $compute_info = json_decode($v['compute_info']);
            $v['max_cpu'] = $compute_info->max_cpu;
        }
        return $list;
    }



    public function del_data(){

        $unique_id = input('uuid');

        //检测是否有虚拟机存在
        if(RemoteSnap::where(array('unique_id'=>$unique_id,'virtual_id'=>array('neq',0)))->find()){
            return $this->error('请先删除虚拟机后重试!');
        }

        $info = RemoteSnap::where(array('unique_id'=>$unique_id))->field('file_name')->limit(0,1)->find();
        if(!$info){
            RemoteDevice::where(array('unique_id'=>$unique_id))->delete();
            return $this->success('删除成功');
        }

        $file_name = $info->file_name;
        $file_name = explode('/',$file_name);
        $count = count($file_name);
        if($count<2){
            return $this->error('参数错误');
        }
        unset($file_name[$count-1]);
        unset($file_name[$count-2]);
        $file_name = implode('/',$file_name);
        if(RemoteSnap::send_delete_file_message($file_name)){
             RemoteSnap::where(array('unique_id'=>$unique_id))->delete();
             RemoteDevice::where(array('unique_id'=>$unique_id))->delete();
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败: 消息发送失败');
        }

    }



    /******************************设备管理*********************************************/
    public function device_add(){


        if(IS_POST){

            $ip = input('ip');
            if($this->device->where(array('ip'=>$ip))->find()){
                return $this->error('设备已存在!');
            }
           
            $type = input('type');
            $this->device->unique_id = $this->device->randomkeys(8);
            $this->device->ip = $ip;
            $this->device->alias = input('alias');
            $this->device->status = input('status');
            $this->device->port = input('port')?:0;
            $this->device->user_name = input('user_name')?:'';
            $this->device->passwd = input('passwd')?:'';
            $this->device->type = $type;

            if(!$this->device->save()){
                return $this->error($this->device->getError());
            }else{
                return $this->success(lang('add').lang('success'),url('/remote/index/index?type=3'));
            }

        }
        $this->setMeta(lang('add').lang('remote').lang('device'));
        return $this->fetch('device_add');
    }

    public function device_edit(){

        $uuid = input('uuid');
        if($_POST){

            $data['type'] = input('type');
            $data['ip'] = input('ip');
            $data['alias'] = input('alias');
            $data['status'] = input('status');
            $data['port'] = input('port')?:0;
            $data['user_name'] = input('user_name')?:'';
            $data['passwd'] = input('passwd')?:'';


            if(!$this->device->save($data,['unique_id' => $uuid])){
                return $this->error($this->device->getError());
            }else{
                return $this->success(lang('edit').lang('success'),url('/remote/index/index?type=3'));
            }

        }

        $device = $this->device->getOne($uuid);
        if(!$device){
            return $this->error($this->device->getError);
        }

        $this->assign('device',$device);
        return $this->fetch('device_add');
    }


    public function device_del(){

        $id = input('uuid');

        if(!$id){
            return $this->error(lang('params').lang('error'));
        }
        if($this->device->del($id)){
            return $this->success(lang('delete').lang('success'),url('/remote/index/index?type=3'));
        }else{
            return $this->error($this->device->getError());
        }

    }


    public function add_auth(){

        $data['user_name'] = input('user_name');
        $data['passwd'] = input('passwd');
        $id = input('id')?:0;
        if(!$data['user_name']||!$data['passwd']){
            return $this->error('用户名或密码不能为空');
        }

       if($id){

           $auth_info = db('remote_auth')->where(array('id'=>$id))->find();
           if($auth_info){
               if($data['user_name']==$auth_info['user_name']&&$data['passwd']==$auth_info['passwd']){
                   return $this->error('未修改');
               }
           }else{
                return $this->error('修改失败');
           }
           if(db('remote_auth')->where(array('id'=>$id))->update($data)){
               return $this->success('修改成功');
           }

       }else{

           if(db('remote_auth')->insert($data)){
               return $this->success('添加成功!');
           }

       }

        return $this->error('操作失败');


    }



    /******************************创建虚拟机*********************************************/
    public function add_virtual(){

        if(IS_POST){

            $unique_id = input('unique_id');
            $group_id = input('snap_group_id');

            $snap = db('remote_snap')
                ->where(array('group_id'=>$group_id,'virtual_id'=>0,'unique_id'=>$unique_id))
                ->order('have_os desc')
                ->select();

            $spath = CdpSnap::get_snap_filepath($snap);

            $snap = CdpSnap::get_temp_vmdk_record($snap);


            $storage_id = $snap[0]['storage_id'];

            //获取磁盘的路劲
            foreach($snap as $k=>$v){
                $data['vmdk_path'][] = $v['file_name'];
                $data['snaps'][] = $v['id'];
            }

            if(!$snap)
                return $this->error(lang('selected_disk_have_os_system'));


            $data['name'] = input('host_name');
            $data['cpu_kernel'] = input('cpu_kernel');
            $data['memory'] = input('memory')*1024;
            $data['net_data'] = input('netdata/a');


            $data['virtual_info'] = json_encode($data);

            $data['device_id'] = 0;
            $data['comput_id'] = input('comput_id');
            //获取远程端口号
            $data['remote_port'] = ComputeVirtual::get_remote_port($data['comput_id']);
            //获取源设备信息
            $device = db('remote_device')->where(array('unique_id'=>$unique_id))->find();

            //2012 Datacenter
            $system_info = json_decode($device['system_info'],true);

            $data['is_sata'] = strpos($system_info['systemversion'],"2012")?1:0;

            $data['source_device_ip'] = $device['ip'];
            $data['source_device_name'] = $device['alias'];
            $data['disktype'] = json_decode($device['harddisk_info'],true)[0]['disktype']?:0;
            $data['module'] = 'cdp';

            $data['status'] = 0;
            $data['snap_id'] = $group_id;
            $data['type'] = 3;
            $data['system_type'] = input('system_type')=='window'? 1 : 0;

            //2012 Datacenter
            $system_info = json_decode($device['system_info'],true);
            $data['is_sata'] = strpos($system_info['systemversion'],"2012")?1:0;

            $data['create_time'] = time();

            if(is_liter_system()){  // 如果是轻量版

                // 修改内存和cpu为计算池的一般
                $compute_info = Compute::getComputeIfo($data['comput_id']);
                $data['cpu_kernel'] = intval($compute_info['max_cpu']/2)?:1;
                $data['memory'] = intval($compute_info["total_mem"]/2)?:1024;

                return  $this->add_virtual_liter($data,$snap,$spath,$storage_id);

            }else{

               return $this->add_virtual_common($data,$snap,$spath,$storage_id,$group_id);
            }
        }
    }

    //轻量版新增虚拟机
    private function add_virtual_liter($data,$snap,$spath,$storage_id){

        //先关闭所有虚拟机
        VirtualManager::stop_all_virtual($data['comput_id']);

        if(VirtualManager::libvirt_create_virtual($data))
        {
            if($insertId = db('compute_virtual')->insert($data,false,true)){
                //插入新的temp数据
                RemoteSnap::insert_temp_snap($snap,$insertId);

                VirtualManager::send_create_virtual_messgae($spath,$storage_id);

                return $this->success(lang('create').lang('success'),url("/device/manage/virtual_detail/id/$insertId"));
            }
            else {
                return $this->error(lang('create') . lang('fail'));
            }
        }
    }

    //普通版新增虚拟机
    private function add_virtual_common($data,$snap,$spath,$storage_id,$group_id=0){

        if(VirtualManager::libvirt_create_virtual($data))
        {
            if($insertId = db('compute_virtual')->insert($data,false,true)){
                //插入新的temp数据
                RemoteSnap::insert_temp_snap($snap,$insertId);
                VirtualManager::send_create_virtual_messgae($spath,$storage_id);
                return $this->success(lang('create').lang('success'),url("/device/manage/virtual_detail/id/$insertId"));
            }
            else {
                return $this->error(lang('create') . lang('fail'));
            }
        }
    }






}