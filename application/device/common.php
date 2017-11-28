<?php





function cacti_snmp_get($hostname, $community, $oid, $version, $username, $password) {
	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	
	if (function_exists("snmpget") == true) {
		/* make sure snmp* is verbose so we can see what types of data
		we are getting back */
		snmp_set_quick_print(0);
		
		$snmp_value = @snmpget($hostname, $community, $oid);
	}else{
		if ($version == "1") {
			$snmp_auth = (read_config_option("smnp_version") == "ucd-snmp") ? "\"$community\"" : "-c \"$community\""; /* v1/v2 - community string */
		}elseif ($version == "2") {
			$snmp_auth = (read_config_option("smnp_version") == "ucd-snmp") ? "\"$community\"" : "-c \"$community\""; /* v1/v2 - community string */
			$version = "2c"; /* ucd/net snmp prefers this over '2' */
		}elseif ($version == "3") {
			$snmp_auth = "-u $username -X $password"; /* v3 - username/password */
		}
		
		/* no valid snmp version has been set, get out */
		if (empty($snmp_auth)) { return; }
		
		if (read_config_option("smnp_version") == "ucd-snmp") {
			$snmp_value = exec(read_config_option("path_snmpget") . " -v$version $hostname $snmp_auth $oid");
		}elseif (read_config_option("smnp_version") == "net-snmp") {
			$snmp_value = exec(read_config_option("path_snmpget") . " $snmp_auth -v $version $hostname $oid");
		}
	}
	
	$snmp_value = format_snmp_string($snmp_value);
	
	return $snmp_value;
}

function cacti_snmp_walk($hostname, $community, $oid, $version, $username, $password) {
	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	
	$snmp_array = array();
	$temp_array = array();
	
	if (function_exists("snmpget") == true) {
		$temp_array = @snmpwalkoid($hostname, $community, $oid);
		$o = 0;
		for (@reset($temp_array); $i = @key($temp_array); next($temp_array)) {
			$snmp_array[$o]["oid"] = ereg_replace("^\.", "", $i); 
			$snmp_array[$o]["value"] = format_snmp_string($temp_array[$i]);
			$o++;
		}
	}else{
		if ($version == "1") {
			$snmp_auth = (read_config_option("smnp_version") == "ucd-snmp") ? "\"$community\"" : "-c \"$community\""; /* v1/v2 - community string */
		}elseif ($version == "2") {
			$snmp_auth = (read_config_option("smnp_version") == "ucd-snmp") ? "\"$community\"" : "-c \"$community\""; /* v1/v2 - community string */
			$version = "2c"; /* ucd/net snmp prefers this over '2' */
		}elseif ($version == "3") {
			$snmp_auth = "-u $username -X $password"; /* v3 - username/password */
		}
		
		if (read_config_option("smnp_version") == "ucd-snmp") {
			$temp_array = exec_into_array(read_config_option("path_snmpwalk") . " -v$version $hostname $snmp_auth $oid");
		}elseif (read_config_option("smnp_version") == "net-snmp") {
			$temp_array = exec_into_array(read_config_option("path_snmpwalk") . " $snmp_auth -v $version $hostname $oid");
		}
		
		if (sizeof($temp_array) == 0) {
			return 0;
		}
		
		for ($i=0; $i < count($temp_array); $i++) {
			$snmp_array[$i]["oid"] = trim(ereg_replace("(.*) =.*", "\\1", $temp_array[$i]));
			$snmp_array[$i]["value"] = format_snmp_string($temp_array[$i]);
		}
	}
	
	return $snmp_array;
}

