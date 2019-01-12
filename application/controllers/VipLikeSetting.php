<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class VipLikeSetting extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "Setup vip like";
        $this->data['sub_title'] = "";
        $this->load->model("task_model");
    }

    public function index()
    {
        $this->load->template("viplikesetting", $this->data);
    }

    public function addTask()
    {
        $uid = !empty($this->input->post("uid")) ? $this->input->post("uid") : false;
        $quantity = !empty($this->input->post("quantity")) ? (int)$this->input->post("quantity") : false;
        $time = !empty($this->input->post("time")) ? (int)$this->input->post("time") : false;
        if(!$uid || !$quantity || !$time)
        {
            echo json_encode(["error" => ["message" => "Invalid data", "code" => 0], "message" => ""]);
            return;
        }
        if($time < 1 || $quantity < 1)
        {
            echo json_encode(["error" => ["message" => "Invalid time, quantity", "code" => 0], "message" => ""]);
            return;
        }

        $data = [
            "uid" => $uid,
            "quantity_like" => $quantity,
            "start_day" => date("Y-m-d H:i:s"),
            "end_day" => date("Y-m-d H:i:s", strtotime("+". $time ." days")),
            "user_id" => $this->session->userdata('user_id')
        ];

        if($this->task_model->insert($data))
        {
            echo json_encode(["error" => 0, "message" => "Create Vip like Task Success"]);
            return;
        }
        else
        {
            echo json_encode(["error" => ["message" => "Create Vip like Task Fail", "code" => 0], "message" => ""]);
            return;
        }
    }

}
