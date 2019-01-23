<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("token_model");
        $this->load->model("task_model");
        $this->load->model("taskcmt_model");
        $this->load->model("process_model");
        $this->data['page_title'] = "Dashboard";
        $this->data['sub_title'] = "";
        $this->data['total_token'] = $this->token_model->count();
        $this->data['total_vip'] = $this->task_model->count() + $this->taskcmt_model->count();
        $this->data['total_process'] = $this->process_model->count();
    }

    public function index()
    {
        $this->load->template("dashboard", $this->data);
    }

    public function price()
    {
        $this->load->template("price", ["page_title" => "Price", "sub_title" => ""]);
    }
}
