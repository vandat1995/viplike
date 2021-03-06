<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_model extends CI_Model
{
	private $__table = "processes";

    public function checkExistPostId($post_id, $type)
    {
        $this->db->select("id");
        $this->db->where("post_id", $post_id);
        $this->db->where("vip_type", $type);
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

    public function getActiveProcesses()
    {
        $this->db->select("p.id, p.vip_type, p.task_id, p.post_id, IFNULL(t.quantity, tc.quantity) quantity, IFNULL(t.quantity_per_cron, tc.quantity_per_cron) quantity_per_cron");
        $this->db->from("$this->__table p");
        $this->db->join("tasks t", "t.id = p.task_id", "left");
        $this->db->join("tasks_cmt tc", "tc.id = p.task_id", "left");
        $this->db->where("p.is_done", 0);
        $query = $this->db->get();
        if( $query->num_rows() > 0 )
            return $query->result();
        else
            return false;
    }

    public function count($user_id = false)
    {
        $this->db->select("count(*) total");
        if( $user_id )
        {
            $this->db->from("{$this->__table} p");
            $this->db->join("tasks t", "t.id = p.task_id");
            $this->db->where("t.user_id", $user_id);
        }
        else {
            $this->db->from($this->__table);
        }
        return $this->db->get()->row()->total;
    }

    public function reCheckProcess()
    {
        $this->db->select("p.id, p.task_id, p.post_id, t.quantity");
        $this->db->from("$this->__table p");
        $this->db->join("tasks t", "t.id = p.task_id");
        $this->db->where("p.had_enough", 0);
        $this->db->where("p.vip_type", "like");
        $this->db->where("p.is_done", 1);
        $this->db->where("p.created_at BETWEEN '". date("Y-m-d") . " 00:00:00' AND '". date("Y-m-d") . " 23:59:59'");
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result() : false;
    }

}