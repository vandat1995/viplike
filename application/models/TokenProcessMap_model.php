<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class TokenProcessMap_model extends CI_Model
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
        $this->db->select("tpm.id, tpm.reaction, t.token, tp.post_id");
        $this->db->from("token_process_map tpm");
        $this->db->join("task_process tp", "tpm.process_id = tp.id");
        $this->db->join("tokens t", "tpm.token_id = t.id");
        $this->db->where(["process_id" => $process_id, "is_runned" => 0]);
        $this->db->order_by("RAND()");
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result() : false;
    }



}