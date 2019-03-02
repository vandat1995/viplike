<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class BotReactions extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "Bot Cảm Xúc";
        $this->data['sub_title'] = "";
        $this->load->model("botreactions_model");
        $this->load->model("user_model");
    }

    public function index() 
    {
        $this->load->template("botreactions", $this->data);
    }

    public function import()
    {
        if( $this->config_model->countCurrentBot() >= MAX_UID_BOT )
        {
            echo json_encode(["error" => ["message" => "Has reached the system threshold", "code" => 0], "message" => ""]);
            return;
        }
        $data_token = !empty($this->input->post("data")) ? $this->input->post("data") : false;
        $token = !empty($this->input->post("token")) ? $this->input->post("token") : false;
        $reactions = !empty($this->input->post("reactions")) ? $this->input->post("reactions") : false;
        $duration = !empty($this->input->post("duration")) ? $this->input->post("duration") : false;

        if( !$data_token || !$token || !$reactions || !$duration || !is_numeric($duration))
        {
            echo json_encode(["error" => ["message" => "Invalid Data Input", "code" => 0], "message" => ""]);
            return;
        }

        $price = $duration * PRICE_BOT_PER_DAY;
        $current_balance = $this->user_model->getBalance($this->session->userdata("user_id"));
        $new_balance = $current_balance - $price;
        if( $new_balance < 0 ) 
        {
            echo json_encode(["error" => ["message" => "Tài khoản của bạn không đủ", "code" => 0], "message" => ""]);
            return;
        }

        $data_token = json_decode($data_token, true);
        $data = [
            "uid"           => isset($data_token["id"]) ? $data_token["id"] : "",
            "name"          => isset($data_token["name"]) ? $data_token["name"] : "",
            "token"         => $token,
            "reactions"     => $reactions,
            "start_day"     => date("Y-m-d H:i:s"),
            "end_day"       => date("Y-m-d H:i:s", strtotime("+". $duration ." days")),
            "user_id"       => $this->session->userdata("user_id")
        ];
        if( $this->botreactions_model->insert($data) )
        {
            if( $this->user_model->updateById(["balance" => $new_balance], $this->session->userdata("user_id")) )
            {
                $this->session->set_userdata(["balance" => number_format($new_balance)]);
                $this->history_model->log("-", $price, "botreactions", "Create Bot reactions {$data_token["id"]} - {$duration} days.", $this->session->userdata("user_id"));
            }
            echo json_encode(["error" => 0, "message" => "Insert DB success"]);
            return;
        }
        else
        {
            echo json_encode(["error" => ["message" => "Insert DB fail", "code" => 0], "message" => ""]);
        }
    }

    public function list()
    {
        $datas = $this->botreactions_model->getAllByUserId($this->session->userdata("user_id"));
        echo !$datas ? json_encode(["error" => 0, "data" => "", "message" => "No data"]) : json_encode(["error" => 0, "data" => $datas]);
    }

    public function delete() 
    {
        $list_id = !empty($this->input->post("list_id")) ? json_decode($this->input->post("list_id"), true) : false;
        
        if( !$list_id )
        {
            echo json_encode(["error" => ["message" => "Invalid input"], "message" => ""]);
            return;
        }

        $success = 0;
        $fail = 0;
        foreach($list_id as $id)
        {
            $old_data = $this->botreactions_model->find($id);
            if( $old_data ) 
            {
                $days_left = strtotime($old_data->end_day) - strtotime(date("Y-m-d H:i:s"));
                $days_left = (int)($days_left / 60 / 60 / 24);
                $refund = $days_left * PRICE_BOT_PER_DAY;
                $current_balance = $this->user_model->getBalance($this->session->userdata("user_id"));
                $new_balance = $current_balance + $refund;
                if( $this->botreactions_model->delete($id, $this->session->userdata("user_id")) )
                {
                    if( $this->user_model->updateById(["balance" => $new_balance], $this->session->userdata("user_id")) )
                    {
                        $this->session->set_userdata(["balance" => number_format($new_balance)]);
                        $this->history_model->log("+", $refund, "botreactions", "Refund bot reactions {$old_data->uid} - {$days_left} days left.", $this->session->userdata("user_id"));
                    }
                    $success++;
                }
                else 
                    $fail++;
            }
            else
            {
                $fail++;
            }
        }
        echo json_encode(["error" => 0, "message" => "Delete success ({$success}). Fail ({$fail})"]);
    }



    

    

}
