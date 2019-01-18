<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
	private $__table = 'users';

	public function attempt($username, $password)
	{
		$this->db->where(['username' => $username, 'password' => $password]);
		$query = $this->db->get($this->__table);
		return $query->num_rows() > 0 ? $query->row() : false;
	}

	public function ínert($data)
	{
		return $this->db->insert($this->__table, $data);
	}

	public function getBalance($username)
	{
		$this->db->select('balance');
		$this->db->where('username', $username);
		return $this->db->get($this->__table)->row()->balance;
	}

	public function getAll()
	{
		$this->db->select("u.id, u.username, u.full_name, u.active, r.name role_name, u.balance, u.created_at");
		$this->db->from("$this->__table u");
		$this->db->join("roles r", "r.id = u.role_id");
		$this->db->order_by("r.name", "asc");
		$query = $this->db->get();
		return $query->num_rows() > 0 ? $query->result() : false;
	}

	public function save($data)
	{
		$query = $this->db->insert($this->__table, $data);
		//return bool (true/false)
		return $query;
	}


}