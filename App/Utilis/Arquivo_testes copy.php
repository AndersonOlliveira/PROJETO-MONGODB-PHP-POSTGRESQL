<?php


class Arquivo_testes extends Controller
{

    protected $gravaProcess;
    protected $RemoveProcesso;
    protected $GravaTransacao;
    protected $CapturaRedeLojaDoContrato;
    protected $CapturaCamposConsultas;
    protected $ValidaCpf;
    protected $ValidaCnpj;
    protected $MontaJsonConfigEHeadersDaConsulta;
    protected $BuscaValorLotePorConsulta;
    protected $db;
    public function __construct()
    {


        $model = new Model();
        $this->db = $model->getConnection();

        require_once __DIR__ . '/../models/GravaProcesso.php';
        $this->gravaProcess = new GravaProcesso();
        // $this->gravaProcess = new GravaProcesso();
        require_once __DIR__ . '/../models/RemoveProcesso.php';
        $this->RemoveProcesso = new RemoveProcesso();
        require_once __DIR__ . '/../models/GravaTransacao.php';
        $this->GravaTransacao = new GravaTransacao();
        require_once __DIR__ . '/../models/CapturaRedeLojaDoContrato.php';
        $this->CapturaRedeLojaDoContrato = new CapturaRedeLojaDoContrato();
        require_once __DIR__ . '/../models/CapturaCamposConsultas.php';
        $this->CapturaCamposConsultas = new CapturaCamposConsultas();
        require_once __DIR__ . '/../models/BuscaValorLotePorConsulta.php';
        $this->BuscaValorLotePorConsulta = new BuscaValorLotePorConsulta();
        require_once __DIR__ . '/../Utilis/MontaJsonConfigEHeadersDaConsulta.php';
        $this->MontaJsonConfigEHeadersDaConsulta = new MontaJsonConfigEHeadersDaConsulta();
    }


