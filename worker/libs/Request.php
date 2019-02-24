<?php 
require_once __DIR__ . "/UserAgent.php";

class Request
{
    private $curl;
    
    public function get($url, $cookie = false)
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($this->curl, CURLOPT_COOKIESESSION, true);
        if($cookie)
			curl_setopt($this->curl, CURLOPT_COOKIE, $cookie);       
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_USERAGENT, $this->__getRandUserAgent());
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 30); 
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($this->curl);
        $httpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        curl_close($this->curl);
        $this->curl = null;
        return $httpcode == 200 ? $result : false;
    }
    
    public function post($url, $cookie, $data)
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_HEADER, 1);
		curl_setopt($this->curl, CURLOPT_USERAGENT, $this->__getRandUserAgent()); 
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->curl, CURLOPT_COOKIESESSION, true);
		if($cookie)
			curl_setopt($this->curl, CURLOPT_COOKIE, $cookie);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($this->curl, CURLOPT_POST, 1);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, 30); 
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1); 
		$result = curl_exec($this->curl);
        $httpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        curl_close($this->curl);
        $this->curl = null;
        return $httpcode == 200 ? $result : false;
    }
    
    private function __getRandUserAgent()
    {
        return \Campo\UserAgent::random();
    }
}