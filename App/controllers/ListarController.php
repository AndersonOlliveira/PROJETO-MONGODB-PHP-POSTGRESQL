<?php


class ListarController extends Controller {

    protected $utils;
    protected $utils_mongo;
    protected $tratamento;


     public function __construct()
     {
        // require_once __DIR__ . '/../Utilis/Arquivos.php';
        // $this->tratamento = $this->Utilis('Arquivos');
        
         $this->utils = $this->Utilis('Arquivos');
         $this->utils_mongo = $this->Utilis('Mongo');
        // $this->utils->teste();

        // Carrega de App/Arquivos ()
        // $this->tratamento = $this->loadFrom('Arquivos', 'Arquivos');

        
     }
    
    
     public function listar($idProcesso = null, $qtLimit = null){

       
        $return = $this->model('process');
        $returns = $return->list_processo($idProcesso, $qtLimit);

        if(empty($returns)){
            echo "Nenhum dado encontrado!\n";
            // return;
        }
         $re = $this->utils->get_dados_id($returns);
        // echo "Listando dados..\n";
        // echo print_r($returns);
      
        //  return $this->view('listar' , ['usuarios' =>  $re]);
         return $this->view('listar');
    } 
    
     public function listar_id($id = null){
      $result_mongo = $this->model('instance');
      $result_mongo = $result_mongo->findById($id);
      foreach($result_mongo as $key => $valores){
 
      }
        //    return $this->view('listar', $result_mongo);
        
    }

    public function mongo()
    {

        $re = $this->utils_mongo->get_dada_all();
        

      return $this->view('List_dados_mongo');
    }
}