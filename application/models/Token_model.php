<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Token_model extends CI_Model
{
	private $__table = 'tokens';

    public function insert($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }

    public function update($id, $data)
    {
        $this->db->where("id", $id);
        return $this->db->update($this->__table, $data);
    }

    public function delete($id)
    {
        $this->db->where("id", $id);
        return $this->db->delete($this->__table) ? true : false;
    }

    public function deleteTokenDie()
    {
        $this->db->where("status", 0);
        return $this->db->delete($this->__table);
    }

    public function count()
    {
        $this->db->select("count(*) total");
        return $this->db->get($this->__table)->row()->total;
    }

    public function getRandOneToken()
    {
        $this->db->select("token");
        $this->db->where("status", 1);
        $this->db->order_by("RAND()");
        $this->db->limit(1);
        $query = $this->db->get($this->__table);
        return ($query->num_rows() > 0) ? $query->row()->token : false;
    }

    public function getRandOneCookie()
    {
        $this->db->select("cookie");
        $this->db->where("status", 1);
        $this->db->order_by("RAND()");
        $this->db->limit(1);
        $query = $this->db->get($this->__table);
        return ($query->num_rows() > 0) ? $query->row()->cookie : false;
    }

    public function getTokens($quantity = false)
    {
        $this->db->select("id, token, cookie, uid");
        $this->db->where("status", 1);
        $this->db->order_by("RAND()");
        if( $quantity !== false )
        {
            $this->db->limit($quantity);
        }
        $query = $this->db->get($this->__table);
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function getCookies($quantity = 0, $process_id) {
        $sql = "select id, token, cookie, uid from tokens where status = 1 and id not in (select token_id from token_process_map where process_id = {$process_id}) order by RAND() limit {$quantity}";
        $query = $this->db->query($sql);
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function getAll()
    {
        $this->db->select("id, fullname, token, cookie, status");
        $query = $this->db->get($this->__table);
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    

}