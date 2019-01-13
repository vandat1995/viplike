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
        $quantity_per_cron = !empty($this->input->post("quantity_per_cron")) ? (int)$this->input->post("quantity_per_cron") : false;
        if(!$uid || !$quantity || !$time || !$quantity_per_cron)
        {
            echo json_encode(["error" => ["message" => "Invalid data", "code" => 0], "message" => ""]);
            return;
        }
        if($time < 1 || $quantity < 1 || $quantity_per_cron > $quantity || $quantity_per_cron < 1)
        {
            echo json_encode(["error" => ["message" => "Invalid time, quantity", "code" => 0], "message" => ""]);
            return;
        }
        if($this->task_model->checkExistUID($uid))
        {
            echo json_encode(["error" => ["message" => "UID was Exist", "code" => 0], "message" => ""]);
            return;
        }

        $data = [
            "uid" => $uid,
            "quantity_like" => $quantity,
            "quantity_per_cron" => $quantity_per_cron,
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

    public function listTask()
    {
        $list_task = $this->task_model->getListUidByUser($this->session->userdata('user_id'), $this->session->userdata('role_id'));
        if(!$list_task)
        {
            echo json_encode(["error" => 0, "data" => [], "message" => ""]);
            return;
        }
        echo json_encode(["error" => 0, "data" => $list_task, "message" => ""]);
    }

    public function deleteTask()
    {
        $task_id = $this->input->post("task_id");
        if($this->session->userdata("role_id") != 1)
        {
            echo json_encode(["error" => ["message" => "You don't have permission.", "code" => 0], "message" => ""]);
            return;
        }

        if($this->task_model->delete($task_id))
        {
            echo json_encode(["error" => 0, "message" => "Delete task success"]);
            return;
        }
        else
        {
            echo json_encode(["error" => ["message" => "Delete task fail.", "code" => 0], "message" => ""]);
            return;
        }
    }

}
