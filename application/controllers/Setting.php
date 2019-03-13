<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "Setting";
        $this->data['sub_title'] = "";
        if( $this->session->userdata('role_id') != 1 ) {
            redirect('dashboard');
        }
        $this->load->model("setting_model");
    }

    public function index()
    {
        $this->load->template("setting", $this->data);
    }

    public function create()
    {
        $this->form_validation->set_rules("quantity", "Số cảm xúc", "required|max_length[11]|numeric");
        $this->form_validation->set_rules("price", "Giá", "required|max_length[20]");
        
        if( $this->form_validation->run() == false )
        {
            echo json_encode(["error" => ["message" => validation_errors(), "code" => 0], "message" => ""]);
            return;
        }

        $data = [
            "quantity"          => (int)xss_clean($this->input->post("quantity")),
            "price_per_month"   => xss_clean($this->input->post("price")),
            "type" => "viplike"
        ];

        echo $this->setting_model->insert($data) ? json_encode(["error" => 0, "message" => "Create price success"]) : json_encode(["error" => ["message" => "Create price fail", "code" => 0], "message" => ""]);     
    }

    public function list()
    {
        $list = $this->setting_model->getAll();
        echo !$list ? json_encode(["error" => 0, "data" => []]) : json_encode(["error" => 0, "data" => $list]);
    }

    public function delete()
    {
        $this->form_validation->set_rules("id", "Price ID", "required|max_length[10]");
        if( $this->form_validation->run() === false )
        {
            echo json_encode(["error" => ["message" => validation_errors(), "code" => 0], "message" => ""]);
            return;
        }
        $id = $this->input->post("id");
        echo $this->setting_model->delete($id) ? json_encode(["error" => 0, "message" => "Delete success"]) : json_encode(["error" => ["message" => "Delete fail", "code" => 0], "message" => ""]);
    }

    public function loadMaxUidVip()
    {
        $maxUid = file_get_contents(APPPATH . 'config/maxuid.txt');
        echo json_encode(["data" => $maxUid]);
    }

    public function setMaxUidVip()
    {
        $maxUid = !empty($this->input->post("maxUid")) ? $this->input->post("maxUid") : "";
        if ($maxUid < 1 || $maxUid == "")
        {
            echo json_decode(["error" => ["message" => "Dữ liệu nhập vào không họp lệ"]]);
            return;
        }
        file_put_contents(APPPATH . 'config/maxuid.txt', $maxUid);
        echo json_encode(["message" => "Thành công"]);
    }

    



}
