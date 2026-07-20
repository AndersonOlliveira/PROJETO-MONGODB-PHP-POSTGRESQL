<?php

class validaCamposEconomico
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
            // return trim(mb_convert_encoding($data, 'UTF-8', 'UTF-8, ISO-8859-1'));
            return trim(mb_convert_encoding($data, 'UTF-8', 'ISO-8859-1'));

            // return trim(utf8_encode(utf8_decode($data)));
        } else {
            return trim(utf8_encode(utf8_decode($data)));
        }
        return $data;
    }
    public static function convertEncode($data)
    {
        $data_utf8 = self::utf8ize($data);
        return $data_utf8;
    }

    private static function latin1ize($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::latin1ize($value);
            }
        } elseif (is_string($data)) {
            // Converte de UTF-8 para ISO-8859-1 (Latin1)
            return trim(mb_convert_encoding($data, 'ISO-8859-1', 'UTF-8'));
        }
        return $data;
    }

    public static function convertToLatin1($data)
    {
        return self::latin1ize($data);
    }




    public static function validarParametrosDados($parametros, $chaves)
    {

        //CHAVES VE EM CHAVES
        $dadosError = [];
        foreach ($chaves as $chave) {
        
        
        if (!array_key_exists($chave, $parametros)) {
                $dadosError[] = "$chave nao foi informado!";
                continue;
            }

            // Sanitize string values; if value is array keep structure and sanitize strings recursively
            $parametros[$chave] = self::sanitizeValue($parametros[$chave]);
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
        // $parametros =  ["idAcao", "tctrid", "status", 'id', 'tctraut'];
        $parametros =  ["idAcao", "value_limite", "contratos_afetar", "c_interno"];

        $parametros = self::validarParametrosDados($dados, $parametros);

        return $parametros;
    }
    public function validarParametrosDadoss($parametros, $chaves)
    {

        foreach ($chaves as $chave) {
            if (!array_key_exists($chave, $parametros)) {
                return ["error" => "$chave nao foi informado!"];
            }

            $parametros[$chave] = self::sanitizeValue($parametros[$chave]);
        }

        return $parametros;
    }

    // Recursively sanitize a value: if string -> strip tags and trim; if array -> recurse
    private static function sanitizeValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = self::sanitizeValue($v);
            }
            return $value;
        }

        if (is_string($value) || is_numeric($value)) {
            return trim(strip_tags((string)$value));
        }

        return $value;
    }

    public function getDados_tabela_grupo(int $tipo): array
    {
        $error = [];
        $dados_tabela = [
            2 => [
                'tabela' => 'grupo_economico.config_limite',
                'campos' => [
                    'contrato_cliente',
                    'limite_nivel',
                    'contrato_interno'
                ]
            ],
            //REGRA PARA TALVEZ LIMPA O CAMPO DE  ATIVO E INATIVO
            3 => [
                'tabela' => 'grupo_economico.config_limite',
                'campos' => [
                    'regra_ativa',
                    'contrato_interno'
                ]
            ],
            4 => [
                'tabela' => 'grupo_economico.config_limite',
                'campos' => [
                    'limite_nivel', //VAI RECEBER NULL OU VAZIO PARA DESFAZER
                    'contrato_interno',
                    'contrato_cliente'
                ]
            ],
            5 => [
                'tabela' => 'grupo_economico.config_limite',
                'campos' => [
                    'limite_nivel', //VAI RECEBER NULL OU VAZIO PARA DESFAZER
                    'contrato_interno',
                    'contrato_cliente'

                ]
            ],


        ];
        if (array_key_exists($tipo, $dados_tabela)) {
            return $dados_tabela[$tipo];
        }

        $error['error'] = ['Tipo enviado não encontrado'];
        return $error;
    }

    public function validar_campo_limite(int $limite): array
    {
        $error = [];
        if ($limite == 0) {

            $error['error'] = ['Limite não pode ter o valor com 0'];
            return $error;
        }

        return [true];
    }
}
