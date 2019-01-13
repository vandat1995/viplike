<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_process_model extends CI_Model
{
	private $__table = "task_process";

    public function checkExistIdLiked($id_liked)
    {
        $this->db->select("id");
        $this->db->where("id_liked", $id_liked);
        $query = $this->db->get($this->__table);
        return ($query->num_rows() > 0) ? true : false;
    }

    public function saveProcessDone($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }

    public function getProcessese()
    {
        $this->db->select("tp.id, tp.task_id, tp.id_liked, tp.remain, tp.success, tp.fail, t.quantity_like, t.quantity_per_cron");
        $this->db->from("task_process tp");
        $this->db->join("tasks t", "t.id = tp.task_id");
        $this->db->where("remain >", 0);
        $query = $this->db->get();
        if($query->num_rows() > 0)
            return $query->result();
        else
            return false;
    }

    public function update($id, $data)
    {
        $this->db->where("id", $id);
        $this->db->update($this->__table, $data);
    }

    public function count()
    {
        $this->db->select("count(*) total");
        return $this->db->get($this->__table)->row()->total;
    }

}