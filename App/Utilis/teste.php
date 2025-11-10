<?php


Class teste extends Controller{




    protected $teste;


    public function __construct()
    {

        $this->teste = $this->model('process');
    }


    public function lista_testessss(){

        // $retorno = $this->teste->get_teste();

        // return $this->view('listar_teste' , ['teste' =>  'dasdasdas']);

    }
}



?>