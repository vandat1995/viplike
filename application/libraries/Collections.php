<?php 

class Collections
{
    protected $_CI;
    public function __construct()
    {
        $this->_CI =& get_instance();
        $this->_CI->load->library("request");
    }

    public function convertUrlToUid($urlProfile)
    {
        $pattern = '/facebook\.com\/(.+)/';
        if (preg_match($pattern, $urlProfile, $matches)) {
            $linkMobile = "https://mbasic." . $matches[0];
            $htmlRaw = $this->_CI->request->get($linkMobile);
            $patternUid = '/rid=([0-9]+)/';
            if (preg_match($patternUid, $htmlRaw, $matches2)) {
                return $matches2[1];
            } else {
                return false;
            }
        } else {
            return $urlProfile;
        }
    }
    
    
}