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
    protected $validaCampos;


    public function __construct()
    {
        $this->utilss = new Instance();

        $this->Process_api = $this->Utilis('Process_api');

        require_once __DIR__ . '/../models/Crc_tratativas.php';
        $this->utilis_pgadmin = new Crc_tratativas();

        //PAGINA DE TRATAMENTO DOS DADOS
        $this->utilis_trativas = $this->Utilis('process_Trativas');
        $this->validaCampos = $this->Utilis('validaCampos');
    }
    public function get_tratativas()
    {
        header('Content-Type: application/json');


        $retorno_dados = $this->utilis_pgadmin->listTipoContrato();

        if ($retorno_dados) {
            $retorno_dados  = $this->validaCampos->convertEncode($retorno_dados);
            echo json_encode(array(
                'status' => 2,
                'sucesso' => true,
                'dados' => $retorno_dados
            ));
        }
    }

    public function nameResponsavel()
    {
        header('Content-Type: application/json');

        $contrato = $_GET['contrato'];


        $retorno_dados = $this->utilis_pgadmin->info_responsavel($contrato);

        if ($retorno_dados) {
            $retorno_dados  = $this->validaCampos->convertEncode($retorno_dados);
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


        $dados = json_decode($_GET['convertidoJson'], true);

        $retorno_dados = $this->utilis_trativas->seachDataAll($dados);
        //chamo a funcao para popular a base
        // self::tratar_Relatorio();

        // $retorno_dados = $this->utilis_pgadmin->getRelatorio();
        if (isset($retorno_dados['msg'])) {
            echo json_encode(array(
                'status' => 1,
                'sucesso' => false,
                'dados' => $retorno_dados
            ));
        } else {

            $retorno_dados  = $this->validaCampos->convertEncode($retorno_dados);

            echo json_encode(array(
                'status' => 2,
                'sucesso' => true,
                'dados' => $retorno_dados
            ));
        }
    }

    public function RelatorioAcoes()
    {


        header('Content-Type: application/json');


        $retorno_dados = $this->utilis_pgadmin->listTipoAcoes();

        if ($retorno_dados) {

            $retorno_dados  = $this->validaCampos->convertEncode($retorno_dados);

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

        $dados['tipo_acoes'] = 0;
        //AQUI VERIFICA OS CAMPOS ENVIADOS SE ESTA CORRETO
        $retorno_campos  =  $this->utilis_trativas->validaCampos($dados);
        //VERIFICA OS DADOS DENTRO DO CAMPOS
        $retorno_verificacao_campos  =  $this->utilis_trativas->validaCampoDados($dados);

        if (isset($retorno_verificacao_campos['error'])) {
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $retorno_verificacao_campos['error']
            ));
            die();
        }


        if (isset($retorno_campos['error'])) {
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $retorno_campos
            ));
            die();
        }


        $retorno_dados = $this->utilis_trativas->trata_dados_trativa($dados);

        if ($retorno_dados['status'] == 'success') {
            $retorno_dados  = $this->validaCampos->convertEncode($retorno_dados);
            echo json_encode(array(
                'status' => 2,
                'sucesso' => true,
                'dados' => $retorno_dados
            ));
        } else {
            echo json_encode(array(
                'status' => 1,
                'sucesso' => false,
                'dados' => $retorno_dados
            ));
        }
    }

    public function tratar_Relatorio()
    {

        $retorno = $this->utilis_pgadmin->getRelatorio_origim();


        foreach ($retorno as $key => $result) {

            if (isset($result['n_nro'])) {
                $processCobranca = $this->utilis_pgadmin->verifry_cobraca($result['n_nro']);
            }
        }
    }


    public function list_dados_data()
    {

        header('Content-Type: application/json');
        $dados = file_get_contents('php://input');
        // PARA CONVERTER OS DADOS VINDO DE FORM DATA
        $dados = json_decode($dados, true);
        $retorno_dados = $this->utilis_trativas->seachDataAll($dados);

        if ($retorno_dados) {

            if (isset($retorno_dados['msg'])) {
                echo json_encode(array(
                    'status' => 1,
                    'sucesso' => false,
                    'dados' => $retorno_dados
                ));
            } else {
                $retorno_dados  = $this->validaCampos->convertEncode($retorno_dados);
                echo json_encode(array(
                    'status' => 2,
                    'sucesso' => false,
                    'dados' => $retorno_dados
                ));
            }
        }
    }

    public function getHistorico()
    {

        header('Content-Type: application/json');
        $dados = file_get_contents('php://input');
        // PARA CONVERTER OS DADOS VINDO DE FORM DATA

        $dados = json_decode($_GET['convertidoJson'], true);
        // $dados = json_decode($dados, true);

        $retorno_dados = $this->utilis_trativas->validaCamposPersolizado($dados, 'numeroCobranca');

        if (isset($retorno_dados['error'])) {
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $retorno_dados['error']
            ));
            die();
        }


        //BUSCO OS DADOS POR NUMERO DA COBRANCA

        $retorno_dados_historico = $this->utilis_pgadmin->getRelatorioAll($dados['numeroCobranca']);

        if ($retorno_dados_historico) {

            if (isset($retorno_dados_historico['msg'])) {
                echo json_encode(array(
                    'status' => 1,
                    'sucesso' => false,
                    'dados' => $retorno_dados_historico
                ));
            } else {
                $retorno_dados_historico  = $this->validaCampos->convertEncode($retorno_dados_historico);
                echo json_encode(array(
                    'status' => 2,
                    'sucesso' => true,
                    'dados' => $retorno_dados_historico
                ));
            }
        }
    }
    public function supensao()
    {
        header('Content-Type: application/json');
        $dados = file_get_contents('php://input');
        // PARA CONVERTER OS DADOS VINDO DE FORM DATA

        $dados = json_decode($_GET['convertidoJson'], true);

        $retorno_suspensao = $this->utilis_pgadmin->list_supensao($dados['tdataInial'], $dados['tdfim']);

        if ($retorno_suspensao) {

            if (isset($retorno_dados_historico['msg'])) {
                echo json_encode(array(
                    'status' => 1,
                    'sucesso' => false,
                    'dados' => $retorno_suspensao
                ));
            } else {

                $retorno_suspensao  = $this->validaCampos->convertEncode($retorno_suspensao);

                echo json_encode(array(
                    'status' => 2,
                    'sucesso' => true,
                    'dados' => $retorno_suspensao
                ));
            }
        }
    }
}
