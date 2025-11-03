<?php


class ListarController extends Controller {
    
    
     public function listar(){

        return $this->view('listar');
        
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