<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class VipMat extends CI_Controller
{

    private $prices;
    public function __construct()
    {
        parent::__construct();
        $this->load->model("vipmat_model");
        $this->load->model("user_model");
        $this->load->model("setting_model");
        $this->load->library("collections");
        $this->prices = $this->setting_model->getAll("vipmat");
    }
    public function index()
    {
        $this->load->template("vipmat", ["page_title" => "Cai dat vip mat", "sub_title" => "", "prices" => $this->prices]);
    }

    public function listTask() 
    {
        $list_task = $this->vipmat_model->getListUidByUser($this->session->userdata('user_id'), $this->session->userdata('role_id'));
        if( !$list_task )
        {
            echo json_encode(["error" => 0, "data" => [], "message" => ""]);
            return;
        }
        echo json_encode(["error" => 0, "data" => $list_task, "message" => ""]);
    }
    
    public function addTask()
    {
        $this->form_validation->set_rules("uid", "UID", "required|max_length[20]");
        $this->form_validation->set_rules("quantity", "Số lượng", "required|max_length[10]|numeric|greater_than[0]");
        $this->form_validation->set_rules("time", "Time", "required|max_length[10]|numeric|greater_than[0]");
        if($this->form_validation->run() == false)
        {
            echo json_encode(["error" => ["message" => validation_errors(), "code" => 0]]);
            return;
        }

        $uid = xss_clean($this->input->post("uid"));
        $quantity = $this->input->post("quantity");
        $time = $this->input->post("time");

        if( $this->vipmat_model->checkExistUID($uid) )
        {
            echo json_encode(["error" => ["message" => "UID này đã tồn tại trong hệ thống", "code" => 0], "message" => ""]);
            return;
        }

        $price_month = 0;
        foreach($this->prices as $val)
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
        $limit_time = (int)$time;
        $time = $time * 30;
        $data = [
            "uid"               => $uid,
            "quantity"          => $quantity,
            "start_day"         => date("Y-m-d H:i:s"),
            "end_day"           => date("Y-m-d H:i:s", strtotime("+". $time ." days")),
            "user_id"           => $this->session->userdata('user_id')
        ];

        if ($this->vipmat_model->createTransaction($limit_time, $data, $new_balance))
        {
            $this->history_model->log("-", $price, "vipmat", "Create Vip {$uid} - {$quantity} mat, {$time} days.", $this->session->userdata("user_id"));
            echo json_encode(["error" => 0, "message" => "Thành công"]);
        }
        else
        {
            echo json_encode(["error" => ["message" => "Thất bại vui lòng thử lại sau."]]);
        }
    }

    public function deleteTask() 
    {
        $task_id = !empty($this->input->post("task_id")) ? $this->input->post("task_id") : false;
        if( !$task_id )
        {
            echo json_encode(["error" => ["message" => "Địt mẹ mày tính làm gì đây con chó", "code" => 0], "message" => ""]);
            return;
        }
        $task = $this->vipmat_model->getById($task_id);
        if( !$task )
        {
            echo json_encode(["error" => ["message" => "UID không tồn tại.", "code" => 0], "message" => ""]);
            return;
        }
        $price_month = 0;
        foreach($this->prices as $val)
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

        if( $this->vipmat_model->deleteTransaction($task_id, $new_balance, $this->session->userdata("user_id"), $task->uid) )
        {
            //$this->session->set_userdata(["balance" => number_format($new_balance)]);
            $this->history_model->log("+", $refund, "vipmat", "Refund vip mắt {$task->uid} - {$task->quantity} like, {$days_left} days left.", $this->session->userdata("user_id"));
            echo json_encode(["error" => 0, "message" => "Delete task success."]);
            return;
        }
        else
        {
            echo json_encode(["error" => ["message" => "Đã có lỗi xảy ra vui lòng thử lại sau", "code" => 0], "message" => ""]);
            return;
        }

    }


}
