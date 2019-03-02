<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "Đăng kí";
        $this->data['sub_title'] = "";
        $this->load->model("user_model");
        if( $this->session->userdata('is_logged_in') )
			redirect("dashboard");
    }

    public function index()
    {
        $this->load->view("register", $this->data);
    }

    public function newUser()
    {
        $this->form_validation->set_rules("username", "Username", "required|max_length[50]|is_unique[users.username]", ["is_unique" => "Username đã tồn tại"]);
        $this->form_validation->set_rules("password", "Password", "required|max_length[50]|min_length[6]");
        $this->form_validation->set_rules("re_password", "Password Confirmation", "required|max_length[50]|min_length[6]|matches[password]");
        $this->form_validation->set_rules("fullname", "Full name", "required|max_length[50]");
        $this->form_validation->set_rules("captcha", "Captcha", "required");
        if ($this->form_validation->run() === FALSE)
        {
            echo json_encode(["error" => ["message" => validation_errors()]]);
            return;
        }
        $captcha = $this->input->post("captcha");
        $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LctA5UUAAAAABSOhWY_3tC4dnOPuJHEi3cHokgH&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
        if ($response["success"] != true)
        {
            echo json_encode(["error" => ["message" => "Địt mẹ mày spam cái lồn."]]);
            return;
        }

        $data = [
            "username" => xss_clean($this->input->post("username")),
            "password" => md5($this->input->post("password")),
            "full_name" => xss_clean($this->input->post("fullname")),
            "role_id" => 3,
            "balance" => 0,
        ];
        echo $this->user_model->insert($data) ? json_encode(["error" => 0, "message" => "Đăng ký thành công"]) : json_encode(["error" => ["message" => "Đăng ký thất bại vui lòng thử lại."]]);

    }

    

}
