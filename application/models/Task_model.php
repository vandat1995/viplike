<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_model extends CI_Model
{
	private $__table = 'tasks';

    public function insert($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }

    public function getTasks()
    {
        $this->db->select("id, uid, quantity_like");
        $this->db->from($this->__table);
        $this->db->where("(end_day - NOW()) > 1");
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

}