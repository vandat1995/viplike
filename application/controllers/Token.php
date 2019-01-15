<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Token extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("token_model");
        $this->data['page_title'] = "Token Management";
        $this->data['sub_title'] = "";
        if($this->session->userdata('role_id') != 1) {
            redirect('dashboard');
        }
    }

    public function index()
    {
        $this->load->template("token", $this->data);
    }

    public function import()
    {
        $data_token = $this->input->post("data");
        $token = ! empty($this->input->post("token")) ? $this->input->post("token") : false;
        if( ! $token) 
        {
            echo json_encode(["error" => ["message" => "Invalid Token", "code" => 0], "message" => ""]);
            return;
        }
        $data_token = json_decode($data_token, true);
        
        $data = [
            "token" => $token, 
            "uid" => $data_token["id"],
            "fullname" => $data_token["name"],
            "gender" => $data_token["gender"]
        ];
        if($this->token_model->insert($data))
        {
            echo json_encode(["error" => 0, "message" => "Insert DB success"]);
            return;
        }
        else
        {
            echo json_encode(["error" => ["message" => "Insert DB fail", "code" => 0], "message" => ""]);
            return;
        }
    }

    public function delete()
    {
        $id = ! empty($this->input->post("id")) ? $this->input->post("id") : false;
        if( ! $id) 
        {
            echo json_encode(["error" => ["message" => "Invalid token id", "code" => 0], "message" => ""]);
            return;
        }
        if($this->token_model->delete($id)) {
            echo json_encode(["error" => 0, "message" => "Delete success"]);
        }
        else {
            echo json_encode(["error" => ["message" => "Delete fail", "code" => 0], "message" => ""]);
        }
    }

    public function getTokens()
    {
        $tokens = $this->token_model->getAll();
        echo json_encode(["error" => 0, "data" => $tokens, "message" => ""]);
    }



}
