<?php

class ArquivoValida
{

    protected $capturaLimitArquivo;
    public function __construct()
    {


        require_once __DIR__ . '/../models/CapturaLimitArquivo.php';
        $this->capturaLimitArquivo = new CapturaLimitArquivo();
    }

    public function ValidaFormat($pathFile)
    {

        echo "<pre>";

        print_R("estou acessando\n");
        print_R($pathFile);



        if (!file_exists($pathFile)) {
            return ['error' => true, 'msg' => 'Arquvivo Não pode ser vazio'];
        }

        $extensao = strtolower(pathinfo($pathFile, PATHINFO_EXTENSION));

        if ($extensao != 'csv') {
            return ['error' => true, 'msg' => 'Verifique o formato do arquivo, precisa ser no formato csv'];
        }

        if (!self::ValidateCod($pathFile)) {

            return  ['error' => true, 'msg' => 'Codificação do arquivo deve ser UTF-8 e Enviar arquivo delimitado por ; (ponto e virgula) formato CSV '];
        }

        // list($qtRegistros, $limiteRegArquivo) = self::ValidaQuantidade($pathFile, $contrato);
        list($qtRegistros, $limiteRegArquivo) = self::ValidaQuantidade($pathFile, 417039);


        echo "<pre>";

        print_r($qtRegistros);
        // if ($qtRegistros > 2500) {
        if ($qtRegistros > $limiteRegArquivo) {
            return [
                'error' => true,
                // 'msg' => "Quantidade de registros no arquivo excede o limite de processamento de " . number_format(2500, 0, ',', '.')
                'msg' => "Quantidade de registros no arquivo excede o limite de processamento de " . number_format($limiteRegArquivo, 0, ',', '.')

            ];
        }


        return ['error' => false, 'msg' => 'Arquivo válido.'];
    }

    public static function ValidateCod($pathFile)
    {
        $encoding = mb_detect_encoding(file_get_contents($pathFile));
        if ($encoding != "UTF-8") {

            return  false;
        }

        return true;
    }
    public function ValidaQuantidade($pathFile)
    {

        // require_once 'Config.php';
        // require_once 'CapturaLimitArquivo.php';

        $newlimiteRegArquivo = $this->capturaLimitArquivo->limitArquivo(417039);

        $limiteRegArquivo = $newlimiteRegArquivo['limite_uso'];
        $contador = 0;

        $fh = fopen($pathFile, "r");

        if (($handle = fopen($pathFile, "r")) !== false) {
            while (($linha = fgets($handle)) !== false) {

                $limpa = trim($linha);
                $checarConteudo = str_replace(';', '', $limpa);


                if ($limpa == '' || $checarConteudo == '') {
                    continue;
                }
                $contador++;
            }
            fclose($handle);
        }
        return [$contador, $limiteRegArquivo];
    }
}
