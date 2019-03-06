<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if( $this->session->userdata("role_id") != 1 ) {
            $this->data['page_title'] = "Trang cá nhân";
        } else {
            $this->data['page_title'] = "Quản lý user";
        }
        
        $this->data['sub_title'] = "";
        
        $this->load->model("user_model");
    }

    public function index()
    {
        $this->load->template("user", $this->data);
    }

    public function listUser()
    {
        if( $this->session->userdata("role_id") != 1 ) redirect("dashboard");
        $list_user = $this->user_model->getAll();
        echo !$list_user ? json_encode(["error" => 0, "data" => []]) : json_encode(["error" => 0, "data" => $list_user]);
    }

    public function create()
    {
        if( $this->session->userdata("role_id") != 1 ) redirect("dashboard");
        $this->form_validation->set_rules("username", "Username", "required|max_length[50]|is_unique[users.username]");
        $this->form_validation->set_rules("password", "Password", "required|max_length[50]|min_length[6]");
        $this->form_validation->set_rules("fullname", "Full name", "required|max_length[50]");
        $this->form_validation->set_rules("avatar", "Avatar", "max_length[255]");
        $this->form_validation->set_rules("permissions", "Permissions", "required|greater_than[0]|less_than[4]");
        $this->form_validation->set_rules("balance", "Balance", "required|greater_than_equal_to[0]|max_length[15]");
        
        if( $this->form_validation->run() == false )
        {
            echo json_encode(["error" => ["message" => validation_errors(), "code" => 0], "message" => ""]);
            return;
        }

        $userdata = [
            "username"  => xss_clean($this->input->post("username")),
            "password"  => md5(xss_clean($this->input->post("password"))),
            "full_name" => xss_clean($this->input->post("fullname")),
            "avatar"    => xss_clean($this->input->post("avatar")),
            "role_id"   => xss_clean($this->input->post("permissions")),
            "balance"   => xss_clean($this->input->post("balance"))
        ];

        echo $this->user_model->insert($userdata) ? json_encode(["error" => 0, "message" => "Create user success"]) : json_encode(["error" => ["message" => "Create user fail", "code" => 0], "message" => ""]);     

    }

    public function deposit()
    {
        if( $this->session->userdata("role_id") != 1 ) redirect("dashboard");
        $this->form_validation->set_rules("username", "Username", "required|max_length[50]");
        $this->form_validation->set_rules("amount", "Amount", "required|greater_than_equal_to[0]|max_length[15]");

        if( $this->form_validation->run() == false )
        {
            echo json_encode(["error" => ["message" => validation_errors(), "code" => 0], "message" => ""]);
            return;
        }

        $username = $this->input->post("username");
        $amount = $this->input->post("amount");
        $userdata = $this->user_model->findByUsername($username);
        if( !$userdata )
        {
            echo json_encode(["error" => ["message" => "Username doesn't exist", "code" => 0], "message" => ""]);
            return;
        }
        if( $this->user_model->update(["balance" => (int)$userdata->balance + (int)$amount], $username) )
        {
            $this->history_model->log("+", $amount, "deposit", "Deposit", $userdata->id);
            echo json_encode(["error" => 0, "message" => "Deposit success"]);
        }
        else
        {
            echo json_encode(["error" => ["message" => "Deposit fail", "code" => 0], "message" => ""]);
        }
    }

    public function delete()
    {
        if( $this->session->userdata("role_id") != 1 ) redirect("dashboard");
        $this->form_validation->set_rules("user_id", "User", "required|max_length[10]");
        if( $this->form_validation->run() == false )
        {
            echo json_encode(["error" => ["message" => validation_errors(), "code" => 0], "message" => ""]);
            return;
        }
        
        $user_id = $this->input->post("user_id");
        if( $user_id == $this->session->userdata("user_id") )
        {
            echo json_encode(["error" => ["message" => "Can't delete your self", "code" => 0], "message" => ""]);
            return;
        }

        echo $this->user_model->delete($user_id) ? json_encode(["error" => 0, "message" => "Delete user success"]) : json_encode(["error" => ["message" => "Delete user fail", "code" => 0], "message" => ""]);
    }

    public function getInfoById()
    {
        if( $this->session->userdata("role_id") != 1 ) redirect("dashboard");
        $user_id = !empty($this->input->get("user_id")) ? $this->input->get("user_id") : "";
        if ($user_id == "") 
        {
            echo json_encode(["error" => ["message" => "Dữ liệu không chính xác."], "message" => ""]);
            return;
        }

        $user_data = $this->user_model->find($user_id);
        echo $user_data !== false ? json_encode(["error" => 0, "data" => $user_data]) : json_decode(["error" => ["message" => "Không có dữ liệu người dùng này."]]);
        
    }

    public function edit()
    {
        $this->form_validation->set_rules("fullname", "Full name", "required|max_length[50]");
        $this->form_validation->set_rules("avatar", "Avatar", "max_length[255]");
        $this->form_validation->set_rules("permissions", "Permissions", "required|greater_than[0]|less_than[4]");
        $this->form_validation->set_rules("balance", "Balance", "required|greater_than_equal_to[0]|max_length[15]");
        
        if( $this->form_validation->run() == false )
        {
            echo json_encode(["error" => ["message" => validation_errors(), "code" => 0], "message" => ""]);
            return;
        }
        $username = $this->input->post("username");
        $userdata = [
            "full_name" => xss_clean($this->input->post("fullname")),
            "avatar"    => xss_clean($this->input->post("avatar")),
            "role_id"   => xss_clean($this->input->post("permissions")),
            "balance"   => xss_clean($this->input->post("balance"))
        ];

        echo $this->user_model->update($userdata, $username) ? json_encode(["error" => 0, "message" => "Cập nhật user thành công"]) : json_encode(["error" => ["message" => "Cập nhật user thất bại", "code" => 0], "message" => ""]);     
    }

    public function info()
    {
        $userData = $this->user_model->find($this->session->userdata("user_id"));
        if ($userData) 
        {
            $data = ["username" => $userData->username, "full_name" => $userData->full_name, "avatar" => $userData->avatar];
            echo json_encode(["data" => $data]);
        }
        else
        {
            echo json_encode(["error" => ["message" => "Không tìm thấy thông tin."]]);
        }
    }

    public function changeInfoProfile()
    {
        $this->form_validation->set_rules("password", "Password", "required|max_length[50]|min_length[6]");
        $this->form_validation->set_rules("fullname", "Full name", "required|max_length[50]");
        $this->form_validation->set_rules("avatar", "Avatar", "max_length[255]");
        if( $this->form_validation->run() == false )
        {
            echo json_encode(["error" => ["message" => validation_errors(), "code" => 0], "message" => ""]);
            return;
        }
        $userdata = [
            "password"  => md5(xss_clean($this->input->post("password"))),
            "full_name" => xss_clean($this->input->post("fullname")),
            "avatar"    => xss_clean($this->input->post("avatar"))
        ];

        if ($this->user_model->updateById($userdata, $this->session->userdata("user_id"))) 
        {
            $this->session->set_userdata(['avatar' => $userdata["avatar"], 'full_name' => $userdata["full_name"]]);
            echo json_encode(["success" => true]);
        }
        else {
            echo json_encode(["error" => ["message" => "Cập nhật thông tin thất bại vui lòng thử lại sau."]]);
        }

    }

}
