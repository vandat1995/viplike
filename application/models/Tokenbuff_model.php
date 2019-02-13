<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Tokenbuff_model extends CI_Model
{
    
    private $__table = "tokenbuff";

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

    public function getTokens($quantity)
    {
        $this->db->select("id, token");
        $this->db->order_by("RAND()");
        $this->db->limit($quantity);
        $query = $this->db->get($this->__table);
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function getAll()
    {
        $this->db->select("id, token, gender, status");
        $query = $this->db->get($this->__table);
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    

}
