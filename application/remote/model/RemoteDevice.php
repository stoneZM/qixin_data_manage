<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/31
 * Time: 16:23
 */

namespace app\remote\model;
use think\Model;

class RemoteDevice extends Base
{

    protected $table = 'qinfo_remote_device';
    protected $autoWriteTimestamp = true;
    protected $updateTime = "create_time";
    protected $createTime = "create_time";

    // 验证规则
    protected $rule = array(
        "ip"=>'require|ip',
        'port'=>'number',
        'user_name'=>'require',
        'passwd'=>'require',

    );

    //验证信息
    protected $msg = array(
        'ip.ip'=>'ip格式不正确',
        'ip.require'=>'ip不能为空',
        'port.number'=>'端口号必须为数字',
        'user_name.require'=>'用户名不能为空',
        'passwd.require'=>'密码不能为空'
    );


    public function getOne($id){

        $res = $this->where(array('unique_id'=>$id))->find();
        if(!$res) {
            $this->error = lang('device_is_not_exist');
            return false;
        }else{
            return $res->toArray();
        }
    }

    public function getSystemInfo($id){

        $system_info = self::where('unique_id',$id)->value('system_info');
        if($system_info){
            return json_decode($system_info,true);
        }else{
            return array();
        }

    }

    /**
     * 删除操作
     * @param $id
     * @return bool
     */
    public function del($id){

        $task = $this->get(['unique_id'=>$id]);
        if(!$task){
            $this->error = lang('task_not_exist');
            return false;
        }

        if($task->where(array('unique_id'=>$id))->delete()){
            return true;
        }else{
            $this->error = lang('delete').lang('fail');
            return false;
        }

    }


    public function randomkeys($length){

        $output='abcdefghijklmnopqrstuvwxyz0123456789';
        $output = str_shuffle($output);

        $uuid = substr($output,0,$length);
        if($this->where(array('unique_id'=>$uuid))->find()){
            return self::randomkeys($length);
        }
        return $uuid;
    }




}