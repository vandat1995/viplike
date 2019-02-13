<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Taskcmt_model extends CI_Model
{
	private $__table = 'tasks_cmt';

    public function insert($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }

    public function delete($id)
    {
        $this->db->where("id", $id);
        return $this->db->delete($this->__table) ? true : false;
    }

    public function count()
    {
        $this->db->select("count(*) total");
        return $this->db->get($this->__table)->row()->total;
    }

    public function getById($id)
    {
        $this->db->where("id", $id);
        $query = $this->db->get($this->__table);
        return $query->num_rows() ? $query->row() : false;
    }

    public function createTransaction($data_task, $data_user, $user_id)
    {
        $this->db->trans_start();
        $this->db->insert($this->__table, $data_task);
        $this->db->update("users", $data_user, "id = {$user_id}");
        $this->db->trans_complete();
        return $this->db->trans_status() === FALSE ? false : true;
    }

    public function deleteTransaction($task_id, $data_user, $user_id)
    {
        if( $this->__isOwner($task_id, $user_id) || $this->session->userdata("role_id") == 1 )
        {
            $this->db->trans_start();
            $this->db->delete($this->__table, ["id" => $task_id]);
            $prs = $this->__getProcessIdByTaskId($task_id);
            if( $prs )
            {
                $this->db->delete("processes", ["task_id" => $task_id]);
                foreach( $prs as $pr )
                {
                    $this->db->delete("token_process_map", ["process_id" => $pr->id]);
                }
            }
            $this->db->update("users", $data_user, "id = {$user_id}");
            $this->db->trans_complete();
            return $this->db->trans_status() === FALSE ? false : true;
        }
        return false;
    }

    private function __getProcessIdByTaskId($task_id)
    {
        $this->db->select("id");
        $this->db->from("processes");
        $this->db->where("task_id", $task_id);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result() : false;
    }

    public function getActiveTasks()
    {
        $this->db->select("id, uid, quantity, quantity_per_cron, msg_cmt");
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

    public function getListUidByUser($user_id, $role_id)
    {
        $this->db->select("t.id, t.uid, t.quantity, t.quantity_per_cron, t.start_day, t.end_day");
        $this->db->from("{$this->__table} t");
        if( $role_id != 1 )
        {
            $this->db->join("users u", "u.id = t.user_id");
            $this->db->where("t.user_id", $user_id);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    private function __isOwner($task_id, $user_id)
    {
        $this->db->from("$this->__table t");
        $this->db->join("users u", "u.id = t.user_id");
        $this->db->where(["t.id" => $task_id, "t.user_id" => $user_id]);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? true : false;
    }
    

    

}