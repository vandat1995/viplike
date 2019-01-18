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
        {
            redirect("dashboard");
        }
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
        
    }

}
