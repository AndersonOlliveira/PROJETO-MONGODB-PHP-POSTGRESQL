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

            $headersConsulta = $colunasEsperadas['headersNew'][$idConsulta][$idConsulta]['cpovars'];


            echo "<pre>";
            echo "HEADERS LOCALIZADOS\n";

            print_r($headersConsulta);

            die();

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



    function ajustarColunas(array $colunas, array $headers): array
    { {
            while (count($colunas) < count($headers)) {
                $colunas[] = '';
            }

            return array_slice($colunas, 0, count($headers));
        }
    }

    function validarLinha(array $associado, int $idConsulta, array &$documentoInvalido, int $linha, &$obrigatoriosPorConsulta)
    {
        $linhaValida = true;
        $contadorLinha = 0;


        foreach ($obrigatoriosPorConsulta as $chave_obrigatoria) {

            $chave_obrigatoria = trim($chave_obrigatoria);

            if (array_key_exists($chave_obrigatoria, $associado) && trim($associado[$chave_obrigatoria]) === '') {
                $linhaValida = false;

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
}
