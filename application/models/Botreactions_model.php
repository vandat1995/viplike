<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Botreactions_model extends CI_Model
{
    
    private $__table = "botlike";

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
		$this->db->select("id, token, reactions");
		$query = $this->db->get($this->__table);
		return $query->num_rows() > 0 ? $query->result() : false;
    }
    
    public function delete($id, $user_id)
	{
		if(is_array($id))
            $this->db->where_in("id", $id);
        else {
            if( !$this->__isOwner($id, $user_id) )
                return false;
            $this->db->where("id", $id);
        }  
        return $this->db->delete($this->__table);
    }
    
    public function getAllByUserId($user_id)
    {
        $this->db->where("user_id", $user_id);
        $query = $this->db->get($this->__table);
		return $query->num_rows() > 0 ? $query->result() : false;
    }

    private function __isOwner($id, $user_id)
    {
        $this->db->where(["id" => $id, "user_id" => $user_id]);
        $query = $this->db->get($this->__table);
        return $query->num_rows() > 0 ? true : false;
    }

    

    

}
