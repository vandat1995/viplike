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
        // $this->__reactionPost($this->data["post_id"], $this->data["reaction"], $this->data["token"]);
        $this->test();
    }

    public function test()
    {
        //echo __DIR__;die;
        
    }

    public function __reactionPost($post_id, $reaction, $token)
    {
        //$request = new Request();
        $url = "https://graph.facebook.com/{$post_id}/reactions?type={$reaction}&access_token={$token}&method=post";
        $response = json_decode($this->request->get($url), true);
        if()
    }

    public function updateTokenDie($token_id)
    {
        require __DIR__ . "/vendor/autoload.php";
        $connection = new PDO('mysql:host=localhost;dbname=viplikedat', 'root', '');
        $h = new \ClanCats\Hydrahon\Builder('mysql', function($query, $queryString, $queryParameters) use($connection)
        {
            $statement = $connection->prepare($queryString);
            $statement->execute($queryParameters);
            
            if ($query instanceof \ClanCats\Hydrahon\Query\Sql\FetchableInterface)
            {
                return $statement->fetchAll(\PDO::FETCH_ASSOC);
            }
        });
        $tokens = $h->table("tokens");
        $tokens->update(["status" => 0])->where("id", $token_id)->execute();
    }
}

$p = new Pool(1);
$p->submit(new Task(2, 2));
while($p->collect());
$p->shutdown();

//$start_time = microtime(TRUE);

// $model = new DataLayer();
// $p = new Pool(10);
// $processes = $model->getActiveProcesses();
// if(count($processes) > 0)
// {
//     foreach($processes as $process)
//     {
//         $datas = $model->getRandByProcessId($process["id"], $process["quantity_per_cron"]);
//         if(count($datas) > 0)
//         {
//             $request = new Request();
//             foreach($datas as $data)
//             {
//                 $p->submit(new Task($data, $request));
//                 $model->updateTokenProcessMap($data["id"], ["status" => 1, "is_runned" => 1]);
//             }
//         }
//         else
//         {
//             $model->updateProcess($process["id"], ["is_done" => 1]);
//         }
//     }
// }
// while($p->collect());
// $p->shutdown();


//$end_time = microtime(TRUE);
//echo $end_time - $start_time;