<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_model extends CI_Model
{
	private $__table = 'tasks';

    public function insert($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }

    public function delete($id)
    {
        $this->db->where("id", $id);
        return $this->db->delete($this->__table) ? true : false;
    }

    public function getActiveTasks()
    {
        $this->db->select("id, uid, quantity_like, quantity_per_cron, reactions");
        $this->db->from($this->__table);
        $this->db->where("(end_day - NOW()) > 1");
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function checkExistUID($uid)
    {
        $this->db->select("id");
        $this->db->where("uid", $uid);
        $query = $this->db->get($this->__table);
        return ($query->num_rows() > 0) ? true : false;
    }

    public function count()
    {
        $this->db->select("count(*) total");
        return $this->db->get($this->__table)->row()->total;
    }

    public function getListUidByUser($user_id, $role_id)
    {
        $this->db->select("t.id, t.uid, t.quantity_like, t.quantity_per_cron, t.start_day, t.end_day");
        $this->db->from("tasks t");
        if( $role_id != 1 )
        {
            $this->db->join("users u", "u.id = t.user_id");
            $this->db->where("t.user_id", $user_id);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

}