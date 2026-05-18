
<?php


class puglin extends Controller
{

    protected $gravaProcess;
    protected $RemoveProcesso;
    protected $GravaTransacao;
    protected $CapturaRedeLojaDoContrato;
    protected $CapturaCamposConsultas;
    protected $ValidaCpf;
    protected $ValidaCnpj;
    protected $MontaJsonConfigEHeadersDaConsulta;
    protected $CapturaJobsFinalizadosGerarSaida;
    protected $CapturaDadosTransacoesJob;
    protected $CapturaRespostasPluginsTransacao;
    protected $db;
    protected $limpaString;
    protected $Config;
    protected $enconde;
    protected $mongo;
    protected $arquivo_teste;
    protected $soaps;

    protected $filtros;

    public function __construct()
    {


        $model = new Model();
        $this->db = $model->getConnection();

        require_once __DIR__ . '/../models/GravaProcesso.php';
        $this->gravaProcess = new GravaProcesso();
        require_once __DIR__ . '/../models/RemoveProcesso.php';
        $this->RemoveProcesso = new RemoveProcesso();
        require_once __DIR__ . '/../models/GravaTransacao.php';
        $this->GravaTransacao = new GravaTransacao();
        require_once __DIR__ . '/../models/CapturaRedeLojaDoContrato.php';
        $this->CapturaRedeLojaDoContrato = new CapturaRedeLojaDoContrato();
        require_once __DIR__ . '/../models/CapturaCamposConsultas.php';
        $this->CapturaCamposConsultas = new CapturaCamposConsultas();

        require_once __DIR__ . '/../models/CapturaDadosTransacoesJob.php';
        $this->CapturaDadosTransacoesJob = new CapturaDadosTransacoesJob();
        require_once __DIR__ . '/../models/CapturaJobsFinalizadosGerarSaida.php';
        $this->CapturaJobsFinalizadosGerarSaida = new CapturaJobsFinalizadosGerarSaida();


        require_once __DIR__ . '/../models/CapturaRespostasPluginsTransacao.php';
        $this->CapturaRespostasPluginsTransacao = new CapturaRespostasPluginsTransacao();


        require_once __DIR__ . '/../Utilis/MontaJsonConfigEHeadersDaConsulta.php';
        $this->MontaJsonConfigEHeadersDaConsulta = new MontaJsonConfigEHeadersDaConsulta();

        require_once __DIR__ . '/../Utilis/MontaJsonConfigEHeadersDaConsulta.php';
        $this->MontaJsonConfigEHeadersDaConsulta = new MontaJsonConfigEHeadersDaConsulta();

        require_once __DIR__ . '/../Utilis/Configs.php';
        $this->Config = new Configs();

        require_once __DIR__ . '/../Utilis/validaCampos.php';
        $this->enconde = new ValidaCampos();

        require_once __DIR__ . '/../Utilis/LimpaString.php';
        $this->limpaString = new LimpaString();

        require_once __DIR__ . '/../models/instance.php';
        $this->mongo = new instance();

        require_once __DIR__ . '/../Utilis/Arquivo_testes.php';
        $this->arquivo_teste = new Arquivo_testes();

        require_once __DIR__ . '/../Utilis/soaps.php';
        $this->soaps = new soaps();
        require_once __DIR__ . '/../models/process.php';
        $this->filtros = new process();
    }


