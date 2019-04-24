<?php 
set_time_limit(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Token extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("token_model");
        $this->load->model("tokenbuff_model");
        $this->load->library("request");
        $this->data['page_title'] = "Token Management";
        $this->data['sub_title'] = "";
        if( $this->session->userdata('role_id') != 1 ) {
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
        $token = !empty($this->input->post("token")) ? $this->input->post("token") : false;
        if( !$token ) 
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
        if( $this->token_model->insert($data) )
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

    public function update()
    {
        $id = !empty($this->input->post("id")) ? $this->input->post("id") : false;
        if( !$id ) 
        {
            echo json_encode(["error" => ["message" => "Invalid token id", "code" => 0], "message" => ""]);
            return;
        }
        if( $this->token_model->update($id, ["status" => 0]) ) {
            echo json_encode(["error" => 0, "message" => "Update success"]);
        }
        else {
            echo json_encode(["error" => ["message" => "Update fail", "code" => 0], "message" => ""]);
        }
    }

    public function delete()
    {
        $id = !empty($this->input->post("id")) ? $this->input->post("id") : false;
        if( !$id ) 
        {
            echo json_encode(["error" => ["message" => "Invalid token id", "code" => 0], "message" => ""]);
            return;
        }
        if( $this->token_model->delete($id) ) {
            echo json_encode(["error" => 0, "message" => "Delete success"]);
        }
        else {
            echo json_encode(["error" => ["message" => "Delete fail", "code" => 0], "message" => ""]);
        }
    }

    public function getTokens()
    {
        $tokens = $this->token_model->getAll();
        if(!$tokens) $tokens = [];
        echo json_encode(["error" => 0, "data" => $tokens, "message" => ""]);
    }

    public function buff()
    {
        $this->load->template("tokenbuff", ["page_title" => "Token buff management", "sub_title" => ""]);
    }

    public function importBuff()
    {
        $data_token = $this->input->post("data");
        $token = !empty($this->input->post("token")) ? $this->input->post("token") : false;
        if( !$token ) 
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
        if( $this->tokenbuff_model->insert($data) )
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

    public function deleteBuff()
    {
        $id = !empty($this->input->post("id")) ? $this->input->post("id") : false;
        if( !$id ) 
        {
            echo json_encode(["error" => ["message" => "Invalid token id", "code" => 0], "message" => ""]);
            return;
        }
        if( $this->tokenbuff_model->delete($id) ) {
            echo json_encode(["error" => 0, "message" => "Delete success"]);
        }
        else {
            echo json_encode(["error" => ["message" => "Delete fail", "code" => 0], "message" => ""]);
        }
    }

    public function getTokensBuff()
    {
        $tokens = $this->tokenbuff_model->getAll();
        echo json_encode(["error" => 0, "data" => $tokens, "message" => ""]);
    }

    private function __checkLiveCookie($cookie)
    {
        $pattern_uid = '/profile_id=(.+?)&/';
        $pattern_name = '/<title>(.+?)<.title>/';
        $html = json_decode($this->request->get("https://www.facebook.com/ajax/flash/user_info.php", $cookie), true);
       // print_r($html);
        //echo $html;
        if (!empty($html["user"])) {
            return ["uid" => (string)$html["user"]];
        }
        return false; 

        // if( preg_match($pattern_uid, $html, $matches) )
        // {
        //     $uid = !empty($matches[1]) ? $matches[1] : "0";
        //     if( preg_match($pattern_name, $html, $matches2) )
        //     {
        //         $name = isset($matches2[1]) ? $matches2[1] : "";
        //         if($uid == "0") 
        //         {
        //             return false;
        //         }
        //         $data = ["name" => $name, "uid" => $uid];
        //         return $data;
        //     }
        // }
        // return false;
    }

    public function importCookie()
    {
        $cookie = !empty($this->input->post("cookie")) ? $this->input->post("cookie") : false;
        if (!$cookie) 
        {
            echo json_encode(["error" => ["message" => "Invalid data", "code" => 0], "message" => ""]);
            return;
        }
        $data_cookie = $this->__checkLiveCookie($cookie);
        if(!$data_cookie)
        {
            echo json_encode(["error" => ["message" => "Cookie die", "code" => 0], "message" => ""]);
            return;
        }
        $data =  [
            "cookie" => $cookie,
            "uid" => $data_cookie["uid"],
            //"fullname" => $data_cookie["name"]
        ];
        echo $this->token_model->insert($data) ? json_encode(["error" => 0, "message" => "Insert DB success"]) : json_encode(["error" => ["message" => "Insert DB fail", "code" => 0], "message" => ""]);
    }

    public function checkCookie()
    {
        $tokens = $this->token_model->getTokens();
        if ( $tokens !== false ) 
        {
            $live = 0;
            $die = 0;
            foreach($tokens as $token)
            {
                if($token->cookie != "")
                {
                    $data_cookie = $this->__checkLiveCookie($token->cookie);
                    if ( !$data_cookie )
                    {
                        $this->token_model->update($token->id, ["status" => 0]);
                        $die++;
                    }
                    else
                        $live++;
                }
            }
            echo json_encode(["error" => 0, "message" => "Live: {$live}, Die: {$die}"]);
        }
        else
        {
            echo json_encode(["error" => 0, "message" => "Không có dữ liệu cookie trong database"]);
        }
    }

    public function test() {
        var_dump($this->__checkLiveCookie("datr=oPVsXEPNoAmPPg5Asif8_dHJ;locale=en_US;c_user=100014836291862;xs=49%3Aw7617IS2IS7eTw%3A2%3A1555273674%3A8788%3A6177;"));
    }
}
