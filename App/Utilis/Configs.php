<?php

class Configs
{

    public static function env($param)
    {

        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'env.json';


        if (!file_exists($filePath)) {
            // Adiciona tratamento de erro caso o arquivo não seja encontrado
            throw new Exception("Arquivo de configuração não encontrado em: " . $filePath);
        }

        $confContent = file_get_contents($filePath);
        $obj = json_decode($confContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Erro ao decodificar JSON: " . json_last_error_msg());
        }

        // Verifica se a chave existe antes de retornar
        if (!isset($obj[$param])) {
            throw new Exception("Parâmetro '{$param}' não encontrado no env.json");
        }

        return $obj[$param];
    }
}
