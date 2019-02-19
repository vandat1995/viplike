<?php 
set_time_limit(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkerCmt extends CI_Controller
{
    private $videos = ["1.mp4", "2.mp4", "3.mp4", "4.mp4", "5.mp4", "6.mp4"];
    public function __construct()
    {
        parent::__construct();
        if( !$this->input->is_cli_request() )
        {
            redirect("dashboard");
        }
        $this->load->library("request");
        $this->load->model("uid_model");
        $this->load->model("cmttoken_model");
    }

    private function __commentPost($token, $post_id, $video)
    {
        $msg = urlencode($msg);
        $video = base_url() . "assets/videos/" . $video;
        $url = "https://graph.facebook.com/{$post_id}/comments?attachment_url={$video}&access_token={$token}&method=post";
        $result = $this->request->get($url);
        if( $result === false )
        {
            return false;
        }
        else
        {
            $fire = json_decode($result, true);
            return isset($fire["id"]) ? true : false;
        }
    }

    private function __getNewFeed($uid, $token)
    {
        $url = "https://graph.facebook.com/{$uid}/feed?limit=1&fields=id,story,privacy,message&method=get&access_token={$token}";
        $feed = json_decode($this->request->get($url), true);
        $privacy = !empty($feed["data"][0]["privacy"]["value"]) ? $feed["data"][0]["privacy"]["value"] : false;
        if( isset($feed["data"][0]["id"]) && $privacy == "EVERYONE" )
        {
            return $feed["data"][0]["id"];
        }
        return false;
    }

    public function runBotComment() 
    {
        $limit = 20;
        $tokens = $this->cmttoken_model->get($limit);
        if ($tokens !== false)
        {
            $uids = $this->uid_model->get($limit);
            if ($uids !== false)
            {
                for($i = 0; $i < count($tokens); $i++)
                {
                    $post_id = $this->__getNewFeed($uids[$i]->uid, $tokens[$i]->token);
                    if ($post_id !== false)
                    {
                        $comment = $this->__commentPost($tokens[$i]->token, $post_id, $this->videos[array_rand($this->videos)]);
                        if ($comment === false)
                        {
                            $this->cmttoken_model->update($tokens[$i]->id, ["status" => 0]);
                        }
                        else
                        {
                            $this->cmttoken_model->update($tokens[$i]->id, ["last_used" => date("Y-m-d H:i:s")]);
                            $this->uid_model->update($uids[$i]->id, ["commented" => 1, "last_comment" => date("Y-m-d H:i:s")]);
                        }
                    }
                    else
                    {
                        $this->uid_model->update($uids[$i]->id, ["status" => 0]);
                    }
                }
            }
        }
    }

    public function importUID()
    {
        $file = FCPATH . "upload/uid.txt";
        $uids = file_get_contents($file);
        $arr = explode("\n", $uids);
        foreach($arr as $uid)
        {
            $this->uid_model->insert(["uid" => $uid]);
        }

    }

    public function importToken()
    {
        $file = FCPATH . "upload/token.txt";
        $uids = file_get_contents($file);
        $arr = explode("\n", $uids);
        foreach($arr as $uid)
        {
            $this->cmttoken_model->insert(["token" => $uid, "last_used" => date("Y-m-d H:i:s")]);
        }
 
    }


}
