<?php
/**
 * Created by PhpStorm.
 * User: Yixiao Chen
 * Date: 2015/6/27 0027
 * Time: 下午 2:08
 */

namespace app\common\model;


class Curl
{
    public function curl($url)
    {
        $cookie_file = 'Runtime/cookie.txt';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT,10);   //只需要设置一个秒的数量就可以
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,3);   //只需要设置一个秒的数量就
		  
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);


		$cloud_cookie = session('cloud_cookie');
        if (isset($cloud_cookie)) {
            curl_setopt($curl, CURLOPT_COOKIE, $this->getCookie(array('PHPSESSID' => $cloud_cookie)));
        }
        $result = curl_exec($curl);
        if($result==false){
            $this->error=curl_error($curl);
            return false;
        }
        curl_close($curl);
        return $result;
    }

    private function getCookie($cookies)
    {

        $cookies_string = '';
        foreach ($cookies as $name => $value) {
            $cookies_string .= $name . '=' . $value . ';';
        }
        return $cookies_string;
    }
}