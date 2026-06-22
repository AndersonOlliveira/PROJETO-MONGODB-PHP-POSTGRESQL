<?php

class validaCampos
{

    public static function execute($tdata)
    {


        if (preg_match('/^\d{4}$/', $tdata) && !empty($tdata)) {

            return true;
        } else {

            return false;
        }
    }

    public static function ValidadataCompleta($tdataInicio, $tdataFim)
    {

        $validaInicio = DateTime::createFromFormat('Y-m-d', $tdataInicio);
        $validaFim = DateTime::createFromFormat('Y-m-d', $tdataFim);

        if (
            $validaInicio && $validaInicio->format('Y-m-d') == $tdataInicio && !empty($tdataInicio) &&
            $validaFim && $validaFim->format('Y-m-d') == $tdataFim && !empty($tdataFim)
        ) {

            return true;
        } else {
            return false;
        }
    }

    //funcao recorsiva, recebe processar e envia o retorno
    private static function utf8ize($data)
    {

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::utf8ize($value);
            }
        } elseif (is_string($data)) {
            //'UTF-8, ISO-8859-1' 
            return trim(mb_convert_encoding($data, 'UTF-8', 'UTF-8, ISO-8859-1'));
        }
        return $data;
    }
    public static function convertEncode($data)
    {
        $data_utf8 = self::utf8ize($data);
        return $data_utf8;
    }


    public static function validarParametrosDados($parametros, $chaves)
    {
        $dadosError = [];

        foreach ($chaves as $chave) {

            if (!array_key_exists($chave, $parametros)) {
                $dadosError[] = "$chave nao foi informado!";
                continue;
            }

            $parametros[$chave] = trim(strip_tags((string)$parametros[$chave]));
        }

        if (!empty($dadosError)) {
            return ['error' => $dadosError];
        }

        return $parametros;
    }

    public static function validarCamposEnviado($dados)
    {
        $error = [];
        foreach ($dados as $key => $values) {

            $valorLimpo = is_string($values) ? trim($values) : $values;

            if ($valorLimpo === '' || $valorLimpo === null || $valorLimpo === 0 || $valorLimpo === '0') {
                $error['error'][$key] = ["Campo $key não pode ser vazio ou com o valor 0"];
            }
        }


        return $error;
    }
    public function dadosVeficar($dados)
    {
        $parametros = ["campos", "ctr", "status", 'id'];

        $parametros = self::validarParametrosDados($dados, $parametros);

        return $parametros;
    }
    public function validarParametrosDadoss($parametros, $chaves)
    {

        foreach ($chaves as $chave) {
            if (!array_key_exists($chave, $parametros)) {
                return ["error" => "$chave nao foi informado!"];
            }

            $parametros[$chave] = trim(strip_tags($parametros[$chave]));
        }

        return $parametros;
    }

    public function getDados_tabela($tipo)
    {
        $error = [];
        $dados_tabela = [
            0 => [
                'tabela' => 'cadastro_job.jobarea',
                'campos' => [
                    'n_area',
                    'status'
                ]
            ],
            1 => [
                'tabela' => 'cadastro_job.jobtipo',
                'campos' => [
                    'n_tipo',
                    'status'
                ]
            ],
            2 => [
                'tabela' => 'cadastro_job.jobstatus',
                'campos' => [
                    'n_status',
                    'status'
                ]
            ],
            3 => [
                'tabela' => 'cadastro_job.jobperfil',
                'campos' => [
                    'n_perfil',
                    'status'
                ]
            ],
            4 => [
                'tabela' => 'cadastro_job.jobexecutor',
                'campos' => [
                    'n_executor',
                    'status'
                ]
            ],
            5 => [
                'tabela' => 'cadastro_job.jobsolicitante',
                'campos' => [
                    'n_solicitante',
                    'status'
                ]
            ],
            6 => [
                'tabela' => 'cadastro_job.executorjobs',
                'campos' => [
                    'executor_id',
                    'area_id'
                ]
            ],
            7 => [
                'tabela' => 'cadastro_job.executorareasolicitante',
                'campos' => [
                    'area_solicitante_id',
                    'solicitante_id'
                ]
            ],
            9 => [
                'tabela' => 'cadastro_job.jobcadjobs',
                'campos' => [
                    'id_solicitante',
                    'tipoJob',
                    'n_cliente',
                    's_tatus',
                    'perfil',
                    'd_soliciticao',
                    'titulo_email',
                    'ctr'
                ]
            ],
            10 => [
                'tabela' => 'cadastro_job.jobusuarios',
                'campos' => [
                    'n_nome_user',
                    'ctr_interno_cad',
                    'status',
                    'id_area'
                ]
            ],
            11 => [
                'tabela' => 'cadastro_job.jobobservacoes',
                'campos' => [
                    'obs',
                    'job_cad_id',
                ]
            ]
        ];
        if (array_key_exists($tipo, $dados_tabela)) {
            $config = $dados_tabela[$tipo];
        } else {
            return $error['error']  = ["error" => 'Tipo enviado não encontrado'];
        }

        return $config;
    }

    public function getDados_atualiza_jobs($tipo)
    {
        $error = [];
        $dados_tabela = [
            1 => [
                'tabela' => 'cadastro_job.jobcadjobs',
                'campos' => [
                    'data_inicio'
                ]
            ],
            2 => [
                'tabela' => 'cadastro_job.jobcadjobs',
                'campos' => [
                    'data_fim'
                ]
            ],
            3 => [
                'tabela' => 'cadastro_job.jobcadjobs',
                'campos' => [
                    'status_id'
                ]
            ],
            4 => [
                'tabela' => 'cadastro_job.jobcadjobs',
                'campos' => [
                    'executante_id'
                ]
            ]
        ];


        if (array_key_exists($tipo, $dados_tabela)) {
            $config = $dados_tabela[$tipo];
        } else {
            return $error['error']  = ["error" => 'Tipo enviado não encontrado'];
        }

        return $config;
    }


    public function dados_solicitante($dados)
    {
        $parametros = ["id_area_solicitante", "id_solicitante"];

        $parametros = self::validarParametrosDados($dados, $parametros);

        return $parametros;
    }
    public function dados_executor($dados)
    {


        $config = self::getDados_tabela($dados['tipo']);


        if (isset($config['error'])) {
            return $config;
        }


        $campoA = $config['campos'][0];
        $campoB    = $config['campos'][1];
        $parametros = [$campoA, $campoB];

        $parametros = self::validarParametrosDados($dados, $parametros);

        return $parametros;
    }
}
