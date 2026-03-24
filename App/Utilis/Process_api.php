<?php
ini_set('memory_limit', '1256M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Process_api
{

    protected $utils;
    protected $filtros;
    protected $arquivos_json;

    public function __construct()
    {
        $this->utils = new Instance();

        require_once __DIR__ . '/../models/process.php';
        $this->filtros = new process();

        require_once __DIR__ . '/../Utilis/Config.php';
        $this->arquivos_json = new Config();
    }

    public function index($dados)
    {

        $retorno = $this->utils->insert_all_paralizar($dados);

        if ($retorno['success'] > 1) {

            return true;
        }
    }

    public function index_cancelar($dados)
    {
        $retorno = $this->utils->insert_all_cancelar($dados);

        if ($retorno['success'] > 1) {

            return true;
        }
    }

    public function insert_all_paralizar_reprocesar($dados)

    {

        $dadosDecode = json_decode($dados, true);

        $retorno = $this->utils->insert_all_paralizar_reprocesar_jobs($dadosDecode['id_processo'], $dadosDecode['paralisado'], $dadosDecode['acao'], $dadosDecode['finger']);
        if ($retorno['success'] == 1) {

            return true;
        }
    }

    public function get_dados_finger_parar($id_processo, $contrato)
    {

        $retorno = $this->utils->busca_dados_finger_parar($id_processo);

        if ($retorno) {

            $prazoMaximos = $this->filtros->get_limit_day_contrato($contrato);
            $data_paralisacao = new DateTime($retorno);

            // $data_paralisacao->modify('-1 day');
            $data_paralisacao->setTime(0, 0, 0);
            $hoje = new DateTime();
            $hoje->setTime(0, 0, 0);


            $diferenca = $data_paralisacao->diff($hoje)->days;

            if ($diferenca > $prazoMaximos) {
                return true;
            } else {
                return false;
            }


            // Loop para adicionar a quantidade X de dias úteis para considierar dias ulteis
            // for ($i = 0; $i < $prazoMaximo; $i++) {
            // 	$data_paralisacao->modify('+1 weekday');
            // }



            // $data_paralisacao->modify('+' . $prazoMaximos .  'days');


            // // echo "Início: " . $retorno . "\n";
            // // echo "Prazo final (" . $prazoMaximos . " dias úteis): " . $data_paralisacao->format('d/m/Y H:i:s') .  "\n";

            // $msg = '';

            // // Comparação com a data atual para encerrar o trabalho
            // $hoje = new DateTime();
            // if ($hoje > $data_paralisacao) {
            //     // echo "STATUS: ENCERRAR TRABALHO (Prazo excedido) " . $id_processo .  " do contrato " .  $contrato . "\n";

            //     return true;
            // } else {
            //     return false;
            // }
        }
    }
    public function get_dados_finger($contrato)
    {


        $dados_completo = [];


        // buscos os dados por correspondencia ao id
        $retorno_finger_paralizar = $this->utils->get_finger_paralizar($contrato);
        $buscar_ids_contrato = $this->filtros->get_ids_contrato($contrato);
        //finger do aceite a processar
        $busca_finger_process = $this->filtros->get_fingers_process_jobs($contrato);
        $busca_finger_download = $this->filtros->get_fingers_downloads_jobs($contrato);

        $busca_finger_cancelar = $this->utils->get_fingers_cancelar($contrato);


        // $data = json_decode($json, true);

        // foreach ($data as $key => $value) {
        //     if (is_string($value)) {
        //         $data[$key] = corrigirTexto($value);
        //     }
        // }

        // die();
        foreach ($busca_finger_process as $keys => $dados_process) {

            $dados = json_decode($dados_process['finger'], true);

            if (is_array($dados) && isset($dados) !== null) {
                array_walk_recursive($dados, function (&$item) {
                    if (is_string($item)) {
                        $item =  self::corrigirTexto($item);
                    }
                });

                $busca_finger_process[$keys]['finger'] = json_encode($dados, JSON_UNESCAPED_UNICODE);

                $busca_finger_process[$keys]['data_cadastro'] = date("Y-m-d H:i:s", strtotime($dados_process['data_cadastro']));
            } else {
                // secho "Erro: JSON inválido na chave $keys";
            }
        }

        if (isset($busca_finger_process)   && !isset($busca_finger_process['success']) === false) {

            $dados_completo['Processamento Inicial'] = $busca_finger_process;
        } else {

            foreach ($busca_finger_process as $keys => $dados_process) {

                $info_reprocess = $this->utils->get_finger_info_reprocess($dados_process['processo_id']);

                $info_reprocess = isset($info_reprocess) ? $info_reprocess : null;

                if ($info_reprocess !== null) {
                    foreach ($info_reprocess as $key => $dados_reprocess) {

                        if (!empty($dados_reprocess->requested)) {
                            $busca_finger_process[$keys]['job_reprocessado'] = [
                                'status' => true,
                                'message' => 'job foi reprocessado',
                                'dados' => $info_reprocess,

                            ];
                        }
                    }
                }


                // if ($dados_process['mensagem_alerta'] != null) {
                //     $busca_finger_process[$key]['job_reprocessado'] = [
                //         'status' => true,
                //         'message' => 'job foi reprocessado'
                //     ];
                // }
            }

            $dados_completo['Processamento Inicial'] = $busca_finger_process;
        }


        if (isset($busca_finger_download)   && !isset($busca_finger_download['success']) === false) {

            $dados_completo['Downloads'] = $busca_finger_download;
        } else {

            foreach ($busca_finger_download as $key => $values) {
                $busca_finger_download[$key]['finger_download']  = $this->utils->removerAcentos($values['finger_download']);
            }


            $dados_completo['Downloads'] = $busca_finger_download;
        }


        if (isset($retorno_finger_paralizar)) {

            $dados_completo['Paralisados'] = $retorno_finger_paralizar;
        } else {
            $dados_completo['Paralisados'] = [
                'success' => (boolval(false)),
                'message' => 'dados para o contrato não localizado'
            ];
        }

        if (isset($busca_finger_cancelar)) {

            $dados_completo['Cancelados'] = $busca_finger_cancelar;
        } else {
            $dados_completo['Cancelados'] = [
                'success' => (boolval(false)),
                'message' => 'dados para o contrato não localizado'
            ];
        }


        if (isset($buscar_ids_contrato) && !isset($buscar_ids_contrato['success']) === false) {
            $dados_completo['Processo Parados'] = $buscar_ids_contrato;
        } else {
            foreach ($buscar_ids_contrato as $key => $values) {
                $resultado = $this->utils->get_finger_parar_reprocessar($values['processo_id']);  // echo
                if (!empty($resultado)) {
                    foreach ($resultado as $doc) {
                        $dados_completo['Processos Parados'][] = $doc;
                    }
                }
            }
        }

        return $dados_completo ?? null;
    }

    public function die_status_process($dados)
    {
        $novoRegistro = [];

        $retorno_path_arquivo = $this->arquivos_json->env_json('path_arquivos_info');
        $arquivos = $retorno_path_arquivo  . DIRECTORY_SEPARATOR .  'meu_arquivo.json';

        $decodificadados  = json_decode($dados, true);

        $documento = file_get_contents($arquivos);

        $dados_json = json_decode($documento, true);

        $novoRegistro = [
            'id_process'     => $decodificadados['idProcess'],
            'data_alteracao' => date('Y-m-d H:i:s'),
            'status' => 6,
            //  'Qta_Original' => $data['qta'],
            // 'infos' => 'Qta_Original  ' . $data['qta'] . ' Qta_Afetada ' . $result_trasantion['qta_afetadas'],
            'info_auditoria_finger' => $decodificadados['info_finger'],
            'paralizar_processos' => $decodificadados['paralizar_processos']
        ];

        $arquivo = $retorno_path_arquivo  . DIRECTORY_SEPARATOR .  'meu_arquivo.json';

        $dados_json = [];

        if (file_exists($arquivo)) {
            $conteudo = file_get_contents($arquivo);
            $dados_json = json_decode($conteudo, true) ?: [];
        }

        $existe = false;

        foreach ($dados_json as $registro) {

            if ($registro['id_process'] === $decodificadados['idProcess']) {
                $existe = true;

                break;
            }
        }
        // Se não existir, adiciona
        if (!$existe) {
            $dados_json[] = $novoRegistro;
            file_put_contents($arquivo, json_encode($dados_json, JSON_PRETTY_PRINT));
            //realizo o up dentro da tabela process 
            return $this->filtros->alter_die_msg_process($decodificadados['idProcess']);
        }
    }


    public function info_reprocess_dados($id)
    {
        $retorno_info_reprocess = $this->utils->get_dados_info_reprocess($id);

        return !empty($retorno_info_reprocess)
            ? [
                'info_reprocess' => $retorno_info_reprocess->info_reprocess,
                'msg' => $retorno_info_reprocess->msg ?? null,
                'data_paralizacao' => $retorno_info_reprocess->data_alteracao ?? null
            ]
            : [
                'info_reprocess' => null,
                'msg' =>  null,
                'data_paralizacao' => null
            ];
    }

    public function paralizar_date_info($id)
    {

        $retorno = $this->utils->get_dados_info_paralizar_die($id);

        //vou contar a quantide de dados que foram parados com a execucáo do paralizar

        $novo_dados = [];

        if ($retorno != null) {

            $contar_paralizar = $this->filtros->count_process_paradados($id);

            $info_msg =   !empty($retorno->processo_finalizado) ? $retorno->processo_finalizado : 'Sem info de data';

            $novo_dados = ['msg_info' => $info_msg .  ',' . ' na data de  ' . $retorno->data_finalizacao . ',' . ' total de registros paralizados ' . $contar_paralizar['total_paralizados']];
        }

        return !empty($retorno)
            ? $novo_dados
            : null;
    }

    function corrigirTexto($texto)
    {
        $correcoes = [
            'So Paulo' => 'Sao Paulo',
            'Rio de Janero' => 'Rio de Janeiro',

        ];

        return strtr($texto, $correcoes);
    }
}
