<?php
require __DIR__ . "/vendor/autoload.php";
use ClanCats\Hydrahon\Query\Expression as Ex;

class DataLayer {
        
    protected $tokens;
    protected $processes;
    protected $token_process_map;
    
    public function __construct()
    {
        $connection = new PDO('mysql:host=localhost;dbname=viplikedat', 'root', '');
        $h = new \ClanCats\Hydrahon\Builder('mysql', function($query, $queryString, $queryParameters) use($connection)
        {
            $statement = $connection->prepare($queryString);
            $statement->execute($queryParameters);
            
            // when the query is fetchable return all results and let hydrahon do the rest
            if ($query instanceof \ClanCats\Hydrahon\Query\Sql\FetchableInterface)
            {
                return $statement->fetchAll(\PDO::FETCH_ASSOC);
            }
        });
        $this->tokens = $h->table("tokens");
        $this->processes = $h->table("processes");
        $this->token_process_map = $h->table("token_process_map");
    }

    public function getActiveProcesses()
    {
        return $this->processes->select("processes.id, processes.vip_type, processes.task_id, processes.post_id, tasks.quantity, tasks.quantity_per_cron")
        ->join("tasks", "tasks.id", "=", "processes.task_id")
        ->where("processes.is_done", 0)
        ->get();
    }

    public function getRandByProcessId($process_id, $limit)
    {
        return $this->token_process_map->select("token_process_map.id, token_process_map.reaction, tokens.token, processes.post_id")
        ->join("processes", "processes.id", "=", "token_process_map.process_id")
        ->join("tokens", "tokens.id", "=", "token_process_map.token_id")
        ->where("processes.vip_type", "like")
        ->where("token_process_map.process_id", $process_id)
        ->where("token_process_map.is_runned", 0)
        ->limit($limit)
        ->orderBy(new Ex("RAND()"))
        ->get();
    }

    public function updateProcess($id, $data)
    {
        return $this->processes->update($data)->where("id", $id)->execute();
    }

    public function updateTokenProcessMap($id, $data)
    {
        return $this->token_process_map->update()->set($data)->where("id", $id)->execute();
    }
    
    
    
}