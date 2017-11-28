<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/6
 * Time: 13:44
 */
namespace app\recover\controller;
use app\cdp\model\CdpSnap;
use app\common\controller\Admin;
use app\device\model\ComputeVirtual;
use app\recover\model\Recover;


class Manage extends Admin
{

    public function index(){


        $device = db('cdp')->where(array('status'=>1))->field(array('device_id'))->select();
        $source_device = array();
        $field = array('id','alias','ip','unique_id','harddisk_info');
        $device_model = db('device');
        foreach($device as $k=>$v) {
            $device = $device_model->where(array('id'=>$v['device_id']))->field($field)->find();
            if(!$device){
                continue;
            }
            $device['disk_size'] = Recover::getHarddiskSize($device['harddisk_info']);
            $device['task'] = Recover::get_task($device['id']);
            unset($device['harddisk_info']);
            $source_device[] = $device ;
        }

        $targetdevice = $device_model->where(array('status'=>1,'attribute'=>2))->select();
        if($targetdevice){
            foreach ($targetdevice as $target_key =>&$vo_target) {
                if($vo_target['harddisk_info']){
                    $vo_target['harddisk_info'] = json_decode($vo_target['harddisk_info'],true);
                }else{
                    unset($targetdevice[$target_key]);
                }
            }
        }

        $field = array('id','source_ip','target_ip','status','create_time','config');
        $task_data = db('recover')->field($field)->paginate(25);

        foreach($task_data as $k=>$v){
            $v['is_temp'] = json_decode($v['config'],true)['is_temp'];
            $task_data[$k] = $v;
        }

        $data = array(
            'data' => $task_data,
            'page' => $task_data->render(),
        );

        $this->assign('sourcedevice',$source_device);
        $this->assign('targetdevice',$targetdevice);
        $this->assign($data);
        $this->setMeta(lang('recover_manage'));

        return $this->fetch();

    }

    /**
     * 获取任务下的所有快照
     *
     */

    public function getVmdk(){

        $task_id = input('id');

        if(!$task_id)
            return json(array('code'=>0,'msg'=>lang('params_error')));

        $snap_field = array('id','task_id','file_path','create_time','harddisk_id','have_os','group_id','virtual_id');
        $snap = db('cdp_snap')->where(array('task_id'=>$task_id,'keli_id'=>0))->field($snap_field)->select();
        $temp_snap = db('cdp_snap')->where(array('task_id'=>$task_id,'keli_id'=>array('neq',0),'virtual_id'=>array('neq',0)))->select();
        $snap = array_merge($snap,$temp_snap);

        foreach($snap as $key=>&$value){
            if($value['virtual_id']!=0){
                $type_info = ComputeVirtual::where(array('id'=>$value['virtual_id']))->column('type');
                $value['virtual_type'] = $type_info[0];
            }
        }

        if(count($snap) == 0){
            return json(array('code'=>0,'msg'=>lang('no_snap_vaild')));
        }else{
            $snaps =  Recover::handle_snap_list($snap);
            return json(array('code'=>1,'data'=>$snaps));
        }
    }

    public function getDevice(){
        $id = input('id');
        $source_id = input('sourceId');
        $field = array('id','harddisk_info');

        $device = db('device')->where(array('id'=>$id))->field($field)->find();
        $diskSize = db('device')->where(array('id'=>$source_id))->field('harddisk_info')->find();
        if(!$device)
            return json(array('code'=>0,'msg'=>lang('choose_device')));

        $diskSize = Recover::getHarddiskSize($diskSize['harddisk_info']);
        $device['disk_size'] = $diskSize;
        $device['harddisk_info'] = Recover::handle_harddisk_info($device['harddisk_info']);
        return json(array('code'=>1,'data'=>$device));

    }


    public function add_task(){

        $source_id = input('source_device');
        $target_id = input('target_device');
        $vmdk_map = input('target_disk_index/a');


        if(count($vmdk_map)<1){
            return $this->error(lang('target_device_is_empty'));
        }


        $model = model('device/device');
		$source_info =  Recover::info_filter($model->getDeviceInfo($source_id));
		$target_info = Recover::info_filter($model->getDeviceInfo($target_id));
		$data['source_ip']= $source_info['ip'];
		$data['source']= json_encode($source_info);
		$data['target_ip']= $target_info['ip'];
		$data['target']= json_encode($target_info);
        $data['status'] = 0;
        $data['create_time'] = time();

        $config_data['recover_type'] = 1;
        $map_str = '';

        $snap_ids = array();
        foreach($vmdk_map as $k=>$v){
            if(!empty($v)){
                $snap_ids[] = explode(':',$v)[0];
                $map_str .= $v.',';
            }
        }

        if($map_str===''){
            return $this->error(lang('target_disk_is_empty'));
        }

        $is_temp = 0;
        foreach($snap_ids as $k=>$v){

          $virtual_id = CdpSnap::where(array('id'=>$v))->column('virtual_id');
          if($virtual_id[0] != 0){
              $is_temp = 1;
              break;
          }
        }

        $config_data['is_temp'] = $is_temp;
        $config_data['vmdk_versus'] = rtrim($map_str,',');
        $data['config'] = json_encode($config_data);

        $insert_id = db('recover')->insert($data,false,true);
        if($insert_id){
            return $this->success(lang('add').lang('success'));
        }else{
            return $this->error(lang('add').lang('success'));
        }

    }


