<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Bufflike_model extends CI_Model
{
    
    private $__table = "bufflike";

    public function insert($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }

    public function update($id, $data)
    {
        $this->db->where("id", $id);
        return $this->db->update($this->__table, $data) ? true : false;
    }

    public function getAll()
	{
		$this->db->order_by("created_at", "desc");
		$query = $this->db->get($this->__table);
		return $query->num_rows() > 0 ? $query->result() : false;
    }
    
    public function delete($id)
	{
		return $this->db->delete($this->__table, ["id" => $id]);
	}

    

    

}
