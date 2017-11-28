<?php
class Socket
{

	private static $WhitelistName = [1057,81062,1019];
	private static $errormsg = ""; //错误信息	
	private static $msgcode = -1; //错误码
	private static $timeout = 5; //超时时间	
    //配置信息
    private static $config = [
		'msg_host'   => '127.0.0.1',    //消息服务器地址
		'msg_port'   => '5000',    //消息服务器端口
		'index_host'    => '127.0.0.1',    //索引服务器地址
		'index_port'    => '5001',    //索引服务器端口
    ];
	
	public function __construct($config = []){
		
    }
    /**
     * 设置配置信息
     * @param array $config
     */
    public static function set_config()
    {	
		$msg_host = config("msg_host");
		if($msg_host){
			$config_msg_host['msg_host'] = $msg_host;
			$config_msg_host['msg_port'] = config("msg_port");
			self::$config =array_merge(self::$config,$config_msg_host);
		}
		
		$index_host = config("index_host");
		if($index_host){
			$config_index_host['index_host'] = $index_host;
			$config_index_host['index_port'] = config("index_port");
			self::$config =array_merge(self::$config,$config_index_host);
		}
		
    }
    /**
     * 验证配置信息
     * @param array $config
     */
    public static function is_config()
    {
		self::set_config();
        $check_config = self::$config;
		if(!$check_config['index_host'] || !$check_config['msg_host']){
            return false;
        }else{
			return true;
		}
    }
    /**
     * 读取数据
     * @param $dir
     * @param bool $parents
     * @return array|bool
     */
	public static function receive_msg($nsocket,$nlen,$ntimeout)
	{
		$recv_len = $nlen;
		$bufferlen = "";
		
		$end_time = time() + $ntimeout;
		while(true)
		{
			if (time() > $end_time){
				break;	
			}

			$tmp = socket_read($nsocket, $recv_len, PHP_BINARY_READ);
			if (strlen($tmp) < $recv_len)
			  {
				  $recv_len = $recv_len - strlen($tmp);
				  $bufferlen = $bufferlen + $tmp;
				  if($recv_len > 0)
					  continue;
			  }
			  else
			  {
				  $bufferlen = $tmp;
				  break;
			  }
		}
		
		if ($nlen != strlen($bufferlen))
		{
			return ""; 
		}	
		return $bufferlen;
	}
	
	
    public static function socket_message($send_data, $typecode, $is_whitelist = false)
    {
		
		if(!self::is_config()){
			self::$msgcode = -1;
			self::$errormsg = "请配置信息";
			return false;
		}		
		
		if ($typecode>0 && $typecode<60000){
			self::$config['host'] = self::$config['msg_host']; 
			self::$config['port'] = self::$config['msg_port'];
		}
		if ($typecode>600000 && $typecode<100000){
			self::$config['host'] = self::$config['index_host'];
			self::$config['port'] = self::$config['index_port'];
		}
		// 建立客户端的socet连接 
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP); 
		$time = time();
		
		//循环的时候每次都减去相应值
		while (!@socket_connect($socket, self::$config['host'], self::$config['port']))    //如果没有连接上就一直死循环
		{
		  $code = socket_last_error($socket);
		  if($code!=0)
		  {
			self::$msgcode = -1;
			self::$errormsg = "消息服务器连接失败！1";
			return false;
		  }
		  if ($code == 115 || $code == 114)
		  {
			if ((time() - $time) >= self::$timeout)    //每次都需要去判断一下是否超时了
			{
			  socket_close($socket);
			  self::$msgcode = -1;
			  self::$errormsg = "服务器连接超时！";
			  return false;
			}
			sleep(1);
			echo "&";
			continue;
		  }
		  self::$errormsg = socket_strerror($err);
		  return false;
		}
		
		//socket_set_block($socket);    //还原阻塞模式
		if (!socket_write($socket,$send_data)) { 
			self::$msgcode = -1;
			self::$errormsg = "消息发送失败！";
			return false;
		}
		if(in_array($typecode, self::$WhitelistName) || $is_whitelist == true)
		{
			self::$msgcode = 0;
			self::$errormsg = '发送成功';
			return false;
		}
		socket_set_nonblock($socket);//务必设置为非阻塞模式
		$bufferlen = self::receive_msg($socket, 4,self::$timeout);    //@socket_read($socket, 4, PHP_BINARY_READ);
		if($bufferlen == "")
		{
			self::$msgcode = -1;
			self::$errormsg = "消息服务器连接失败！2";
			return false;
		} 
		if (preg_match("/not connect/",$bufferlen)) {
			self::$msgcode = -1;
			self::$errormsg = "没有连接成功！";
			return false;
		}
		else 
		{ 
			$buflen = unpack('V*', $bufferlen);
			$buffertype = self::receive_msg($socket, 2,self::$timeout);
			if($buffertype == "")
			{
				self::$msgcode = -1;
				self::$errormsg = "消息服务器连接失败！3";
				return false;
			}
			$buftype = unpack('v*', $buffertype);	
			$bufferdata = self::receive_msg($socket,$buflen[1]-2,self::$timeout);
			if($bufferdata == "")
			{
				self::$msgcode = -1;
				self::$errormsg = "消息服务器连接失败！4";
				return false;
			}else{
				self::$msgcode = 1;
				self::$errormsg = "发送成功！2";
				return $bufferdata;
			}
		} 

    }
    /**
     * 读取错误信息
     * @return mixed
     */
    public static function getError()
    {
		$data['code'] = self::$msgcode;
		$data['msg'] = self::$errormsg;
        return $data;
    }
	
}
