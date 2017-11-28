<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/18
 * Time: 13:35
 */

namespace app\remote\model;
use app\cdp\model\Cdp;
use app\cdp\model\CdpTask;

class Remote extends Base
{



    protected $table = 'qinfo_remote_task';

    // 验证规则
    protected $rule = array(
                        "from"=>'require|ip',
                        "task_id"=>'require',
                        'to'=>'require',

                         );

                 //验证信息
    protected $msg = array(
                     'from.require'=>'源设备不能为空',
                     'to.require' => '目标设备不能为空',
                     'task_id.require' => '任务不能为空',
                     'from.ip'=>'源设备ip格式不正确',
                  
                 );



    public function getSource($type='cdp'){

      $cdp = new Cdp();
      return $cdp->getDevice($type);

    }


    public function getDeviceTask($device_id){

      return CdpTask::getSourceTaskByDeviceId($device_id,true);

    }


    public function getTargetDevice(){

        $devices = db('remote_device')->select();
        if($devices)
            return $devices;
        else
            return array();
    }



    /*********************************发送消息*************************************************/

     static public function send_message($task_id,$ip_str,$type,$unique_id=0){

        $messgae_str = "W2P_Remote_Host";
        $data['nTaskId'] = $task_id;
        $data['sips'] = $ip_str.";";
        $data['ntype'] = $type;
        $data['sUnId'] = $unique_id;
        $data['strserverip'] = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');


        // 0=> 本地分发  1=> 二次分发
        $result = \Netmessage::send_message($messgae_str,$data);
        if($result['code'] == 1)
            return true;
        else
            return false;

    }


}