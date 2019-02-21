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
        if(USE_COOKIE === true)
        {
            $this->__runTasksCookie();
        }
        else 
        {
            $this->__runTasks();
        }
        
    }

    public function FireInTheHole()
    {
        //$this->__runTasksCmt();
        if(USE_COOKIE === true)
        {
            $this->__runProcessesCookie();
        }
        else
        {
            $this->__runProcesses();
        }
    }

    public function TaskBuffLike()
    {
        $this->__runTaskBuffLike();
    }

    public function TaskBotReactions()
    {
        $this->__runBotLike();
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

    private function __getStatusBotLike($token)
    {
        $url = "https://graph.facebook.com/fql?q=SELECT%20post_id%20FROM%20stream%20WHERE%20source_id%20IN%20%28SELECT%20uid2%20FROM%20friend%20WHERE%20uid1%20%3D%20me%28%29%29&access_token=" . $token;
        $datas = $this->request->get($url);
        if( $datas == false )
            return false;
        $datas = json_decode($datas, true);
        return isset($datas["data"][0]["post_id"]) ? $datas["data"][0]["post_id"] : "";
    }

    private function __checkLiked($post_id, $token)
    {
        $url = "https://graph.facebook.com/v3.2/{$post_id}?fields=likes&access_token={$token}";
        $result = json_decode($this->request->get($url), true);
        return isset($result["likes"]["data"]) ? true : false;
    }

    private function __runBotLike()
    {
        $this->load->model("botreactions_model");
        $tasks = $this->botreactions_model->getAll();
        if( !$tasks ) return;
        foreach($tasks as $task)
        {
            $post_id = $this->__getStatusBotLike($task->token);
            if( $post_id === false )
            {
                // curl false -> token die;
                $this->botreactions_model->update($task->id, ["status" => 0]);
                continue;
            }
            else if( $post_id == "" ) 
            {
                continue;
            }
            else 
            {
                if ( !$this->__checkLiked($post_id, $task->token) )
                {
					// Neu id la avatar thi ...
                    if( $this->__reactionPost($task->token, $post_id, $task->reactions) != "true" )
					{
						$post_id = explode("_", $post_id)[1];
						$this->__reactionPost($task->token, $post_id, $task->reactions);
					}
                }
            }
        }
    }


    private function __runTasksCookie()
    {
        $tasks = $this->task_model->getActiveTasks();
        
        if( !$tasks )
        {
            return;
        }
        foreach($tasks as $task)
        {
            $tokens = $this->token_model->getTokens(($task->quantity + 6));
            
            $post_id = $this->__getNewFeedCookie($task->uid, $tokens[array_rand($tokens)]->cookie);

            if( $this->__checkProcessExist($post_id, "like") || !$post_id )
            {
                continue;
            }

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

    private function __runProcessesCookie()
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
                    $res = $this->__reactionPostCookie($data->cookie, $data->uid, $data->vip_uid, $data->post_id, $data->reaction);
                    $this->tokenprocessmap_model->update($data->id, ["status" => (int)$res, "is_runned" => 1]);
                    //nếu res = false thì cookie die;
                    $this->token_model->update($data->token_id, ["status" => (int)$res]);
                }
            }
        }
    }

    private function __getNewFeedCookie($vip_uid, $cookie)
    {
        $url = "https://m.facebook.com/{$vip_uid}";
        $html = $this->request->get($url, $cookie);
        if( $html !== false )
        {
            $pattern = '/story_fbid=([0-9]*)/';
            if( preg_match($pattern, $html, $matches) )
            {
                return $matches[1];
            }
        }
        return false;
    }

    private function __reactionPostCookie($cookie, $ck_uid, $vip_uid, $post_id, $reaction)
    {
        switch ($reaction) {
            case "LIKE":
                $reaction = "1";
                break;
            case "LOVE":
                $reaction = "2";
                break;
            case "WOW":
                $reaction = "3";
                break;
            case "HAHA":
                $reaction = "4";
                break;
            case "SAD":
                $reaction = "7";
                break;
            case "ANGRY":
                $reaction = "8";
                break;

        }
        $fb_dtsg = $this->get_fb_dtsg($cookie);
        if ( $fb_dtsg != "" )
        {
            $uuid4 = $this->gen_uuid();
            $url = "https://m.facebook.com/ufi/reaction/?ft_ent_identifier={$post_id}&story_render_location=timeline&feedback_source=0&is_sponsored=0&ext=1550929755&hash=AeRl5qtpU72zCHkR&refid=17&_ft_=mf_story_key.{$post_id}%3Atop_level_post_id.{$post_id}%3Atl_objid.{$post_id}%3Acontent_owner_id_new.{$vip_uid}%3Athrowback_story_fbid.{$post_id}%3Astory_location.4%3Athid.{$vip_uid}%3A306061129499414%3A2%3A0%3A1551427199%3A-4622699766100878414&__tn__=%3E*W-R&av={$ck_uid}&client_id=1550670584237%3A388496674&session_id={$uuid4}";
            $data = [
                "reaction_type" => $reaction,
                "ft_ent_identifier" => $post_id, //post_id
                "m_sess" => "",
                "fb_dtsg" => $fb_dtsg,
                "jazoest" => "22110",
                "__dyn" => "1KQdAmm1gxu4U4ifGh28sBBgS5UqxKcwRwAxu3-UcodUbEnwjUuK1lwZxm6Uhx6484G583rx65of8dE5K260Sob852q3q5U2nwvE6W787S78gwJwWwnElzawlo3_xyeKdwGwFU6i3Kq1sxq1gwwyo",
                "__req" => "7",
                "__ajax__" => "AYlG62zkYhS96cdP3gIfuVN7tkPs3qzdGhPlUfFUdtO5EJ9hfKoJN93kLGFsHJIgcOVI5GjQK1EI6EuILhni2sc2XdHZ1DNrZw-Des1Dc6A9uQ",
                "__user" => $ck_uid // owner user
            ];
            $data = http_build_query($data);
            $this->request->post($url, $cookie, $data);
            return true;
        }
        return false;

    }

    public function test()
    {
        $cookie = "c_user=100032901218520;xs=10:uiLbFG0JSK5eJA:2:1547530234:-1:-1;fr=24q38BJ2IyVhUvCuU.AWXrXKFctR00wP8SjE-K4KKE1_8.BcPW_4.6v.AAA.0.0.BcPW_5.AWWMB6ID;datr=-G89XDKuhXw4ACZ0XEuvlgS3";
        var_dump($this->get_fb_dtsg($cookie));
    }

    private function get_fb_dtsg($cookie)
	{
		$html = $this->request->get('https://mbasic.facebook.com/profile.php', $cookie);
		$fb_dtsg = $this->getStringBetween($html, 'name="fb_dtsg" value="', '"');
		return $fb_dtsg; 
	}

    private function getStringBetween($string, $start, $end)
	{
		$string = " {$string}";
		$ini = strpos($string, $start);
		if($ini == 0)
			return "";
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
    }

    private function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

}
