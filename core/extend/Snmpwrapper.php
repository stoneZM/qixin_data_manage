<?php
class Snmpwrapper {
        
	protected $_host;
    protected $_community;
    protected $_version;

    public function __construct($host='localhost',$community='public',$version=1)
    {
        $this->_host = $host;
        $this->_community = $community;
        switch ($version) {
            case 2:
                $this->_version = '2';
                break;
            case 3:
                $this->_version = '3';
            default:
                $this->_version = '';
                break;
        }
    }

    public function __call($func,$args)
    {
        $func = strtolower(preg_replace('/([A-Z])/', '_$1', $func));	
        $function = 'snmp' . $this->_version . (empty($this->_version) ? '' : '_') . $func;
        if (function_exists($function)) {
			$res = @call_user_func_array($function, array_merge(array($this->_host,$this->_community),$args));
			if($res){
				return $res;
			}else{
				return '';
			} 
        }
    }
	
	
	public function host_system($cmd,$arg='',$index=''){
		
		$oids = array(
		"index" => ".1.3.6.1.2.1.1",
		"name" => ".1.3.6.1.2.1.1.5.0",
		"desc" => ".1.3.6.1.2.1.1.1.0",
		"uptime" => ".1.3.6.1.2.1.1.3.0",
		"service" => ".1.3.6.1.2.1.25.1.6",
		);
		
		if ($cmd == "index") {
			return reindex(cacti_snmp_walk($this->_host, $this->_community, $oids["index"], "1", "", ""));
		}elseif ($cmd == "get") {
			return cacti_snmp_get($this->_host, $this->_community, $oids[$arg], "1", "", "");
		}elseif ($cmd == "getone") {
			$return_data = @snmpwalk($this->_host, $this->_community, $oids[$arg]);
			return $return_data[0];
		}
		
	}
	
	public function get_system(){
		/*system*/
		$system_index = $this->host_system('index');		
		if($system_index){
			$system['name'] = $this->host_system('get','name');
			$system['desc'] = $this->host_system('get','desc');
			$system['uptime'] = $this->host_system('get','uptime');
			$system['service'] = $this->host_system('getone','service');
			if(strstr(strtolower($system['desc']),"windows"))
			{
				$system['systype'] = 'windows';
			}
			elseif(strstr(strtolower($system['desc']),"linux"))
			{
				$system['systype'] = 'linux';
			}
			else
			{
				$system['systype'] = '未知';
			}
			if($system){
				return $system;
			}
		}
	}
	
	
	public function host_storage($cmd,$arg='',$index=''){
		
		$oids_partitions = array(
		"total" => ".1.3.6.1.2.1.25.2.3.1.5",
		"used" => ".1.3.6.1.2.1.25.2.3.1.6",
		"failures" => ".1.3.6.1.2.1.25.2.3.1.7",
		"index" => ".1.3.6.1.2.1.25.2.3.1.1",
		"description" => ".1.3.6.1.2.1.25.2.3.1.3",
		"sau" => ".1.3.6.1.2.1.25.2.3.1.4",
		"type" => ".1.3.6.1.2.1.25.2.3.1.2"
		);
		
		if ($cmd == "index") {
			$return_arr = reindex(cacti_snmp_walk($this->_host, $this->_community, $oids_partitions["index"], "1", "", ""));
			/*for ($i=0;($i<count($return_arr));$i++) {
				print $return_arr[$i] . "\n";
			}*/
			return $return_arr;
			
		}elseif ($cmd == "query") {
			$arr_index = reindex(cacti_snmp_walk($this->_host, $this->_community, $oids_partitions["index"], "1", "", ""));
			$arr = reindex(cacti_snmp_walk($this->_host, $this->_community, $oids_partitions[$arg], "1", "", ""));
			for ($i=0;($i<sizeof($arr_index));$i++) {
				$return_arr[$arr_index[$i]]  = $arr[$i];
			}
			return $return_arr;
			
		}elseif ($cmd == "get") {
			return cacti_snmp_get($this->_host, $this->_community, $oids_partitions[$arg] . ".$index", "1", "", "");
		}
		
	}
	
