<?php
class Ssh {
        
    //连接资源
    private $link;
	
	//错误代码
    public $code = 1;
	
	//远程根目录
    public $remote_dir_ext;
	
    public $mode = 0644;
	
    /**
     * 连接ssh协议的服务器
     * @param string  $server['HOST']    　服务器IP地址
     * @param string  $server['USERNAME']  用户名
     * @param string  $server['PASSWORD']  密码
     * @param integer $server['PORT'] 　　 服务器端口，默认值为22
     */
    public function __construct($server) {
        $host = $server['host'];
		$username = $server['uname'] ? $server['uname'] : '';
		$password = $server['password'] ? $server['password'] : '';
		$port = $server['port'] ? $server['port'] : '22';
		
		if(function_exists('ssh2_connect')==true){
			$this->link = @ssh2_connect($host,$port);
			if(!$this->link){
				$this->code = 2;
				return false;
			}
			if(@ssh2_auth_password($this->link, $username, $password)){
				$this->code = 1;
				return true;
			}else {
				$this->code = 3;
				return false;
			}
		}else{
			$this->code = 1000;
			return false;
		}
    }
	public function cmdlong($cmd)
    {
		$stdout_stream = ssh2_exec($this->link, $cmd);
		sleep(1);
		
		$stderr_stream = ssh2_fetch_stream($stdout_stream, SSH2_STREAM_STDERR);
		$err_string = "";
		$out_string = "";
		
		while($errline = fgets($stderr_stream)) {
			flush();
			$err_string .=  $errline; 
		}
		while($outline = fgets($stdout_stream)) {
			flush();
			$out_string .= $outline;
		}
		
		
		if($err_string){
			$err_string = ">>>>>>>>>".lang('error_log').">>>>>>>>>【------】".$err_string;
		}
		if($out_string){
			$out_string =  ">>>>>>>>>".lang('execution_results').">>>>>>>>>【------】".$out_string;
		}
		
		$data['output'] = $this->replace_string($out_string);
		$data['error'] = $this->replace_string($err_string);
		fclose($stdout_stream);
		return  $data;
    }
	
    public function cmd($cmd)
    {
		$stream = ssh2_exec($this->link, $cmd);
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		
		// Enable blocking for both streams
		stream_set_blocking($errorStream, true);
		stream_set_blocking($stream, true);
		
		$data['output'] = $this->replace_string(stream_get_contents($stream));
		$data['error'] = $this->replace_string(stream_get_contents($errorStream));
		
		// Close the streams        
		fclose($errorStream);
		fclose($stream);
		return  $data;

    }
	
    public function cmd_sql($cmd)
    {
		// Run a command that will probably write to stderr (unless you have a folder named /hom)
		$stream = ssh2_exec($this->link, $cmd);
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		// Enable blocking for both streams
		stream_set_blocking($errorStream, true);
		stream_set_blocking($stream, true);
		
		$data['output'] = $this->replace_string(stream_get_contents($stream));
		$data['error'] = $this->replace_string(stream_get_contents($errorStream));
		// Close the streams        
		fclose($errorStream);
		fclose($stream);
		return  $data;
    }	
	
	
	
    private function replace_string($string)
    {
		$string = htmlspecialchars($string);
		$string = str_replace(array("'","\n\n","\n","\r","\t"),array("\'","<br>","<br>","<br>","<br>"),$string);
		$string = preg_replace('/(<br\s*?\/?>)+$/i','',$string);
		return $string; 
    }
	
	
        
    /**
     * 上传文件
     * @param string $remote 远程存放地址
     * @param string $filename 需要保存的名字，不包含后缀
     * @param string $tagname 标签名称
     */
    public function upload($remote, $filename=null, $tagname='') {
		if(empty($tagname)){
			foreach($_FILES as $value){
				$local = $value['tmp_name'];
				$extension = pathinfo($value['name'], PATHINFO_EXTENSION);//后缀
			}
		}else{
		    $local = $_FILES[$tagname]['tmp_name'];
			if(empty($local)){
				$this->code = 6;
				return false;
			}
			$extension = pathinfo($_FILES[$tagname]['name'], PATHINFO_EXTENSION);//后缀
		}
		
        $remote = trim($remote,'/').'/';
		$this->mkdir($this->remote_dir_ext.$remote); // 创建目录
		if(!empty($filename)){
		    $file = $filename;
		}else{
		    $file = time().'_'.rand(100,999);
		}
		$dir_path = $this->remote_dir_ext.$remote.$file.'.'.$extension;
		
        if(ssh2_scp_send($this->link, $local, $dir_path, $this->mode)){ //上传
			return $remote.$file.'.'.$extension;
        }else{
            $this->code = 5;
            return false;
        }
    }
	
	/**
	 * 创建文件夹
	 * @param string $dir_path 远程目录
	 * @return bool
	 */
	public function mkdir($dir_path){
		$sftp = ssh2_sftp($this->link);
		return ssh2_sftp_mkdir($sftp, $dir_path);
	}
	
	/**
     * 保存文件
     * @param string $remote 远程存放地址
     * @param string $filename 需要保存的名字，包含后缀
     * @param text $text 文件内容
     */
	public function put_content($remote,$filename,$text){
		$sftp = ssh2_sftp($this->link);
		$stream = fopen("ssh2.sftp://$sftp". $this->remote_dir_ext . $remote . $filename, 'w+');
		//"ssh2.sftp://$sftp/server/html/res.ipail.com/fx/aa.html";
		fwrite($stream, $text);
		fclose($stream);
		return $remote . $filename;
	}
        
    /**
     * 获取错误信息
     */
    public function get_error() {
        $err_msg = array(

            '2' => lang('server_is_unavailable'),
            '3' => lang('server_authentication_failed'),
            '4' => lang('remote_root_directory_not_found'),
            '5' => lang('upload_file_failed'),
			'6' => lang('submitted_name_error'),
			'1000' => lang('ssh2_module_was_not_detected'),
        );
        return $err_msg[$this->code];
    }
}
?>