function format_snmp_string($string) {
	
	
	
	/* strip off all leading junk (the oid and stuff) */
	$string = trim(ereg_replace(".*= ?", "", $string));
	/* remove ALL quotes */
	$string = str_replace("\"", "", $string);
	
	if (preg_match("/(hex:\?)?([a-fA-F0-9]{1,2}(:|\s)){5}/", $string)) {
		$octet = "";
		
		/* strip of the 'hex:' */
		$string = eregi_replace("hex: ?", "", $string);
		
		/* split the hex on the delimiter */
		$octets = preg_split("/\s|:/", $string);
		
		/* loop through each octet and format it accordingly */
		for ($i=0;($i<count($octets));$i++) {
			$octet .= str_pad($octets[$i], 2, "0", STR_PAD_LEFT);
			
			if (($i+1) < count($octets)) {
				$octet .= ":";
			}
		}
		
		/* copy the final result and make it upper case */
		$string = strtoupper($octet);
	}
	
	$string = eregi_replace(REGEXP_SNMP_TRIM, "", $string);
	
	return trim($string);
}
function hexStr2Ascii($hexStr,$separator = ':'){
    $hexStrArr = explode($separator,$hexStr);
    $asciiOut = null;
    foreach($hexStrArr as $octet){
        $asciiOut .= chr(hexdec($octet));
    }
    return $asciiOut;
}
function format_course($now_data,$now_system_uptime,$old_system_uptime) {
	if($now_data){
		
		foreach($now_data as $key=> &$vo){
			$c_name = 'course_data_'.$info['ip'].'_'.$vo['index'];
			$old_data = cache($c_name);
			$s_data['index'] = $vo['index'];
			$s_data['cpu'] = $vo['cpu'];
			$s_data['uptime'] = $vo['uptime'];
			$s_data['system_uptime_now'] = $now_system_uptime;
			$s_data['system_uptime_old'] = $old_system_uptime;
			if(!$old_data){
				$s_data['cpu_usage'] = 0;
				cache($c_name,$s_data);
				$vo['cpu_usage'] = 0;
			}else{
				
				if($vo['cpu'] == $old_data['cpu']){
					$vo['cpu_usage'] = $old_data['cpu_usage'];
				}else{
					$usage = ($vo['cpu'] - $old_data['cpu'])/($now_system_uptime);
					$usage =  round($usage*100*100,0);
					$s_data['cpu_usage'] = $usage;
					cache($c_name,$s_data);
					$vo['cpu_usage'] =$usage;
				}	
			}
		}
	}
	return $now_data;	
	

}


function host_cpu($hostname,$snmp_community,$cmd){
	
	$oids_cpu = array(
	"index" => ".1.3.6.1.2.1.25.3.3.1",
	"usage" => ".1.3.6.1.2.1.25.3.3.1"
	);
	
	if ($cmd == "index") {
		$arr_index = get_indexes($hostname, $snmp_community);
		
		for ($i=0;($i<sizeof($arr_index));$i++) {
			print $arr_index[$i] . "\n";
		}
	}elseif ($cmd == "query") {
		$arg = $_SERVER["argv"][4];
		
		$arr_index = get_indexes($hostname, $snmp_community);
		$arr = get_cpu_usage($hostname, $snmp_community);
		
		for ($i=0;($i<sizeof($arr_index));$i++) {
			if ($arg == "usage") {
				print $arr_index[$i] . "!" . $arr[$i] . "\n";
			}elseif ($arg == "index") {
				print $arr_index[$i] . "!" . $arr_index[$i] . "\n";
			}
		}
	}elseif ($cmd == "get") {
		$arg = $_SERVER["argv"][4];
		$index = $_SERVER["argv"][5];
		
		$arr_index = get_indexes($hostname, $snmp_community);
		$arr = get_cpu_usage($hostname, $snmp_community);
		
		if (isset($arr_index[$index])) {
			print $arr[$index];
		}
	}
	
}



/*host_cpu*/
function get_indexes($hostname, $snmp_community) {
	$arr = reindex(cacti_snmp_walk($hostname, $snmp_community, ".1.3.6.1.2.1.25.3.3.1", "1", "", ""));
	$return_arr = array();
	$j = 0;
	for ($i=0;($i<sizeof($arr));$i++) {
		if (ereg("^[0-9]+$", $arr[$i])) {
			$return_arr[$j] = $j;
			$j++;
		}
	}
	return $return_arr;
}

function get_cpu_usage($hostname, $snmp_community) {
	$arr = reindex(cacti_snmp_walk($hostname, $snmp_community, ".1.3.6.1.2.1.25.3.3.1", "1", "", ""));
	$return_arr = array();
	
	$j = 0;
	
	for ($i=0;($i<sizeof($arr));$i++) {
		if (ereg("^[0-9]+$", $arr[$i])) {
			$return_arr[$j] = $arr[$i];
		}
	}
	
	return $return_arr;
}

/*host_partitions*/
function reindex($arr) {
	$return_arr = array();
	for ($i=0;($i<count($arr));$i++) {
		$return_arr[$i] = $arr[$i]["value"];
	}
	return $return_arr;
}

function get_storage_type($type_id=''){
	
	
	$license_data = get_license();
	
	$types = array();
	if($license_data['config_info']['storage']['storage_net']){
		$types[1] = lang('net_model');
	}
	if($license_data['config_info']['storage']['storage_ali']){
		$types[2] = lang('ali_cloud');
	}
	if($license_data['config_info']['storage']['storage_baidu']){
		$types[3] = lang('baidu_cloud');
	}
	if($license_data['config_info']['storage']['storage_qiniu']){
		$types[4] = lang('qiniu_cloud');
	}
	
	if($type_id){
		return $types[$type_id];
	}else{
		return $types;
	}
	

}
