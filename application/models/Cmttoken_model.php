<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Cmttoken_model extends CI_Model
{
    
    private $__table = "cmt_tokens";

    public function insert($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }

    public function update($id, $data)
    {
        $this->db->where("id", $id);
        return $this->db->update($this->__table, $data) ? true : false;
    }

    public function get($limit)
    {
        $this->db->select("id, token");
        $this->db->where("status", 1);
        $this->db->where("(NOW() - last_used) > 3000");
        $this->db->limit($limit);
        $this->db->order_by("RAND()");
        $query = $this->db->get($this->__table);
        return $query->num_rows() > 0 ? $query->result() : false;
    }
    

    

}