    // Função genérica para validar CPF ou CNPJ
    public function validarDocumento($doc, $tipo, &$validos, &$invalidos)
    {
        $resultado = ($tipo === 'cpf') ? self::ValidaCpf($doc) : self::ValidaCnpj($doc);

        if (isset($resultado['valid']) && $resultado['valid'] == 0) {
            if (!isset($invalidos[$doc])) {
                $invalidos[$doc] = [
                    'documento'  => $doc,
                    'reason'     => $resultado['reason'],
                    'quantidade' => 1
                ];
            } else {
                $invalidos[$doc]['quantidade']++;
            }
            return false;
        } else {

            $validos[] = $doc;

            return true;
        }
    }
    function processarArquivoCSVNew(
        $fh,
        array $headersConsulta,
        int $idConsulta,
        array &$coluna_obrigatorio,
        $pathFile,
        $contrato,
        $nomeArquivo,
        $valortotal,
        $fingers,
        $colunasEsperadas
    ) {


        $valorTotal = 0;
        $contadorLinha = 0;
        $registros = [];
        $documentoInvalido = [];
        $documentoInvalidos = [];
        $registrosValidos = [];
        $newregistros = [];

        rewind($fh);

        $dadosCtr = $this->CapturaRedeLojaDoContrato->execute($contrato);

        while (($linha = fgets($fh)) !== false) {

            $contadorLinha++;

            $linha = preg_replace('/^\xEF\xBB\xBF/', '', $linha);

            if (trim($linha) == '') {
                continue;
            }
            // separa colunas do CSV
            $colunas = str_getcsv($linha, ';');

            //  normaliza "vazio"
            foreach ($colunas as &$valor) {
                if (strtolower(trim($valor)) === 'vazio') {
                    $valor = '';
                }
            }

            // $colunas = self::ajustarColunas($colunas, $headersConsulta, $idConsulta);
            $colunas = self::ajustarColunas($colunas, $headersConsulta);

            $associado = array_combine($headersConsulta, $colunas);

            if (!$associado) {
                continue;
            }


            if (!self::validarLinhaNew($associado, $idConsulta, $documentoInvalido, $contadorLinha, $coluna_obrigatorio, $registrosValidos, $colunasEsperadas)) {
                continue;
            }



            $registros[] = implode(';', array_values($associado));

            // foreach ($idConsulta as $idConsultas) {
            // $idConsulta = (int) trim($idConsulta);

            // if (!isset($headersConsulta['headersNew'][$idConsulta][$idConsulta])) {
            //     continue;
            // }

            // $headersConsultaa = $headersConsulta['headersNew'][$idConsulta][$idConsulta]['cpovars'];

            // $colunas = self::ajustarColunas($colunas, $headersConsultaa);

            // $associados = array_combine($headersConsultaa, $colunas);

            // if (!$associados) {
            //     continue;
            // }

            // if (!self::validarLinhaNew($associados, $idConsulta, $documentoInvalidos, $contadorLinha, $coluna_obrigatorio, $registrosValidos, $headersConsulta)) {
            //     continue;
            // }

            // // Usar o idConsulta como índice e associar os dados
            // if (!isset($newregistros[$idConsulta])) {
            //     $newregistros[$idConsulta] = [];
            // }
            // $registro = implode(';', array_values($associado));

            // if (!in_array($registro, $newregistros[$idConsulta], true)) {
            //     $newregistros[$idConsulta][] = $registro;
            // }
            // $newregistros[$idConsulta][] = implode(';', array_values($associado));
        }
        // }
        // fclose($fh);

        $registros = array_unique($registros);

        $totalRegistrosporArray = count($registros);

        // // echo "O array tem " . $totalRegistros . " registros.";
        // // echo "<pre>";
        // echo "meu new associado\n";
        // echo "minha quantidade\n";

        // print_R(array_count_values($registros));

        // $totalErros = 0;
        // foreach ($documentoInvalido as $doc) {
        //     $totalErros += $doc['quantidade'];
        // }

        // $totalvalidos = count($registrosValidos);
        $totalRegistros = count($registros);

        echo "<pre>";
        echo "MEU TOTAL DE ARQUIVOS A SER PROCESSADOS\n";

        // print_r($registros);
        print_r($documentoInvalido);


        die();


        $confCns = $this->MontaJsonConfigEHeadersDaConsulta->execute($idConsulta);
        $resultado = $this->CapturaCamposConsultas->heades($idConsulta);

        $redeLoja = $this->CapturaRedeLojaDoContrato->execute($contrato);
        list($valorLoteConsulta, $modulo) = $this->BuscaValorLotePorConsulta->calcula($idConsulta, $redeLoja['rede'], $totalRegistrosporArray);

        $valorTotal += $valorLoteConsulta;

        echo "Quantide nova Atual : {$valorTotal}\n";


        $cabe = $resultado[$idConsulta]['tes'];
        $heders = $resultado[$idConsulta]['cpovars'];




        foreach ($registros as $reg) {
            // echo "<pre>";

            // $valores = array_map('trim', explode(';', $reg));

            // // corta ou ajusta conforme o header
            $valores = array_slice($valores, 0, count($heders));
            $registroAssociado = array_combine($heders, $valores);

            // // echo "meu registro final\n";
            // // print_R($registroAssociado);


            // // // print_r($registroAssociado);
            // // $valores = array_values($registroAssociado);  // ['89600000000', '01091995', ...]
            // // $payload = json_encode($registroAssociado, JSON_UNESCAPED_UNICODE);


            $valores = array_map('trim', $valores);
            $payloads = implode(';', $valores);

            // // echo "<pre>";


            // // print_r($registroAssociado);
            echo "<pre>";
            echo "final";
            print_r($payloads);
        }

        // $idProcesso = $this->gravaProcess->execute(
        //     $contrato,
        //     $dadosCtr['rede'],
        //     $dadosCtr['loja'],
        //     $idConsulta,
        //     $nomeArquivo,
        //     true,
        //     null,
        //     $confCns['json_config'],
        //     // "tcpfcnpj",
        //     $cabe, // salva cabecalho para realizar o processo
        //     $confCns['header_arquivo_principal'],
        //     $valortotal,
        //     $fingers,
        //     true,
        // );

        // $this->db->beginTransaction();

        $totalInseridos = 0;
        try {

            foreach ($registros as $registro) {

                echo "<pre>";

                $valores = array_map('trim', explode(';', $registro));

                // corta ou ajusta conforme o header
                $valores = array_slice($valores, 0, count($heders));
                $registroAssociado = array_combine($heders, $valores);

                // echo "meu registro final\n";
                // print_R($registroAssociado);


                // // print_r($registroAssociado);
                // $valores = array_values($registroAssociado);  // ['89600000000', '01091995', ...]
                // $payload = json_encode($registroAssociado, JSON_UNESCAPED_UNICODE);


                $valores = array_map('trim', $valores);
                // $valoresUnicos = array_unique($valores);
                $payloads = implode(';', $valores);
                // $payloads = str_replace(';', $valores);

                // echo "<pre>";




                // print_r($registroAssociado);
                echo "<pre>";
                echo "final\n";

                print_r($payloads);




                // print_r()
                // $id = $this->GravaTransacao->execute(
                //     $idProcesso,
                //     $payloads,
                //     0,
                //     0
                // );

                // if ($id) {
                //     $totalInseridos++;
                // }
            }


            // $this->db->commit();
        } catch (Exception $e) {


            $this->db->rollBack();
            throw $e;
        }
        // }

        return "Sucesso";
    }

