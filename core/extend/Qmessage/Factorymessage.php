<?php
namespace Qmessage;
include('pb_proto_NetMessage.php');

class Factorymessage
{
	private static $write_class;
	private static $read_class;
	private static $typecode;
	private static $class_name;
	private static $errormsg = ""; //错误信息	
	
	private static $classlist = 	array (
	 'w2p_update_module' => 
	  array (
		'messagecode' => 2001,
		'messageclass' => 
		array (
		  0 => 'Net_message_W2PUpdateModule',
		  1 => 'Net_message_P2WUpdateModuleAck',
		),
	  )
	); //错误信息
	
	function __construct($class_name='') {
		if($class_name){
			$class_name= strtolower($class_name);
		}
		self::$class_name = $class_name;
	}
	
	public function check_config(){
		if(!self::$class_name){
			self::$errormsg = "请指定消息名称";
			return false;
		}
		$classlist = self::$classlist;
		$get_map = include('map.php');
		if(!empty($get_map)){
			$classlist = $get_map;
		}
		if(!isset($classlist[self::$class_name])){
			self::$errormsg = self::$class_name . "映射库不存在";
			return false;
		}
		$c_class = $classlist[self::$class_name];
		
		self::$typecode = $c_class['messagecode'];
		if(!isset($c_class['messageclass'])){
			self::$errormsg = $c_class['messageclass'] . "映射子库不存在";
			return false;
		}
		foreach ($c_class['messageclass'] as $key => $val){
			if (!class_exists($val)) {
				self::$errormsg = $val . "类不存在";
				return false;
			}
			$class_data[$key] = $val;
		}
		if(isset($class_data[0]) && $class_data[0]){
			self::$write_class = $class_data[0];
		}
		if(isset($class_data[1]) && $class_data[1]){
			self::$read_class = $class_data[1];
		}
	}

	public function write_message($content_data){
		self::check_config();

		if(self::$write_class){
			
			$re_data['header']['nfrom'] = 1;
			foreach($content_data as $dk=>$dv){
				$re_data[strtolower($dk)] = $dv;
			}
			$simple = self::SetData(self::$write_class,$re_data);
			$send_data['message'] = self::PacketMsg($simple);
			$send_data['typecode'] = self::$typecode;
			return $send_data;
		}else{
			self::$errormsg = "写入类不存在";
			return false;
			
		}
	}
	
	public static function SetData($class,$content)
	{
		$simple = new $class();
		
		$Fields = $simple->fields();
		foreach($Fields as $k=>$v){
			$v_name = strtolower($v['name']);
			if(strpos($v['type'], 'Net_message') !== false){
					$class_data = self::SetData($v['type'],$content[$v_name]);
					if(strpos($v['type'], 'Net_message_list') !== false){
						$field_functon = 'append'.$v_name;
					}else{
						$field_functon = 'set'.$v_name;
					}
					$simple->$field_functon($class_data);
			}else{
				
				if($v['required'] && !isset($content[$v_name]) && !$content[$v_name] ){
					$field_data = $v['default'];
				}else{
					$field_data = $content[$v_name];
				}
				$field_functon = 'set'.$v_name;
				$simple->$field_functon($field_data);
				
			}
		}
		return $simple;
	}
	

	
	public function read_message($content_data){
		if(self::$read_class){
			$simple = new self::$read_class();
			$simple->parseFromString($content_data);
			$Fields = $simple->fields();
				foreach($Fields as $k=>$v){
				if(strpos($v['type'], 'Net_message') !== false){
					$v_name = strtolower($v['name']);
					$child_class = 'get'.$v_name;
					$simple_child = $simple->$child_class();
					if (is_array($simple_child)) {
						$repeat_data = array();
						foreach ($simple_child as $key => $obj) {
							$fields_header = $obj->fields();
							if($fields_header){
								foreach($fields_header as $k_h=>$v_h){
									$v_h_name = strtolower($v_h['name']);
									$child_functon = 'get'.$v_h_name;
									$repeat_data[$v_h_name] = $obj->$child_functon();
								}
							}
							$read_data[$v_name][] = $repeat_data;
						}
					}else{
						$fields_header = $simple_child->fields();
						if($fields_header){
							foreach($fields_header as $k_h=>$v_h){
								$v_h_name = strtolower($v_h['name']);
								$child_functon = 'get'.$v_h_name;
								$read_data[$v_name][$v_h_name] = $simple_child->$child_functon();
							}
						}
					}

				}else{
					$field_functon = 'get'.$v['name'];
					$read_data[$v['name']] = $simple->$field_functon();
				}	
			}
			$data_check  = check_message_data($result['code']);
			if($data_check==1 || $data_check==''){
				return $read_data;
			}else{
				self::$errormsg = $data_check;
				return false;	
			}
			
		}else{
			self::$errormsg = "读取类不存在";
			return false;
		}
	}
	
	
	public static function PacketMsg($content)
	{
		$json = $content->serializeToString();
		$len = strlen($json) + 2;
		$bin0 = pack('V*', $len );
		$bin1 = pack('v*', self::$typecode);
		$json = $bin0 . $bin1 . $json;
		return $json;
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
?>