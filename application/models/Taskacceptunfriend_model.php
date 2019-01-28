<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Taskacceptunfriend_model extends CI_Model
{
	private $__table = 'tasks_accept_unfriend';

    public function insert($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }

    public function update($id, $data)
    {
        $this->db->where("id", $id);
        return $this->db->update($this->__table, $data) ? true : false;
    }

    public function delete($id)
    {
        $this->db->where("id", $id);
        return $this->db->delete($this->__table) ? true : false;
    }

    public function count()
    {
        $this->db->select("count(*) total");
        return $this->db->get($this->__table)->row()->total;
    }

    public function getAllByType($type)
    {
        $this->db->select("id, uid, token, type, url");
        $this->db->where("is_done", 0);
        $this->db->where("type", $type);
        $query = $this->db->get($this->__table);
        return $query->num_rows() > 0 ? $query->result() : false;
    }

    

    
    

    

}