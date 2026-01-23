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
    protected $utilis_process_valida;
    protected $BuscaValorLotePorConsulta;
    protected $CapturaRedeLojaDoContrato;

    public function __construct()
    {
        $this->utilis_process = $this->Utilis('Process_Utilis');
        $this->utilis_processs = $this->Utilis('teste');
        $this->utilis_processs_teste = $this->Utilis('Arquivo_testes');
        $this->utilis_processs_new = $this->Utilis('new_arquivo');
        $this->utilis_process_valida = $this->Utilis('ArquivoValida');
        // $this->utilis_processs_teste_arquivos = $this->Utilis_arquivo('CONSULTAS');
        // $this->utilis_processs_teste_arquivos = $this->Utilis_arquivo('teste-base');
        // $this->utilis_processs_teste_arquivos_ = $this->Utilis_arquivo('CONSULTAS-testes');
        // $this->utilis_processs_teste_arquivos_ = $this->Utilis_arquivo('CPF-VARIOS-CAMPOS- Copia');
        // $this->utilis_processs_teste_arquivos_ = $this->Utilis_arquivo('cabecalho-AJUSTADOSa');
        $this->utilis_processs_teste_arquivos_ = $this->Utilis_arquivo('new_lista_crm copy');
        // $this->utilis_processs_teste_arquivos_ = $this->Utilis_arquivo('POUCAS-LINHAS-VARIAS-CONSULTAS');
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
        $arquivoValida = $this->utilis_process_valida;
        $pathFile = $this->utilis_processs_teste_arquivos_;

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
        $consultas = [262936]; #CONSULTA PARA CRM 
        // $consultas = 283111; #CONSULTA PARA CPF 
        // $consultas = 262936;
        // $consultas = [265919, 283092];
        $contrato = 417039;
        $filename = 'CONSULTAS.csv';
        $valortotal = 0;
        $tipo = 'cpnp';
        $dados_consulta = [];
        // $headers = 'tcpfcnpj,tdatnsc,tcelnum,tnumtel,tdatnsc';
        // $headers = 'tcpfcnpj,tcep,tcelnum,tnumtel,tdatnsc';
        // $headers = 'tcpfcnpj,tdatnsc,tcep,tcelnum,tnumtel';
        $headers = 'tlidersinistroufmed,tpsqnom,tlidersinistrocrmmed';
        // $headers = 'tdatabase;tcpfcnpj;tdatnsc';
        $finger = '{"ip":"177.25.93.211","city":"Santos","region":"São Paulo","country":"BR","loc":"-23.9608,-46.3336","timezone":"America/Sao_Paulo"}';



        if (is_array($consultas)) {
            $consultasStr = implode(',', $consultas);
        } else {
            $consultasStr = $consultas;
        }



        $validateArquivo = $arquivoValida->ValidaFormat($pathFile);

        // echo "<pre>";
        // print_R($consultasStr);


        $dateNow = date("Y-m-d H:i:s");
        // echo "[$dateNow] --- NOVO PROCESSO: $pathFile, $consultas, $contrato, $filename \n";

        // require_once 'Arquivo_testes.php';
        $result = $this->utilis_processs_teste->process_new($pathFile, $consultas, $contrato, $filename, $valortotal, $headers, $finger);
        // $validate = $this->utilis_processs_new->validate($pathFile, $consultas, $headers);



        // echo "<pre>";
        // echo "meus dados dos validate\n";

        // print_R($validate);


        // $qta_original = $validate['quantidade'];

        // if ($validate['totalErros'] > 0) {

        //     $validate['quantidade'] = ($validate['quantidade'] - $validate['totalErros']);
        // }
        // // // FATURAMENTO
        // $valorTotal = 0;
        // // $teste = [];
        // $valoresPorConsulta = [];


        // foreach ($consultas as $consulta) {

        //     echo "Consulta Atual : {$consulta}\n";
        //     if (isset($validate['info_consultas'][$consulta]) && is_array($validate['info_consultas'][$consulta])) {
        //         $valoresPorConsulta[$consulta] = count($validate['info_consultas'][$consulta]);
        //     } else {
        //         $valoresPorConsulta[$consulta] = 0;
        //     }

        //     $redeLoja = $this->CapturaRedeLojaDoContrato->execute($contrato);
        //     list($valorLoteConsulta, $modulo) = $this->BuscaValorLotePorConsulta->calcula($consulta, $redeLoja['rede'],  $valoresPorConsulta[$consulta]);
        //     echo "Quantide nova Atual : {$valorLoteConsulta}\n";
        //     $valorTotal += $valorLoteConsulta;
        // }




        // $json = json_encode(array(
        //     'sucesso' => true,
        //     'valor' => "R$ " . number_format($valorTotal, 2, ',', '.'),
        //     'valorTotal' => $valorTotal,
        //     'dados_consultas' => $valoresPorConsulta,
        //     'quantidade' => $validate['quantidade'],
        //     'quantidade_total' => $qta_original,
        //     'erros' => $validate['erros'],
        //     // 'modulo' => $modulo,
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
        $id = 32;

        // print_R()

        $validate = $this->utilis_processs_new->gerar_ar($id);
    }

    public function MongoDelete()
    {
        echo "<pre>";

        echo "Estou acessando para deletar dados da controller\n";

        $retornoMongo = $this->utilis_processs_new->MongoDell();
    }


    public function JsonArquivo()
    {

        echo "Estou chamando o json aqui\n";
        $retornoMongo = $this->utilis_processs_new->json_processs();
    }
}