	public function get_win_memory(){
		/*memory*/
		$storage_index = $this->host_storage('index');		
		if($storage_index){
			foreach ($storage_index as $s_key => &$s_vo){
				$storage[$s_vo]['index'] = $s_vo;
				$storage[$s_vo]['description'] = $this->host_storage('get','description',$s_vo);
				$storage[$s_vo]['sau'] = $this->host_storage('get','sau',$s_vo);
				$storage[$s_vo]['total'] = $this->host_storage('get','total',$s_vo);
				$storage[$s_vo]['used'] = $this->host_storage('get','used',$s_vo);
				$storage[$s_vo]['failures'] = $this->host_storage('get','failures',$s_vo);
				$storage[$s_vo]['total_size'] = $storage[$s_vo]['sau']*$storage[$s_vo]['total'];
				$storage[$s_vo]['used_size'] = $storage[$s_vo]['sau']*$storage[$s_vo]['used'];
				$storage[$s_vo]['use'] = round(@($storage[$s_vo]['used_size'] / $storage[$s_vo]['total_size'])*100,2);
				$storage[$s_vo]['total_size_unit'] =format_bytes($storage[$s_vo]['total_size']) ;
				$storage[$s_vo]['used_size_unit'] = format_bytes($storage[$s_vo]['used_size']);
			}
			
			
			if($storage){
				foreach ($storage as $sd_key => &$sd_vo){
					if(strtolower($sd_vo['description']) == 'virtual memory'){
						$memory_data['virtual'] = $sd_vo;
					}elseif(strtolower($sd_vo['description']) == 'physical memory'){
						$memory_data['physical'] = $sd_vo;
					}
				}
				return $memory_data;
				
				
			}
		}
		
	}
	
	public function get_win_storage(){
		/*storage*/
		$storage_index = $this->host_storage('index');		
		if($storage_index){
			foreach ($storage_index as $s_key => &$s_vo){
				$storage[$s_vo]['index'] = $s_vo;
				$storage[$s_vo]['description'] = $this->host_storage('get','description',$s_vo);
				$storage[$s_vo]['sau'] = $this->host_storage('get','sau',$s_vo);
				$storage[$s_vo]['total'] = $this->host_storage('get','total',$s_vo);
				$storage[$s_vo]['used'] = $this->host_storage('get','used',$s_vo);
				$storage[$s_vo]['failures'] = $this->host_storage('get','failures',$s_vo);
				$storage[$s_vo]['total_size'] = $storage[$s_vo]['sau']*$storage[$s_vo]['total'];
				$storage[$s_vo]['used_size'] = $storage[$s_vo]['sau']*$storage[$s_vo]['used'];
				$storage[$s_vo]['use'] = round(@($storage[$s_vo]['used_size'] / $storage[$s_vo]['total_size'])*100,2);
				$storage[$s_vo]['total_size_unit'] =format_bytes($storage[$s_vo]['total_size']) ;
				$storage[$s_vo]['used_size_unit'] = format_bytes($storage[$s_vo]['used_size']);
			}
			if($storage){
				foreach ($storage as $sd_key => &$sd_vo){
					if(strtolower($sd_vo['description']) =="virtual memory" || strtolower($sd_vo['description']) == 'physical memory'){
					}else{	
						$storage_data[$sd_key] = $sd_vo;
					}	
					
				}
				return $storage_data;
			}
		}
	}
	
	
	
	
	public function get_win_cpu(){
		/*cpu*/
		
		$oids = array(
			"used" => ".1.3.6.1.2.1.25.3.3.1.2",
		);
		
		$cpu_data = reindex(cacti_snmp_walk($this->_host, $this->_community, $oids["used"], "1", "", ""));
		if($cpu_data){
			
			$cpu_all = 0;
			$cpu_count = count($cpu_data);
			foreach ($cpu_data as $c_key => &$c_vo) {
					$cpu_all += $c_vo;
			}
			$cpu_usage = round(@($cpu_all / $cpu_count),2);
		}
		
		$data['cpu_usage'] = $cpu_usage;
		$data['cpu_record'] = $cpu_data;
		return $data;
	}
	
	public function host_addar($cmd,$arg='',$index=''){
		
		$oids = array(
			"addar" => ".1.3.6.1.2.1.4.20.1.1",
			"index" => ".1.3.6.1.2.1.4.20.1.2",
			"mask" => ".1.3.6.1.2.1.4.20.1.3",
			"bcastaddr" => ".1.3.6.1.2.1.4.20.1.4",
			"maxsize" => ".1.3.6.1.2.1.4.20.1.5",
		);
		if ($cmd == "addar") {
			$return_arr = reindex(cacti_snmp_walk($this->_host, $this->_community, $oids["addar"], "1", "", ""));
			/*for ($i=0;($i<count($return_arr));$i++) {
				print $return_arr[$i] . "\n";
			}*/
			return $return_arr;
			
		}elseif ($cmd == "query") {
			$arr_index = reindex(cacti_snmp_walk($this->_host, $this->_community, $oids["index"], "1", "", ""));
			$arr = reindex(cacti_snmp_walk($this->_host, $this->_community, $oids[$arg], "1", "", ""));
			for ($i=0;($i<sizeof($arr_index));$i++) {
				$return_arr[$arr_index[$i]]  = $arr[$i];
			}
			return $return_arr;
			
		}elseif ($cmd == "get") {
			return cacti_snmp_get($this->_host, $this->_community, $oids[$arg] . ".$index", "1", "", "");
		}
		
	}

