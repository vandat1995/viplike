<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class BotReactions extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "Bot Reactions";
        $this->data['sub_title'] = "";
        $this->load->model("botreactions_model");
    }

    public function index() 
    {
        $this->load->template("botreactions", $this->data);
    }

    public function import()
    {
        $data_token = !empty($this->input->post("data")) ? $this->input->post("data") : false;
        $token = !empty($this->input->post("token")) ? $this->input->post("token") : false;
        $reactions = !empty($this->input->post("reactions")) ? $this->input->post("reactions") : false;

        if( !$data_token || !$token || !$reactions )
        {
            echo json_encode(["error" => ["message" => "Invalid Data Input", "code" => 0], "message" => ""]);
            return;
        }

        $data_token = json_decode($data_token, true);
        $data = [
            "uid"           => isset($data_token["id"]) ? $data_token["id"] : "",
            "name"          => isset($data_token["name"]) ? $data_token["name"] : "",
            "token"         => $token,
            "reactions"     => $reactions,
            "user_id"       => $this->session->userdata("user_id")
        ];

        echo $this->botreactions_model->insert($data) ? json_encode(["error" => 0, "message" => "Insert DB success"]) : json_encode(["error" => ["message" => "Insert DB fail", "code" => 0], "message" => ""]);
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
            $this->botreactions_model->delete($id, $this->session->userdata("user_id")) ? $success++ : $fail++;
        }
        echo json_encode(["error" => 0, "message" => "Delete success ({$success}). Fail ({$fail})"]);
    }



    

    

}
