<?php 
set_time_limit(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Worker extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // if( ! $this->input->is_cli_request())
        // {
        //     redirect("dashboard");
        // }
        $this->load->model("token_model");
        $this->load->model("task_model");
        $this->load->model("taskprocess_model");
        $this->load->model("tokenprocessmap_model");
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
        $tasks = $this->task_model->getActiveTasks();
        if(!$tasks)
        {
            return;
        }
        foreach($tasks as $task)
        {
            $tokens = $this->token_model->getTokens(($task->quantity_like + 6));
            $post_id = $this->__getNewFeed($task->uid, $tokens[array_rand($tokens)]->token);
            if($this->__checkProcessExist($post_id) || !$post_id)
            {
                continue;
            }
            $total_token = count($tokens);

            //Create process
            $process_id = $this->taskprocess_model->insert(["task_id" => $task->id, "post_id" => $post_id]);
            if( ! $process_id) {
                log_message("ERROR", "Create Process Fail: [{$process_id}]");
                continue;
            }
            $reactions = json_decode($task->reactions, true);
            $tmp = 0;
            foreach($reactions as $key => $val)
            {
                for($i = $tmp; $i < ($val + $tmp); $i++)
                {
                    $this->tokenprocessmap_model->insert(["process_id" => $process_id, "token_id" => $tokens[$i]->id, "reaction" => $key]);
                }
                $tmp += $val;
            }
        }
    }

    private function __runProcessese()
    {
        $processese = $this->taskprocess_model->getActiveProcessese();
        if(!$processese)
        {
            return;
        }
        foreach($processese as $process)
        {
            $datas = $this->tokenprocessmap_model->getRandByProcessId($process->id, $process->quantity_per_cron);
            if( ! $datas) {
                //Done 1 task process
                $this->taskprocess_model->update($process->id, ["is_done" => 1]);
                continue;
            }

            foreach($datas as $data)
            {
                $res = $this->__reactionPost($data->token, $data->post_id, $data->reaction);
                $this->tokenprocessmap_model->update($data->id, ["status" => (int)$res, "is_runned" => 1]);
            }
        }
    }

    private function __checkProcessExist($post_id)
    {
        return $this->taskprocess_model->checkExistPostId($post_id);
    }

    private function __getNewFeed($vip_uid, $token)
    {
        $url = "https://graph.facebook.com/{$vip_uid}/feed?limit=1&fields=id,story,privacy,message&method=get&access_token={$token}";
        $feed = json_decode($this->request->get($url), true);
        $privacy = !empty($feed["data"][0]["privacy"]["value"]) ? $feed["data"][0]["privacy"]["value"] : false;
        if(isset($feed["data"][0]["id"]) && $privacy == "EVERYONE")
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

}
