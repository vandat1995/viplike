<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Config_model extends CI_Model
{
    
    public function countCurrentVip()
    {
        return $this->db->count_all("tasks") + $this->db->count_all("tasks_cmt");
    }

    public function countCurrentBot()
    {
        return $this->db->count_all("botlike");
    }
}
