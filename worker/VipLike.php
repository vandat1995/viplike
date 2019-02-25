<?php 

require __DIR__ . "/DataLayer.php" ;
require __DIR__. "/libs/Request.php";


class Task extends Threaded {

    public $data;
    public $request;

    public function __construct($val, $request) {
        $this->data = $val;
        $this->request = $request;
    }

    public function run()
    {
        $this->__reactionPost($this->data["post_id"], $this->data["reaction"], $this->data["token"]);
    }

    public function __reactionPost($post_id, $reaction, $token)
    {
        //$request = new Request();
        $url = "https://graph.facebook.com/{$post_id}/reactions?type={$reaction}&access_token={$token}&method=post";
        $this->request->get($url);
    }
}
//$start_time = microtime(TRUE);
$model = new DataLayer();
$request = new Request();
$p = new Pool(20);
$processes = $model->getActiveProcesses();
if(count($processes) > 0)
{
    foreach($processes as $process)
    {
        $datas = $model->getRandByProcessId($process["id"], $process["quantity_per_cron"]);
        if(count($datas) > 0)
        {
            foreach($datas as $data)
            {
                $p->submit(new Task($data, $request));
                $model->updateTokenProcessMap($data["id"], ["status" => 1, "is_runned" => 1]);
            }
        }
        else
        {
            $model->updateProcess($process["id"], ["is_done" => 1]);
        }
    }
}
while($p->collect());
$p->shutdown();
//$end_time = microtime(TRUE);
//echo $end_time - $start_time;