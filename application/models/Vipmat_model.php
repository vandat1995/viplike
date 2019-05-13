<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class VipMat_model extends CI_Model
{
    private $__table = 'vipmat';
    private $__apiUser = "taideptraiok3";
    private $__apiPassword = "25f9e794323b453885f5181f1b624d0b";
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library("request");
        
    }
    
    public function insert($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }
    
    public function delete($id)
    {
        $this->db->where("id", $id);
        return $this->db->delete($this->__table) ? true : false;
    }
    
    public function count($user_id = false)
    {
        $this->db->select("count(*) total");
        if( $user_id )
        {
            $this->db->where("user_id", $user_id);
        }
        return $this->db->get($this->__table)->row()->total;
    }
    
    public function getById($id)
    {
        $this->db->where("id", $id);
        $query = $this->db->get($this->__table);
        return $query->num_rows() ? $query->row() : false;
    }
    
    public function addTask($uid, $quantity, $time)
    {
        
    }
    
    public function createTransaction($limit_time, $data, $new_balance)
    {
        $this->db->trans_begin();
        $this->db->insert($this->__table, $data);
        $this->db->update("users", ["balance" => $new_balance], ["id" => $this->session->userdata('user_id')]);
        
        $api = "http://viplive.site/api/api.php?user={$this->__apiUser}&key={$this->__apiPassword}&act=add.vip.livestream&fbid={$data['uid']}&name={$data['uid']}&limit_time={$limit_time}&package={$data['quantity']}&public_group=1&id_public_group=1|2|3&power_viewers=0";
        $res = json_decode($this->request->get($api), true);
        if (isset($res["error"])) 
        {
            if ($res["error"] == false) 
            {
                // update session balance 
                $this->session->set_userdata(["balance" => number_format($new_balance)]);
                $this->db->trans_commit();
                return true;
            }
            
        }
        $this->db->trans_rollback();
        return false;
        
    }
    
    public function deleteTransaction($task_id, $new_balance, $user_id, $uid) 
    {
        if ($this->__isOwner($task_id, $user_id))
        {
            $this->db->trans_begin();
            $this->db->delete($this->__table, ["id" => $task_id]);
            $this->db->update("users", ["balance" => $new_balance], ["id" => $user_id]);
            
            $api = "http://viplive.site/api/api.php?user={$this->__apiUser}&key={$this->__apiPassword}&act=delete.vip.livestream&fbid={$uid}";
            $res = json_decode($this->request->get($api), true);
            if (isset($res["error"])) 
            {
                if ($res["error"] == false) 
                {
                    // update session balance 
                    $this->session->set_userdata(["balance" => number_format($new_balance)]);
                    $this->db->trans_commit();
                    return true;
                }
            }
            $this->db->trans_rollback();
        }
        return false;
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
        $this->db->select("t.id, t.uid, t.quantity, t.start_day, t.end_day, (end_day - NOW()) expired");
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