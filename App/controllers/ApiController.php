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

    public function inserir_info_cancelados_true()
    {

        $input = file_get_contents('php://input');

        $type_condific  = $_SERVER['CONTENT_TYPE'];
        if (stripos($type_condific, 'application/json') !== false) {
            $data = $input;
            // echo 'saida no opreim';
        } else {
            // echo 'saida aqui ou aqui ';
            $data = json_encode($_POST, true);
        }

        $retorno_dados = $this->Process_api->index_cancelar($data);

        if ($retorno_dados) {
            // echo "<pre>";
            // return 'informação salva com sucesso!';
            echo json_encode(array(
                'sucesso' => true,
                'mensagem' => "Processo paralisado com sucesso!"
            ));
        }
    }
    public function inserir_info_paralizados_reprocessar()
    {

        $input = file_get_contents('php://input');


        $type_condific  = $_SERVER['CONTENT_TYPE'];
        if (stripos($type_condific, 'application/json') !== false) {
            $data = $input;
        } else {
            $data = $_POST;
        }
        // vou reprocesar o job

        $retorno_dados = $this->Process_api->insert_all_paralizar_reprocesar($data);

        if ($retorno_dados) {
            // echo "<pre>";
            // return 'informação salva com sucesso!';
            echo json_encode(array(
                'sucesso' => true,
                'mensagem' => "Processo Reiniciado com sucesso!"
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

    public function verificar_data_reprocessar($idProcess, $contrato)
    {

        $retorno_busca = $this->Process_api->get_dados_finger_parar($idProcess, $contrato);

        if ($retorno_busca) {

            http_response_code(422);

            $response = [
                'sucesso' => false,
                'status'  => 1,
                'reprocessar' => 1,
                'mensagem' => 'Tempo para solicitar o reprocessamento expirou'
            ];
        } else {


            http_response_code(200);

            $response = [
                'sucesso' => true,
                'status'  => 0,
                'reprocessar' => 0,
                'mensagem' => 'Pode seguir com o reprocessamento'
            ];
        }

        ob_clean();
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        die();
    }

    public function push_status_dies()
    {
        $input = file_get_contents('php://input');

        $type_condific  = $_SERVER['CONTENT_TYPE'];

        if (stripos($type_condific, 'application/json') !== false) {

            $data = $input;
        } else {
            $data = $_POST;
        }
        //processar os dados para salvar dentro do json pois a estrura esta assim.
        $retorno_json = $this->Process_api->die_status_process($data);

        if (isset($retorno_json)) {
            http_response_code(200);
            ob_clean();
            echo json_encode([
                'sucesso' => true,
                'status'  => 0,
                'reprocessar' => 1,
                'mensagem' => 'Solicitação efetuada com sucesso!'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            die();
        }
    }

    public function testes()
    {
        print_r('CONSIGO ACESSAR A API');
    }
    public function info_reprocess($idProcess)
    {
        $retorno = $this->Process_api->info_reprocess_dados($idProcess);

        http_response_code(200);
        ob_clean();
        echo json_encode([
            'sucesso' => true,
            'data' => $retorno

        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        exit;
    }

    public function info_paralizar_die($idProcess)
    {
        $retorno = $this->Process_api->paralizar_date_info($idProcess);

        if ($retorno) {

            http_response_code(200);
            ob_clean();
            echo json_encode([
                'sucesso' => true,
                'data' => $retorno

            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            exit;
        } else {

            http_response_code(422);
            ob_clean();
            echo json_encode([
                'sucesso' => false,
                'data' => $retorno

            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            exit;
        }
    }

    //aqui vou criar uma api para gerar novamente os dados e criar o arquivo corretamente e atualizar os dados dentro da tabela de processos
    public function gerar_novo_arquivo($id)
    {

        echo "<pre>";
        echo "meu id enviado\n";

        $qtLimit = 1;
        print_r($id);

        $pegar_dados_parados = $this->utilis_pgadmin->list_processo_parar($id, $qtLimit, false);

        echo "<pre>";

        print_r($pegar_dados_parados);
    }
}
