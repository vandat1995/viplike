<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "User Management";
        $this->data['sub_title'] = "";
        if( $this->session->userdata("role_id") != 1 )
            redirect("dashboard");

        $this->load->model("user_model");
    }

    public function index()
    {
        $this->load->template("user", $this->data);
    }

    public function listUser()
    {
        $list_user = $this->user_model->getAll();
        echo !$list_user ? json_encode(["error" => 0, "data" => []]) : json_encode(["error" => 0, "data" => $list_user]);
    }

    public function create()
    {
        $this->form_validation->set_rules("username", "Username", "required|max_length[50]|is_unique[users.username]");
        $this->form_validation->set_rules("password", "Password", "required|max_length[50]|min_length[6]");
        $this->form_validation->set_rules("fullname", "Full name", "required|max_length[50]");
        $this->form_validation->set_rules("avatar", "Avatar", "max_length[255]");
        $this->form_validation->set_rules("permissions", "Permissions", "required|greater_than[0]|less_than[4]");
        $this->form_validation->set_rules("balance", "Balance", "required|greater_than_equal_to[0]|max_length[15]");
        
        if( $this->form_validation->run() == false )
        {
            echo json_encode(["error" => ["message" => validation_errors(), "code" => 0], "message" => ""]);
            return;
        }

        $userdata = [
            "username"  => $this->input->post("username"),
            "password"  => md5($this->input->post("password")),
            "full_name" => $this->input->post("fullname"),
            "avatar"    => $this->input->post("avatar"),
            "role_id"   => $this->input->post("permissions"),
            "balance"   => $this->input->post("balance")
        ];

        echo $this->user_model->insert($userdata) ? json_encode(["error" => 0, "message" => "Create user success"]) : json_encode(["error" => ["message" => "Create user fail", "code" => 0], "message" => ""]);
            
    }

}
