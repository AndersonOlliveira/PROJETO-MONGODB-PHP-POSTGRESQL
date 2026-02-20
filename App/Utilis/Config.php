<?php

class Config
{

    public static function env_old($param)
    {

        // $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'env.json';
        $filePath = file_get_contents('https://site2.proscore.com.br/progestor/env.json');
        $obj = json_decode($filePath, true);

        return $obj[$param];
        // echo "<pre>";
        // echo "meu caminho é: " . $filePath . "\n";

        // print_r($filePath);


        // if (!file_exists($filePath)) {
        //     // Adiciona tratamento de erro caso o arquivo não seja encontrado
        //     throw new Exception("Arquivo de configuração não encontrado em: " . $filePath);
        // }

        // $confContent = file_get_contents($filePath);
        // $obj = json_decode($confContent, true);

        // if (json_last_error() !== JSON_ERROR_NONE) {
        //     throw new Exception("Erro ao decodificar JSON: " . json_last_error_msg());
        // }

        // // Verifica se a chave existe antes de retornar
        // if (!isset($obj[$param])) {
        //     throw new Exception("Parâmetro '{$param}' não encontrado no env.json");
        // }

        // return $obj[$param];
    }

    public static function env($param)
    {

        $confContent = file_get_contents('/usr/chp/pub/prod/pag/progestor/env.json');
        $obj = json_decode($confContent, true);

        return $obj[$param];
    }

    public static function env_json($param)
    {

        $confContent = file_get_contents('https://site2.proscore.com.br/progestor/env.json');

        echo "<pre>";
        echo "meu json";

        print_r($confContent);
        $obj = json_decode($confContent, true);

        return $obj[$param];
    }
}
