<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Uid_model extends CI_Model
{
    
    private $__table = "uids";
    
    public function insert($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }

    public function update($id, $data)
    {
        $this->db->where("id", $id);
        return $this->db->update($this->__table, $data) ? true : false;
    }

    public function count()
    {
        $this->db->select("count(*) total");
        return $this->db->get($this->__table)->row()->total;
    }

    public function get($limit)
    {
        $this->db->select("id, uid");
        $this->db->where(["status" => 1, "commented" => 0]);
        $this->db->limit($limit);
        $query = $this->db->get($this->__table);
        return $query->num_rows() > 0 ? $query->result() : false;
    }


    

    

}
