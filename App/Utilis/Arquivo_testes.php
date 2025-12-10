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


        require_once __DIR__ . '/../Utilis/MontaJsonConfigEHeadersDaConsulta.php';
        $this->MontaJsonConfigEHeadersDaConsulta = new MontaJsonConfigEHeadersDaConsulta();
        // require_once 'ValidaCpf.php';
        // $this->ValidaCpf = new ValidaCpf();
        // require_once 'ValidaCnpj.php';
        // $this->ValidaCnpj = new ValidaCnpj();
    }


    // Função genérica para validar CPF ou CNPJ
    public function validarDocumento($doc, $tipo, &$validos, &$invalidos)
    {

        // require_once 'ValidaCnpj.php';
        // require_once 'ValidaCpf.php';

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


    public function process($pathFile, $consultas, $contrato, $nomeArquivo, $valortotal, $headers, $fingers)
    {

        echo "<pre>";
        echo "chamei aquo\n";


        $r = 0;
        $contadorLinha = 0;
        $dadosCtr = $this->CapturaRedeLojaDoContrato->execute($contrato);
        $verificarCampos = $this->CapturaCamposConsultas;
        // $verifryTcpfCnpj =  $verificarCampos->Consultation_header_tdados($consultas[0]);
        $colunasEsperadas = $this->CapturaCamposConsultas->Consultation_header_new(283091);
        // $colunasEsperadas = $verificarCampos->Consultation_header_new($consulta[0]);
        $coluna_obrigatorio = $colunasEsperadas['campos'];

        $documentoInvalido = [];
        $registrosValidos = [];
        $registros = [];
        $fh = fopen($pathFile, "r");

        if ($fh) {
            while (($linha = fgets($fh)) !== false) {

                $linhaValida = true;
                $linhaDados = [];

                $linha = preg_replace('/^\xEF\xBB\xBF/', '', $linha);


                $linhaLimpa = trim($linha);
                if ($linhaLimpa === '') {
                    continue;
                }
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

                if (empty($associado)) {
                    continue;
                }

                $contadorLinha++;
                foreach ($coluna_obrigatorio as $chave_obrigatoria) {

                    $chave_obrigatoria = trim($chave_obrigatoria);


                    if (isset($associado[$chave_obrigatoria]) && empty($associado[$chave_obrigatoria])) {

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


        // $consultas = explode(",", $consultas);

        //troco poor virgula
        die();
        $headers = str_replace(';', ',', $headers);

        foreach ($consultas as $consulta) {

            $confCns = $this->MontaJsonConfigEHeadersDaConsulta->execute($consulta);




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
            //     $headers, // salva cabecalho para realizar o processo
            //     $confCns['header_arquivo_principal'],
            //     $valortotal,
            //     $fingers,
            // );
            $this->db->beginTransaction();

            $totalInseridos = 0;

            foreach ($registros as $registro) {
                $id = $this->GravaTransacao->execute(20, $registro, 0, 0);
                if ($id) $totalInseridos++;
            }

            $this->db->rollBack();

            echo "Total de INSERTS executados (simulados): $totalInseridos";


            // if ($idProcesso) {

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
}