    public function detail() {

        $id = input('id');
        if(!$id){
            return $this->error(lang('parameter_error'));
        }
        $info = db('recover')->where(array('id'=>$id))->find();

        if(!$info){
            return $this->error(lang('task_does_not_exist'));
        }

        $config = json_decode($info['config'],true);
        $is_temp = $config['is_temp'];
        $vmdk_versus = $config['vmdk_versus'];

        $vmdk_versus = explode(',',$vmdk_versus);
        $versus = array();

        foreach($vmdk_versus as $key=>$value){
            $value = explode(':',$value);

            $snap = CdpSnap::where(array('id'=>$value[0]))->column('update_time');
            $data['snap'] = date("Y-m-d H:i:s",$snap[0]);
            $data['disk'] = $value[1];
            $versus[] = $data;
        }


        $source_info = json_decode($info['source'],true);
        $target_info = json_decode($info['target'],true);

        $field = array('status');
        $device_source = db('device')->where(array('id'=>$source_info['id']))->field($field)->find();
        $device_target = db('device')->where(array('id'=>$target_info['id']))->field($field)->find();

        $source_info['status']  = $device_source['status'];
        $target_info['status']  = $device_target['status'];

        $this->assign('versus',$versus);
        $this->assign('is_temp',$is_temp);
        $this->assign('source_info',$source_info);
        $this->assign('target_info',$target_info);
        $this->assign('move_info',$info);


        $this->setMeta(lang('task_details'));
        return $this->fetch();
    }

    /**
     * 删除历史任务
     */
    public function del_task(){

        $id = input('id');

        if(!$id)
            return $this->error(lang('parameter_error'));
        $task = db('recover')->where(array('id'=>$id))->find();
        if(!$task)
            return $this->error(lang('task').lang('not_exist'));
//        if($task['status'] != 0 &&  $task['status'] != 3)
//            return $this->error(lang('only_failed_and_failed_tasks_can_only_be_deleted'));
        if(db('recover')->where(array('id'=>$id))->delete())
            return $this->success(lang('delete').lang('success'));
        else
            return $this->error(lang('delete').lang('fail'));

    }

    /**
     * 启动 或 暂停任务
     */
    public function start_task(){

        $id = input('id');
        $status = input('status');

        if(!$id){
            return $this->error(lang('parameter_error'));
        }
        $task = db('recover')->where(array('id'=>$id))->find();

        if(!$task){
            return $this->error(lang('task').lang('not_exist'));
        }


        $data['is_temp'] = 0;
        if($status==0) $status_str = 'pause';
        if($status==3) $status_str = 'retry';
        if($status==1) $status_str = 'startup';
        if($status==4) {

            $status_str = "sync";
            $config = $task['config'];
            $config = json_decode($config,true);
            $data['is_temp'] = $config['is_temp'];
        }

        if($task['status'] == $status)
            return $this->error(lang('task').lang('have').lang($status_str));

        $data['status'] = $status;
        $data['id'] = $id;

        //发送消息
        if(Recover::send_status_cdp_message($data)){
            return $this->success(lang('task').lang($status_str).lang('success'));
        }else{
            return $this->error(lang('task').lang($status_str).lang('fail'));
        }

    }

    /**
     * 读取恢复信息
     */
    public function get_recover_state(){

        $id = input('id');

        if(!$id)
            return $this->error(lang('parameter_error'));
        $info = db('recover')->where(array('id'=>$id))->find();
        if(!$info)
            return $this->error(lang('task').lang('not_exist'));

        if($info['status']==2){
            $data['code'] = 2;
            $data['msg'] = lang('恢复已完成');
            return json($data);
        }

        if($info['status']==3)
            return $this->error('恢复任务失败');

        $result = Recover::send_get_recover_info_message($id);

        if ($result['code'] == 1) {

            $res_data['code'] = $result['code'];
            $res_data['istemp'] = $result['recoveringinfo']['nistemp']?1:0;
            $res_data['harddisk'] = $result['recoveringinfo']['nharddisk'];
            $res_data['progress'] = $result['recoveringinfo']['progress'];
            $res_data['speed'] = $result['recoveringinfo']['speed'];
            $res_data['useTime'] = $result['recoveringinfo']['elapsedtime'];
            return json($res_data);
        }
        //else {
         //   return $this->error('获取信息失败');
       // }
    }


}