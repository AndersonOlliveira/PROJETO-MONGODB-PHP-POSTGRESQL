<?php

class ArquivoValida
{

    protected $CapturaLimitArquivo;
    public function __construct()
    {


        require_once __DIR__ . '/../models/CapturaLimitArquivo.php';
        $this->CapturaLimitArquivo = new CapturaLimitArquivo();
    }

    public static function ValidaFormat($pathFile)
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

        require_once 'Config.php';
        require_once 'CapturaLimitArquivo.php';

        // $newlimiteRegArquivo = $this->capturaLimitArquivo->limitArquivo(417039);

        $limiteRegArquivo = $newlimiteRegArquivo['limite_uso'];
        $contador = 0;

        $fh = fopen($pathFile, "r");

        if (($handle = fopen($pathFile, "r")) !== false) {
            while (($linha = fgets($handle)) !== false) {

                if (trim($linha) == '') {
                    continue;
                }
                $contador++;
            }
            fclose($handle);
        }
        return [$contador, $limiteRegArquivo];
    }
}