    function validarLinhaNew(array $associado, int $idConsulta, array &$documentoInvalido, int $linha, array $obrigatoriosPorConsulta, array &$registrosValidos, $colunasEsperadas)
    {
        $linhaValida = true;
        $contadorLinha = 0;
        $registrosValidos = [];
        $documentosProcessados = [];
        $documentoInvalido = [
            'linha'      => [],          // Array vazio para armazenar os números das linhas problemáticas
            'erro_tipo'  => '',
            'quantidade' => 0,           // Contador de erros inicializado em zero
        ];

        // echo "<pre>";
        // echo "minha lista de associado \n";
        // echo "minha linha {$linha} \n";

        echo "<pre>";
        echo "minhas colunas e chaves por id da consultas \n";
        print_R($associado);
        // colunasEsperadas


        foreach ($obrigatoriosPorConsulta as $chave_obrigatoria) {

            $chave_obrigatoria = trim($chave_obrigatoria);

            if (array_key_exists($chave_obrigatoria, $associado) && trim($associado[$chave_obrigatoria]) === '') {
                $linhaValida = false;

                $documentoInvalido['linha'][] = $linha;

                // Incrementa a quantidade de erros
                $documentoInvalido['quantidade']++;

                // Define o tipo de erro
                $documentoInvalido['erro_tipo'] = 'Campo Obrigatório Faltante';

                if (!isset($documentoInvalido[$chave_obrigatoria])) {
                    $documentoInvalido[$chave_obrigatoria] = [
                        'linha'   => [$linha],
                        'erro_tipo'  => 'Campo Obrigatório Faltante',
                        'quantidade' => 1
                    ];
                }
            }


            if (isset($associado['tcpfcnpj']) && !empty($associado['tcpfcnpj'])) {

                $docBruto = preg_replace("/\r|\n/", "", $associado['tcpfcnpj']);
                $numero = preg_replace("/\D/", "", $docBruto);



                if (isset($documentosProcessados[$numero])) {
                    continue;
                }

                $documentosProcessados[$numero] = true;

                if (strlen($numero) === 11) {
                    if (!$this->validarDocumento($numero, 'cpf', $registrosValidos, $documentoInvalido)) {
                        $linhaValida = false;
                    }
                } elseif (strlen($numero) === 14) {
                    if (!$this->validarDocumento($numero, 'cnpj', $registrosValidos, $documentoInvalido)) {
                        $linhaValida = false;
                    }
                } else {
                    if (!isset($documentoInvalido[$numero])) {
                        $documentoInvalido[$numero] = [
                            'documento'  => $numero,
                            'valid'      => 0,
                            'reason'     => 'comprimento',
                            'quantidade' => 1
                        ];
                    } else {
                        $documentoInvalido[$numero]['quantidade']++;
                    }
                    $linhaValida = false;
                }


                // array_unique(array_column($associado, $chave_obrigatoria));
            }


            $coluna  = preg_replace("/\r|\n/", "", implode(';', array_values($associado)));

            echo "<pre>";
            echo "minha coluna depois do pregrEPLACES\n";

            print_R($coluna);
            $numero = preg_replace("/\D/", "", $coluna);

            if (!$linhaValida) {
                return false;
            }

            // SE a linha for válida, guarda a linha ORIGINAL
            if ($linhaValida) {

                return true;
            }
        }
    }

    public function process_new($pathFile, $consultas, $contrato, $nomeArquivo, $valortotal, $headers, $fingers)
    {

        // $r = 0;
        // $contadorLinha = 0;
        // $dadosCtr = $this->CapturaRedeLojaDoContrato->execute($contrato);
        $colunasEsperadas = $this->CapturaCamposConsultas->Consultation_header_new($consultas, $headers);
        // $cabecalhos = [];

        $idConsultation = array_map('intval', $consultas);
        $id_headres = implode(',', $idConsultation);

        //trasformo os ids em int e vira um array, para busca
        $idsConsulta = is_array($id_headres)
            ? $id_headres
            : explode(',', $id_headres);


        // $documentoInvalido = [];
        // $registrosValidos = [];
        // $registros = [];
        $fh = fopen($pathFile, "r");
        $coluna_obrigatorio = $colunasEsperadas['campos'];

        foreach ($idsConsulta as $idConsulta) {

            $idConsulta = (int) trim($idConsulta);

            if (!isset($colunasEsperadas['headersNew'][$idConsulta][$idConsulta])) {
                continue;
            }

            // $cabe   = $colunasEsperadas['headersNew'][$idConsulta][$idConsulta]['tes'];
            // $heders = $colunasEsperadas['headersNew'][$idConsulta][$idConsulta]['cpovars'];
            $headersConsulta = $colunasEsperadas['headersNew'][$idConsulta][$idConsulta]['cpovars'];


            echo "<pre>";
            echo "HEADERS LOCALIZADOS\n";

            print_r($headersConsulta);

            $retornodosdados = self::processarArquivoCSVNew(
                $fh,
                $headersConsulta,
                $idConsulta,
                $coluna_obrigatorio,
                $pathFile,
                $contrato,
                $nomeArquivo,
                $valortotal,
                $fingers,
                $colunasEsperadas
            );
        } //final do foreach

        fclose($fh);
    }



