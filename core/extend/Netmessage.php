<?php
class Netmessage
{

/*
$data['deviceuniqueid'] ='0050562ac0be';
$data['module'] =array('name'=>'move','version'=>'1.0.3');
$result = \Netmessage::send_message('W2P_Update_Module', $data);
if ($result) {
	print_r($result);
	exit;
} else {
	exit(\Netmessage::getError());
}
*/

	private static $errormsg = ""; //错误信息	
	public function __construct($config = []){
    }
   
    public static function send_message($class, $data, $is_whitelist = false)
    {
		
		$content = $data;
		$reflection = new \Qmessage\Factorymessage($class);
		$write_data = $reflection->write_message($content);
		if(!$write_data){
			self::$errormsg = $reflection->getError();
			return false;	
		}
		
		$socket_data = \Socket::socket_message($write_data['message'],$write_data['typecode'],$is_whitelist);


		if($socket_data){
			$read_data = $reflection->read_message($socket_data);
			if(!$read_data){
				self::$errormsg = $reflection->getError();
				return false;	
			}else{
				return $read_data;
			}
		}else{
			$msg_data = \Socket::getError();
			if($msg_data['code'] == 0){
				self::$errormsg = $msg_data;
				return true;
			}else{
				self::$errormsg = $msg_data['msg'];
				return false;
			}
		}
    }
	
    /**
     * 读取错误信息
     * @return mixed
     */
    public static function getError()
    {
        return self::$errormsg;
    }
	
}
