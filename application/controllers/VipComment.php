<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class VipComment extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "Setup vip comment";
        $this->data['sub_title'] = "";
        $this->load->model("task_model");
        $this->load->model("token_model");
        $this->load->model("user_model");
    }

    public function index()
    {
        $this->load->template("vipcomment", $this->data);
    }

    public function addTask()
    {
        
    }



}
