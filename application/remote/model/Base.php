<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/31
 * Time: 16:24
 */

namespace app\remote\model;
use think\Model;
use think\Validate;



class Base extends Model
{

    protected $resultSetType = 'collection';

    protected $rule = array();
    protected $msg = array();
    protected $listRow = 20;
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';


    function __construct($data=[])
    {
        parent::__construct($data);

        $this->listRow = config('paginate')['list_rows'];
        $this->error = "操作失败!";

        self::beforeInsert(function(){

            return $this->validate_data($this->rule,$this->msg);

        });
        self::beforeUpdate(function(){
            return $this->validate_data($this->rule,$this->msg);
        });
    }

    public function getList($map=null,$list_name='list'){

        $list =  $this->where($map)->order('create_time desc')->paginate($this->listRow);
        if($list->total()){
            return array(
                $list_name=>$list,
                'page'=>$list->render()
            );
        }
        return array();
    }


    /**
     * 删除操作
     * @param $id
     * @return bool
     */
    public function del($id){

        $task = $this->get($id);
        if(!$task){
            $this->error = lang('task_not_exist');
            return false;
        }

        if($task->where(array('id'=>$id))->delete()){
            return true;
        }else{
            $this->error = lang('delete').lang('fail');
            return false;
        }

    }

    /** 数据验证
     * @return array|bool|string
     */
    protected function validate_data($rule,$msg){

        if(!$rule&&!$msg){
            return true;
        }
        $validate = new Validate($rule,$msg);
        if(!$validate->check($this->data)){
            $this->error = $validate->getError();
            return false;
        }else{
            return true;
        }

    }

}