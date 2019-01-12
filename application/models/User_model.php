<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
	private $__table = 'users';

	public function attempt($username, $password)
	{
		$this->db->where(['username' => $username, 'password' => $password]);
		$query = $this->db->get($this->__table);
		if($query->num_rows() > 0)
			return $query->row();
		else
			return false;
	}

	public function getBalance($username)
	{
		$this->db->select('balance');
		$this->db->where('username', $username);
		return $this->db->get($this->__table)->row()->balance;
	}

	public function save($data)
	{
		$query = $this->db->insert($this->__table, $data);
		//return bool (true/false)
		return $query;
	}


}