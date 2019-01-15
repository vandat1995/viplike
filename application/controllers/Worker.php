<?php 
set_time_limit(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Worker extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if( ! $this->input->is_cli_request())
        {
            redirect("dashboard");
        }
        $this->load->model("token_model");
        $this->load->model("task_model");
        $this->load->model("task_process_model");
        $this->load->library("request");
    }

    public function index()
    {
        $this->FireInTheHole();
    }

    public function FireInTheHole()
    {
        $this->__runTasks();
        $this->__runProcessese();
    }

    private function __runTasks()
    {
        $tasks = $this->task_model->getTasks();
        if(!$tasks)
        {
            return;
        }
        foreach($tasks as $task)
        {
            $tokens = $this->token_model->getTokens($task->quantity_per_cron);
            $id_post = $this->__getNewFeed($task->uid, $tokens[0]->token);
            if($this->__checkLiked($id_post))
            {
                break;
            }
            $success = 0;
            $fail = 0;
            foreach($tokens as $token)
            {
                $this->__likeFeed($token->token, $id_post) ? $success++ : $fail++;
            }
            $this->task_process_model->saveProcessDone(["task_id" => $task->id, "id_liked" => $id_post, "remain" => ($task->quantity_like - $task->quantity_per_cron), "success" => $success, "fail" => $fail]);
        }
    }

    private function __runProcessese()
    {
        $processese = $this->task_process_model->getProcessese();
        if(!$processese)
        {
            return;
        }
        foreach($processese as $process)
        {
            $tokens = $this->token_model->getTokens($process->quantity_per_cron);
            $id_post = $process->id_liked;
            $success = 0;
            $fail = 0;
            foreach($tokens as $token)
            {
                $this->__likeFeed($token->token, $id_post) ? $success++ : $fail++;
            }
            $this->task_process_model->update($process->id, ["remain" => ($process->remain - $process->quantity_per_cron), "success" => ($process->success + $success), "fail" => ($process->fail + $fail)]);
        }
    }

    private function __checkLiked($id_post)
    {
        return $this->task_process_model->checkExistIdLiked($id_post);
    }

    private function __getNewFeed($vip_uid, $token)
    {
        $url = "https://graph.facebook.com/{$vip_uid}/feed?limit=1&fields=id,story,privacy,message&method=get&access_token={$token}";
        $feed = json_decode($this->request->get($url), true);
        $privacy = $feed["data"][0]["privacy"]["value"];
        if(isset($feed["data"][0]["id"]) && $privacy == "EVERYONE")
        {
            $uid = explode("_", $feed["data"][0]["id"])[0];
            if($uid != $vip_uid)
            {
                return false;
            }
            $id_post = $feed["data"][0]["id"];
            return $id_post;
        }
        return false;
    }

    private function __likeFeed($token, $id_post)
    {
        $url = "https://graph.facebook.com/{$id_post}/likes?access_token={$token}&method=post";
        $like = json_decode($this->request->get($url), true);
        if($like == "true")
        {
            return true;
        }
        return false;
    }

    

    



}
