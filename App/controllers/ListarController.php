<?php


class ListarController extends Controller {

    protected $utils;
    protected $tratamento;


     public function __construct()
     {
        // require_once __DIR__ . '/../Utilis/Arquivos.php';
        // $this->tratamento = $this->Utilis('Arquivos');
        
         $this->utils = $this->Utilis('Arquivos');
        // $this->utils->teste();

        // Carrega de App/Arquivos ()
        // $this->tratamento = $this->loadFrom('Arquivos', 'Arquivos');

        
     }
    
    
     public function listar(){

        $return = $this->model('process');
        $returns = $return->list_processo();


        
        $re = $this->utils->get_dados_id($returns);
        // $re = $this->utils->teste();



        return $this->view('listar' , ['usuarios' =>  $re]);
    } 
    
     public function listar_id($id = null){

                echo $id;
              $result_mongo = $this->model('instance');
         
             $result_mongo = $result_mongo->findById($id);

              echo "<pre>";

            //   print_r($result_mongo);

              foreach($result_mongo as $key => $valores){

                echo "<pre>";

                print_r($key . $valores);
              }
        //    return $this->view('listar', $result_mongo);
        
    }
}