<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class BuffLike extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "Buff like";
        $this->data['sub_title'] = "";
        $this->load->model("bufflike_model");
        $this->load->model("tokenbuff_model");
    }

    public function index()
    {
        $this->load->template("bufflike", $this->data);
    }

    public function addTask()
    {
        $this->form_validation->set_rules("post_id", "Post ID", "required|max_length[20]");
        $this->form_validation->set_rules("quantity", "Quantity", "required|max_length[10]|numeric|greater_than[0]");
        if($this->form_validation->run() == false)
        {
            echo json_encode(["error" => ["message" => validation_errors(), "code" => 0], "message" => ""]);
            return;
        }
        $post_id            = xss_clean($this->input->post("post_id"));
        $quantity           = (int)$this->input->post("quantity");

        $total_token = $this->tokenbuff_model->count();
        if( $quantity > $total_token )
        {
            echo json_encode(["error" => ["message" => "Quantity cann't more than current total token", "code" => 0], "message" => ""]);
            return;
        }

        $data = [
            "post_id" => $post_id,
            "quantity" => $quantity
        ];

        echo $this->bufflike_model->insert($data) ? json_encode(["error" => 0, "message" => "Create Buff Like Task Success"]) : json_encode(["error" => ["message" => "Create Buff Like Task Fail", "code" => 0], "message" => ""]);
        
    }

    public function listTask()
    {
        $list_task = $this->bufflike_model->getAll();
        if( !$list_task )
        {
            echo json_encode(["error" => 0, "data" => [], "message" => ""]);
            return;
        }
        echo json_encode(["error" => 0, "data" => $list_task, "message" => ""]);
    }

    public function deleteTask() 
    {
        $id = !empty($this->input->post("id")) ? $this->input->post("id") : false;
        
        if( !$id )
        {
            echo json_encode(["error" => ["message" => "Invalid input"], "message" => ""]);
            return;
        }
        echo $this->bufflike_model->delete($id) ? json_encode(["error" => 0, "message" => "Delete success"]) : json_encode(["error" => ["message" => "Delete fail"], "message" => ""]);
    }

    

}
