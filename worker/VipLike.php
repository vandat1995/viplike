<?php 

require __DIR__ . "/DataLayer.php";
require __DIR__ . "/libs/Request.php";

class Task extends Threaded
{

    public $data;
    public $request;

    public function __construct($val, $request)
    {
        $this->data = $val;
        $this->request = $request;
    }

    public function run()
    {
        $this->__reactionPost($this->data["post_id"], $this->data["reaction"], $this->data["token"], $this->data["token_id"], $this->data["id"]);
    }

    public function __reactionPost($post_id, $reaction, $token, $token_id, $tpm_id)
    {
        $url = "https://graph.facebook.com/{$post_id}/reactions?type={$reaction}&access_token={$token}&method=post";
        $response = json_decode($this->request->get($url), true);
        if (isset($response["error"])) {
            if (isset($response["error"]["code"]) && $response["error"]["code"] == 190) {
                $this->updateTokenDieAndStatus($token_id, $tpm_id, 0);
            } else {
                $this->updateTokenDieAndStatus(false, $tpm_id, 0);
            }
        } else {
            $this->updateTokenDieAndStatus(false, $tpm_id, 1);
        }
    }

    public function updateTokenDieAndStatus($token_id = false, $tpm_id, $tpm_status)
    {
        $conn = mysqli_connect("localhost", "viplikesim", "viplikesim", "viplikesim");
        if ($token_id) {
            mysqli_query($conn, "UPDATE tokens set status = 0 where id = " . $token_id);
        }
        mysqli_query($conn, "UPDATE token_process_map set status = " . $tpm_status . ", is_runned = 1 where id = " . $tpm_id);
        mysqli_close($conn);
    }
}

// $p = new Pool(1);
// $p->submit(new Task(2, 2));
// while($p->collect());
// $p->shutdown();

//$start_time = microtime(TRUE);

$model = new DataLayer();
$p = new Pool(5);
$processes = $model->getActiveProcesses();

if (count($processes) > 0) {
    foreach ($processes as $process) {
        $datas = $model->getRandByProcessId($process["id"], $process["quantity_per_cron"]);

        if (count($datas) > 0) {
            $request = new Request();
            foreach ($datas as $data) {
                $p->submit(new Task($data, $request));
                // $model->updateTokenProcessMap($data["id"], ["status" => 1, "is_runned" => 1]);
            }
        } else {
            $model->updateProcess($process["id"], ["is_done" => 1]);
        }
    }
}
while ($p->collect());
$p->shutdown();


//$end_time = microtime(TRUE);
