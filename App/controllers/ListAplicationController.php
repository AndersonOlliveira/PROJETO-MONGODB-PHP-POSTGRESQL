<?php



class ListAplicationController extends Controller
{
 
        protected $aplication;

        public function __construct()
        {
        require_once "aplication/CapturaAplication.php";
        $this->aplication = new CapturaAplication();
        // require_once "Validacoes.php";
        // $this->validar = new Validacoes();

        // require_once 'ProscoreModel.php';
        // $this->models = new ProscoreModel();

        // require_once 'validaCampos.php';
        // $this->jsonEnconde = new validaCampos();
    }
    public function layout_aplication()
    {
        // require_once 'Validacoes.php';
        // $validacoes = new Validacoes();
        // $parametros = array("tctrid", "tctraut");
        // $parametros = $validacoes->validarParametrosDados($_REQUEST, $parametros);
        // foreach ($parametros as $key => $value) {
        //     ${$key} = $value;
        // }

        // require_once 'ProscoreModel.php';
        // $proscoreModel = new ProscoreModel();
        // $proscoreModel->validarAutenticacao($tctrid, $tctraut);

        // $resultado_aplication = $this->aplication->get_aplication($tctrid);


        // if (isset($resultado_aplication)) {
        //     $resultado_aplication = $this->jsonEnconde->convertEncode($resultado_aplication);
        //     http_response_code(200);
        //     ob_clean();
        //     echo json_encode([
        //         'status'  => 2,
        //         'data'  => $resultado_aplication,
        //         'message' => 'DADOS ENCONTRADOS'
        //     ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        //     die();
        // } else {


        //     http_response_code(422);

        //     ob_clean();
        //     echo json_encode([
        //         'status'  => 0,
        //         'data'    => [],
        //         'qtr'    => 0,
        //         'message' => 'DADOS NÃO ENCONTRADOS'
        //     ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        //     die();
        // }
    }
    public function layout_aplication_user()
    {
    //     require_once 'Validacoes.php';
    //     $validacoes = new Validacoes();
    //     $parametros = array("tctrid", "tctraut");
    //     $parametros = $validacoes->validarParametrosDados($_REQUEST, $parametros);
    //     foreach ($parametros as $key => $value) {
    //         ${$key} = $value;
    //     }

    //     require_once 'ProscoreModel.php';
    //     $proscoreModel = new ProscoreModel();
    //     $proscoreModel->validarAutenticacao($tctrid, $tctraut);

    //     $resultado_aplication_user = $this->aplication->get_data_user($tctrid);

    //     if (isset($resultado_aplication_user)) {
    //         $resultado_aplication_user = $this->jsonEnconde->convertEncode($resultado_aplication_user);
    //         http_response_code(200);
    //         ob_clean();
    //         echo json_encode([
    //             'status'  => 2,
    //             'data'  => $resultado_aplication_user,
    //             'message' => 'DADOS ENCONTRADOS'
    //         ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    //         die();
    //     } else {


    //         http_response_code(422);

    //         ob_clean();
    //         echo json_encode([
    //             'status'  => 0,
    //             'data'    => [],
    //             'qtr'    => 0,
    //             'message' => 'DADOS NÃO ENCONTRADOS'
    //         ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    //         die();
    //     }
    // }
}
}
