<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends CI_Model
{
	private $__table = 'settings';

    public function insert($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }

    public function delete($id)
    {
        $this->db->where("id", $id);
        return $this->db->delete($this->__table) ? true : false;
    }

    public function getAll($type = '')
    {
        if ($type != '') {
            $this->db->where(["type" => $type]);
        }
        $this->db->order_by("quantity", "asc");
        $query = $this->db->get($this->__table);
        return $query->num_rows() > 0 ? $query->result() : false;
    }

    public function getById($id)
    {
        $this->db->where("id", $id);
        $query = $this->db->get($this->__table);
        return $query->num_rows() ? $query->row() : false;
    }

    

    

}