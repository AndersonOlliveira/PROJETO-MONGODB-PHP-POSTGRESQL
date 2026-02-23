<?php


class ApiController extends Controller
{


    protected $utilss;
    protected $Process_api;
    protected $utilis_pgadmin;


    public function __construct()
    {
        $this->utilss = new Instance();

        $this->Process_api = $this->Utilis('Process_api');

        require_once __DIR__ . '/../models/process.php';
        $this->utilis_pgadmin = new process();
    }

    public function inserir_info_paralizado()
    {

        $input = file_get_contents('php://input');

        $data = json_decode($input, true);

        echo "<pre>";
        echo "tenho o resultado aqui\n";
        print_r($data);

        $retorno_dados = $this->Process_api->index($input);

        if ($retorno_dados) {
            echo "<pre>";
            return 'informação salva com sucesso!';
        }
    }

    public function inserir_info_paralizados()
    {

    }
}
