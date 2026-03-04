<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

    public function inserir_info_paralizados_true()
    {

        $input = file_get_contents('php://input');


        $type_condific  = $_SERVER['CONTENT_TYPE'];
        if (stripos($type_condific, 'application/json') !== false) {
            $data = $input;
        } else {
            $data = $_POST;
        }


        $retorno_dados = $this->Process_api->index($data);

        if ($retorno_dados) {
            // echo "<pre>";
            // return 'informação salva com sucesso!';
            echo json_encode(array(
                'sucesso' => true,
                'mensagem' => "Processo paralisado com sucesso!"
            ));
        }
    }

    public function inserir_info_paralizados() {}


    public function push_fingers($contrato)
    {

        if (!isset($contrato)) {

            http_response_code(422);
            ob_clean();
            echo json_encode([
                'status'  => 0,
                'message' => 'Número do contrato Precisa ser Enviado'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            die();
        }


        $retorno_busca = $this->Process_api->get_dados_finger($contrato);
        if ($retorno_busca) {

            http_response_code(200);
            ob_clean();
            echo json_encode([
                'status'  => 0,
                'dados' => $retorno_busca,
                'message' => 'Sucesso em consultar os dados'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            die();
        }

        // $contrato = $_GET['contrato'] ?? null;

        // echo "<pre>";
        // var_dump($contrato);
        // // $type_condific  = $_SERVER['CONTENT_TYPE'];
        // if (stripos($type_condific, 'application/json') !== false) {
        //     $data = $input;
        // } else {
        //     $data = $_POST;
        // }

        // echo "<pre>";

        // // print_r('')
        // var_dump($data);
    }
}
