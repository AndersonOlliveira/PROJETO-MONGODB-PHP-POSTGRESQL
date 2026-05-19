<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ApiControllerTratativas extends Controller
{


    protected $utilss;
    protected $Process_api;
    protected $utilis_pgadmin;
    protected $utilis_trativas;


    public function __construct()
    {
        $this->utilss = new Instance();

        $this->Process_api = $this->Utilis('Process_api');

        require_once __DIR__ . '/../models/Crc_tratativas.php';
        $this->utilis_pgadmin = new Crc_tratativas();

        //PAGINA DE TRATAMENTO DOS DADOS
        // require_once __DIR__ . '/../models/Crc_tratativas.php';
        $this->utilis_trativas = $this->Utilis('process_Trativas');
    }

    public function get_tratativas()
    {
        header('Content-Type: application/json');

        $retorno_dados = $this->utilis_pgadmin->get_lista_tratativas();

        if ($retorno_dados) {
            // echo "<pre>";
            // return 'informação salva com sucesso!';
            echo json_encode(array(
                'status' => 2,
                'sucesso' => true,
                'dados' => $retorno_dados
            ));
        }
    }
    public function tratativasRelatorio()
    {
        header('Content-Type: application/json');

        $retorno_dados = $this->utilis_pgadmin->getRelatorio();

        if ($retorno_dados) {
            // echo "<pre>";
            // return 'informação salva com sucesso!';
            echo json_encode(array(
                'status' => 2,
                'sucesso' => true,
                'dados' => $retorno_dados
            ));
        }
    }


    public function tratativaInsert()
    {
        header('Content-Type: application/json');

        // echo "O método é: " . htmlspecialchars($_SERVER['REQUEST_METHOD']);

        $dados = file_get_contents('php://input');
        // PARA CONVERTER OS DADOS VINDO DE FORM DATA
        $dados = json_decode($dados, true);


        // echo "<pre>";
        // echo "MEUS DADOS\n";

        // print_r($dados);
        // die();

        $retorno_dados = $this->utilis_trativas->trata_dados_trativa($dados);

        // if ($retorno_dados) {
        //     // echo "<pre>";
        //     // return 'informação salva com sucesso!';
        //     echo json_encode(array(
        //         'status' => 2,
        //         'sucesso' => true,
        //         'dados' => $retorno_dados
        //     ));
        // }
    }

    public function tratar_Relatorio()
    {

        $retorno = $this->utilis_pgadmin->getRelatorio_origim();

        // echo "<pre>";

        // print_r($retorno);

        // die();

        foreach ($retorno as $key => $result) {

            // echo '<pre>';
            // print_r($result);
            if (isset($result['n_nro'])) {
                // echo "<pre>";
                // echo "ESTOU SAINDO DENTRO DA ROTA PARA A CONSULTA";

                $processCobranca = $this->utilis_pgadmin->verifry_cobraca($result['n_nro']);

                // echo "</pre>";
            }
        }
    }


    public function list_dados_data(){
        
    header('Content-Type: application/json');
    $dados = file_get_contents('php://input');
        // PARA CONVERTER OS DADOS VINDO DE FORM DATA
    $dados = json_decode($dados, true);
    $retorno_dados = $this->utilis_trativas->seachDataAll($dados);

    if($retorno_dados){
        
    if(isset($retorno_dados['msg'])){
     echo json_encode(array(
                'status' => 1,
                'sucesso' => false,
                'dados' => $retorno_dados
            ));

    }else{
                echo json_encode(array(
                'status' => 2,
                'sucesso' => false,
                'dados' => $retorno_dados
            ));
    }
     
        } 
    }
}
