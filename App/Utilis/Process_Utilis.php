<?php



class Process_Utilis{

    protected $utilis_process;

    public function __construct(){

    require_once __DIR__ . '/../models/process.php';
	$this->utilis_process = new process();

    }
   
    public function get_query(){

    $teste = $this->utilis_process->get_query_all();
     
       echo "<pre>";

       print_r($teste);
    }

}