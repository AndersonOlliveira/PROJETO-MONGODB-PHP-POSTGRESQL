<?php

require_once __DIR__ . '/../models/instance.php';
class Controller
{


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


    public function view($view, $data = [])
    {
        extract($data);
        require_once "App/views/$view.php";
    }

    public function model($model)
    {
        require_once "App/models/$model.php";
        return new $model();
    }

    public function Utilis($className)
    {
        //para windows

        // echo "<pre>";

        // print_R($className);
        $file = __DIR__ . "../../Utilis/{$className}.php";
        // $file = __DIR__ . "/../Utilis/{$className}.php";
//
        if (!file_exists($file)) {
            throw new \Exception("Arquivo {$file} não encontrado! \n");
        }

        require_once $file;

        if (!class_exists($className)) {
            throw new \Exception("Classe {$className} não encontrada dentro do arquivo! \n");
        }

        return new $className();
    }

    public function Utilis_arquivo($nameArchive)
    {

        //$file = __DIR__ . "../../Utilis/{$className}.php";
        $file = __DIR__ . "/../Arquivos/{$nameArchive}.csv";

        if (!file_exists($file)) {
            throw new \Exception("Arquivo {$file} não encontrado! \n");
        }

        return $file;
    }
    public function Utilis_arquivo_json($nameArchiveJson)
    {

        $file = __DIR__ . "/../Arquivos/{$nameArchiveJson}.json";

        if (!file_exists($file)) {
            throw new \Exception("Arquivo {$file} não encontrado! \n");
        }

        return $file;
    }

    public function loadFrom($folder, $className)
    {
        $file = __DIR__ . "/../{$folder}/{$className}.php";

        if (!file_exists($file)) {
            throw new \Exception("Arquivo {$file} não encontrado!");
        }

        require_once $file;

        if (!class_exists($className)) {
            throw new \Exception("Classe {$className} não encontrada dentro do arquivo!");
        }

        return new $className();
    }

    protected function MongoConect($modelName)
    {
        $modelClass = 'App\models\\' . $modelName . '.php';


        if (class_exists($modelClass)) {
            // Se o seu model 'instance' tem um construtor, basta instanciar:
            return new $modelClass();
        }

        // Se houver falha, lida com o erro
        // die("Modelo MongoDB '{$modelName}' não encontrado ou não pôde ser carregado.");
    }
}
