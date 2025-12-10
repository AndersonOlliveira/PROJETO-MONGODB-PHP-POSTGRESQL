<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ListarController extends Controller
{

  protected $utils;
  protected $utilss;
  protected $utilis_pgadmin;
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

    require_once __DIR__ . '/../models/process.php';
    $this->utilis_pgadmin = new process();

    // Carrega de App/Arquivos ()
    // $this->tratamento = $this->loadFrom('Arquivos', 'Arquivos');

    require_once __DIR__ . '/../Utilis/Funcoes.php';
    $this->utils_functions = new Funcoes();
  }


  public function listar($idProcesso = null, $qtLimit = null)
  {

    $result_idProcess = [];
    $return = $this->model('process');
    $returns = $return->list_processo($idProcesso, $qtLimit);

    # PEGO O QUE FOI FINALIZADO JÁ 
    $return_finish = $return->list_processo_modulo($idProcesso, $qtLimit);


    $returns_alert = $return->list_processo_qta_process($qtLimit);

    $result_idProcess = array_values(
      array_column(
        array_filter($returns, fn($row) => !empty($row['processo_id'])),
        'processo_id'
      )
    );


    if (empty($returns)) {
      echo "Nenhum dado encontrado!\n";
    }

    if (empty($returns_alert)) {

      echo "Nenhum dado encontrado\n";
    }


    if (empty($returns_modulos)) {

      echo "Nenhum dado encontrado\n";
    }

    echo "<pre>";
    echo "meu dados modulos\n";

    print_r($return_finish);

    $consult_modulos = [];


    if (!empty($return_finish)) {

      foreach ($return_finish as $key => $values_modulos) {


        $dados = $return->push_value_modulo(
          $values_modulos['rede'],
          $values_modulos['codcns'],
          $values_modulos['data_cadastro'],
          $values_modulos['data_finalizacao'],
          null
        );



        if (!empty($dados)) {

          $consult_modulos[] = [
            'dados' => $dados,
          ];

          $consult_modulos['processo_id'] = $values_modulos['processo_id'];
          $consult_modulos['valor_original'] = $values_modulos['valor_total'];
        }
      }
    }



    if (isset($consult_modulos) && !empty($consult_modulos)) {
      $this->utils->updados_modulos($consult_modulos);
    }






    $result_resposta = array_values(array_filter($returns_alert, function ($row) {
      return !empty($row['info']);
    }));

    if (isset($result_resposta)) {
      $list_dados = [];
      foreach ($result_resposta as $key => $values) {

        if ($values['qta_processar'] > 0) {

          $list_dados = $return->list_processo_alert($values['processo_id'],  $values['qta_processar']);
        } else {


          $return->finish_process_die($values['processo_id']);
        }
      }    #tenho 

      $re = $this->utils->get_dados_id($list_dados);
      echo "estou saindao aqui";
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
    $qta_row = $this->utilss->get_qta_row();

    $size_pgAdmin = $this->utilis_pgadmin->size_pgAdmin();
    $soma = 0;
    $sum_rows = 0;
    $result = [];
    foreach ($size_pgAdmin as $dados_pg) {
      $soma += $dados_pg['tamanho_total'];
      $sum_rows += $dados_pg['estimated_rows'];
      $dados_pg['tamanho_total'] = $this->utils_functions->formatarTamanho($dados_pg['tamanho_total']);
      $result[] = $dados_pg;
    }
    $size_pgAdmin = $result;
    $size_bank_pgAdmin =  $this->utils_functions->formatarTamanho($soma);
    $tam_banco  = $this->utils_functions->formatarTamanho($tam_banco);

    $data  = [
      'size_bank_mongo' => $tam_banco,
      'qta_row_mongo' => $qta_row,
      'dados_pg' => $size_pgAdmin,
      'size_bank_pgAdmin' => $size_bank_pgAdmin,
      'qta_rows_pgAdmin' => $sum_rows
    ];
    //criar api para busca do tamanho do banco 
    http_response_code(201);
    ob_clean();
    $dados = json_encode(array(
      'status' => 2,
      'sucesso' => true,
      'data' => $data,
      'mensagem' => 'Sucesso em obter tamanho do banco',
      JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    ));

    echo $dados;

    // return $dados;
  }



  public function teste_teste()
  {

    echo "chameii\n";

    $this->utils_out->generateOutputFiles(382);
  }
}
