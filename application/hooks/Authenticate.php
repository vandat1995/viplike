<?php

class Authenticate
{
	protected $CI;
	private $current_controller;
	private $exclude_controller = ['Authentication'];

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->current_controller = $this->CI->router->class;
		
	}

	public function checkUserLogin()
	{
		if( $this->CI->input->is_cli_request() )
		{
			return true;
		}
		
		if( !$this->CI->session->userdata('is_logged_in') && !in_array($this->current_controller, $this->exclude_controller) )
		{
			$c_user = get_cookie('username');
			$c_pass = get_cookie('token');
			if( !empty($c_user) && !empty($c_pass) )
			{
				$this->CI->load->model('User_model');
				$attempt = $this->CI->User_model->attempt($c_user, $c_pass);
				if( $attempt )
				{
					$userdata = [
						'user_id' 		=> $attempt->id,
						'username' 		=> $attempt->username,
						'full_name' 	=> $attempt->full_name,
						'avatar' 		=> $attempt->avatar,
						'balance' 		=> number_format($attempt->balance),
						'role_id'		=> $attempt->role_id,
						'is_logged_in' 	=> true
					];
					$this->CI->session->set_userdata($userdata);
				}
				else
					redirect('login');
			}
			else
				redirect('login');
		}
	}
}