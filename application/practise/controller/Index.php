<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/6
 * Time: 13:44
 */
namespace app\practise\controller;
use app\cdp\model\CdpSnap;
use app\common\controller\Admin;
use app\cdp\model\Cdp;
use app\device\model\ComputeVirtual;
use app\Device\model\Device;
use app\practise\model\PractiseModel;
use app\common\controller\Excel;

class Index extends Admin
{

    protected $cdp;
    protected $device;

    public function _initialize() {

        parent::_initialize();
        $this->cdp = new Cdp();
        $this->device = new Device();

    }

    function index(){

        $list = $this->cdp->getList();

        foreach($list as $key=>$item){
            $item = $item['data'];
            $system_info = json_decode($item['system_info'],true);
            if(key_exists('kernel_version',$system_info)){
                $version = $system_info['kernel_version'];
                $version = explode('.',$version);
                if($version[0]>2||$version[1]>6||($version[1]==6&&$version['2']>28)){
                    continue;
                }else{
                    unset($list[$key]);
                }
            }
        }

        $data = array(
            'list' => $list,
            'page' => $list->render(),
        );

        $logs = get_log_list(array('model'=>"practise"));
        $this->setMeta(lang('practise_manage'));
        $this->assign($data);
        $this->assign('logs',$logs);


        return $this->fetch();
    }

    function auto_config(){

        $compute_model = db('compute');

        $device_id = input('id');

        $auto_config = db('cdp_config')->where(array('device_id'=>$device_id,'type'=>1))->select();

        $compute = $compute_model->field(array('id','name','ip'))->select();

        $virtual_model = db('compute_virtual');

        $client_system_type = Device::get_system_type($device_id);

        $best_compute_count = 999;
        foreach($compute as $k=>$v){

            $count = $virtual_model->where(array('comput_id'=>$v['id']))->count();
            if($best_compute_count > $count){
                $best_compute_count = $count;
                $best_compute_id = $v['id'];
            }
        }


        $exercise_config['have_config'] = 0;
        if(count($auto_config)>0){
            $device_model = db('device');
            foreach ($auto_config as $k=>$v) {
                $device_info = $device_model->where(array('id'=>$v['device_id']))->field(array('alias','ip'))->find();
                $compute_info = $compute_model->where(array('id'=>$v['compute_id']))->field(array('name','ip'))->find();

                $v['network'] = count(json_decode($v['net_data'],true));
                $v['system_type_alias'] = $v['system_type']==0?'Linux':'Windows';
                $v['device_ip'] = $device_info['ip'];
                $v['device_name'] = $device_info['alias'];
                $v['compute_ip'] = $compute_info['ip'];
                $v['compute_name'] = $compute_info['name'];

                $v['old_memory'] =  $v['memory']*1024*1024;
                $v['day_alias'] = PractiseModel::num2char($v['day']);
                $v['day_str'] = PractiseModel::get_day_str($v['day']);
                $v['type_alias'] = $v['type']==0 ? lang('take_over') : lang('practise');
                $v['netdata'] = json_decode($v['net_data'],true);
                $v['accurate_time_alias'] = PractiseModel::switch_time($v['accurate_time']);
                $exercise_config['have_config'] =1;
                $exercise_config['config']=$v;

            }
        }

        $vhost_info = PractiseModel::get_virtual_log($device_id);
        $data = array(
            'vhost_info'=>$vhost_info,
            'page'=>$vhost_info->render()
        );
        $this->assign('client_system',$client_system_type);
        $this->assign('best_id',$best_compute_id);
        $this->assign('computing_list',$compute);
        $this->assign('device_id',$device_id);
        $this->assign($data);

        $this->assign('exercise',$exercise_config);
        $this->setMeta(lang('practise_manage'));
        return $this->fetch();
    }



    /******************导出表格*********************************/
    public function export_excel(){

        $device_id = input('device_id');

        $logs = PractiseModel::get_virtual_log($device_id,false);

        if(count($logs)<1){
            return $this->error('无数据可导出');
        }
        $col_titles = array("A1"=>'编号',"B1"=>"快照时间节点","C1"=>"是否正常");
        $col_width = array("A"=>10,"B"=>20,"C"=>10);
        $sheel_title = "审计报表"."(".$logs[0]['vhost_source_ip'].")".'.xls';
        Excel::export_excel($sheel_title,$col_titles,$col_width,$logs);

    }


}