<?php 

class Collections
{
    protected $_CI;
    public function __construct()
    {
        $this->_CI =& get_instance();
        $this->_CI->load->library("request");
    }

    public function convertUrlToUid($urlProfile, $token)
    {
        if (is_numeric($urlProfile)) 
        {
            return $urlProfile;
        }
        
        $pattern = '/facebook\.com\/(.+)(\?)*/';
        if (preg_match($pattern, $urlProfile, $matches)) 
        {
            $username = explode("?", $matches[1])[0];
            $url = "https://graph.facebook.com/v3.2/{$username}?access_token={$token}";
            $data = json_decode($this->_CI->request->get($url), true);
            if (isset($data["id"])) 
            {
                return $data["id"];
            }
        }
        return false;
        
    }
    
    
}