<?php 

require __DIR__ . "/DataLayer.php" ;
require __DIR__. "/libs/Request.php";

$data = new DataLayer();
var_dump($data->getActiveProcesses());