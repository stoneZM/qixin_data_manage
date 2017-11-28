<?php

namespace app\device\model;

class Libvirt  {
		public  $conn;
		private $last_error;
		private $allow_cached = true;
		private $dominfos = array();
		private $lang_str;
		private $enabled = false;

		function __construct($uri = false, $debug=false) {
			if ($debug)
				$this->set_logfile($debug);
			if ($uri != false) {
				$this->enabled = true;
				$this->connect($uri);
			}
		}

		function _set_last_error()
		{
			$this->last_error = libvirt_get_last_error();
			return false;
		}

		function enabled() {
			return $this->enabled;
		}

		function set_logfile($filename)
		{
			if (!libvirt_logfile_set($filename))
				return $this->_set_last_error();
			return true;
		}

		function get_capabilities() {
			$tmp = libvirt_connect_get_capabilities($this->conn);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_default_emulator() {
			$tmp = libvirt_connect_get_capabilities($this->conn, '//capabilities/guest/arch/domain/emulator');
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

	function domain_new($name, $vcpus, $features, $mem, $maxmem, $clock, $nic, $disk, $port,$disktype, $persistent=true,$other=array()) {

		$uuid = $this->domain_generate_uuid();

//		$emulator = $this->get_default_emulator();


		$fs = '';
		for ($i = 0; $i < sizeof($features); $i++) {
			$fs .= '<'.$features[$i].' />';
		}

		$diskstr = '';
		//virtio
		$bus = $other['is_sata']?'sata':'virtio';
		$bus_count = 0;

		if (!empty($disk) && !empty($disk['path'])) {
            $devArr = array('a','b','c','d','e','f','g');
			foreach($disk['path'] as $k=>$v){
				$bus_count++;
				if(!$other['is_sata']&&$disktype&&$bus_count==1){
					$bus = "ide";
				}
				$dev = 'sd'.$devArr[$k];
				$diskstr .= "
						<disk type='file' device='disk'>
						<driver name='qemu' type='qcow2' />
                        <source file='$v'/>
                        <target dev='$dev' bus='$bus'/>
                        </disk>";
			}
		}

		$netstr = '';

		//根据系统版本判断model
//		if($other && $other['system_type']==1){
		//	$network_model_type = "virtio";
//		}else{
			$network_model_type = "e1000";
//		}

		if (!empty($nic)) {
 			foreach($nic as $k=>$v){
			 	if ($v['type'] == 'NAT'){
					$source_name = 'network';
					$source_value = 'default';
					$type = 'network';
				}
				else{
					$source_name = 'bridge';
					$source_value = $v['type'];
					$type = 'bridge';
				}

				$netstr .= "
					    <interface type='$type'>
					      <mac address='{$v['mac']}'/>
					      <source $source_name='$source_value' />
					      <model type='$network_model_type'/>
					    </interface>";
			}
		}


	$video_model_type = 'cirrus';
         $ram = '';
       $loader_str = '';
		//是否是GPT格式
       if ($disktype) {
       		$loader_str = "<loader>/usr/share/edk2.git/ovmf-x64/OVMF-pure-efi.fd</loader>";
			$video_model_type = 'qxl';
			$ram = "ram='16384'";
       }

		$xml = "
				<domain type='kvm'>
				  <name>$name</name>
				  <uuid>$uuid</uuid>
				  <memory unit='KiB'>$maxmem</memory>
				  <currentMemory unit='KiB'>$mem</currentMemory>
				  <vcpu>$vcpus</vcpu>
				  <os>
					<type arch='x86_64' machine='pc-i440fx-rhel7.0.0'>hvm</type>
					<boot dev='hd'/>
					$loader_str
				  </os>
				  <features>
					$fs
				 </features>
				  <clock offset='localtime'/>
				  <on_poweroff>destroy</on_poweroff>
				  <on_reboot>restart</on_reboot>
				  <on_crash>destroy</on_crash>
				  <devices>
					<emulator>/usr/libexec/qemu-kvm</emulator>
					$diskstr
					<controller type='usb' index='0'>
					</controller>
					 $netstr
					 <serial type='pty'>
				      <target port='0'/>
				    </serial>
				    <console type='pty'>
				      <target type='serial' port='0'/>
				    </console>
				    <channel type='unix'>
				      <target type='virtio' name='org.qemu.guest_agent.0'/>
				    </channel> 
					<input type='tablet' bus='usb'/>
					<input type='mouse' bus='ps2'/>
					<input type='keyboard' bus='usb'/>
					<input type='keyboard' bus='ps2'/>
					<graphics type='vnc' port='$port' autoport='no' listen='0.0.0.0'>
					  <listen type='address' address='0.0.0.0'/>
					</graphics>
					<video>
					  <model type='$video_model_type'  $ram   vram='16384' heads='1'/>
					</video>
					<memballoon model='virtio'>
					</memballoon>
				  </devices>
				</domain>";

		$tmp = libvirt_domain_define_xml($this->conn, $xml);

		if (!$tmp){
			return $this->_set_last_error();
		}
		return $tmp;

	}

    	//获取cpu的状态
	   function get_cpu_stats($cpunr=VIR_NODE_CPU_STATS_ALL_CPUS ){

		   $tmp = libvirt_node_get_cpu_stats($this->conn,$cpunr);
		   return ($tmp)?$tmp:$this->_set_last_error();

	   }
	 //获取最大cpu
	function get_maxvcpus(){

		$tmp = libvirt_connect_get_maxvcpus($this->conn);
		return $tmp ? $tmp : $this->_set_last_error();

	}

	  // 获取node内存状态
	function get_mem_stats(){

		$tmp = libvirt_node_get_mem_stats($this->conn);
		return  $tmp ? $tmp : $this->_set_last_error();
	}

	// 获取最大cpu 和 内存
	static function get_compute_info($ip){

		$url = get_libvirt_url($ip);
		$lv = new Libvirt($url);
		$ci  = $lv->get_connect_information();
		$maxcpu = $ci['hypervisor_maxvcpus'];
		$memStat = $lv->get_mem_stats();

		$data['max_cpu'] = $maxcpu;
		$data['total_mem'] = $memStat['total'];
		$data['free_mem'] = $memStat['free'];

		return $data;

	}

	// 获取domain 内存状态
	function domain_get_memory_stats($domain) {
		$dom = $this->get_domain_object($domain);
		if (!$dom)
			return false;
		$tmp = libvirt_domain_memory_stats($dom);
		return ($tmp) ? $tmp : $this->_set_last_error();
	}
	//获取存储池列表
	function get_list_storagepools($active=true){

		if($active){
			$tmp = libvirt_list_active_storagepools($this->conn);
		}else{
			$tmp = libvirt_list_inactive_storagepools($this->conn);
		}

		return ($tmp) ? $tmp : $this->_set_last_error();
	}

	function domain_get_job_info($domain){

		$dom = $this->get_domain_object($domain);
		if(!$dom)
			return false;
		$tmp = libvirt_domain_get_job_info($dom);
		return $tmp ? $tmp : $this->_set_last_error();

	}



		function create_image($image, $size, $driver) {
			$tmp = libvirt_image_create($this->conn, $image, $size, $driver);
                        return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function remove_image($image, $ignore_error_codes=false ) {
			$tmp = libvirt_image_remove($this->conn, $image);
			if ((!$tmp) && ($ignore_error_codes)) {
				$err = libvirt_get_last_error();
				$comps = explode(':', $err);
				$err = explode('(', $comps[sizeof($comps)-1]);
				$code = (int)Trim($err[0]);

				if (in_array($code, $ignore_error_codes))
					return true;
			}

			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function generate_connection_uri($hv, $remote, $remote_method, $remote_username, $remote_hostname, $session=false) {
			if ($hv == 'qemu') {
				if ($session)
					$append_type = 'session';
				else
					$append_type = 'system';
			}

			if (!$remote) {
				if ($hv == 'xen')
					return 'xen:///';
				if ($hv == 'qemu')
					return 'qemu:///'.$append_type;

				return false;
			}

			if ($hv == 'xen')
				return 'xen+'.$remote_method.'://'.$remote_username.'@'.$remote_hostname;
			else
			if ($hv == 'qemu')
				return 'qemu+'.$remote_method.'://'.$remote_username.'@'.$remote_hostname.'/'.$append_type;
		}

		function test_connection_uri($hv, $rh, $rm, $un, $rp, $hn, $session=false) {
	                $uri = $this->generate_connection_uri($hv, $rh, $rm, $un, $hn, $session);
	                if ($rp) {
				$credentials = array(VIR_CRED_AUTHNAME => $un, VIR_CRED_PASSPHRASE => $rp);
                		$test = libvirt_connect($uri, false, $credentials);
	                }
        	        else
                		$test = libvirt_connect($uri);
			$ok = is_resource($test);
			unset($test);

			if (!$ok)
				$this->_set_last_error();

			return $ok;
		}

		function print_resources() {
			return libvirt_print_binding_resources();
		}

		function connect($uri = 'null') {

			$credentials=array(VIR_CRED_AUTHNAME=>"admin",VIR_CRED_PASSPHRASE=>"admin");
			$this->conn=libvirt_connect($uri,false,$credentials);
			if ($this->conn==false)
				return $this->_set_last_error();
		}

        function domain_disk_add($domain, $img, $dev, $type='scsi', $driver='raw') {
                        $dom = $this->get_domain_object($domain);
                        $tmp = libvirt_domain_disk_add($dom, $img, $dev, $type, $driver);
                        return ($tmp) ? $tmp : $this->_set_last_error();
                }

		function domain_change_numVCpus($domain, $num) {
			$dom = $this->get_domain_object($domain);
			$tmp = libvirt_domain_change_vcpus($dom,$num);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_change_memory_allocation($domain, $memory, $maxmem) {

			$dom = $this->get_domain_object($domain);

			$tmp = libvirt_domain_change_memory($dom,$memory,$maxmem);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_change_boot_devices($domain, $first, $second) {
			$dom = $this->get_domain_object($domain);

			$tmp = libvirt_domain_change_boot_devices($dom, $first, $second);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_get_screenshot($domain) {

			$dom = $this->get_domain_object($domain);

			$tmp = libvirt_domain_get_screenshot($dom, $this->get_hostname(), 8 );
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_get_screenshot_thumbnail($domain, $w=120) {
			$screen = $this->domain_get_screenshot($domain);
			$imgFile = tempnam("/tmp", "libvirt-php-tmp-resize-XXXXXX");;

			if ($screen) {
				$fp = fopen($imgFile, "wb");
				fwrite($fp, $screen);
				fclose($fp);
			}

			if (file_exists($imgFile) && $screen) {
				list($width, $height) = getimagesize($imgFile); 
				$h = ($height / $width) * $w;
			} else {
				$w = $h = 1;
				//$h = $w * (3 / 4.5);
			}

			$new = imagecreatetruecolor($w, $h);
			if ($screen) {
				$img = imagecreatefrompng($imgFile);
				imagecopyresampled($new,$img,0,0,0,0, $w,$h,$width,$height);
				imagedestroy($img);
			}
			else {
				$c = imagecolorallocate($new, 255, 255, 255);
				imagefill($new, 0, 0, $c);
			}

			imagepng($new, $imgFile);
			imagedestroy($new);

			$fp = fopen($imgFile, "rb");
			$data = fread($fp, filesize($imgFile));
			fclose($fp);

			unlink($imgFile);
			return $data;
		}

	    function domain_get_screen_dimensions($domain) {
			$dom = $this->get_domain_object($domain);

			$tmp = libvirt_domain_get_screen_dimensions($dom, $this->get_hostname() );
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_send_keys($domain, $keys) {
			$dom = $this->get_domain_object($domain);

			$tmp = libvirt_domain_send_keys($dom, $this->get_hostname(), $keys);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_send_pointer_event($domain, $x, $y, $clicked = 1) {
			$dom = $this->get_domain_object($domain);

			$tmp = libvirt_domain_send_pointer_event($dom, $this->get_hostname(), $x, $y, $clicked, true);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_disk_remove($domain, $dev) {
			$dom = $this->get_domain_object($domain);

			$tmp = libvirt_domain_disk_remove($dom, $dev);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function supports($name) {
			return libvirt_has_feature($name);
		}

		function macbyte($val) {
			if ($val < 16)
				return '0'.dechex($val);

			return dechex($val);
		}

		function generate_random_mac_addr($seed=false) {
			if (!$seed)
				$seed = 1;

			if ($this->get_hypervisor_name() == 'qemu')
				$prefix = '52:54:00';
			else
			if ($this->get_hypervisor_name() == 'xen')
				$prefix = '00:16:3e';
			else
				$prefix = $this->macbyte(($seed * rand()) % 256).':'.
                                $this->macbyte(($seed * rand()) % 256).':'.
                                $this->macbyte(($seed * rand()) % 256);

			return $prefix.':'.
				$this->macbyte(($seed * rand()) % 256).':'.
				$this->macbyte(($seed * rand()) % 256).':'.
				$this->macbyte(($seed * rand()) % 256);
		}

		function domain_nic_add($domain, $mac, $network, $model=false) {
			$dom = $this->get_domain_object($domain);

			if ($model == 'default')
				$model = false;

			$tmp = libvirt_domain_nic_add($dom, $mac, $network, $model);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_nic_remove($domain, $mac) {
			$dom = $this->get_domain_object($domain);

			$tmp = libvirt_domain_nic_remove($dom, $mac);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_connection() {
			return $this->conn;
		}

		function get_hostname() {
			return libvirt_connect_get_hostname($this->conn);
		}

		function get_domain_object($nameRes) {
			if (is_resource($nameRes))
				return $nameRes;

			$dom=libvirt_domain_lookup_by_name($this->conn, $nameRes);
			if (!$dom) {
				$dom=libvirt_domain_lookup_by_uuid_string($this->conn, $nameRes);
				if (!$dom)
					return $this->_set_last_error();
			}

			return $dom;
		}

		function get_xpath($domain, $xpath, $inactive = false) {
			$dom = $this->get_domain_object($domain);
			$flags = 0;
			if ($inactive)
				$flags = VIR_DOMAIN_XML_INACTIVE;

			$tmp = libvirt_domain_xml_xpath($dom, $xpath, $flags); 
			if (!$tmp)
				return $this->_set_last_error();

			return $tmp;
		}

		function get_cdrom_stats($domain, $sort=true) {
			$dom = $this->get_domain_object($domain);

			$buses =  $this->get_xpath($dom, '//domain/devices/disk[@device="cdrom"]/target/@bus', false);
			$disks =  $this->get_xpath($dom, '//domain/devices/disk[@device="cdrom"]/target/@dev', false);
			$files =  $this->get_xpath($dom, '//domain/devices/disk[@device="cdrom"]/source/@file', false);

			$ret = array();
			for ($i = 0; $i < $disks['num']; $i++) {
				$tmp = libvirt_domain_get_block_info($dom, $disks[$i]);
				if ($tmp) {
					$tmp['bus'] = $buses[$i];
					$ret[] = $tmp;
				}
				else {
					$this->_set_last_error();

					$ret[] = array(
							'device' => $disks[$i],
							'file'   => $files[$i],
							'type'   => '-',
							'capacity' => '-',
							'allocation' => '-',
							'physical' => '-',
							'bus' => $buses[$i]
                                                        );
				}
			}

			if ($sort) {
				for ($i = 0; $i < sizeof($ret); $i++) {
					for ($ii = 0; $ii < sizeof($ret); $ii++) {
						if (strcmp($ret[$i]['device'], $ret[$ii]['device']) < 0) {
							$tmp = $ret[$i];
							$ret[$i] = $ret[$ii];
							$ret[$ii] = $tmp;
						}
					}
				}
			}

			unset($buses);
			unset($disks);
			unset($files);

			return $ret;
		}

		function get_disk_stats($domain, $sort=true) {
			$dom = $this->get_domain_object($domain);

			$buses =  $this->get_xpath($dom, '//domain/devices/disk[@device="disk"]/target/@bus', false);
			$disks =  $this->get_xpath($dom, '//domain/devices/disk[@device="disk"]/target/@dev', false);
			$files =  $this->get_xpath($dom, '//domain/devices/disk[@device="disk"]/source/@file', false);

			$ret = array();
			for ($i = 0; $i < $disks['num']; $i++) {
				$tmp = libvirt_domain_get_block_info($dom, $disks[$i]);
				if ($tmp) {
					$tmp['bus'] = $buses[$i];
					$ret[] = $tmp;
				}
				else {
					$this->_set_last_error();

					$ret[] = array(
							'device' => $disks[$i],
							'file'   => $files[$i],
							'type'   => '-',
							'capacity' => '-',
							'allocation' => '-',
							'physical' => '-',
							'bus' => $buses[$i]
							);
				}
			}

			if ($sort) {
				for ($i = 0; $i < sizeof($ret); $i++) {
					for ($ii = 0; $ii < sizeof($ret); $ii++) {
						if (strcmp($ret[$i]['device'], $ret[$ii]['device']) < 0) {
							$tmp = $ret[$i];
							$ret[$i] = $ret[$ii];
							$ret[$ii] = $tmp;
						}
					}
				}
			}

			unset($buses);
			unset($disks);
			unset($files);

			return $ret;
		}

        function get_nic_info($domain) {
                        $dom = $this->get_domain_object($domain);

                        $macs =  $this->get_xpath($dom, '//domain/devices/interface/mac/@address', false);
			if (!$macs)
				return $this->_set_last_error();

			$ret = array();
			for ($i = 0; $i < $macs['num']; $i++) {
				$tmp = libvirt_domain_get_network_info($dom, $macs[$i]);
				if ($tmp)
					$ret[] = $tmp;
				else {
					$this->_set_last_error();

					$ret[] = array(
							'mac' => $macs[$i],
							'network' => '-',
							'nic_type' => '-'
							);
				}
			}

                        return $ret;
                }

        function get_domain_type($domain) {
                        $dom = $this->get_domain_object($domain);

                        $tmp = $this->get_xpath($dom, '//domain/@type', false);
                        if ($tmp['num'] == 0)
                            return $this->_set_last_error();

                        $ret = $tmp[0];
                        unset($tmp);

                        return $ret;
                }

        function get_domain_emulator($domain) {
                        $dom = $this->get_domain_object($domain);

                        $tmp =  $this->get_xpath($dom, '//domain/devices/emulator', false);
                        if ($tmp['num'] == 0)
                            return $this->_set_last_error();

                        $ret = $tmp[0];
                        unset($tmp);

                        return $ret;
                }

		function get_network_cards($domain) {
			$dom = $this->get_domain_object($domain);

			$nics =  $this->get_xpath($dom, '//domain/devices/interface[@type="network"]', false);
			if (!is_array($nics))
				return $this->_set_last_error();

			return $nics['num'];
		}

		function get_disk_capacity($domain, $physical=false, $disk='*', $unit='?') {
			$dom = $this->get_domain_object($domain);
			$tmp = $this->get_disk_stats($dom);

			$ret = 0;
			for ($i = 0; $i < sizeof($tmp); $i++) {
				if (($disk == '*') || ($tmp[$i]['device'] == $disk))
					if ($physical)
						$ret += $tmp[$i]['physical'];
					else
						$ret += $tmp[$i]['capacity'];
			}
			unset($tmp);

			return $this->format_size($ret, 2, $unit);
		}

		function get_disk_count($domain) {
			$dom = $this->get_domain_object($domain);
			$tmp = $this->get_disk_stats($dom);
			$ret = sizeof($tmp);
			unset($tmp);

			return $ret;
		}

		function format_size($value, $decimals, $unit='?') {
			if ($value == '-')
				return 'unknown';

			/* Autodetect unit that's appropriate */
			if ($unit == '?') {
				/* (1 << 40) is not working correctly on i386 systems */
				if ($value > 1099511627776)
					$unit = 'T';
				else
				if ($value > (1 << 30))
					$unit = 'G';
				else
				if ($value > (1 << 20))
					$unit = 'M';
				else
				if ($value > (1 << 10))
					$unit = 'K';
				else
					$unit = 'B';
			}

			$unit = strtoupper($unit);

			switch ($unit) {
				case 'T': return number_format($value / (float)1099511627776, $decimals, '.', ' ').' TB';
				case 'G': return number_format($value / (float)(1 << 30), $decimals, '.', ' ').' GB';
				case 'M': return number_format($value / (float)(1 << 20), $decimals, '.', ' ').' MB';
				case 'K': return number_format($value / (float)(1 << 10), $decimals, '.', ' ').' kB';
				case 'B': return $value.' B';
			}

			return false;
		}

		function get_uri() {
			$tmp = libvirt_connect_get_uri($this->conn);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_domain_count() {
			$tmp = libvirt_domain_get_counts($this->conn);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_storagepools() {
			$tmp = libvirt_list_storagepools($this->conn);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_storagepool_res($res) {
			if ($res == false)
				return false;
			if (is_resource($res))
				return $res;

			$tmp = libvirt_storagepool_lookup_by_name($this->conn, $res);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_storagepool_info($name) {
			if (!($res = $this->get_storagepool_res($name)))
				return false;

			$path = libvirt_storagepool_get_xml_desc($res, '/pool/target/path');
			if (!$path)
				return $this->_set_last_error();
			$perms = libvirt_storagepool_get_xml_desc($res, '/pool/target/permissions/mode');
			if (!$perms)
				return $this->_set_last_error();
			$otmp1 = libvirt_storagepool_get_xml_desc($res, '/pool/target/permissions/owner');
			if (!is_string($otmp1))
				return $this->_set_last_error();
			$otmp2 = libvirt_storagepool_get_xml_desc($res, '/pool/target/permissions/group');
			if (!is_string($otmp2))
				return $this->_set_last_error();
			$tmp = libvirt_storagepool_get_info($res);
			$tmp['volume_count'] = sizeof( libvirt_storagepool_list_volumes($res) );
			$tmp['active'] = libvirt_storagepool_is_active($res);
			$tmp['path'] = $path;
			$tmp['permissions'] = $perms;
			$tmp['id_user'] = $otmp1;
			$tmp['id_group'] = $otmp2;

			return $tmp;
		}

		function storagepool_get_volume_information($pool, $name=false) {
			if (!is_resource($pool))
				$pool = $this->get_storagepool_res($pool);
			if (!$pool)
				return false;

			$out = array();
			$tmp = libvirt_storagepool_list_volumes($pool);
			for ($i = 0; $i < sizeof($tmp); $i++) {
				if (($tmp[$i] == $name) || ($name == false)) {
					$r = libvirt_storagevolume_lookup_by_name($pool, $tmp[$i]);
					$out[$tmp[$i]] = libvirt_storagevolume_get_info($r);
					$out[$tmp[$i]]['path'] = libvirt_storagevolume_get_path($r);
					unset($r);
				}
			}

			return $out;
		}

		function storagevolume_delete($path) {
			$vol = libvirt_storagevolume_lookup_by_path($this->conn, $path);
			if (!libvirt_storagevolume_delete($vol))
				return $this->_set_last_error();

			return true;
		}

		function translate_volume_type($type) {
			if ($type == 1)
				return 'Block device';

			return 'File image';
		}

		function translate_perms($mode) {
			$mode = (string)((int)$mode);

			$tmp = '---------';

			for ($i = 0; $i < 3; $i++) {
				$bits = (int)$mode[$i];
				if ($bits & 4)
					$tmp[ ($i * 3) ] = 'r';
				if ($bits & 2)
					$tmp[ ($i * 3) + 1 ] = 'w';
				if ($bits & 1)
					$tmp[ ($i * 3) + 2 ] = 'x';
			}
			

			return $tmp;
		}

		function parse_size($size) {
			$unit = $size[ strlen($size) - 1 ];

			$size = (int)$size;
			switch (strtoupper($unit)) {
				case 'T': $size *= 1099511627776;
					  break;
				case 'G': $size *= 1073741824;
					  break;
				case 'M': $size *= 1048576;
					  break;
				case 'K': $size *= 1024;
					  break;
			}

			return $size;
		}

		function storagevolume_create($pool, $name, $capacity, $allocation) {
			$pool = $this->get_storagepool_res($pool);

			$capacity = $this->parse_size($capacity);
			$allocation = $this->parse_size($allocation);

			$xml = "<volume>\n".
                               "  <name>$name</name>\n".
                               "  <capacity>$capacity</capacity>\n".
                               "  <allocation>$allocation</allocation>\n".
                               "</volume>";

			$tmp = libvirt_storagevolume_create_xml($pool, $xml);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_hypervisor_name() {
			$tmp = libvirt_connect_get_information($this->conn);
			$hv = $tmp['hypervisor'];
			unset($tmp);

			switch (strtoupper($hv)) {
				case 'QEMU': $type = 'qemu';
					break;
				case 'XEN': $type = 'xen';
					break;

				default:
					$type = $hv;
			}

			return $type;
		}

		function get_connect_information() {
			$tmp = libvirt_connect_get_information($this->conn);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_change_xml($domain, $xml) {
			$dom = $this->get_domain_object($domain);

			if (!($old_xml = libvirt_domain_get_xml_desc($dom, NULL)))
				return $this->_set_last_error();
			if (!libvirt_domain_undefine($dom))
				return $this->_set_last_error();
			if (!libvirt_domain_define_xml($this->conn, $xml)) {
				$this->last_error = libvirt_get_last_error();
				libvirt_domain_define_xml($this->conn, $old_xml);
				return false;
			}

			return true;
		}

		function network_change_xml($network, $xml) {
			$net = $this->get_network_res($network);

			if (!($old_xml = libvirt_network_get_xml_desc($net, NULL))) {
				return $this->_set_last_error();
			}
			if (!libvirt_network_undefine($net)) {
				return $this->_set_last_error();
			}
			if (!libvirt_network_define_xml($this->conn, $xml)) {
				$this->last_error = libvirt_get_last_error();
				libvirt_network_define_xml($this->conn, $old_xml);
				return false;
			}

			return true;
		}

		function network_new($name, $ipinfo, $dhcpinfo=false, $forward=false, $forward_dev=false, $bridge=false) {
			$uuid = $this->network_generate_uuid();
			if (!$bridge) {
				$maxid = -1;
				$nets = $this->get_networks();
				for ($i = 0; $i < sizeof($nets); $i++) {
					$bridge = $this->get_network_bridge($nets[$i]);
					if ($bridge) {
						$tmp = explode('br', $bridge);
						$id = (int)$tmp[1];

						if ($id > $maxid)
							$maxid = $id;
					}
				}

				$newid = $maxid + 1;
				$bridge = 'virbr'.$newid;
			}

			$forwards = '';
			if ($forward) {
				if (!$forward_dev)
					$forwards = "<forward mode='$forward' />";
				else
					$forwards = "<forward mode='$forward' dev='$forward_dev' />";
			}

			/* array('ip' => $ip, 'netmask' => $mask) has been passed */
			if (is_array($ipinfo)) {
				$ip = $ipinfo['ip'];
				$mask = $ipinfo['netmask'];
			}
			else {
				/* CIDR definition otherwise, like 192.168.122.0/24 */
				$tmp = explode('/', $ipinfo);
				$ipc = explode('.', $tmp[0]);
				$ipc[3] = (int)$ipc[3] + 1;
				$ip = implode('.', $ipc);

				$bin = '';
				for ($i = 0; $i < $tmp[1]; $i++)
					$bin .= '1';

				$tmp = bindec($bin);
				$ipc[0] = $tmp         % 256;
				$ipc[1] = ($tmp >> 8 ) % 256;
				$ipc[2] = ($tmp >> 16) % 256;
				$ipc[3] = ($tmp >> 24) % 256;

				$mask = implode('.', $ipc);
			}

			$dhcps = '';
			if ($dhcpinfo) {
				/* For definition like array('start' => $dhcp_start, 'end' => $dhcp_end) */
				if (is_array($dhcpinfo)) {
					$dhcp_start = $dhcpinfo['start'];
					$dhcp_end = $dhcpinfo['end'];
				}
				else {
					/* Definition like '$dhcp_start - $dhcp_end' */
					$tmp = explode('-', $dhcpinfo);
					$dhcp_start = Trim($tmp[0]);
					$dhcp_end = Trim($tmp[1]);
				}

				$dhcps = "<dhcp>
                                                <range start='$dhcp_start' end='$dhcp_end' />
                                        </dhcp>";
			}

			$xml = "<network>
				<name>$name</name>
				<uuid>$uuid</uuid>
				$forwards
				<bridge name='$bridge' stp='on' delay='0' />
				<ip address='$ip' netmask='$mask'>
					$dhcps
				</ip>
				</network>";

			return $this->network_define($xml);
		}

		function network_define($xml) {
			$tmp = libvirt_network_define_xml($this->conn, $xml);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function network_undefine($network) {
			$net = $this->get_network_res($network);
			$tmp = libvirt_network_undefine($net);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function translate_storagepool_state($state) {

			$ret = 'unknown';
			switch ($state) {
				case 0: $ret = 'pool_not_running';
					break;
				case 1: $ret = 'pool_building';
					break;
				case 2: $ret = 'pool_running';
					break;
				case 3: $ret = 'pool_running_deg';
					break;
				case 4: $ret = 'pool_running_inac';
					break;
			}


			return $ret;
		}

		function get_domain_uuid($domain){
		$dom = $this->get_domain_object($domain);
		return libvirt_domain_get_uuid_string($dom);
		}

		function get_domains() {

			$tmp = libvirt_list_domains($this->conn);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_domain_by_name($name) {
			$tmp = libvirt_domain_lookup_by_name($this->conn, $name);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_networks($type = VIR_NETWORKS_ALL) {
			$tmp = libvirt_list_networks($this->conn, $type);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_nic_models() {
			return array('default', 'rtl8139', 'e1000', 'pcnet', 'ne2k_pci', 'virtio');
		}

		function get_network_res($network) {
			if ($network == false)
				return false;
			if (is_resource($network))
				return $network;

			$tmp = libvirt_network_get($this->conn, $network);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_network_bridge($network) {
			$res = $this->get_network_res($network);
			if ($res == false)
				return false;

			$tmp = libvirt_network_get_bridge($res);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_network_active($network) {
			$res = $this->get_network_res($network);
			if ($res == false)
				return false;

			$tmp = libvirt_network_get_active($res);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function set_network_active($network, $active = true) {
			$res = $this->get_network_res($network);
			if ($res == false)
				return false;

			if (!libvirt_network_set_active($res, $active ? 1 : 0))
				return $this->_set_last_error();

			return true;
		}

		function get_network_information($network) {
			$res = $this->get_network_res($network);
			if ($res == false)
				return false;

			$tmp = libvirt_network_get_information($res);
			if (!$tmp)
				return $this->_set_last_error();
			$tmp['active'] = $this->get_network_active($res);
			return $tmp;
		}

		function get_network_xml($network) {
			$res = $this->get_network_res($network);
			if ($res == false)
				return false;

			$tmp = libvirt_network_get_xml_desc($res, NULL);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_node_devices($dev = false) {
			$tmp = ($dev == false) ? libvirt_list_nodedevs($this->conn) : libvirt_list_nodedevs($this->conn, $dev);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_node_device_res($res) {
			if ($res == false)
				return false;
			if (is_resource($res))
				return $res;

			$tmp = libvirt_nodedev_get($this->conn, $res);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_node_device_caps($dev) {
			$dev = $this->get_node_device_res($dev);

			$tmp = libvirt_nodedev_capabilities($dev);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_node_device_cap_options() {
			$all = $this->get_node_devices();

			$ret = array();
			for ($i = 0; $i < sizeof($all); $i++) {
				$tmp = $this->get_node_device_caps($all[$i]);

				for ($ii = 0; $ii < sizeof($tmp); $ii++)
					if (!in_array($tmp[$ii], $ret))
						$ret[] = $tmp[$ii];
			}

			return $ret;
		}

	        function get_node_by_name($name){
		   $tmp = libvirt_nodedev_get($this->conn,$name);
		   return ($tmp) ? $tmp : $this->_set_last_error();
	        }

		function get_node_device_xml($dev) {
			$dev = $this->get_node_device_res($dev);
			$tmp = libvirt_nodedev_get_xml_desc($dev, NULL);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function get_node_device_information($dev) {
			$dev = $this->get_node_device_res($dev);

			$tmp = libvirt_nodedev_get_information($dev);			
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_get_name($res) {
			$res = $this->get_domain_object($res);
			return libvirt_domain_get_name($res);
		}

		function domain_get_info_call($name = false, $name_override = false) {
			$ret = array();

			if ($name != false) {
				$dom = $this->get_domain_object($name);
				if (!$dom)
					return false;

				if ($name_override)
					$name = $name_override;

				$ret[$name] = libvirt_domain_get_info($dom);
				return $ret;
			}
			else {
				$doms = libvirt_list_domains($this->conn);
				foreach ($doms as $dom) {
					$tmp = $this->get_domain_object($dom);
					$ret[$dom] = libvirt_domain_get_info($tmp);
				}
			}

			ksort($ret);
			return $ret;
		}

		function domain_get_info($name = false, $name_override = false) {
			if (!$name)
				return false;

			if (!$this->allow_cached)
				return $this->domain_get_info_call($name, $name_override);

			$domname = $name_override ? $name_override : $name;
			$domkey  = $name_override ? $name_override : $this->domain_get_name($name);
			if (!array_key_exists($domkey, $this->dominfos)) {
				$tmp = $this->domain_get_info_call($name, $name_override);
				$this->dominfos[$domkey] = $tmp[$domname];
			}

			return $this->dominfos[$domkey];
		}

		function get_last_error() {
			return $this->last_error;
		}

		function domain_get_xml($domain, $get_inactive = false) {
			$dom = $this->get_domain_object($domain);
			if (!$dom)
				return false;

			$tmp = libvirt_domain_get_xml_desc($dom, $get_inactive ? VIR_DOMAIN_XML_INACTIVE : 0);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function network_get_xml($network) {
			$net = $this->get_network_res($network);
			if (!$net)
				return false;

			$tmp = libvirt_network_get_xml_desc($net, NULL);
			return ($tmp) ? $tmp : $this->_set_last_error();;
		}

		function domain_get_id($domain, $name = false) {
			$dom = $this->get_domain_object($domain);
			if ((!$dom) || (!$this->domain_is_running($dom, $name)))
				return false;

			$tmp = libvirt_domain_get_id($dom);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_get_interface_stats($nameRes, $iface) {
			$dom = $this->get_domain_object($nameRes);
			if (!$dom)
				return false;

			$tmp = libvirt_domain_interface_stats($dom, $iface);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}



		function domain_start($dom) {
			$dom=$this->get_domain_object($dom);
			if ($dom) {
				$ret = libvirt_domain_create($dom);
				$this->last_error = libvirt_get_last_error();
				return $ret;
			}

			$ret = libvirt_domain_create_xml($this->conn, $dom);
			$this->last_error = libvirt_get_last_error();
			return $ret;
		}

		function domain_define($xml) {
			$tmp = libvirt_domain_define_xml($this->conn, $xml);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_destroy($domain) {
			$dom = $this->get_domain_object($domain);
			if (!$dom)
				return false;

			$tmp = libvirt_domain_destroy($dom);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_reboot($domain) {
			$dom = $this->get_domain_object($domain);
			if (!$dom)
				return false;

			$tmp = libvirt_domain_reboot($dom);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_suspend($domain) {
			$dom = $this->get_domain_object($domain);
			if (!$dom)
				return false;

			$tmp = libvirt_domain_suspend($dom);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_resume($domain) {
			$dom = $this->get_domain_object($domain);
			if (!$dom)
				return false;

			$tmp = libvirt_domain_resume($dom);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_get_name_by_uuid($uuid) {
			$dom = libvirt_domain_lookup_by_uuid_string($this->conn, $uuid);
			if (!$dom)
				return false;
			$tmp = libvirt_domain_get_name($dom);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function generate_uuid($seed=false) {
			if (!$seed)
				$seed = time();
			srand($seed);

			$ret = array();
			for ($i = 0; $i < 16; $i++)
				$ret[] = $this->macbyte(rand() % 256);

			$a = $ret[0].$ret[1].$ret[2].$ret[3];
			$b = $ret[4].$ret[5];
			$c = $ret[6].$ret[7];
			$d = $ret[8].$ret[9];
			$e = $ret[10].$ret[11].$ret[12].$ret[13].$ret[14].$ret[15];

			return $a.'-'.$b.'-'.$c.'-'.$d.'-'.$e;
		}

		function domain_generate_uuid() {
			$uuid = $this->generate_uuid();

//			while ($this->domain_get_name_by_uuid($uuid))
//				$uuid = $this->generate_uuid();

			return $uuid;
		}

		function network_generate_uuid() {
			/* TODO: Fix after virNetworkLookupByUUIDString is exposed
				 to libvirt-php to ensure UUID uniqueness */
			return $this->generate_uuid();
		}

		function domain_shutdown($domain) {
			$dom = $this->get_domain_object($domain);
			if (!$dom)
				return false;

			$tmp = libvirt_domain_shutdown($dom);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_undefine($domain) {
			$dom = $this->get_domain_object($domain);
			if (!$dom)
				return false;
			$tmp = libvirt_domain_undefine($dom);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}

		function domain_is_running($domain, $name = false) {
			$dom = $this->get_domain_object($domain);
			if (!$dom)
				return false;

			$tmp = $this->domain_get_info( $domain, $name );
			if (!$tmp)
				return $this->_set_last_error();
			$ret = ( ($tmp['state'] == VIR_DOMAIN_RUNNING) || ($tmp['state'] == VIR_DOMAIN_BLOCKED) );
			unset($tmp);
			return $ret;
		}

		function domain_state_translate($state) {

			$ret = 'unknown';
			switch ($state) {
				case VIR_DOMAIN_RUNNING:  $ret = 'running';
						  	  break;
				case VIR_DOMAIN_NOSTATE:  $ret = 'nostate';
							  break;
				case VIR_DOMAIN_BLOCKED:  $ret = 'blocked';
							  break;
				case VIR_DOMAIN_PAUSED:   $ret = 'paused';
							  break;
				case VIR_DOMAIN_SHUTDOWN: $ret = 'shutdown';
							  break;
				case VIR_DOMAIN_SHUTOFF:  $ret = 'shutoff';
							  break;
				case VIR_DOMAIN_CRASHED:  $ret = 'crashed';
							  break;
			}

			return $ret;
		}

		function domain_get_vnc_port($domain) {
			$tmp = $this->get_xpath($domain, '//domain/devices/graphics/@port', false);
			$var = (int)$tmp[0];
			unset($tmp);

			return $var;
		}

		function domain_get_arch($domain) {
			$domain = $this->get_domain_object($domain);

			$tmp = $this->get_xpath($domain, '//domain/os/type/@arch', false);
			$var = $tmp[0];
			unset($tmp);

			return $var;
		}

		function domain_get_description($domain) {
			$tmp = $this->get_xpath($domain, '//domain/description', false);
			$var = $tmp[0];
			unset($tmp);

			return $var;
		}

		function domain_get_clock_offset($domain) {
			$tmp = $this->get_xpath($domain, '//domain/clock/@offset', false);
			$var = $tmp[0];
			unset($tmp);

			return $var;
		}

		function domain_get_feature($domain, $feature) {
			$tmp = $this->get_xpath($domain, '//domain/features/'.$feature.'/..', false);
			$ret = ($tmp != false);
			unset($tmp);

			return $ret;
		}

		function domain_get_boot_devices($domain) {
			$tmp = $this->get_xpath($domain, '//domain/os/boot/@dev', false);
			if (!$tmp)
				return false;

			$devs = array();
			for ($i = 0; $i < $tmp['num']; $i++)
				$devs[] = $tmp[$i];

			return $devs;
		}

		function _get_single_xpath_result($domain, $xpath) {
			$tmp = $this->get_xpath($domain, $xpath, false);
			if (!$tmp)
				return false;

			if ($tmp['num'] == 0)
				return false;

			return $tmp[0];
		}

		function domain_get_multimedia_device($domain, $type, $display=false) {
			$domain = $this->get_domain_object($domain);

			if ($type == 'console') {
				$type = $this->_get_single_xpath_result($domain, '//domain/devices/console/@type');
				$targetType = $this->_get_single_xpath_result($domain, '//domain/devices/console/target/@type');
				$targetPort = $this->_get_single_xpath_result($domain, '//domain/devices/console/target/@port');

				if ($display)
					return $type.' ('.$targetType.' on port '.$targetPort.')';
				else
					return array('type' => $type, 'targetType' => $targetType, 'targetPort' => $targetPort);
			}
			else
			if ($type == 'input') {
				$type = $this->_get_single_xpath_result($domain, '//domain/devices/input/@type');
				$bus  = $this->_get_single_xpath_result($domain, '//domain/devices/input/@bus');

				if ($display)
					return $type.' on '.$bus;
				else
					return array('type' => $type, 'bus' => $bus);
			}
			else
			if ($type == 'graphics') {
				$type = $this->_get_single_xpath_result($domain, '//domain/devices/graphics/@type');
				$port = $this->_get_single_xpath_result($domain, '//domain/devices/graphics/@port');
				$autoport = $this->_get_single_xpath_result($domain, '//domain/devices/graphics/@autoport');

				if ($display)
					return $type.' on port '.$port.' with'.($autoport ? '' : 'out').' autoport enabled';
				else
					return array('type' => $type, 'port' => $port, 'autoport' => $autoport);
			}
			else
			if ($type == 'video') {
				$type  = $this->_get_single_xpath_result($domain, '//domain/devices/video/model/@type');
				$vram  = $this->_get_single_xpath_result($domain, '//domain/devices/video/model/@vram');
				$heads = $this->_get_single_xpath_result($domain, '//domain/devices/video/model/@heads');

				if ($display)
					return $type.' with '.($vram / 1024).' MB VRAM, '.$heads.' head(s)';
				else
					return array('type' => $type, 'vram' => $vram, 'heads' => $heads);
			}
			else
				return false;
		}

		function domain_get_host_devices_pci($domain) {
			$xpath = '//domain/devices/hostdev[@type="pci"]/source/address/@';

			$dom  = $this->get_xpath($domain, $xpath.'domain', false);
			$bus  = $this->get_xpath($domain, $xpath.'bus', false);
			$slot = $this->get_xpath($domain, $xpath.'slot', false);
			$func = $this->get_xpath($domain, $xpath.'function', false);

			$devs = array();
			for ($i = 0; $i < $bus['num']; $i++) {
				$d = str_replace('0x', '', $dom[$i]);
				$b = str_replace('0x', '', $bus[$i]);
				$s = str_replace('0x', '', $slot[$i]);
				$f = str_replace('0x', '', $func[$i]);
				$devid = 'pci_'.$d.'_'.$b.'_'.$s.'_'.$f;
				$tmp2 = $this->get_node_device_information($devid);
				$devs[] = array('domain' => $dom[$i], 'bus' => $bus[$i],
						'slot' => $slot[$i], 'func' => $func[$i],
						'vendor' => $tmp2['vendor_name'],
						'vendor_id' => $tmp2['vendor_id'],
						'product' => $tmp2['product_name'],
						'product_id' => $tmp2['product_id']);
			}

			return $devs;
		}

		function _lookup_device_usb($vendor_id, $product_id) {
			$tmp = $this->get_node_devices(false);
			for ($i = 0; $i < sizeof($tmp); $i++) {
				$tmp2 = $this->get_node_device_information($tmp[$i]);
				if (array_key_exists('product_id', $tmp2)) {
					if (($tmp2['product_id'] == $product_id)
						&& ($tmp2['vendor_id'] == $vendor_id))
							return $tmp2;
				}
			}

			return false;
		}

		function domain_get_host_devices_usb($domain) {
			$xpath = '//domain/devices/hostdev[@type="usb"]/source/';

			$vid = $this->get_xpath($domain, $xpath.'vendor/@id', false);
			$pid = $this->get_xpath($domain, $xpath.'product/@id', false);

			$devs = array();
			for ($i = 0; $i < $vid['num']; $i++) {
				$dev = $this->_lookup_device_usb($vid[$i], $pid[$i]);
				$devs[] = array('vendor_id' => $vid[$i], 'product_id' => $pid[$i],
						'product' => $dev['product_name'],
						'vendor' => $dev['vendor_name']);
			}

			return $devs;
		}

		function domain_get_host_devices($domain) {
			$domain = $this->get_domain_object($domain);

			$devs_pci = $this->domain_get_host_devices_pci($domain);
			$devs_usb = $this->domain_get_host_devices_usb($domain);

			return array('pci' => $devs_pci, 'usb' => $devs_usb);
		}

		function domain_set_feature($domain, $feature, $val) {
			$domain = $this->get_domain_object($domain);

			if ($this->domain_get_feature($domain, $feature) == $val)
				return true;

			$xml = $this->domain_get_xml($domain, true);
			if ($val) {
				if (strpos('features', $xml))
					$xml = str_replace('<features>', "<features>\n<$feature/>", $xml);
				else
					$xml = str_replace('</os>', "</os><features>\n<$feature/></features>", $xml);
			}
			else
				$xml = str_replace("<$feature/>\n", '', $xml);

			return $this->domain_change_xml($domain, $xml);
		}

		function domain_set_clock_offset($domain, $offset) {
			$domain = $this->get_domain_object($domain);

			if (($old_offset = $this->domain_get_clock_offset($domain)) == $offset)
				return true;

			$xml = $this->domain_get_xml($domain, true);
			$xml = str_replace("<clock offset='$old_offset'/>", "<clock offset='$offset'/>", $xml);

			return $this->domain_change_xml($domain, $xml);
		}

		function domain_set_description($domain, $desc) {
			$domain = $this->get_domain_object($domain);

			$description = $this->domain_get_description($domain);
			if ($description == $desc)
				return true;

			$xml = $this->domain_get_xml($domain, true);
			if (!$description)
				$xml = str_replace("</uuid>", "</uuid><description>$desc</description>", $xml);
			else {
				$tmp = explode("\n", $xml);
				for ($i = 0; $i < sizeof($tmp); $i++)
					if (strpos('.'.$tmp[$i], '<description'))
						$tmp[$i] = "<description>$desc</description>";

				$xml = join("\n", $tmp);
			}

			return $this->domain_change_xml($domain, $xml);
		}

		function host_get_node_info() {
			$tmp = libvirt_node_get_info($this->conn);
			return ($tmp) ? $tmp : $this->_set_last_error();
		}
	}
?>
