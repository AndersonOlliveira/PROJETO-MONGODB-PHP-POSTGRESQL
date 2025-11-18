<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ListarController extends Controller
{

  protected $utils;
  protected $utilss;
  protected $utils_out;
  protected $utils_mongo;
  protected $tratamento;
  protected $utils_functions;


  public function __construct()
  {
    // require_once __DIR__ . '/../Utilis/Arquivos.php';
    // $this->tratamento = $this->Utilis('Arquivos');

    $this->utilss = new Instance();

    $this->utils = $this->Utilis('Arquivos');
    $this->utils_out = $this->Utilis('GerarOutput');
    $this->utils_mongo = $this->Utilis('Mongo');
    // $this->utils->teste();

    // Carrega de App/Arquivos ()
    // $this->tratamento = $this->loadFrom('Arquivos', 'Arquivos');

    require_once __DIR__ . '/../Utilis/Funcoes.php';
    $this->utils_functions = new Funcoes();
  }


  public function listar($idProcesso = null, $qtLimit = null)
  {


    $return = $this->model('process');
    $returns = $return->list_processo($idProcesso, $qtLimit);

    if (empty($returns)) {
      echo "Nenhum dado encontrado!\n";
      // return;
    }
    $re = $this->utils->get_dados_id($returns);

    return $this->view('listar');
  }

  public function listar_id($id = null)
  {
    $result_mongo = $this->model('instance');
    $result_mongo = $result_mongo->findById($id);
    foreach ($result_mongo as $key => $valores) {
    }
    //    return $this->view('listar', $result_mongo);

  }

  public function mongo()
  {

    $re = $this->utils_mongo->get_dada_all();


    return $this->view('List_dados_mongo');
  }
  public function mongo_size()
  {
    $tam_banco = $this->utilss->get_size_database();
    $tam_banco  = $this->utils_functions->formatarTamanho($tam_banco);
    //criar api para busca do tamanho do banco 
    http_response_code(201);
    ob_clean();
    $dados = json_encode(array(
      'status' => 2,
      'sucesso' => true,
      'data' => $tam_banco,
      'mensagem' => 'Sucesso em obter tamanho do banco',
      JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    ));

    echo $dados;

    return $dados;
  }



  public function teste_teste()
  {

    echo "chameii\n";

    $this->utils_out->generateOutputFiles(382);
  }
}
