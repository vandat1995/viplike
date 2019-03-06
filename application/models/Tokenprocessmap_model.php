<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Tokenprocessmap_model extends CI_Model
{
    
    private $__table = "token_process_map";

    public function insert($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }

    public function update($id, $data)
    {
        $this->db->where("id", $id);
        return $this->db->update($this->__table, $data) ? true : false;
    }

    public function getRandByProcessId($process_id, $limit = 0)
    {
        $this->db->select("tpm.id, tpm.token_id, tpm.reaction, tpm.cmt, ts.uid vip_uid, t.token, t.cookie, t.uid, p.post_id");
        $this->db->from("token_process_map tpm");
        $this->db->join("processes p", "tpm.process_id = p.id");
        $this->db->join("tokens t", "tpm.token_id = t.id");
        $this->db->join("tasks ts", "ts.id = p.task_id");
        $this->db->where(["process_id" => $process_id, "is_runned" => 0]);
        $this->db->order_by("RAND()");
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result() : false;
    }

    public function count($user_id = false)
    {
        $this->db->select("count(*) total");
        if( $user_id )
        {
            $this->db->from("{$this->__table} tpm");
            $this->db->join("processes p", "p.id = tpm.process_id");
            $this->db->join("tasks t", "t.id = p.task_id");
            $this->db->where("t.user_id", $user_id);
            $this->db->where("tpm.is_runned", "1");
        }
        else {
            $this->db->from($this->__table);
        }
        return $this->db->get()->row()->total;
    }

}