	public function get_win_addar(){
		/*addar*/
		$addar_index = $this->host_addar('addar');		
		if($addar_index){
			foreach ($addar_index as $s_key => &$s_vo){
				$addar_data[$s_vo]['addar'] = $s_vo;
				$addar_data[$s_vo]['index'] = $this->host_addar('get','index',$s_vo);
				$addar_data[$s_vo]['mask'] = $this->host_addar('get','mask',$s_vo);
				$addar_data[$s_vo]['bcastaddr'] = $this->host_addar('get','bcastaddr',$s_vo);
				$addar_data[$s_vo]['maxsize'] = $this->host_addar('get','maxsize',$s_vo);
			}			
			if($addar_data){
				return $addar_data;
			}
		}
	}
	
	public function host_course($cmd,$arg='',$index=''){
		
		$oids = array(
			"runid" => ".1.3.6.1.2.1.25.4.2.1.3",
			"index" => ".1.3.6.1.2.1.25.4.2.1.1",
			"runname" => ".1.3.6.1.2.1.25.4.2.1.2",
			"runpath" => ".1.3.6.1.2.1.25.4.2.1.4",
			"runparame" => ".1.3.6.1.2.1.25.4.2.1.5",
			"runtype" => ".1.3.6.1.2.1.25.4.2.1.6",
			"runstatus" => ".1.3.6.1.2.1.25.4.2.1.7",
			"runcpu" => ".1.3.6.1.2.1.25.5.1.1.1",
			"runmem" => ".1.3.6.1.2.1.25.5.1.1.2",
			"uptime" => ".1.3.6.1.2.1.25.1.1",
		);
		if ($cmd == "index") {
			$return_arr = reindex(cacti_snmp_walk($this->_host, $this->_community, $oids["index"], "1", "", ""));
			/*for ($i=0;($i<count($return_arr));$i++) {
				print $return_arr[$i] . "\n";
			}*/
			return $return_arr;
			
		}elseif ($cmd == "query") {
			$arr_index = reindex(cacti_snmp_walk($this->_host, $this->_community, $oids["index"], "1", "", ""));
			$arr = reindex(cacti_snmp_walk($this->_host, $this->_community, $oids[$arg], "1", "", ""));
			for ($i=0;($i<sizeof($arr_index));$i++) {
				$return_arr[$arr_index[$i]]  = $arr[$i];
			}
			return $return_arr;
			
		}elseif ($cmd == "get") {
			return cacti_snmp_get($this->_host, $this->_community, $oids[$arg] . ".$index", "1", "", "");
		}elseif ($cmd == "getone") {
			$uptime = @snmpwalk($this->_host, $this->_community, $oids[$arg]);
			return $uptime[0];
		}
		
	}
	

	public function get_win_course(){
		/*course*/
		$course_index = $this->host_course('index');
		if($course_index){
			$course_time = $this->host_course('getone','uptime');
			foreach ($course_index as $s_key => &$s_vo){
				$course_data[$s_vo]['index'] = $s_vo;
				$course_data[$s_vo]['name'] = $this->host_course('get','runname',$s_vo);
				$course_data[$s_vo]['path'] = $this->host_course('get','runpath',$s_vo);
				$course_data[$s_vo]['cpu'] = $this->host_course('get','runcpu',$s_vo);
				$course_data[$s_vo]['mem'] = format_bytes($this->host_course('get','runmem',$s_vo)*1024);
				$course_data[$s_vo]['uptime'] = $course_time;
			}
			if($course_data){
				return $course_data;
			}
		}
	}
	
