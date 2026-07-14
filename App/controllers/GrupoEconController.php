
<?php



class GrupoEconController extends Controller
{
    protected $process_dados;
    protected $validaCampos;

    public function __construct()
    {
        $this->process_dados = $this->Utilis('GrupoEconomico/grupoEconomicoUtilis');

        $this->validaCampos = $this->Utilis('validaCampos');
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
}
