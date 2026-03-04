<?php
ini_set('memory_limit', '1256M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Process_api
{

    protected $utils;
    protected $filtros;

    public function __construct()
    {
        $this->utils = new Instance();

        require_once __DIR__ . '/../models/process.php';
        $this->filtros = new process();
    }

    public function index($dados)
    {

        $retorno = $this->utils->insert_all_paralizar($dados);

        if ($retorno['success'] > 1) {

            return true;
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

        // $busca_finger_download = $this->utils->get_fingers_parliszar($contrato);



        if (isset($busca_finger_process)   && !isset($busca_finger_process['success']) === false) {

            $dados_completo['processar_inicial'] = $busca_finger_process;
        } else {

            foreach ($busca_finger_process as $key => $dados_process) {

                if ($dados_process['mensagem_alerta'] != null) {
                    $busca_finger_process[$key]['job_reprocessado'] = [
                        'status' => true,
                        'message' => 'job foi reprocessado'
                    ];
                }
            }

            $dados_completo['processar_inicial'] = $busca_finger_process;
        }

        if (isset($busca_finger_download)   && !isset($busca_finger_download['success']) === false) {

            $dados_completo['info_downloads'] = $busca_finger_download;
        } else {
            $dados_completo['info_downloads'] = $busca_finger_download;
        }


        if (isset($retorno_finger_paralizar)) {

            $dados_completo['paralisado'] = $retorno_finger_paralizar;
        }


        if (isset($buscar_ids_contrato) && !isset($buscar_ids_contrato['success']) === false) {
            $dados_completo['jobs_parados'] = $buscar_ids_contrato;
        } else {
            foreach ($buscar_ids_contrato as $key => $values) {
                $resultado = $this->utils->get_finger_parar_reprocessar($values['processo_id']);  // echo
                if (!empty($resultado)) {
                    foreach ($resultado as $doc) {
                        $dados_completo['jobs_parados'][] = $doc;
                    }
                }
            }
        }

        return $dados_completo ?? null;
    }
}
