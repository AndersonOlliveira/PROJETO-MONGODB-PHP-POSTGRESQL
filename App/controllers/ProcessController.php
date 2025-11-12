<?php


class ProcessController extends Controller {
    
    protected $utilis_process;
    protected $utilis_processs;

    public function __construct()
    {
        $this->utilis_process = $this->Utilis('Process_Utilis');
        $this->utilis_processs = $this->Utilis('teste');
    }   

    public function get_all_query(){

        $return = $this->utilis_process->get_query();
        
        echo "<pre>";

        print_r($return);

        return $this->view('Query_active');



    } 
        public function get_all_teste(){

        $return = $this->utilis_processs->lista_testessss();
        
      

        return $this->view('listar_teste');



    } 
    


}
