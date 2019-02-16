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
        $this->load->model("token_model");
        $this->load->model("user_model");
    }

    public function index()
    {
        $this->load->template("viplikesetting", $this->data);
    }

    public function addTask()
    {
        $uid = !empty($this->input->post("uid")) ? xss_clean($this->input->post("uid")) : false;
        $quantity = !empty($this->input->post("quantity")) ? xss_clean((int)$this->input->post("quantity")) : false;
        $time = !empty($this->input->post("time")) ? xss_clean((int)$this->input->post("time")) : false;
        $quantity_per_cron = !empty($this->input->post("quantity_per_cron")) ? xss_clean((int)$this->input->post("quantity_per_cron")) : false;
        $reactions = !empty($this->input->post("reactions")) ? xss_clean($this->input->post("reactions")) : false;
        if( !$uid || !$quantity || !$time || !$quantity_per_cron || !$reactions )
        {
            echo json_encode(["error" => ["message" => "Invalid data", "code" => 0], "message" => ""]);
            return;
        }
        if( $time < 1 || $quantity < 1 || $quantity_per_cron > $quantity || $quantity_per_cron < 1 )
        {
            echo json_encode(["error" => ["message" => "Invalid time, quantity", "code" => 0], "message" => ""]);
            return;
        }
        if( $this->task_model->checkExistUID($uid) )
        {
            echo json_encode(["error" => ["message" => "UID was Exist", "code" => 0], "message" => ""]);
            return;
        }

        $reactions = $this->__parseReactions($reactions, $quantity);
        if( $reactions === false )
        {
            echo json_encode(["error" => ["message" => "Địt mẹ mày tính làm gì đây nhóc", "code" => 0], "message" => ""]);
            return;
        }

        $total_token = $this->token_model->count();
        if( ($quantity + 6) > $total_token )
        {
            echo json_encode(["error" => ["message" => "Quantity cann't more than current total token", "code" => 0], "message" => ""]);
            return;
        }        

        $price = $quantity * $time * PRICE_PER_LIKE;
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
            "reactions"         => $reactions,
            "start_day"         => date("Y-m-d H:i:s"),
            "end_day"           => date("Y-m-d H:i:s", strtotime("+". $time ." days")),
            "user_id"           => $this->session->userdata('user_id')
        ];

        if( $this->task_model->createTransaction($data, ["balance" => $new_balance], $this->session->userdata("user_id")) )
        {
            $this->session->set_userdata(["balance" => number_format($new_balance)]);
            $this->history_model->log("-", $price, "viplike", "Create Vip {$uid} - {$quantity} like, {$time} days.", $this->session->userdata("user_id"));
            echo json_encode(["error" => 0, "message" => "Create Vip like Task Success"]);
            return;
        }
        else
        {
            echo json_encode(["error" => ["message" => "Create Vip like Task Fail", "code" => 0], "message" => ""]);
            return;
        }
    }

    private function __parseReactions($reactions, $quantity) 
    {
        $result = [];
        $count = count($reactions);
        foreach( $reactions as $r )
        {
            if( $r != "LIKE" && $r != "LOVE" && $r != "WOW" && $r != "HAHA" && $r != "SAD" && $r != "ANGRY" )
            {
                return false;
            } 
            else 
            {
                $result[$r] = $r == "LIKE" ?  ceil(0.8 * $quantity) : ceil((0.2 / ($count - 1 )) * $quantity);
            }
        }
        return json_encode($result);
    }

    public function listTask()
    {
        $list_task = $this->task_model->getListUidByUser($this->session->userdata('user_id'), $this->session->userdata('role_id'));
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

        $task = $this->task_model->getById($task_id);
        if( !$task )
        {
            echo json_encode(["error" => ["message" => "Task does not exist.", "code" => 0], "message" => ""]);
            return;
        }

        $days_left = strtotime($task->end_day) - strtotime(date("Y-m-d H:i:s"));
        $days_left = (int)($days_left / 60 / 60 / 24);
        $refund = $days_left * (int)$task->quantity * PRICE_PER_LIKE;
        $current_balance = $this->user_model->getBalance($this->session->userdata("user_id"));
        $new_balance = $current_balance + $refund;

        if( $this->task_model->deleteTransaction($task_id, ["balance" => $new_balance], $this->session->userdata("user_id")) )
        {
            $this->session->set_userdata(["balance" => number_format($new_balance)]);
            $this->history_model->log("+", $refund, "viplike", "Refund vip {$task->uid} - {$task->quantity} like, {$days_left} days left.", $this->session->userdata("user_id"));
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
