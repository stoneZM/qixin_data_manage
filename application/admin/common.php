<?php
use think\Session;
use think\Response;
use think\Request;
use think\Url;
use think\DB;
 
function cloudU($url, $p = array())
{
    $url = url($url, $p,'html','');
    return str_replace(__ROOT__, '', $url);
}

function appstoreU($url, $p = array())
{
    return cloud_url() . cloudU($url, $p);
}


function show_cloud_cover($path){
    //不存在http://
    $not_http_remote=(strpos($path, 'http://') === false);
    //不存在https://
    $not_https_remote=(strpos($path, 'https://') === false);
    if ($not_http_remote && $not_https_remote) {
        //本地url
        return str_replace('//', '/', cloud_url() . $path); //防止双斜杠的出现
    } else {
        //远端url
        return $path;
    }
}

/**云市场url
 * @return string
 * @author:zzl(郑钟良) zzl@ourstu.com
 */
function cloud_url()
{
	$os_url = config('systemconfig.updata_url')?config('systemconfig.updata_url'):config('os_update_url');
	$not_http_remote=(strpos($os_url, 'http://') === false);
    //不存在https://
    $not_https_remote=(strpos($os_url, 'https://') === false);
	if ($not_http_remote && $not_https_remote) {
        //本地url
        return 'http://'.$os_url; //防止双斜杠的出现
    } else {
        //远端url
        return $os_url;
    }
}


	
function downsofeware_progress($resource,$download_size, $downloaded, $upload_size, $uploaded){
	
	//刚开始下载或上传时，$dltotal和$ultotal为0，此处避免除0错误
	if(!empty($download_size)){
		
		//$dltotal:download total 下载文件总大小
		//$dlnow:download now 当前已经下载大小
		//$ultotal:upload total 上传文件总大小
		//$ulnow:upload now 当前已经上传大小
		//echo "$dltotal, $dlnow, $percent, $now , $ultotal, $ulnow"."";
		/*set_time_limit(0);
		$map['id'] = 2;
		
		$tmp_path ="data/software/progress";
		
		if ($file = fopen($tmp_path, "wb")) {
			fwrite($file,$downloaded);
			fclose($file);
		}
		ob_flush();
		flush();*/
		
		
		//echo($downloaded);
		//$info['download_size'] = $download_size;
		//$info['downloaded'] = $downloaded;
		//$info['upload_size'] = $upload_size;
		//$info['uploaded'] = $uploaded;
		//$res = db('software',FALSE)->where($map)->update($info);
	}
	

}