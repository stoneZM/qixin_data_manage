<?php
use think\Session;
use think\Response;
use think\Request;
use think\Url;


function create_guid($namespace = null) {  
    static $guid = '';  
    $uid = uniqid ( "", true );  
      
    $data = $namespace;  
    $data .= $_SERVER ['REQUEST_TIME'];     // 请求那一刻的时间戳  
    $data .= $_SERVER ['HTTP_USER_AGENT'];  // 获取访问者在用什么操作系统  
    $data .= $_SERVER ['SERVER_ADDR'];      // 服务器IP  
    $data .= $_SERVER ['SERVER_PORT'];      // 端口号  
    $data .= $_SERVER ['REMOTE_ADDR'];      // 远程IP  
    $data .= $_SERVER ['REMOTE_PORT'];      // 端口信息  
      
    $hash = strtoupper ( hash ( 'ripemd128', $uid . $guid . md5 ( $data ) ) );  
    $guid =  substr ( $hash, 0, 8 ) . '-' . substr ( $hash, 8, 4 ) . '-' . substr ( $hash, 12, 4 ) . '-' . substr ( $hash, 16, 4 ) . '-' . substr ( $hash, 20, 12 );  
      
    return $guid;  
}  