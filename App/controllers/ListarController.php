<?php


class ListarController extends Controller {
    
    
     public function listar(){

        return $this->view('listar');
        
    } 
    
     public function listar_id($id = null){

          $dados = ['lista' => $id];
          
           return $this->view('listar', $dados);
        
    }
}