	public function host_net($cmd,$arg='',$index=''){
		
		$oids = array(
			"index" => ".1.3.6.1.2.1.2.2.1.1",
			"desc" => ".1.3.6.1.2.1.2.2.1.2",
			"type" => ".1.3.6.1.2.1.2.2.1.3",
			"mtu" => ".1.3.6.1.2.1.2.2.1.4",
			"speed" => ".1.3.6.1.2.1.2.2.1.5",
			"physaddress" => ".1.3.6.1.2.1.2.2.1.6",
			"adminstatus" => ".1.3.6.1.2.1.2.2.1.7",
			"operstatus" => ".1.3.6.1.2.1.2.2.1.8",
			"listchange" => ".1.3.6.1.2.1.2.2.1.9",
			"inoctets" => ".1.3.6.1.2.1.2.2.1.10",
			"inucastpkts" => ".1.3.6.1.2.1.2.2.1.11",
			"innucastpkts" => ".1.3.6.1.2.1.2.2.1.12",
			"indiscards" => ".1.3.6.1.2.1.2.2.1.13",
			"outoctets" => ".1.3.6.1.2.1.2.2.1.16",
			"outucastpkts" => ".1.3.6.1.2.1.2.2.1.17",
			"outnucastpkts" => ".1.3.6.1.2.1.2.2.1.18",
			"outdiscards" => ".1.3.6.1.2.1.2.2.1.19",
			"outqlen" => ".1.3.6.1.2.1.2.2.1.21",
		);
		if ($cmd == "index") {
			$return_arr = reindex(cacti_snmp_walk($this->_host, $this->_community, $oids["index"], "1", "", ""));
			/*for ($i=0;($i<count($return_arr));$i++) {
				print $return_arr[$i] . "\n";
			}*/
			return $return_arr;
			
		}elseif ($cmd == "query") {
			$arr_index = reindex(cacti_snmp_walk($this->_host, $this->_community, $oids["index"], "1", "", ""));
			$arr = reindex(cacti_snmp_walk($this->_host, $this->_community, $oids[$arg], "1", "", ""));
			for ($i=0;($i<sizeof($arr_index));$i++) {
				$return_arr[$arr_index[$i]]  = $arr[$i];
			}
			return $return_arr;
			
		}elseif ($cmd == "get") {
			return cacti_snmp_get($this->_host, $this->_community, $oids[$arg] . ".$index", "1", "", "");
		}elseif ($cmd == "getone") {
			$uptime = @snmpwalk($this->_host, $this->_community, $oids[$arg].".$index");
			return $uptime[0];
		}
		
	}
	
	public function get_win_net(){
		/*net*/
		$net_index = $this->host_net('index');
		if($net_index){
			$ip_addar = $this->get_win_addar();
			if($ip_addar){
				foreach ($ip_addar as $i_key => &$i_vo){
					$addar_group[$i_vo['index']] = $i_vo;
				}	
			}
			foreach ($net_index as $s_key => &$s_vo){
				
				if(!$addar_group[$s_vo]['addar']){
					continue;	
				}
				$net_data[$s_vo]['index'] = $s_vo;
				$net_data[$s_vo]['desc'] = iconv("GB2312", "UTF-8", $this->host_net('get','desc',$s_vo));
				$net_data[$s_vo]['type'] = $this->host_net('get','type',$s_vo);
				$net_data[$s_vo]['mtu'] = $this->host_net('get','mtu',$s_vo);
				$net_data[$s_vo]['addar'] = $addar_group[$s_vo]['addar'];
				$net_data[$s_vo]['mask'] = $addar_group[$s_vo]['mask'];
				$net_data[$s_vo]['operstatus'] = $this->host_net('get','operstatus',$s_vo);

				$speed = $this->host_net('get','speed',$s_vo);
				$inoctets = $this->host_net('get','inoctets',$s_vo);
				$outoctets = $this->host_net('get','outoctets',$s_vo);
				
				$net_data[$s_vo]['speed'] = format_bytes_net($speed);
				$net_data[$s_vo]['inoctets'] = format_bytes_net($inoctets);
				$net_data[$s_vo]['outoctets'] = format_bytes_net($outoctets);
				$net_data[$s_vo]['alloctets'] = format_bytes_net(($inoctets+$outoctets));
			}
			if($net_data){
				return $net_data;
			}
		}
	}
	
	
    public function getwalk($oid)
    {
		$res = @snmpwalk($this->_host, $this->_community, $oid);
		if($res){
			for ($i=0; $i<count($res); $i++) {
				$data[$i] = $res[$i];
			}
			return $data;
		}else{
			return '';
		}	
    }
	
    public function getwalkoid($oid)
    {	
		$res = @snmpwalkoid($this->_host, $this->_community, $oid);
		if($res){
			for (reset($res); $i = key($res); next($res)) {
				$data[$i] = $res[$i];
			}
			return $data;
		}else{
			return '';
		}
    }
	
	
}
?>