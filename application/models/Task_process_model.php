<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_process_model extends CI_Model
{
	private $__table = "task_process";

    public function checkExistId($id_liked)
    {
        $this->db->where("id_liked", $id_liked);
        $query = $this->db->get($this->__table);
        return ($query->num_rows() > 0) ? true : false;
    }

    public function saveProcessDone($data)
    {
        return $this->db->insert($this->__table, $data) ? true : false;
    }

}