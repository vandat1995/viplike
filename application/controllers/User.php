<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "User Management";
        $this->data['sub_title'] = "";
        if($this->session->userdata("role_id") != 1)
        {
            redirect("dashboard");
        }
    }

    public function index()
    {
        $this->load->template("user", $this->data);
    }
}