    public function index_puglin($pathFile = null)
    {
        echo "Estou na chamanda dentro da rota nova\n";

        $agrupado = [];
        $retorno_plugin = $this->filtros->get_lista_plugin_with();

        // echo "<pre>";
        // echo "MEU RETORNO DOS PUGLINS QUE NÃO TEM NA TABELA DENTRO DO SCHEMA PROGESTOR\n";

        // print_r($retorno_plugin);


        // die();
        // LENDO O ARQUIVO 
        if (($handle = fopen($pathFile, "r")) !== false) {
            $headerSkipped = false;
            while (($linha = fgets($handle)) !== false) {
                $linha = trim($linha);
                if ($linha === '') {
                    continue;
                }

                if (!$headerSkipped && stripos($linha, 'regcod') !== false && stripos($linha, 'cpodsc') !== false) {
                    $headerSkipped = true;
                    continue;
                }

                // echo $linha . "<br>";

                $parts = array_map('trim', explode(';', $linha, 2));
                if (count($parts) < 2) {
                    continue;
                }

                $id = $parts[0];
                $descricao = $parts[1];

                if (!isset($agrupado[$id])) {
                    $agrupado[$id] = [
                        'descricao' => $descricao,
                        'id_plugin' => $id
                    ];
                } else {
                    $agrupado[$id]['descricao'] .= " ; " . $descricao;
                }
            }
            fclose($handle);
        }

        // echo "<pre>";
        // echo "minha lista\n";

        // print_r($agrupado);

        // die();
        foreach ($retorno_plugin as $value) {
            $id = $value['regcod'];

            echo 'ID DO PLUGIN : ' . $id . "\n";
            $descricao = trim($value['cpodsc']);

            echo 'ID DO PLUGIN : ' . $descricao . "\n";

            // Se ainda não existe esse ID no array, inicializa 
            if (!isset($agrupado[$id])) {
                $agrupado[$id] = [
                    'descricao' => $descricao,
                    'id_plugin' => $id
                ];
            } else {
                // Se já existe, concatena com ponto e vírgula
                $agrupado[$id]['descricao'] .= " ; " . $descricao; ## COLOCO JUNTO NA MESMA COLUNA  EXEMPLO: 01 - CPF/CNPJ ; 03 - DATA DE NASCIMENTO ; 14 - TELEFONE
            }
        }

        // print_r($agrupado);

        $i = 0; // INICIA O CONTADOR
        //INICIO A CONEXAO COM O TRANSATION
        $this->db->beginTransaction();

        foreach ($agrupado as $item) {
            echo "ID DO PLUGIN: " . $item['id_plugin'] . " - DESCRIÇÃO: " . $item['descricao'] . "\n";

            $descricaoParts = array_filter(array_map('trim', explode(';', $item['descricao'])));
            $placeholders = implode(',', array_fill(0, count($descricaoParts), '?'));

            //PEGO O NUMERO DE COLUNAS PARA SE R INSERIDO
            $total_de_coluna = count($descricaoParts);

            $campos_para_inserir = implode(', ', array_map(function ($index) {
                return "parametro" . ($index + 1);
            }, range(0, count($descricaoParts) - 1)));

            // echo "TENHO O TOTA DE COLUNAS DO PLACEHOLDERS: " . $total_de_coluna . " DO ID ::" . $item['id_plugin'] . "\n";
            // echo "NOME DA COLUNA PARA INSERIR O DADO : " . $campos_para_inserir . " DO ID ::" . $item['id_plugin'] . "\n";

            #INSERT MONTADO DINAMICAMENTE PARA INSERIR O DADO DO PLUGIN
            $sql = "INSERT INTO progestor.plugin_campos_input (numero_plugin, $campos_para_inserir) VALUES (?, $placeholders)";

            // 3. O PULO DO GATO: Mesclar o ID com as descrições em um único array de valores
            // Isso cria algo como: [104, "CPF/CNPJ", "DATA NASC", "TELEFONE"] //COM OSS CAMPOS QUE ELE PEGAR NO placeholders
            $valores_para_bind = array_merge([$item['id_plugin']], array_values($descricaoParts));

            echo "<pre>";

            echo "VALORE A SER CRIADO COM O ARRAY MERGE E ARRAY VALUES";

            print_r($valores_para_bind);

            // 4. Executa passando o array único
            // $this->filtros->insert_plugin($sql, $valores_para_bind); //COMENTADO POR SEGURAÇA

            $i++;
            if (($i % 200) == 0) {
                $this->db->commit();
                $this->db->beginTransaction();
            }
        }
        $this->db->commit();




        // Aqui você pode preparar a consulta e executar usando PDO, por exemplo:
        // $stmt = $pdo->prepare($sql);
        // $stmt->execute([$item['id_plugin'], ...$descricaoParts]);

        // echo  "ID DO PLUGIN: " . $item['id_plugin'] . " - PLACEHOLDERS: " . $placeholders . "\n";
    }
    public function index_puglin_ativossss()
    {
        echo "Estou na chamanda dentro da rota nova\n";

        $agrupado = [];
        $retorno_plugins = $this->filtros->get_lista_plugin_ativoss();

        echo "<pre>";
        echo "MEU RETORNO DOS PUGLINS QUE NÃO TEM NA TABELA DENTRO DO SCHEMA PROGESTOR\n";

        print_r($retorno_plugins);



        // foreach ($retorno_plugins as $values) {



        //     $ids = $values['codigo_consulta'];

        //     echo 'ID DO PLUGIN : ' . $ids . "\n";
        //     $descricao = trim($values['codigo_consulta']);

        //     echo 'ID DO PLUGIN : ' . $descricao . "\n";

        //     // Se ainda não existe esse ID no array, inicializa 
        //     if (!isset($agrupado[$ids])) {
        //         $agrupado[$ids] = [
        //             'descricao' => $descricao,
        //             'id_plugin' => $ids
        //         ];
        //     } else {
        //         // Se já existe, concatena com ponto e vírgula
        //         $agrupado[$ids]['descricao'] .= " ; " . $descricao; ## COLOCO JUNTO NA MESMA COLUNA  EXEMPLO: 01 - CPF/CNPJ ; 03 - DATA DE NASCIMENTO ; 14 - TELEFONE
        //     }
        // }
        // print_r($agrupado);
    }
}






?>