    //script original
    public function process($pathFile, $consultas, $contrato, $nomeArquivo, $valortotal, $headers, $fingers)
    {

        echo "<pre>";
        echo "chamei aquo\n";


        $r = 0;
        $contadorLinha = 0;
        $dadosCtr = $this->CapturaRedeLojaDoContrato->execute($contrato);
        $verificarCampos = $this->CapturaCamposConsultas;
        $colunasEsperadas = $this->CapturaCamposConsultas->Consultation_header_new($consultas, $headers);
        $cabecalhos = [];

        $idConsultation = array_map('intval', $consultas);
        $id_headres = implode(',', $idConsultation);


        $idsConsulta = is_array($id_headres)
            ? $id_headres
            : explode(',', $id_headres);

        // echo "<pre>";
        // echo "depois do implode\n";

        // print_r($idsConsulta);

        $documentoInvalido = [];
        $registrosValidos = [];
        $registros = [];
        $fh = fopen($pathFile, "r");
        $coluna_obrigatorio = $colunasEsperadas['campos'];
        echo "<pre>";

        print_R($coluna_obrigatorio);
        foreach ($idsConsulta as $idConsulta) {

            $idConsulta = (int) trim($idConsulta);

            if (!isset($colunasEsperadas['headersNew'][$idConsulta][$idConsulta])) {
                continue; // ou throw Exception
            }

            $cabe   = $colunasEsperadas['headersNew'][$idConsulta][$idConsulta]['tes'];
            $heders = $colunasEsperadas['headersNew'][$idConsulta][$idConsulta]['cpovars'];

            echo "<pre>";
            echo "Consulta {$idConsulta}\n";
            print_r($heders);

            $headersConsulta = $colunasEsperadas['headersNew'][$idConsulta][$idConsulta]['cpovars'];

            echo "<pre>Processando consulta {$idConsulta}</pre>";


            $retornodosdados =  self::processarArquivoCSV(
                $fh,
                $headersConsulta,
                $idConsulta,
                $coluna_obrigatorio,
                $pathFile,
                $contrato,
                $nomeArquivo,
                $valortotal,
                $fingers,


            );
        }

        fclose($fh);


        // die();






        // echo "<pre>";
        // echo "depois do colunas esperadas\n";

        // print_r($heders);
        die();





        // foreach ($consultas as $consulta) {

        //     $resultado = $this->CapturaCamposConsultas->heades($consulta);
        //     echo "MEU JOGO DE COLUNAS PARA VIM\n";
        //     print_R($resultado);
        //     // Mescla mantendo o ID como chave
        //     $cabecalhos = array_merge($cabecalhos, $resultado);
        // }
        // $colunasEsperadas = $verificarCampos->Consultation_header_new($consulta[0]);


        // echo "<pre>";

        // $cabe = $colunasEsperadas['headersNew'][$consultas][$consultas]['tes'];
        // $heders = $colunasEsperadas['headersNew'][$consultas][$consultas]['cpovars'];



        // echo "<pre>";
        // echo "meu cabecalhos dinamico";

        // print_R($cabe);


        $documentoInvalido = [];
        $registrosValidos = [];
        $registros = [];
        $fh = fopen($pathFile, "r");

        if ($fh) {
            while (($linha = fgets($fh)) !== false) {


                echo "<pre>";
                echo "minha lista\n";

                print_r($linha);

                $linhaValida = true;
                $linhaDados = [];

                $linha = preg_replace('/^\xEF\xBB\xBF/', '', $linha);

                $linhaLimpa = trim($linha);
                if ($linhaLimpa === '') {
                    continue;
                }

                $colunas = str_getcsv($linha, ';');

                echo "<pre>";
                echo "minha coluna\n";

                print_r($colunas);


                $keys = str_getcsv($headers, ';');

                echo "<pre>";
                echo "minhas key\n";

                print_r($keys);

                foreach ($colunas as $i => $valor) {
                    if (strtolower(trim($valor)) == 'vazio') {
                        // if (strtolower(trim($valor)) == 'null') {
                        $colunas[$i] = '';
                    }
                }


                // $keys = str_getcsv($headers, ';');
                // if (count($colunas) == count($keys)) {
                //     $colunas = array_slice($colunas, 0, count($keys));
                // }


                while (count($colunas) < count($keys)) {
                    $colunas[] = "";
                }

                echo "<pre>";
                echo "minha coluna vai sair antes do associado\n";

                print_r($colunas);

                $associado = array_combine($keys, $colunas);


                // $valores = array_map('trim', $colunas);

                // // corta ou ajusta conforme o header
                // $valores = array_slice($valores, 0, count($keys));



                // $associado = array_combine($keys, $valores);





                echo "<pre>";
                echo "meu dados associados\n";

                print_R($associado);

                if (empty($associado)) {
                    continue;
                }

                $contadorLinha++;
                foreach ($coluna_obrigatorio as $chave_obrigatoria) {

                    $chave_obrigatoria = trim($chave_obrigatoria);


                    if (isset($associado[$chave_obrigatoria]) && empty($associado[$chave_obrigatoria])) {


                        echo "<pre>";

                        print_R('sai aqui');

                        $linhaValida = false;
                        //A chave existe, mas o valor é ""
                        if (!isset($documentoInvalido[$chave_obrigatoria])) {
                            $documentoInvalido[$chave_obrigatoria] = [
                                'linha'   => [$contadorLinha],
                                'erro_tipo'  => 'Campo Obrigatório Faltante',
                                'quantidade' => 1
                            ];
                        } else {


                            $documentoInvalido[$chave_obrigatoria]['quantidade']++;
                            //array de linhas que falharam
                            $documentoInvalido[$chave_obrigatoria]['linha'][] = $contadorLinha;
                        }
                    }
                }

                //array associativo
                // $associado = array_combine($keys, $colunas);

                // valida campo documento
                if (isset($associado['tcpfcnpj']) && !empty($associado['tcpfcnpj'])) {

                    $docBruto = preg_replace("/\r|\n/", "", $associado['tcpfcnpj']);
                    $numero = preg_replace("/\D/", "", $docBruto);

                    // if ($numero === "") {
                    // 	$documentoInvalido[$numero] = [
                    // 		'documento'  => $numero,
                    // 		'valid'      => 0,
                    // 		'reason'     => 'vazio',
                    // 		'quantidade' => 1
                    // 	];
                    // 	$linhaValida = false;
                    // }
                    if (strlen($numero) === 11) {
                        if (!$this->validarDocumento($numero, 'cpf', $registrosValidos, $documentoInvalido)) {
                            $linhaValida = false;
                        }
                    } elseif (strlen($numero) === 14) {
                        if (!$this->validarDocumento($numero, 'cnpj', $registrosValidos, $documentoInvalido)) {
                            $linhaValida = false;
                        }
                    } else {
                        if (!isset($documentoInvalido[$numero])) {
                            $documentoInvalido[$numero] = [
                                'documento'  => $numero,
                                'valid'      => 0,
                                'reason'     => 'comprimento',
                                'quantidade' => 1
                            ];
                        } else {
                            $documentoInvalido[$numero]['quantidade']++;
                        }
                        $linhaValida = false;
                    }
                }

                $coluna  = preg_replace("/\r|\n/", "", $colunas);
                $numero = preg_replace("/\D/", "", $coluna);

                if (!$linhaValida) {
                    continue;
                }

                // SE a linha for válida, guarda a linha ORIGINAL
                if ($linhaValida) {
                    $registros[] = implode(';', $numero);
                }
            }

            fclose($fh);
        }



        $totalErros = 0;
        foreach ($documentoInvalido as $doc) {
            $totalErros += $doc['quantidade'];
        }




        $totalvalidos = count($registrosValidos);
        $totalRegistros = count($registros);


        echo "<pre>";
        echo "total Validos\n";

        // print_R($registros);
        print_R($totalvalidos . "\n");
        echo "total Registros\n";
        print_R($totalRegistros . "\n");

        echo "total Registros a ser inserido \n";
        print_R($totalRegistros . "\n");



        echo "MEUS REGISTROS \n";

        print_R($registros);

        // $headers = str_replace(';', ',', $headers);

        // echo "MEU CABECALHO \n";
        // print_R($headers);
        // $consultas = explode(",", $consultas);

        //troco poor virgula
        // die();
        // $headers = str_replace(';', ',', $headers);

        foreach ($consultas as $consulta) {

            echo "<pre>";

            print_r($consulta);

            $confCns = $this->MontaJsonConfigEHeadersDaConsulta->execute($consulta);
            $resultado = $this->CapturaCamposConsultas->heades($consulta);
            // echo "MEU JOGO DE COLUNAS PARA VIM\n";
            // print_R($resultado);

            $cabe = $resultado[$consulta]['tes'];
            $heders = $resultado[$consulta]['cpovars'];


            // echo "MEU CABECALHO FILTRADO \n";
            // print_r($cabe);




            // $idProcesso = $this->gravaProcess->execute(
            //     $contrato,
            //     $dadosCtr['rede'],
            //     $dadosCtr['loja'],
            //     $consulta,
            //     $nomeArquivo,
            //     true,
            //     null,
            //     $confCns['json_config'],
            //     // "tcpfcnpj",
            //     $cabe, // salva cabecalho para realizar o processo
            //     $confCns['header_arquivo_principal'],
            //     $valortotal,
            //     $fingers,
            // );
            // $this->db->beginTransaction();

            // $totalInseridos = 0;

            foreach ($registros as $registro) {

                $valores = array_map('trim', explode(';', $registro));

                // corta ou ajusta conforme o header
                $valores = array_slice($valores, 0, count($heders));

                $registroAssociado = array_combine($heders, $valores);

                // print_r($registroAssociado);
                $valores = array_values($registroAssociado);  // ['89600000000', '01091995', ...]
                $payload = json_encode($registroAssociado, JSON_UNESCAPED_UNICODE);


                $valores = array_map('trim', $valores);
                $payloads = implode(';', $valores);
                // echo "<pre>";
                // echo "meu valores\n";

                // print_R(implode(',', $valores));
                // print_R($payloads);
                // $id = $this->GravaTransacao->execute(20, $registro, 0, 0);
                // $id = $this->GravaTransacao->execute($idProcesso, $payloads, 0, 0);
                // if ($id) $totalInseridos++;
            }

            // $this->db->rollBack();

            // echo "Total de INSERTS executados (simulados): $totalInseridos";


            // // if ($idProcesso) {

            //     try {

            //         $i = 0;

            //         $this->db->beginTransaction();
            //         foreach ($registros as $registro) {

            //             $this->GravaTransacao->execute($idProcesso, $registro, 0, 0);

            //             if (($i % 200) == 0 && $i > 0) { // commit transaction a cada 200 registros
            //                 $this->db->commit();
            //                 $this->db->beginTransaction();
            //             }

            //             $i++;
            //         }
            //         $this->db->commit();
            //     } catch (PDOException $e) {

            //         $this->db->rollback();
            //         $this->RemoveProcesso->execute($idProcesso);
            //         return "Erro ao gravar registros no banco de dados, contate o suporte!";
            //     }
            // } else {

            //     return "Erro ao gravar processo no banco de dados, contate o suporte!";
            // }
        }

        return "Sucesso";
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


    // function ajustarColunas(array $colunas, array $headers, int $idConsulta): array
    function ajustarColunas(array $colunas, array $headers): array
    {
        // // Adiciona o índice do idConsulta como primeira coluna
        // array_unshift($colunas, $idConsulta);

        // // Adiciona o nome do campo do idConsulta como primeiro header
        // array_unshift($headers, $idConsulta);

        // while (count($colunas) < count($headers)) {
        //     $colunas[] = '';
        // }

        // return array_slice($colunas, 0, count($headers));
        {
            while (count($colunas) < count($headers)) {
                $colunas[] = '';
            }

            return array_slice($colunas, 0, count($headers));
        }
    }


    //script original
    function processarArquivoCSV(
        $fh,
        array $headersConsulta,
        int $idConsulta,
        array &$coluna_obrigatorio,
        $pathFile,
        $contrato,
        $nomeArquivo,
        $valortotal,
        $fingers
    ) {


        $contadorLinha = 0;
        $registros = [];
        $documentoInvalido = [];

        rewind($fh);
        echo "<pre>";
        echo "minhas colunas obrigatorias vinda do array\n";


        print_R($coluna_obrigatorio);



        $dadosCtr = $this->CapturaRedeLojaDoContrato->execute($contrato);


        while (($linha = fgets($fh)) !== false) {

            $contadorLinha++;

            $linha = preg_replace('/^\xEF\xBB\xBF/', '', $linha);


            $linha = trim($linha);

            if ($linha === '') {
                continue;
            }

            // separa colunas do CSV
            $colunas = str_getcsv($linha, ';');
            echo "<pre>";
            echo "minhas linhas\n";

            print_R($colunas);

            //  normaliza "vazio"
            foreach ($colunas as &$valor) {
                if (strtolower(trim($valor)) === 'vazio') {
                    $valor = '';
                }
            }



            // $colunas = self::ajustarColunas($colunas, $headersConsulta, $idConsulta);
            $colunas = self::ajustarColunas($colunas, $headersConsulta);


            $associado = array_combine($headersConsulta, $colunas);

            // echo "<pre>";
            // echo "dados vem do meu assosiado";
            // print_R($associado);

            if ($associado === false) {
                continue;
            }


            if (!self::validarLinha($associado, $idConsulta, $documentoInvalido, $contadorLinha, $coluna_obrigatorio)) {
                continue;
            }

            // echo "<pre>";
            // echo "documento invalidos";

            // print_R($documentoInvalido);
            $registros[] = implode(';', array_values($associado));
        }
        // fclose($fh);
        // echo "<pre>";
        // echo "meus dados geral";

        // print_R($registros);



        // $totalErros = 0;
        // foreach ($documentoInvalido as $doc) {
        //     $totalErros += $doc['quantidade'];
        // }

        // $totalvalidos = count($registrosValidos);
        $totalRegistros = count($registros);

        // echo "<pre>";

        // print_r($totalRegistros);


        $confCns = $this->MontaJsonConfigEHeadersDaConsulta->execute($idConsulta);
        $resultado = $this->CapturaCamposConsultas->heades($idConsulta);


        $cabe = $resultado[$idConsulta]['tes'];
        $heders = $resultado[$idConsulta]['cpovars'];


        // echo "<pre>";
        // echo "tes";

        // print_r($cabe);
        // echo "<pre>";
        // echo "cpovars";
        // print_r($heders);


        foreach ($registros as $reg) {
            // echo "<pre>";

            // $valores = array_map('trim', explode(';', $reg));

            // // corta ou ajusta conforme o header
            // $valores = array_slice($valores, 0, count($heders));
            // $registroAssociado = array_combine($heders, $valores);

            // // echo "meu registro final\n";
            // // print_R($registroAssociado);


            // // // print_r($registroAssociado);
            // // $valores = array_values($registroAssociado);  // ['89600000000', '01091995', ...]
            // // $payload = json_encode($registroAssociado, JSON_UNESCAPED_UNICODE);


            // $valores = array_map('trim', $valores);
            // $payloads = implode(';', $valores);

            // // echo "<pre>";


            // // print_r($registroAssociado);
            // echo "<pre>";
            // echo "final";
            // print_r($payloads);
        }

        // $idProcesso = $this->gravaProcess->execute(
        //     $contrato,
        //     $dadosCtr['rede'],
        //     $dadosCtr['loja'],
        //     $idConsulta,
        //     $nomeArquivo,
        //     true,
        //     null,
        //     $confCns['json_config'],
        //     // "tcpfcnpj",
        //     $cabe, // salva cabecalho para realizar o processo
        //     $confCns['header_arquivo_principal'],
        //     $valortotal,
        //     $fingers,
        // );

        // $this->db->beginTransaction();

        $totalInseridos = 0;
        try {

            foreach ($registros as $registro) {

                echo "<pre>";

                $valores = array_map('trim', explode(';', $registro));

                // corta ou ajusta conforme o header
                $valores = array_slice($valores, 0, count($heders));
                $registroAssociado = array_combine($heders, $valores);

                // echo "meu registro final\n";
                // print_R($registroAssociado);


                // // print_r($registroAssociado);
                // $valores = array_values($registroAssociado);  // ['89600000000', '01091995', ...]
                // $payload = json_encode($registroAssociado, JSON_UNESCAPED_UNICODE);


                $valores = array_map('trim', $valores);
                $payloads = implode(';', $valores);
                // $payloads = str_replace(';', $valores);

                // echo "<pre>";


                // print_r($registroAssociado);
                echo "<pre>";
                echo "final";
                print_r($payloads);
                // $id = $this->GravaTransacao->execute(
                //     $idProcesso,
                //     $payloads,
                //     0,
                //     0
                // );

                // if ($id) {
                //     $totalInseridos++;
                // }
            }


            // $this->db->commit();
        } catch (Exception $e) {


            // $this->db->rollBack();
            throw $e;
        }
        // }

        return "Sucesso";
    }

    function validarLinha(array $associado, int $idConsulta, array &$documentoInvalido, int $linha, &$obrigatoriosPorConsulta)
    {
        $linhaValida = true;
        $contadorLinha = 0;

        // // obrigatórios por consulta
        // $obrigatoriosPorConsulta = [
        //     265919 => ['tcpfcnpj', 'tdatnsc'],
        //     283092 => ['tcpfcnpj']
        // ];

        // foreach ($obrigatoriosPorConsulta[$idConsulta] ?? [] as $campo) {
        //     if (empty($associado[$campo])) {
        //         $documentoInvalido[$campo]['linha'][] = $linha;
        //         $documentoInvalido[$campo]['quantidade'] =
        //             ($documentoInvalido[$campo]['quantidade'] ?? 0) + 1;
        //         $linhaValida = false;
        //     }
        // }

        foreach ($obrigatoriosPorConsulta as $chave_obrigatoria) {

            $chave_obrigatoria = trim($chave_obrigatoria);

            // echo "<pre>";
            // echo "meus dados da chave obrigatorio\n";


            if (array_key_exists($chave_obrigatoria, $associado) && trim($associado[$chave_obrigatoria]) === '') {
                $linhaValida = false;

                if (!isset($documentoInvalido[$chave_obrigatoria])) {
                    $documentoInvalido[$chave_obrigatoria] = [
                        'linha'   => [$linha],
                        'erro_tipo'  => 'Campo Obrigatório Faltante',
                        'quantidade' => 1
                    ];
                }
                //  / else {

                //     $documentoInvalido[$chave_obrigatoria]['quantidade']++;
                //     //array de linhas que falharam
                //     $documentoInvalido[$chave_obrigatoria]['linha'][] = $contadorLinha;
                //     if (!in_array($contadorLinha, $documentoInvalido[$chave_obrigatoria]['linhas'], true)) {
                //         $documentoInvalido[$chave_obrigatoria]['linhas'][] = $contadorLinha;
                //     }
                // }
            }


            if (isset($associado['tcpfcnpj']) && !empty($associado['tcpfcnpj'])) {

                $docBruto = preg_replace("/\r|\n/", "", $associado['tcpfcnpj']);
                $numero = preg_replace("/\D/", "", $docBruto);

                if (strlen($numero) === 11) {
                    if (!$this->validarDocumento($numero, 'cpf', $registrosValidos, $documentoInvalido)) {
                        $linhaValida = false;
                    }
                } elseif (strlen($numero) === 14) {
                    if (!$this->validarDocumento($numero, 'cnpj', $registrosValidos, $documentoInvalido)) {
                        $linhaValida = false;
                    }
                } else {
                    if (!isset($documentoInvalido[$numero])) {
                        $documentoInvalido[$numero] = [
                            'documento'  => $numero,
                            'valid'      => 0,
                            'reason'     => 'comprimento',
                            'quantidade' => 1
                        ];
                    } else {
                        $documentoInvalido[$numero]['quantidade']++;
                    }
                    $linhaValida = false;
                }
            }


            $coluna  = preg_replace("/\r|\n/", "", implode(';', array_values($associado)));
            $numero = preg_replace("/\D/", "", $coluna);

            if (!$linhaValida) {
                return false;
            }

            // SE a linha for válida, guarda a linha ORIGINAL
            if ($linhaValida) {
                // $registros[] = implode(';', $numero); // This line is not needed here, as $registros is not defined in this scope
                return true;
            }

            //     fclose($fh);
        }
    }

    // // 🔹 valida CPF / CNPJ
    // if (!empty($associado['tcpfcnpj'])) {

    //     $numero = preg_replace('/\D/', '', $associado['tcpfcnpj']);

    //     if (strlen($numero) === 11) {
    //         return self::validarDocumento($numero);
    //     }

    //     if (strlen($numero) === 14) {
    //         return self::validarDocumento($numero);
    //     }

    //     return false;
    // }

    // return $linhaValida;



    public function validarLinhadados($coluna_obrigatorio, $colunas, $associado, &$documentoInvalido, &$contador)
    {
        //     $contadorLinha++;

        //     $chave_obrigatoria = trim($chave_obrigatoria);
        //     // 2. Verificar se a chave existe (Usando isset() para ser mais seguro)
        //     if (isset($associado[$chave_obrigatoria]) && empty($associado[$chave_obrigatoria])) {

        //         if (!isset($documentoInvalido[$chave_obrigatoria])) {
        //             $documentoInvalido[$contadorLinha] = [
        //                 'linha'   =>  implode(';', $colunas),
        //                 'numero_linha' => $contadorLinha,
        //                 // 'documento' => $numero,
        //                 'campo_faltantes' => implode(',', $coluna_obrigatorio),
        //                 'erro_tipo'  => 'Campos_Obrigatorio_faltante',
        //                 'quantidade' => 1
        //             ];

        //             $linhaValida = false;
        //         } else {
        //             $documentoInvalido[$contadorLinha]['quantidade']++;
        //             $documentoInvalido[$contadorLinha][] = $contadorLinha;
        //         }
        //     }


        //     if (isset($documentoInvalido[$contadorLinha])) {
        //         continue;
        //     }

        //     if (isset($associado['tcpfcnpj']) && !empty($associado['tcpfcnpj'])) {

        //         $coluna  = preg_replace("/\r|\n/", "", $associado['tcpfcnpj']);
        //         $numero = preg_replace("/\D/", "", $coluna);

        //         // 3. CPF
        //         if (strlen($numero) === 11) {
        //             if (!self::validarDocumento($numero, 'cpf', $registrosValidos, $documentoInvalido)) {
        //                 $linhaValida = false;
        //             }
        //         } elseif (strlen($numero) === 14) {
        //             if (!self::validarDocumento($numero, 'cnpj', $registrosValidos, $documentoInvalido)) {
        //                 $linhaValida = false;
        //             }
        //         }
        //         // 5. Tamanho inválido
        //         else {
        //             if (!isset($documentoInvalido[$numero])) {
        //                 $documentoInvalido[$numero] = [
        //                     'documento'  => $numero,
        //                     'valid'      => 0,
        //                     'reason'     => 'Número fora do padrão de CPF OU CNPJ',
        //                     'quantidade' => 1
        //                 ];
        //             } else {
        //                 $documentoInvalido[$numero]['quantidade']++;
        //             }
        //             $linhaValida = false;
        //             // break;
        //         }
        //     }

        //     if (!$linhaValida) {
        //         continue;
        //     }
        // }
    }
}
