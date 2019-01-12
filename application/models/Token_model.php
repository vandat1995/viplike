<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Token_model extends CI_Model
{
	private $__table = 'tokens';

    public function insert($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }

    public function getRandOneToken()
    {
        $this->db->select("token");
        $this->db->order_by("RAND()");
        $this->db->limit(1);
        $query = $this->db->get($this->__table);
        return ($query->num_rows() > 0) ? $query->row()->token : false;
    }

    public function getTokens($quantity)
    {
        $this->db->select("token");
        $this->db->order_by("RAND()");
        $this->db->limit($quantity);
        $query = $this->db->get($this->__table);
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

}