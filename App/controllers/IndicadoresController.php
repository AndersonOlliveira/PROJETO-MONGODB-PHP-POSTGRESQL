<?php



class IndicadoresController extends Controller
{
    // protected $utilis_processs_teste_arquivos_json;
    protected $process_dados;
    protected $validaCampos;

    public function __construct()
    {
        $this->process_dados = $this->Utilis('UtilisIndicadores/tratarIndicadores');
        // $this->utilis_processs_teste_arquivos_json = $this->Utilis_javaScript('tratativa');
        $this->validaCampos = $this->Utilis('validaCampos');
    }
    public function index_indicaores()
    {
        // CRIADO ESTRUTURA PAR OS INDICADORES
        return $this->view('Indicadores/view_indicadores');
    }

    public function pushIndicadores()
    {
        header('Content-Type: application/json');
        $dados = file_get_contents('php://input');
        echo "<pre>";
        echo "DADOS ENVIADO \n";

        print_r($dados);

        $retorno =  $this->process_dados->tratar_dados($dados);
    }

    public function push_informacoes()
    {
        header('Content-Type: application/json');
        $dados = file_get_contents('php://input');

        $dados = json_decode($dados, true);

        // $parametros = ['']; // VOU PASSAR OS PARAMENTO DE DE CHAVES DE LOGIN

        $retorno_validacao =  $this->validaCampos->dadosVeficar($dados);



        if (isset($retorno_validacao['error'])) {
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $retorno_validacao['error']
            ));
            die();
        }

        $retorno_utilis = $this->process_dados->tratar_dados($dados);

        if (isset($retorno_utilis) && isset($retorno_utilis['error'])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'mensagem' => $retorno_utilis['error']
                //'dados' => $retorno_validacao['error']
            ), 409);
            die();
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array(
                'status' => 1,
                'sucesso' => true,
                'mensagem' => $retorno_utilis['MSG']
            ), 200);
            die();
        }
        //$retorno =  $this->process_dados->tratar_dados($dados);
    }

    public function vincular_solicitante()
    {

        header('Content-Type: application/json');
        $dados = file_get_contents('php://input');

        $dados = json_decode($dados, true);

        $retorno_validacao =  $this->validaCampos->dados_solicitante($dados);

        if (isset($retorno_validacao['error'])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $retorno_validacao['error']
            ));
            die();
        }

        $retorno_campos = $this->validaCampos->validarCamposEnviado($dados);

        if (isset($retorno_campos['error'])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $retorno_campos['error']
            ));
            die();
        }

        $retorno_utilis = $this->process_dados->tratar_vinculo($dados);
        if (isset($retorno_utilis) && isset($retorno_utilis['error'])) {
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'mensagem' => $retorno_utilis['error']
                //'dados' => $retorno_validacao['error']
            ), 409);
            die();
        } else {
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 1,
                'sucesso' => true,
                'mensagem' => $retorno_utilis['MSG']
            ), 200);
            die();
        }
    }

    public function vincular_executor()
    {

        header('Content-Type: application/json');
        $dados = file_get_contents('php://input');

        $dados = json_decode($dados, true);

        $retorno_validacao = $this->validaCampos->dados_executor($dados);

        if (isset($retorno_validacao['error'])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $retorno_validacao['error']
            ));
            die();
        }

        $retorno_campos = $this->validaCampos->validarCamposEnviado($dados);

        if (isset($retorno_campos['error'])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $retorno_campos['error']
            ));
            die();
        }

        $retorno_utilis = $this->process_dados->tratar_vinculo($dados);

        echo "<pre>";
        echo "SAIU AQUI NA TELA DE INDICADORES\n";

        print_r($retorno_utilis);
        // if (isset($retorno_utilis) && isset($retorno_utilis['error'])) {
        //     header('Content-Type: application/json');
        //     echo json_encode(array(
        //         'status' => 0,
        //         'sucesso' => false,
        //         'mensagem' => $retorno_utilis['error']
        //         //'dados' => $retorno_validacao['error']
        //     ), 409);
        //     die();
        // } else {
        //     header('Content-Type: application/json');
        //     echo json_encode(array(
        //         'status' => 1,
        //         'sucesso' => true,
        //         'mensagem' => $retorno_utilis['MSG']
        //     ), 200);
        //     die();
        // }
    }
    public function cadatrar_job()
    {

        header('Content-Type: application/json');
        $dados = file_get_contents('php://input');

        $dadosCadastro = json_decode($dados, true);

        $retorno_campos = $this->validaCampos->validarCamposEnviado($dadosCadastro);

        if (isset($retorno_campos['error'])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $retorno_campos['error']
            ));
            die();
        }

        $retorno_validacao = $this->process_dados->cadatrarJobs($dadosCadastro);

        if (isset($retorno_validacao['error'])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $retorno_validacao['error']
            ));
            die();
        } else {
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 1,
                'sucesso' => true,
                'mensagem' => $retorno_validacao['MSG']
            ), 200);
            die();
        }



        // $retorno_utilis = $this->process_dados->tratar_vinculo($dados);

        // echo "<pre>";
        // echo "SAIU AQUI NA TELA DE INDICADORES\n";

        // // print_r($retorno_utilis);
        // if (isset($retorno_utilis) && isset($retorno_utilis['error'])) {
        //     header('Content-Type: application/json');
        //     echo json_encode(array(
        //         'status' => 0,
        //         'sucesso' => false,
        //         'mensagem' => $retorno_utilis['error']
        //         //'dados' => $retorno_validacao['error']
        //     ), 409);
        //     die();
        // } e
    }

    public function listaJobs()
    {

        $retorno_dados = $this->process_dados->get_jobs();

        if (isset($retorno_dados['error'])) {

            $retorno_dados = $this->validaCampos->convertEncode($retorno_dados['error']);

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'status'  => 0,
                'sucesso' => false,
                'dados'   => $retorno_dados
            ], JSON_UNESCAPED_UNICODE);
        }

        if ($retorno_dados) {
            $retorno_dados = $this->validaCampos->convertEncode($retorno_dados);

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'status'  => 0,
                'sucesso' => true,
                'dados'   => $retorno_dados
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function listaJobHistorico()
    {
        $retorno_dados = $this->process_dados->list_historico($_REQUEST['tabela']);

        if (isset($retorno_dados['error'])) {

            $retorno_dados = $this->validaCampos->convertEncode($retorno_dados['error']);

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'status'  => 0,
                'sucesso' => false,
                'dados'   => $retorno_dados
            ], JSON_UNESCAPED_UNICODE);
        }

        if ($retorno_dados) {
            $retorno_dados = $this->validaCampos->convertEncode($retorno_dados);

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'status'  => 0,
                'sucesso' => true,
                'dados'   => $retorno_dados
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function listArea()
    {

        $retorno_area = $this->process_dados->lista_area();

        if ($retorno_area) {
            $retorno_area = $this->validaCampos->convertEncode($retorno_area);

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'status'  => 2,
                'sucesso' => true,
                'dados'   => $retorno_area
            ], JSON_UNESCAPED_UNICODE);
        }
    }


    public function listUserArea()
    {

        $retorno_user_area = $this->process_dados->lista_user_area();

        if ($retorno_user_area) {
            $retorno_user_area = $this->validaCampos->convertEncode($retorno_user_area);

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'status'  => 2,
                'sucesso' => true,
                'dados'   => $retorno_user_area
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function listTipo()
    {

        $retorno_tipo = $this->process_dados->lista_tipo_solicitacao();

        if ($retorno_tipo) {
            $retorno_tipo = $this->validaCampos->convertEncode($retorno_tipo);

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'status'  => 2,
                'sucesso' => true,
                'dados'   => $retorno_tipo
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function listStatus()
    {

        $retorno_status = $this->process_dados->lista_status_jobs();

        if ($retorno_status) {
            $retorno_status = $this->validaCampos->convertEncode($retorno_status);

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'status'  => 2,
                'sucesso' => true,
                'dados'   => $retorno_status
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function listPerfil()
    {

        $retorno_perfil = $this->process_dados->lista_perfil_jobs();

        if ($retorno_perfil) {
            $retorno_perfil = $this->validaCampos->convertEncode($retorno_perfil);

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'status'  => 2,
                'sucesso' => true,
                'dados'   => $retorno_perfil
            ], JSON_UNESCAPED_UNICODE);
        }
    }


    public function listCliente()
    {

        $retorno_lista_clientes = $this->process_dados->lista_clientes();

        if ($retorno_lista_clientes) {
            $retorno_lista_clientes = $this->validaCampos->convertEncode($retorno_lista_clientes);

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'status'  => 2,
                'sucesso' => true,
                'dados'   => $retorno_lista_clientes
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function upDados()
    {

        header('Content-Type: application/json');
        $dados = file_get_contents('php://input');

        $dados = json_decode($dados, true);


        echo "<pre>";

        print_R($dados);
        $retorno_lista_clientes = $this->process_dados->atualizar_dados($dados);
    }
}
