<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class TaskProcess_model extends CI_Model
{
	private $__table = "task_process";

    public function checkExistPostId($post_id)
    {
        $this->db->select("id");
        $this->db->where("post_id", $post_id);
        $query = $this->db->get($this->__table);
        return ($query->num_rows() > 0) ? true : false;
    }

    public function insert($data)
    {
        return $this->db->insert($this->__table, $data) ? $this->db->insert_id() : false;
    }

    public function update($id, $data)
    {
        $this->db->where("id", $id);
        return $this->db->update($this->__table, $data) ? true : false;
    }

    public function getActiveProcessese()
    {
        $this->db->select("tp.id, tp.task_id, tp.post_id, t.quantity_like, t.quantity_per_cron");
        $this->db->from("task_process tp");
        $this->db->join("tasks t", "t.id = tp.task_id");
        $this->db->where("tp.is_done", 0);
        $query = $this->db->get();
        if($query->num_rows() > 0)
            return $query->result();
        else
            return false;
    }

    public function count()
    {
        $this->db->select("count(*) total");
        return $this->db->get($this->__table)->row()->total;
    }

}