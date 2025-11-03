<?php

require_once __DIR__ . '/../models/instance.php';
class Controller {

    
    private $model;

    public function __construct()
    {
        // $this->model = new instance();
    }
    //  public function index()
    // {
    //     $usuarios = $this->model->all();
    //     foreach ($usuarios as $u) {
    //         echo "Nome: " . $u->nome . "<br>";
    //  }
    
    // }
     

    public function view($view, $data = []) {
        extract($data);
        require_once "App/views/$view.php";
    }

    public function model($model) {
        require_once "App/models/$model.php";
        return new $model();
    }

    protected function MongoConect($modelName)
    {

        echo "<pre>";
        echo $modelName;
                // 1. Constrói o caminho e o nome completo da classe
        // Ex: 'instance' se torna '\App\models\instance'
        $modelClass = 'App\models\\' . $modelName . '.php'; 

        echo "<pre>";
        echo $modelClass;

        
        // 2. Verifica se a classe existe e a retorna (Instancia o Singleton)
        if (class_exists($modelClass)) {
            // Se o seu model 'instance' tem um construtor, basta instanciar:
            return new $modelClass(); 
        }
        
        // Se houver falha, lida com o erro
        // die("Modelo MongoDB '{$modelName}' não encontrado ou não pôde ser carregado.");
    }
}