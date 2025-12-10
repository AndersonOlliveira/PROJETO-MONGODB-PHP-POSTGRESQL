


<?php


class new_arquivo extends Controller
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

        require_once __DIR__ . '/../Utilis/Config.php';
        $this->Config = new Config();

        require_once __DIR__ . '/../Utilis/ValidaCampos.php';
        $this->enconde = new ValidaCampos();

        require_once __DIR__ . '/../Utilis/LimpaString.php';
        $this->limpaString = new LimpaString();

        // require_once 'ValidaCpf.php';
        // $this->ValidaCpf = new ValidaCpf();
        // require_once 'ValidaCnpj.php';
        // $this->ValidaCnpj = new ValidaCnpj();
    }


    public function process_headers($consulta)
    {
        // $verificarCampos = $this->CapturaCamposConsultas->Consultation_header_new($consulta);

        echo "<pre>";

        $teste = [
            'tdatabase' => 'vazio',
            'tcpfcnpj'  => '',
            'tdatnsc'   => 'vazio',


        ];
        $campoObrigatorio = ['tcpfcnpj'];

        echo "meu teste";

        $ignorarLinha = false;

        foreach ($campoObrigatorio as $campo) {

            if (array_key_exists($campo, $teste)) {
                echo "A chave 'nome' existe no array.\n";
            }
        }

        $linhasComErro = [];
        $contadorLinha = 0;
        foreach ($campoObrigatorio as $campo) {
            $contadorLinha++;
            if (
                !isset($teste[$campo]) ||
                trim($teste[$campo]) === '' ||
                strtolower(trim($teste[$campo])) === 'vazio'
            ) {

                $linhasComErro[] = [
                    'linha'  => $contadorLinha,
                    'campo'  => $campoObrigatorio,
                    'valor'  => $teste[$campo] ?? null,
                    'dados'  => $teste
                ];
                $ignorarLinha = false;
                continue; // sai da validação e já pula a linha inteira
            }
        }

        if (!$ignorarLinha) {
            echo "Linha ignorada: campo obrigatório tcpfcnpj não preenchido<br>";
            echo "minha quantidade de linhas " . $contadorLinha . "\n";
            echo "total erros" . count($linhasComErro) . "\n";

            print_r($linhasComErro);
        }
    }



    public function validate($pathFile, $consulta, $headers)
    {

        echo "estoui chamdando\n";

        $verificarCampos = $this->CapturaCamposConsultas->Consultation_header_new($consulta);

        $limiteRegArquivo = 30000;

        $qtRegistros = 0;
        $documentoInvalido = [];

        $registrosValidos = [];
        $registros = [];
        $linhasComErro = [];
        $contadorLinha = 0;

        $fh = fopen($pathFile, "r");
        $erros = [];



        $colunasEsperadas = $this->CapturaCamposConsultas->Consultation_header_new(262936);
        // $colunasEsperadas = $verificarCampos->Consultation_header_new($consulta[0]);
        $coluna_obrigatorio = $colunasEsperadas['campos'];

        echo "<pre>";
        print_r($coluna_obrigatorio);


        if ($fh) {
            while (($linha = fgets($fh)) !== false) {

                $qtRegistros++;


                $linhaValida = true;
                $linha = preg_replace('/^\xEF\xBB\xBF/', '', $linha);

                $colunas = str_getcsv($linha, ';');
                $keys = str_getcsv($headers, ';');

                foreach ($colunas as $i => $valor) {
                    if (strtolower(trim($valor)) == 'vazio') {
                        // if (strtolower(trim($valor)) == 'null') {
                        $colunas[$i] = '';
                    }
                }

                $keys = str_getcsv($headers, ';');
                while (count($colunas) < count($keys)) {
                    $colunas[] = "";
                }
                $associado = array_combine($keys, $colunas);


                // echo "<pre>";
                // echo "meu array associado \n";


                // print_r($associado);



                foreach ($coluna_obrigatorio as $chave_obrigatoria) {
                    $contadorLinha++;

                    $chave_obrigatoria = trim($chave_obrigatoria);

                    echo "<pre>";
                    echo "minha chave Obrigatorio\n";
                    print_r(trim($chave_obrigatoria));
                    $chave_obrigatoria = trim($chave_obrigatoria);



                    // // 2. Verificar se a chave existe (Usando isset() para ser mais seguro)
                    if (isset($associado[$chave_obrigatoria]) && empty($associado[$chave_obrigatoria])) {




                        echo "<pre>";
                        echo "ESTOU SAINDO AQUI?";
                        $linhaValida = false;
                        print_r($associado);
                        //A chave existe, mas o valor é ""
                        if (!isset($documentoInvalido[$chave_obrigatoria])) {





                            if ($linhaValida) {
                                $registros[] = implode(';', $numero);
                            }



                            $documentoInvalido[$chave_obrigatoria] = [
                                'linha'   => [$associado],
                                'erro_tipo'  => 'Campo_Obrigatorio_faltante',
                                'quantidade' => 1
                            ];

                            $linhaValida = false;
                        } else {


                            $documentoInvalido[$chave_obrigatoria]['quantidade']++;
                            // $documentoInvalido['linha']['quantidade']++;

                            //   Adicionar a linha atual ao array de linhas que falharam
                            $documentoInvalido[$chave_obrigatoria][] = $contadorLinha;
                            // $documentoInvalido['linha'][] = $contadorLinha;
                        }
                    }
                }


                // $camposObrigatorios = ['tcpfcnpj']; // pode vir do seu sistema

                // $camposInvalidos = [];

                // foreach ($camposObrigatorios as $campoObrigatorio) {

                //     $valor = $associado[$campoObrigatorio];

                //     if (empty($valor)) {

                //         echo "<pre>";

                //         echo "que resultado tenho aqui\n";
                //         echo "meu campo e vazio?\n";

                //         print_R($valor);
                //     }



                //     if (
                //         $valor === null ||
                //         trim($valor) === '' ||
                //         strtolower(trim($valor)) === 'vazio'
                //     ) {
                //         $camposInvalidos[] = $campoObrigatorio;
                //     }
                // }

                // // Se houver qualquer erro, a linha é inválida
                // if (!empty($camposInvalidos)) {

                //     $documentoInvalido[] = [
                //         'linha'   => $contadorLinha,
                //         'campos'  => $camposInvalidos,  // agora pode ter vários
                //         'dados'   => $associado
                //     ];

                //     continue;
                // }

                // if (!$linhaValida) {
                //     echo "Linha ignorada: campo obrigatório tcpfcnpj não preenchido\n";
                //     echo "minha quantidade de linhas " . $contadorLinha . "\n";
                //     echo "total erros" . count($linhasComErro) . "\n";
                // }

                // print_r($linhasComErro);






                if (isset($associado['tcpfcnpj']) && !empty($associado['tcpfcnpj'])) {

                    $coluna  = preg_replace("/\r|\n/", "", $associado['tcpfcnpj']);
                    $numero = preg_replace("/\D/", "", $coluna);

                    // if ($numero == "") {
                    //     $documentoInvalido[$numero] = [
                    //         'documento'  => $numero,
                    //         'valid'      => 0,
                    //         'reason'     => 'vazio',
                    //         'quantidade' => 1
                    //     ];
                    //     $linhaValida = false;
                    // }
                    // 3. CPF
                    if (strlen($numero) === 11) {
                        if (!$this->validarDocumento($numero, 'cpf', $registrosValidos, $documentoInvalido)) {
                            $linhaValida = false;
                        }
                    } elseif (strlen($numero) === 14) {
                        if (!$this->validarDocumento($numero, 'cnpj', $registrosValidos, $documentoInvalido)) {
                            $linhaValida = false;
                        }
                    }
                    // 5. Tamanho inválido
                    else {
                        if (!isset($documentoInvalido[$numero])) {
                            $documentoInvalido[$numero] = [
                                'documento'  => $numero,
                                'valid'      => 0,
                                'reason'     => 'Número fora do padrão de CPF OU CNPJ',
                                'quantidade' => 1
                            ];
                        } else {
                            $documentoInvalido[$numero]['quantidade']++;
                        }
                        $linhaValida = false;
                        // break;
                    }
                }

                $coluna  = preg_replace("/\r|\n/", "", $colunas);
                $numero = preg_replace("/\D/", "", $coluna);


                if (!$linhaValida) {
                    continue;
                }

                foreach ($associado as $valor) {
                    $registrosValidos[] = trim($valor);
                }

                if ($linhaValida) {
                    $registros[] = implode(';', $coluna);
                }
            }
            fclose($fh);
        }

        echo "<pre>";
        echo "meus documento invalidos";

        print_R($documentoInvalido);




        $totalErros = 0;
        foreach ($documentoInvalido as $doc) {
            $totalErros += $doc['quantidade'];
        }

        $totalvalidos = 0;
        foreach ($registrosValidos as $validos) {
            // echo "<pre>";
            // echo "validos\n";


            print_r($validos);
            // $totalErros += $validos['quantidade'];
        }

        echo "<pre>";
        echo "minha quantidade de registros\n";

        print_r($totalErros);
        echo "<pre>";
        echo "meus registros\n";

        print_r($registros);

        if ($qtRegistros == 0) {
            $erros[] =  [
                'msg' =>     "Nenhum registros encontrado no arquivo",
                'dados' => 2,
                'total_erros' => $totalErros
            ];
        }
        if ($qtRegistros > $limiteRegArquivo) {
            $erros[] =  [
                'msg' => "Quantidade de registros no arquivo excede o limite de processamento",
                'dados' => 3,
                'total_erros' => $totalErros
            ];
        }
        if (!empty($documentoInvalido)) {
            $erros[] = [
                'msg' => mb_convert_encoding("Encontrados documentos inválidos ou de tipo diferente do escolhido no arquivo", 'UTF-8', 'UTF-8'),
                'dados' => array_values($documentoInvalido),
                'total_erros' => $totalErros
            ];

            return [
                'erros' => $erros,
                'quantidade' => $qtRegistros,
                'totalErros' => $totalErros,
                'registros_validos' => $registrosValidos
            ];
        }

        return [
            'erros' => empty($erros) ? [] : $erros,
            'quantidade' => $qtRegistros,
            'totalErros' => 0,
            'registros_validos' => $registrosValidos
        ];
    }

    function ValidaCpf($cpf)
    {


        // Extrai somente os números
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return ['valid' => 0, 'reason' => 'comprimento'];
            // return false;
        }
        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return ['valid' => 0, 'reason' => 'sequencia'];
            // return false;
        }

        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return ['valid' => 0, 'reason' => 'digit_mismatch', 'possicao' => $t];
            }
        }

        return ['valid' => true, 'reason' => 'ok'];
    }


    function ValidaCnpj($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

        // Valida tamanho
        if (strlen($cnpj) != 14) {
            return ['valid' => 0, 'reason' => 'comprimento'];
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return ['valid' => 0, 'reason' => 'sequencia'];
        }

        // Valida dígitos verificadores
        if (self::validaDigito($cnpj, 12)) {
            return ['valid' => 0, 'reason' => 'digito1'];
        }
        if (self::validaDigito($cnpj, 13)) {
            return ['valid' => 0, 'reason' => 'digito2'];
        }

        return ['valid' => 1, 'reason' => null];
    }

    function validaDigito($cnpj, $pos)
    {
        $tamanho = $pos;
        $multiplicador = $pos - 7;
        $soma = 0;

        for ($i = 0; $i < $tamanho; $i++) {
            $soma += $cnpj[$i] * $multiplicador;
            $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
        }

        $resto = $soma % 11;
        $digito = ($resto < 2) ? 0 : 11 - $resto;

        return $cnpj[$pos] == $digito;
    }

    /**
     * Valida documento (CPF ou CNPJ) e atualiza os arrays de válidos e inválidos.
     */
    protected function validarDocumento($numero, $tipo, &$registrosValidos, &$documentoInvalido)
    {
        if ($tipo === 'cpf') {
            $resultado = $this->ValidaCpf($numero);
            if ($resultado['valid']) {
                $registrosValidos[] = $numero;
                return true;
            } else {
                if (!isset($documentoInvalido[$numero])) {
                    $documentoInvalido[$numero] = [
                        'documento'  => $numero,
                        'valid'      => 0,
                        'reason'     => $resultado['reason'],
                        'quantidade' => 1
                    ];
                } else {
                    $documentoInvalido[$numero]['quantidade']++;
                }
                return false;
            }
        } elseif ($tipo === 'cnpj') {
            $resultado = $this->ValidaCnpj($numero);
            if ($resultado['valid']) {
                $registrosValidos[] = $numero;
                return true;
            } else {
                if (!isset($documentoInvalido[$numero])) {
                    $documentoInvalido[$numero] = [
                        'documento'  => $numero,
                        'valid'      => 0,
                        'reason'     => $resultado['reason'],
                        'quantidade' => 1
                    ];
                } else {
                    $documentoInvalido[$numero]['quantidade']++;
                }
                return false;
            }
        }
        return false;
    }



    public function  ler_arquivo_json($patth)
    {

        echo "<pre>";
        echo "\n";

        print_r($patth);

        if (is_dir($patth)) {

            echo 'e um dirétorio';
        }

        $string_json = file_get_contents($patth);

        // 2. Decodificar a string JSON em um objeto PHP
        $dados_objeto = json_decode($string_json);

        // echo "<pre>";
        print_r($dados_objeto);
        // file_put_contents($patth, '');


        // $filePath = $caminhoArquivo . DIRECTORY_SEPARATOR . $A[2];

        // $string_json = file_get_contents($filePath);

        // // 2. Decodificar a string JSON em um objeto PHP
        // $dados_objeto = json_decode($string_json);

        // echo "<pre>";
        // print_r($dados_objeto);


        echo "chamei dentro do tramento";
    }


    public function gerar_ar($idJob)
    {

        echo "chamei dentro de gerar\n";

        $job = $this->CapturaJobsFinalizadosGerarSaida->execute($idJob);


        $colunasEsperadas = $this->CapturaCamposConsultas->Consultation_description($job['campos_aquisicao']);

        $transacoes = $this->CapturaDadosTransacoesJob->execute($idJob);


        // echo "<pre>";
        // print_r($transacoes);



        echo "<pre>";

        print_r($colunasEsperadas);
        echo " </pre>";

        // die();
        if ($transacoes) {

            $dir = $this->Config->env('path_arquivos_interno');




            if (!file_exists("$dir/JOB_$idJob.zip")) {

                $conteudoArquivoPrincipal = "";
                mkdir("$dir/JOB_$idJob/", 0755, true);

                $nomeArquivoPrincipal = "$dir/JOB_$idJob/SAIDA_PRINCIPAL__" . $job['nome_arquivo'];


                // $conteudoArquivoPrincipal .= "CPF/CNPJ;" . utf8_encode($job['header_arquivo']) . "\n";
                $conteudoArquivoPrincipal .= implode(';', $colunasEsperadas) . ";";

                $conteudoArquivoPrincipal .= self::garantirUtf8($job['header_arquivo']) . "\n";

                $fpPrincipal = fopen($nomeArquivoPrincipal, 'a');
                //pra gerar um utf8
                file_put_contents($nomeArquivoPrincipal, "\xEF\xBB\xBF");

                $tCount = 0;
                $plugins = array();


                foreach ($transacoes as $registro) {

                    if (trim($registro['resposta']) != "" && $registro['resposta'] != null) {
                        // $conteudoArquivoPrincipal .= utf8_encode($registro['resposta']) . "\n";
                        $conteudoArquivoPrincipal .= self::garantirUtf8($registro['resposta']) . "\n";
                    } else {
                        $conteudoArquivoPrincipal .= self::garantirUtf8($registro['campo_aquisicao'])  . ";\n";
                        // $conteudoArquivoPrincipal .= utf8_encode($registro['campo_aquisicao']) . ";\n";

                    }

                    if (($tCount % 2000) == 0 && $tCount > 0) {

                        $conteudoArquivoPrincipal = $this->limpaString->limpaConteudoArquivo($conteudoArquivoPrincipal);

                        file_put_contents($nomeArquivoPrincipal, $conteudoArquivoPrincipal, FILE_APPEND);
                        unset($conteudoArquivoPrincipal);
                        // fwrite($fpPrincipal, $conteudoArquivoPrincipal);

                        $conteudoArquivoPrincipal = "";
                    }

                    $respPlugins = $this->CapturaRespostasPluginsTransacao->execute($registro['transacao_id']);


                    echo "<pre>";
                    echo "minha resposta do plugins\n";

                    // print_r($respPlugins);



                    if ($respPlugins) {

                        foreach ($respPlugins as $resp) {

                            $nomeArquivoPlugin = $this->enconde->convertEncode("$dir/JOB_$idJob/SAIDA_PLUGIN_" . $resp['plugin'] . "__" . $job['nome_arquivo']);

                            if (!file_exists($nomeArquivoPlugin)) {
                                // $header_plugin = self::garantirUtf8($resp['header_arquivo']) . "\n";
                                $header_plugin =  self::garantirUtf8($resp['header_arquivo']);

                                echo "<pre>";
                                echo "MEU HEADER DO PULGIN\n";
                                print_r($header_plugin);
                                file_put_contents($nomeArquivoPlugin, "CPF/CNPJ;" . $header_plugin . "\n", FILE_APPEND); // grava header
                                // file_put_contents(self::garantirUtf8($nomeArquivoPlugin),  implode(';', $colunasEsperadas) . ";" . self::garantirUtf8($resp['header_arquivo'])  . "\n", FILE_APPEND); // grava header
                                $conteudoArquivoPlg[$resp['plugin']] = "";
                                $plugins[] = $resp['plugin'];
                            }
                        }

                        foreach ($respPlugins as $resp) {

                            $nomeArquivoPlugin = "$dir/JOB_$idJob/SAIDA_PLUGIN_" . $resp['plugin'] . "__" . $job['nome_arquivo'];
                            if (trim($resp['resposta']) != "" && $resp['resposta'] != null) {
                                $conteudoArquivoPlg[$resp['plugin']] .= self::garantirUtf8($resp['resposta']) . "\n";
                            }
                        }
                    }

                    $tCount++;
                }
                // file_put_contents($nomeArquivoPlugin, "\xEF\xBB\xBF");
                file_put_contents($nomeArquivoPrincipal, $conteudoArquivoPrincipal, FILE_APPEND);

                if (count($plugins) > 0) {
                    foreach ($plugins as $plg) {

                        $nomeArquivoPlugin = "$dir/JOB_$idJob/SAIDA_PLUGIN_" . $plg . "__" . $job['nome_arquivo'];
                        file_put_contents($nomeArquivoPlugin, $conteudoArquivoPlg[$plg], FILE_APPEND);
                    }
                }

                // ZIP
                // exec("cd $dir; zip JOB_$idJob.zip JOB_$idJob/* -P '" . $job['contrato'] . "';");
            }

            return "Sucesso";
        }
    }


    public static function garantirUtf8($texto)
    {


        echo "<pre>";
        echo "meu texto recebido  \n";

        print_r($texto);



        $result_texot =  mb_check_encoding($texto, 'UTF-8')
            ? $texto
            : mb_convert_encoding($texto, 'UTF-8', 'ISO-8859-1');


        echo "<pre>";
        echo "meu texto convertido \n";

        print_r($result_texot);

        return $result_texot;
    }
}
