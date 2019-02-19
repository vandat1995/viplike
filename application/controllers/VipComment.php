<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class VipComment extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "Cài đặt Vip comment";
        $this->data['sub_title'] = "";
        $this->load->model("taskcmt_model");
        $this->load->model("token_model");
        $this->load->model("user_model");
    }

    public function index()
    {
        $this->load->template("vipcomment", $this->data);
    }

    public function addTask()
    {
        if( $this->config_model->countCurrentVip() >= MAX_UID_VIP )
        {
            echo json_encode(["error" => ["message" => "Has reached the system threshold", "code" => 0], "message" => ""]);
            return;
        }
        $this->form_validation->set_rules("uid", "UID", "required|max_length[20]");
        $this->form_validation->set_rules("quantity", "Quantity", "required|max_length[10]|numeric|greater_than[0]");
        $this->form_validation->set_rules("time", "Time", "required|max_length[10]|numeric|greater_than[0]");
        $this->form_validation->set_rules("quantity_per_cron", "Quantity comment per crontab", "required|max_length[10]|numeric|greater_than[0]");
        $this->form_validation->set_rules("msg_cmt", "Message comment", "required|max_length[4000]");
        if($this->form_validation->run() == false)
        {
            echo json_encode(["error" => ["message" => validation_errors(), "code" => 0], "message" => ""]);
            return;
        }

        $uid                = xss_clean($this->input->post("uid"));
        $quantity           = xss_clean((int)$this->input->post("quantity"));
        $time               = xss_clean((int)$this->input->post("time"));
        $quantity_per_cron  = xss_clean((int)$this->input->post("quantity_per_cron"));
        $msg_cmt            = xss_clean($this->input->post("msg_cmt"));
        
        if( $quantity_per_cron > $quantity )
        {
            echo json_encode(["error" => ["message" => "Invalid quantity per cron", "code" => 0], "message" => ""]);
            return;
        }
        
        if( $this->taskcmt_model->checkExistUID($uid) )
        {
            echo json_encode(["error" => ["message" => "UID was Exist", "code" => 0], "message" => ""]);
            return;
        }

        $total_token = $this->token_model->count();
        if( $quantity > $total_token )
        {
            echo json_encode(["error" => ["message" => "Quantity cann't more than current total token", "code" => 0], "message" => ""]);
            return;
        }

        $price = $quantity * $time * PRICE_PER_CMT;
        $current_balance = $this->user_model->getBalance($this->session->userdata("user_id"));
        $new_balance = $current_balance - $price;
        if( $new_balance < 0 ) 
        {
            echo json_encode(["error" => ["message" => "Your money is not enough", "code" => 0], "message" => ""]);
            return;
        }

        $data = [
            "uid"               => $uid,
            "quantity"          => $quantity,
            "quantity_per_cron" => $quantity_per_cron,
            "msg_cmt"           => json_encode(explode("\n", $msg_cmt)),
            "start_day"         => date("Y-m-d H:i:s"),
            "end_day"           => date("Y-m-d H:i:s", strtotime("+". $time ." days")),
            "user_id"           => $this->session->userdata('user_id')
        ];

        if( $this->taskcmt_model->createTransaction($data, ["balance" => $new_balance], $this->session->userdata("user_id")) )
        {
            $this->session->set_userdata(["balance" => number_format($new_balance)]);
            $this->history_model->log("-", $price, "vipcmt", "Create Vip {$uid} - {$quantity} comment, {$time} days.", $this->session->userdata("user_id"));
            echo json_encode(["error" => 0, "message" => "Create Vip Cmt Task Success"]);
            return;
        }
        else
        {
            echo json_encode(["error" => ["message" => "Create Vip Cmt Task Fail", "code" => 0], "message" => ""]);
            return;
        }

    }

    public function listTask()
    {
        $list_task = $this->taskcmt_model->getListUidByUser($this->session->userdata('user_id'), $this->session->userdata('role_id'));
        if( !$list_task )
        {
            echo json_encode(["error" => 0, "data" => [], "message" => ""]);
            return;
        }
        echo json_encode(["error" => 0, "data" => $list_task, "message" => ""]);
    }

    public function deleteTask()
    {
        $task_id = !empty($this->input->post("task_id")) ? $this->input->post("task_id") : false;
        if( !$task_id )
        {
            echo json_encode(["error" => ["message" => "Địt mẹ mày tính làm gì đây con chó", "code" => 0], "message" => ""]);
            return;
        }

        $task = $this->taskcmt_model->getById($task_id);
        if( !$task )
        {
            echo json_encode(["error" => ["message" => "Task does not exist.", "code" => 0], "message" => ""]);
            return;
        }

        $days_left = strtotime($task->end_day) - strtotime(date("Y-m-d H:i:s"));
        $days_left = (int)($days_left / 60 / 60 / 24);
        $refund = $days_left * (int)$task->quantity * PRICE_PER_CMT;
        $current_balance = $this->user_model->getBalance($this->session->userdata("user_id"));
        $new_balance = $current_balance + $refund;

        if( $this->taskcmt_model->deleteTransaction($task_id, ["balance" => $new_balance], $this->session->userdata("user_id")) )
        {
            $this->session->set_userdata(["balance" => number_format($new_balance)]);
            $this->history_model->log("+", $refund, "vipcmt", "Refund vip {$task->uid} - {$task->quantity} comment, {$days_left} days left.", $this->session->userdata("user_id"));
            echo json_encode(["error" => 0, "message" => "Delete task success."]);
            return;
        }
        else
        {
            echo json_encode(["error" => ["message" => "Delete task fail.", "code" => 0], "message" => ""]);
            return;
        }
    }



}
