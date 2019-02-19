<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class History extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "Lịch sử";
        $this->data['sub_title'] = "";
        
    }

    public function index()
    {
        $this->load->template("history", $this->data);
    }

    public function list()
    {
        $list = $this->history_model->getAll();
        if( !$list )
        {
            echo json_encode(["error" => 0, "data" => [], "message" => ""]);
            return;
        }
        echo json_encode(["error" => 0, "data" => $list, "message" => ""]);
    }

    



}
