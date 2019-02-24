<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class FriendManagement extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "Setup Accept, Unfriend";
        $this->data['sub_title'] = "";
        if( $this->session->userdata("role_id") != 1 )
            redirect("dashboard");
        $this->load->model("taskacceptunfriend_model");
    }

    public function index()
    {
        $this->load->template("acceptunfriend.php", $this->data);
    }

    public function import()
    {
        $data_token = $this->input->post("data");
        $token = !empty($this->input->post("token")) ? $this->input->post("token") : false;
        $type = $this->input->post("type");
        if( !$token || !$type )
        {
            echo json_encode(["error" => ["message" => "Invalid Token or Type", "code" => 0], "message" => ""]);
            return;
        }
        $data_token = json_decode($data_token, true);
        $url = $type == "accept" ? "https://graph.facebook.com/me?fields=friendrequests.limit(". MAX_ACCEPT .")&access_token={$token}" : "https://graph.facebook.com/v3.2/me?fields=friends.limit(". MAX_UNFRIEND .")&access_token={$token}";

        $data = [
            "token"         => $token,
            "uid"           => $data_token["id"],
            "type"          => $type,
            "url"           => $url,
            "user_id"       => $this->session->userdata("user_id")
        ];

        echo $this->taskacceptunfriend_model->insert($data) ? json_encode(["error" => 0, "message" => "Insert DB success"]) : json_encode(["error" => ["message" => "Insert DB fail", "code" => 0], "message" => ""]);
       
    }

    public function list()
    {
        $datas = $this->taskacceptunfriend_model->getAll();
        echo !$datas ? json_encode(["error" => 0, "data" => "", "message" => "No data"]) : json_encode(["error" => 0, "data" => $datas]);
    }

    public function deleteTask() 
    {
        $list_id = !empty($this->input->post("list_id")) ? json_decode($this->input->post("list_id"), true) : false;
        
        if( !$list_id )
        {
            echo json_encode(["error" => ["message" => "Invalid input"], "message" => ""]);
            return;
        }
        echo $this->taskacceptunfriend_model->delete($list_id) ? json_encode(["error" => 0, "message" => "Delete success"]) : json_encode(["error" => ["message" => "Delete fail"], "message" => ""]);
    }

}
