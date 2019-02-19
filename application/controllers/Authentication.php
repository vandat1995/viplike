<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author Nguyễn Văn Dạt
 * @link https://www.facebook.com/datvannguyen.net
 */
class Authentication extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
	}

	public function index()
	{
		$this->Login();
	}

	public function Login()
	{
		if( $this->session->userdata('is_logged_in') )
			redirect("dashboard");
		$this->load->view('login');
	}

	public function Logout()
	{
		$this->session->sess_destroy();
		delete_cookie('username');
		delete_cookie('token');
		redirect('login');
	}

	public function validate()
	{
		if( $this->session->userdata('is_logged_in') )
			redirect("dashboard");
		$this->form_validation->set_rules('username', 'Username', 'required|max_length[50]');
		$this->form_validation->set_rules('password', 'Password', 'required|max_length[50]');
		if( $this->form_validation->run() != false )
		{
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$remember = empty($this->input->post('remember')) ? 1 : 0;
			$attempt = $this->user_model->attempt($username, md5($password));
			if( $attempt )
			{
				if( $attempt->active == 1 )
				{
					$userdata = [
						'user_id'			=> $attempt->id,
						'username' 			=> $attempt->username,
						'full_name' 		=> $attempt->full_name,
						'avatar' 			=> $attempt->avatar,
						'balance' 			=> number_format($attempt->balance),
						'role_id'			=> $attempt->role_id,
						'is_logged_in' 		=> true
					];
					if( $remember == 1 )
					{
						$this->session->set_userdata($userdata);
						// Remember login 1 month
						set_cookie('username', $attempt->username, 3600*24*30);
						set_cookie('token', $attempt->password, 3600*24*30);
					}
					else
					{
						$this->session->set_userdata($userdata);
					}
					echo json_encode(['error' => 0, 'message' => 'Loggin success.', 'url' => 'dashboard']);
				}
				else
				{
					echo json_encode(['error' => ['message' => 'User was blocked', 'code' => 0], 'message' => '']);
				}
			}
			else
			{
				echo json_encode(['error' => ['message' => 'Invalid username or password', 'code' => 0], 'message' => '']);
			}
		}
		else
		{
			echo json_encode(['error' => ['message' => validation_errors(), 'code' => 0], 'message' => '']);
		}
	}

	public function insertUser()
	{
		if( $this->input->is_cli_request() ) {
			$data = ['username' => 'admin2', 'password' => md5('admin'), 'full_name' => 'Dat Nguyen', 'role_id' => 1];
			$result = $this->user_model->save($data);
			var_dump($result);
		}
		else {
			echo "Địt mẹ mày tính làm gì vậy thằng nhóc..";
		}
	}
}