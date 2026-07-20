
<?php



class GrupoEconController extends Controller
{
    protected $process_dados;
    protected $validaCampos;

    public function __construct()
    {
        $this->process_dados = $this->Utilis('GrupoEconomico/grupoEconomicoUtilis');

        $this->validaCampos = $this->Utilis('validaCamposEconomico');
    }
    public function index_grupo_economico()
    {
        // CRIADO ESTRUTURA PARA O VIEW
        return $this->view('grupo/view_grupo');
    }
    public function index_grupo_economico_list()
    {
        // CRIADO ESTRUTURA PARA O VIEW
        return $this->view('grupo/view_grupo_dados');
    }


    public function search_clientes()
    {

        header('Content-Type: application/json');


        $dados = json_decode($_GET['playloadJson'], true);
        $retorno_validacao =  $this->validaCampos->validarCamposEnviado($dados);

        if (isset($retorno_validacao['error'])) {
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $retorno_validacao['error']
            ), 409);
            die();
        }

        $retorno = $this->process_dados->search($dados);

        if ($retorno) {
            $retorno = $this->validaCampos->convertEncode($retorno);

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'status'  => 2,
                'sucesso' => true,
                'rota' => 'viewDadosGrupo',
                'dados'   => $retorno
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'status'  => 0,
                'sucesso' => false,

                'dados'   => 'Falha em localizar dados informados!!'
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    public function search_clientes_limites()
    {

        $dados = $_REQUEST['id_contrato'];

        // $retorno_validacao =  $this->validaCampos->validarCamposEnviado($dados);

        // if (isset($retorno_validacao['error'])) {
        //     header('Content-Type: application/json');
        //     echo json_encode(array(
        //         'status' => 0,
        //         'sucesso' => false,
        //         'dados' => $retorno_validacao['error']
        //     ), 409);
        //     die();
        // }
        $retorno = $this->process_dados->search_limite($dados);

        if ($retorno) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'status'  => 2,
                'sucesso' => true,
                'dados'   => $retorno
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function cad_clientes_limites()
    {
        header('Content-Type: application/json');

        $dados = file_get_contents('php://input');
        // PARA CONVERTER OS DADOS VINDO DE FORM DATA
        $dados = json_decode($dados, true);

        $dados_enviados = $this->validaCampos->dadosVeficar($dados);
        if (isset($dados_enviados['error'])) {
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $dados_enviados['error']
            ), 409);
            die();
        }



        $retorno_validacao =  $this->validaCampos->validarCamposEnviado($dados);

        if (isset($retorno_validacao['error'])) {
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $retorno_validacao['error']
            ), 409);
            die();
        }

        $retorno_processamento = $this->process_dados->process_dados_grupo($dados);

        if (isset($retorno_processamento['error'])) {
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $retorno_processamento['error']
            ), 409);
            die();
        }


        if (isset($retorno_processamento['info'])) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'status'  => 2,
                'sucesso' => true,
                'dados'   => $retorno_processamento
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 0,
                'sucesso' => false,
                'dados' => $retorno_processamento
            ), 409);
            die();
        }
    }
}
