<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page_title'] = "Dashboard";
        $this->data['sub_title'] = "";
    }

    public function index()
    {
        $this->load->template("dashboard", $this->data);
    }
}
