<?php 
set_time_limit(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Worker extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if( !$this->input->is_cli_request() )
        {
            redirect("dashboard");
        }
        $this->load->model("token_model");
        $this->load->model("task_model");
        $this->load->model("taskcmt_model");
        $this->load->model("process_model");
        $this->load->model("tokenprocessmap_model");
        $this->load->model("taskacceptunfriend_model");
        $this->load->model("bufflike_model");
        $this->load->model("tokenbuff_model");
        $this->load->library("request");
    }

    public function index()
    {
        $this->__runTasks();
    }

    public function FireInTheHole()
    {
        //$this->__runTasksCmt();
        $this->__runProcesses();
    }

    public function TaskBuffLike()
    {
        $this->__runTaskBuffLike();
    }

    public function TaskAcceptFriend()
    {
        $datas = $this->taskacceptunfriend_model->getAllByType("accept");
        if( !$datas )
            return;
        foreach($datas as $data)
        {
            $api = $this->__getApiAcceptUnFriend($data->url, $data->type, $data->token);
            if( !$api )
            {
                $this->taskacceptunfriend_model->update($data->id, ["is_done" => 1]);
                continue;
            }
            else 
            {
                $this->request->get($api);
            }
        }
    }

    public function TaskUnFriend()
    {
        $datas = $this->taskacceptunfriend_model->getAllByType("unfriend");
        if( !$datas )
            return;
        foreach($datas as $data)
        {
            $api = $this->__getApiAcceptUnFriend($data->url, $data->type, $data->token);
            if( !$api )
            {
                $this->taskacceptunfriend_model->update($data->id, ["is_done" => 1]);
                continue;
            }
            else 
            {
                $this->request->get($api);
            }
        }
    }

    private function __runTasks()
    {
        $tasks = $this->task_model->getActiveTasks();
        if( !$tasks )
        {
            return;
        }
        foreach($tasks as $task)
        {
            $tokens = $this->token_model->getTokens(($task->quantity + 6));
            $post_id = $this->__getNewFeed($task->uid, $tokens[array_rand($tokens)]->token);
            if( $this->__checkProcessExist($post_id, "like") || !$post_id )
            {
                continue;
            }
            $total_token = count($tokens);

            // Create process
            $process_id = $this->process_model->insert(["vip_type" => "like", "task_id" => $task->id, "post_id" => $post_id]);
            if( !$process_id ) {
                log_message("ERROR", "Create Process Fail: [{$process_id}]");
                continue;
            }
            $reactions = json_decode($task->reactions, true);
            $tmp = 0;
            foreach( $reactions as $key => $val )
            {
                for($i = $tmp; $i < ($val + $tmp); $i++)
                {
                    $this->tokenprocessmap_model->insert(["process_id" => $process_id, "token_id" => $tokens[$i]->id, "reaction" => $key]);
                }
                $tmp += $val;
            }
        }
    }

    private function __runTasksCmt()
    {
        $tasks = $this->taskcmt_model->getActiveTasks();
        if( !$tasks )
        {
            return;
        }
        foreach($tasks as $task)
        {
            $tokens = $this->token_model->getTokens($task->quantity);
            $post_id = $this->__getNewFeed($task->uid, $tokens[array_rand($tokens)]->token);
            if( $this->__checkProcessExist($post_id, "cmt") || !$post_id)
            {
                continue;
            }
            
            $process_id = $this->process_model->insert(["vip_type" => "cmt", "task_id" => $task->id, "post_id" => $post_id]);
            if( !$process_id ){
                log_message("ERROR", "Create Process Fail: [{$process_id}]");
                continue;
            }
            $array_cmt = json_decode($task->msg_cmt, true);

            for($i = 0; $i < $task->quantity; $i++) 
            {
                $this->tokenprocessmap_model->insert(["process_id" => $process_id, "token_id" => $tokens[$i]->id, "cmt" => $array_cmt[array_rand($array_cmt)]]);
            }
        }
    }

    private function __runProcesses()
    {
        $processes = $this->process_model->getActiveProcesses();
        
        if( !$processes )
        {
            return;
        }
        foreach( $processes as $process )
        {
            $datas = $this->tokenprocessmap_model->getRandByProcessId($process->id, $process->quantity_per_cron);
            if( !$datas ) {
                // Done 1 task process
                $this->process_model->update($process->id, ["is_done" => 1]);
                continue;
            }
            
            if( $process->vip_type == "like" )
            {
                foreach( $datas as $data )
                {
                    $res = $this->__reactionPost($data->token, $data->post_id, $data->reaction);
                    $this->tokenprocessmap_model->update($data->id, ["status" => (int)$res, "is_runned" => 1]);
                }
            }
            else if( $process->vip_type == "cmt" )
            {
                foreach( $datas as $data )
                {
                    $res = $this->__cmtPost($data->token, $data->post_id, $data->cmt);
                    $this->tokenprocessmap_model->update($data->id, ["status" => (int)$res, "is_runned" => 1]);
                }
            }
        }
    }

    private function __checkProcessExist($post_id, $type)
    {
        return $this->process_model->checkExistPostId($post_id, $type);
    }

    private function __getNewFeed($vip_uid, $token)
    {
        $url = "https://graph.facebook.com/{$vip_uid}/feed?limit=1&fields=id,story,privacy,message&method=get&access_token={$token}";
        $feed = json_decode($this->request->get($url), true);
        $privacy = !empty($feed["data"][0]["privacy"]["value"]) ? $feed["data"][0]["privacy"]["value"] : false;
        if( isset($feed["data"][0]["id"]) && $privacy == "EVERYONE" )
        {
            $uid = explode("_", $feed["data"][0]["id"])[0];
            if($uid != $vip_uid)
            {
                return false;
            }
            return $feed["data"][0]["id"];
        }
        return false;
    }

    private function __reactionPost($token, $post_id, $reaction)
    {
        $url = "https://graph.facebook.com/{$post_id}/reactions?type={$reaction}&access_token={$token}&method=post";
        $fire = json_decode($this->request->get($url), true);
        return !empty($fire["success"]) ? $fire["success"] : false;
    }

    private function __cmtPost($token, $post_id, $msg)
    {
        $msg = urlencode($msg);
        $url = "https://graph.facebook.com/{$post_id}/comments?message={$msg}&access_token={$token}&method=post";
        $fire = json_decode($this->request->get($url), true);
        return !empty($fire["id"]) ? true : false;
    }

    private function __getApiAcceptUnFriend($url, $type, $token)
    {
        if( $type == "accept" )
        {
            $fr_request_list = json_decode($this->request->get($url), true);
            if( isset($fr_request_list["friendrequests"]["data"][0]["from"]["id"]) )
            {
                return "https://graph.facebook.com/v3.2/me/friends/{$fr_request_list["friendrequests"]["data"][0]["from"]["id"]}?method=post&access_token={$token}";
            }
        }
        else if( $type == "unfriend" )
        {
            $fr_list = json_decode($this->request->get($url), true);
            if( isset($fr_list["friends"]["data"][0]["id"]) )
            {
                return "https://graph.facebook.com/v3.2/me/friends/{$fr_list["friends"]["data"][0]["id"]}?method=delete&access_token={$token}";
            }
        }
        return false;
    }

    private function __runTaskBuffLike()
    {
        $task = $this->bufflike_model->getOne();
        if( !$task ) return;
        $this->bufflike_model->update($task->id, ["is_running" => 1]);
        $tokens = $this->tokenbuff_model->getTokens($task->quantity);
        foreach($tokens as $token) 
        {
            $this->request->get("https://graph.facebook.com/{$task->post_id}/likes?access_token={$token->token}&method=post");
            
            //$this->__reactionPost($token->token, $task->post_id, "LIKE");
        }
        $this->bufflike_model->update($task->id, ["is_done" => 1]);
    }

}
