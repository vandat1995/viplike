<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class VipLikeSetting extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "Cài đặt Vip Like";
        $this->data['sub_title'] = "";
        $this->load->model("task_model");
        $this->load->model("token_model");
        $this->load->model("user_model");
        $this->load->model("setting_model");
        $this->data["prices"] = $this->setting_model->getAll();
    }

    public function index()
    {
        $this->load->template("viplikesetting", $this->data);
    }

    public function addTask()
    {
        if( $this->config_model->countCurrentVip() >= MAX_UID_VIP )
        {
            echo json_encode(["error" => ["message" => "Has reached the system threshold", "code" => 0], "message" => ""]);
            return;
        }
        $uid = !empty($this->input->post("uid")) ? xss_clean($this->input->post("uid")) : false;
        $quantity = !empty($this->input->post("quantity")) ? xss_clean((int)$this->input->post("quantity")) : false;
        $time = !empty($this->input->post("time")) ? xss_clean((int)$this->input->post("time")) : false;
        $quantity_per_cron = !empty($this->input->post("quantity_per_cron")) ? xss_clean((int)$this->input->post("quantity_per_cron")) : false;
        $reactions = !empty($this->input->post("reactions")) ? xss_clean($this->input->post("reactions")) : false;
        if( !$uid || !$quantity || !$time || !$quantity_per_cron || !$reactions )
        {
            echo json_encode(["error" => ["message" => "Dữ liệu nhập vào không hợp lệ.", "code" => 0], "message" => ""]);
            return;
        }
        if( $time < 1 || $quantity < 1 || $quantity_per_cron > $quantity || $quantity_per_cron < 1 )
        {
            echo json_encode(["error" => ["message" => "Dữ liệu nhập vào không hợp lệ.", "code" => 0], "message" => ""]);
            return;
        }
        if( $this->task_model->checkExistUID($uid) )
        {
            echo json_encode(["error" => ["message" => "UID này đã tồn tại trong hệ thống", "code" => 0], "message" => ""]);
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
            echo json_encode(["error" => ["message" => "Số lượng token trong kho không đủ", "code" => 0], "message" => ""]);
            return;
        }        
        $price_month = 0;
        foreach($this->data["prices"] as $val)
        {
            if($val->quantity == $quantity)
            {
                $price_month = $val->price_per_month;
                break;
            }     
        }
        if($price_month == 0) 
        {
            echo json_encode(["error" => ["message" => "Địt mẹ mày bug clg đây hả con chó.", "code" => 0], "message" => ""]);
            return;
        }
        $price = $time * $price_month;
        $current_balance = $this->user_model->getBalance($this->session->userdata("user_id"));
        $new_balance = $current_balance - $price;
        if( $new_balance < 0 ) 
        {
            echo json_encode(["error" => ["message" => "Tài khoản của bạn không đủ tiền.", "code" => 0], "message" => ""]);
            return;
        }
        $time = $time * 30;
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
        if(count($result) == 1)
        {
            foreach($result as $key => $val)
            {
                $result[$key] = $quantity;
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
            echo json_encode(["error" => ["message" => "UID không tồn tại.", "code" => 0], "message" => ""]);
            return;
        }

        $price_month = 0;
        foreach($this->data["prices"] as $val)
        {
            if($val->quantity == $task->quantity)
            {
                $price_month = $val->price_per_month;
                break;
            }     
        }
        $days_left = strtotime($task->end_day) - strtotime(date("Y-m-d H:i:s"));
        $days_left = (int)($days_left / 60 / 60 / 24);
        $refund = (int)(($days_left / 30) * $price_month);
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
