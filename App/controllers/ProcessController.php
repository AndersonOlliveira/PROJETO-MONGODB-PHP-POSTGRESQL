<?php


class ProcessController extends Controller
{

    protected $utilis_process;
    protected $utilis_processs;
    protected $utilis_processs_teste_arquivos;
    protected $utilis_processs_teste_arquivos_;
    protected $utilis_processs_teste_arquivos_json;
    protected $utilis_processs_teste;
    protected $utilis_processs_new;
    protected $BuscaValorLotePorConsulta;
    protected $CapturaRedeLojaDoContrato;

    public function __construct()
    {
        $this->utilis_process = $this->Utilis('Process_Utilis');
        $this->utilis_processs = $this->Utilis('teste');
        $this->utilis_processs_teste = $this->Utilis('Arquivo_testes');
        $this->utilis_processs_new = $this->Utilis('new_arquivo');
        // $this->utilis_processs_teste_arquivos = $this->Utilis_arquivo('CONSULTAS');
        // $this->utilis_processs_teste_arquivos = $this->Utilis_arquivo('teste-base');
        $this->utilis_processs_teste_arquivos_ = $this->Utilis_arquivo('CONSULTAS-testes');
        // $this->utilis_processs_teste_arquivos_ = $this->Utilis_arquivo('ta');
        // $this->utilis_processs_teste_arquivos_ = $this->Utilis_arquivo('new_lista_crm');
        // $this->utilis_processs_teste_arquivos_ = $this->Utilis_arquivo('crm');
        $this->utilis_processs_teste_arquivos_json = $this->Utilis_arquivo_json('meu_arquivo');
        // $this->utilis_processs_teste_arquivos_ = $this->Utilis_arquivo('CONSULTA-BASE');

        require_once __DIR__ . '/../models/BuscaValorLotePorConsulta.php';
        $this->BuscaValorLotePorConsulta = new BuscaValorLotePorConsulta();

        require_once __DIR__ . '/../models/CapturaRedeLojaDoContrato.php';
        $this->CapturaRedeLojaDoContrato = new CapturaRedeLojaDoContrato();
    }

    public function get_all_query()
    {

        $return = $this->utilis_process->get_query();

        echo "<pre>";

        print_r($return);

        return $this->view('Query_active');
    }
    public function get_all_teste()
    {

        $return = $this->utilis_processs->lista_testessss();



        return $this->view('listar_teste');
    }

    public function cpu_server()
    {

        try {
            $cpu_load = self::get_cpu_usage();

            http_response_code(200);
            ob_clean();
            echo json_encode(
                [
                    'status' => 2,
                    'messsage' => 'sucesso em solicitar',
                    'data' => $cpu_load
                ]
            );
        } catch (Exception $e) {
            echo "Falha ao obter CPU: " . $e->getMessage();
        }
    }

    function get_cpu_usage()
    {
        $processes = shell_exec("ps aux --sort=-%cpu | head -10");
        return $processes;
    }

    public function c_headers()
    {

        $consultas = 262936; #CONSULTA PARA CRM 
        // $consultas = 283091; #CONSULTA BASE

        echo "Estou saindo aqui";

        $nave_headers = $this->utilis_processs_new->process_headers($consultas);
    }

    public function teste_envio()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $dateNow = '';
        echo "estou na minha pagina arquivo\n";
        $pathFile = $this->utilis_processs_teste_arquivos_;
        // $pathFile = $this->utilis_processs_teste_arquivos_;

        if (file_exists($pathFile)) {
            echo "o arquivo existe";
        }

        // print_r($pathFile);
        // if (($handle = fopen($pathFile, "r")) !== false) {
        //     while (($linha = fgets($handle)) !== false) {
        //         echo $linha . "<br>";
        //     }
        //     fclose($handle);
        // }


        // $consultas = 283091; #CONSULTA PARA QUE CONTEM BASE
        // $consultas = 262936; #CONSULTA PARA CRM 
        // $consultas = 283111; #CONSULTA PARA CPF 
        // $consultas = 262936;
        $consultas = 283111;
        $contrato = 417039;
        $filename = 'CONSULTAS.csv';
        $valortotal = 0;
        $tipo = 'cpnp';
        $headers = 'tcpfcnpj';
        // $headers = 'tlidersinistroufmed;tpsqnom;tlidersinistrocrmmed';
        // $headers = 'tdatabase;tcpfcnpj;tdatnsc';
        $finger = '{"ip":"177.25.93.211","city":"Santos","region":"São Paulo","country":"BR","loc":"-23.9608,-46.3336","timezone":"America/Sao_Paulo"}';

        $consultas = explode(",", $consultas);

        echo "<pre>";
        print_R($consultas);

        $dateNow = date("Y-m-d H:i:s");
        // echo "[$dateNow] --- NOVO PROCESSO: $pathFile, $consultas, $contrato, $filename \n";

        // require_once 'Arquivo_testes.php';
        $result = $this->utilis_processs_teste->process($pathFile, $consultas, $contrato, $filename, $valortotal, $headers, $finger);
        // $validate = $this->utilis_processs_new->validate($pathFile, $consultas[0], $headers);

        //  $result 

        // echo "<pre>";

        // print_R($validate);


        // $qta_original = $validate['quantidade'];

        // if ($validate['totalErros'] > 0) {

        //     $validate['quantidade'] = ($validate['quantidade'] - $validate['totalErros']);
        // }
        // // FATURAMENTO
        // $valorTotal = 0;

        // foreach ($consultas as $consulta) {

        //     $redeLoja = $this->CapturaRedeLojaDoContrato->execute($contrato);
        //     $valorLoteConsulta = $this->BuscaValorLotePorConsulta->calcula($consulta, $redeLoja['rede'], $validate['quantidade']);
        //     $valorTotal = ($valorTotal + $valorLoteConsulta);
        // }
        // $dateNow = date("Y-m-d H:i:s");

        // $json = json_encode(array(
        //     'sucesso' => true,
        //     'valor' => "R$ " . number_format($valorTotal, 2, ',', '.'),
        //     'valorTotal' => $valorTotal,
        //     'quantidade' => $validate['quantidade'],
        //     'quantidade_total' => $qta_original,
        //     'erros' => $validate['erros'],
        //     'mensagem' => '',
        //     JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        // ));

        // echo $json;


        // echo "[$dateNow] > --- RESULTADO: [$result] - $pathFile, $consultas, $contrato, $filename \n";
    }



    public function ler_arquivo()
    {

        echo "estou na minha pagina arquivo\n";
        $pathFile = $this->utilis_processs_teste_arquivos_json;
        // $pathFile = $this->utilis_processs_teste_arquivos_;

        if (file_exists($pathFile)) {
            echo "o arquivo existe";
        }
        echo "<pre>";

        print_r("estou chamando esta rota\n");
        $validate = $this->utilis_processs_new->ler_arquivo_json($pathFile);
    }

    public function gerar_arquivo()
    {

        echo "<pre>";
        echo "ESTOU AQUI";
        $id = 109;

        // print_R()

        $validate = $this->utilis_processs_new->gerar_ar($id);
    }
}
