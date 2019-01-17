<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class TokenProcessMap_model extends CI_Model
{
    
    private $__table = "token_process_map";

    public function insert($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }

    public function update($id, $data)
    {
        $this->db->where("id", $id);
        return $this->db->update($this->__table, $data) ? true : false;
    }

    public function getRandByProcessId($process_id, $limit = 0)
    {
        $this->db->where(["process_id" => $process_id, "is_runned" => 0]);
        $this->db->limit($limit);
    }



}