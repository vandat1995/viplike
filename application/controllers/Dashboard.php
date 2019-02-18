<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    private $__user_id;
    public function __construct()
    {
        parent::__construct();
        $this->__user_id = $this->session->userdata("user_id");
        $this->load->model("token_model");
        $this->load->model("task_model");
        $this->load->model("taskcmt_model");
        $this->load->model("process_model");
        $this->load->model("tokenprocessmap_model");
        $this->data['page_title'] = "Dashboard";
        $this->data['sub_title'] = "";
        $this->data['total_token'] = $this->session->userdata("role_id") == 1 ? $this->token_model->count() : 10000;
        $this->data['total_vip'] = $this->session->userdata("role_id") == 1 ? $this->task_model->count() + $this->taskcmt_model->count() : $this->task_model->count($this->__user_id) + $this->taskcmt_model->count($this->__user_id);
        $this->data['total_process'] = $this->session->userdata("role_id") == 1 ? $this->process_model->count() : $this->process_model->count($this->__user_id);
        $this->data['total_like_process'] = $this->session->userdata("role_id") == 1 ? $this->tokenprocessmap_model->count() : $this->tokenprocessmap_model->count($this->__user_id);
    }

    public function index()
    {
        $this->load->template("dashboard", $this->data);
    }

    public function price()
    {
        $this->load->template("price", ["page_title" => "Price", "sub_title" => ""]);
    }

    public function test()
    {
        echo $this->process_model->count(4);
    }
}
