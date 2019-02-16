<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class History_model extends CI_Model
{
    
    private $__table = "history";

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
        $this->db->where("user_id", $this->session->userdata("user_id"));
        $this->db->order_by("created_at", "desc");
        $this->db->limit(2000);
		$query = $this->db->get($this->__table);
		return $query->num_rows() > 0 ? $query->result() : false;
    }
    
    public function delete($id)
	{
		return $this->db->delete($this->__table, ["id" => $id]);
    }

    public function log($action, $amount, $module, $detail, $user_id)
    {
        $data = [
            "action" => $action,
            "amount" => number_format($amount),
            "module" => $module,
            "detail" => $detail,
            "user_id" => $user_id
        ];
        return $this->insert($data);
    }
    
}
