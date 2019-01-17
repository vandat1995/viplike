<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("token_model");
        $this->load->model("task_model");
        $this->load->model("taskprocess_model");
        $this->data['page_title'] = "Dashboard";
        $this->data['sub_title'] = "";
        $this->data['total_token'] = $this->token_model->count();
        $this->data['total_vip'] = $this->task_model->count();
        $this->data['total_process'] = $this->taskprocess_model->count();
    }

    public function index()
    {
        $this->load->template("dashboard", $this->data);
    }
}
