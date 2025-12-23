<?php

class ArquivoValida
{


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

        echo "<pre>";
        echo "chamei aqui\n";

        $contador = 0;


        echo "<pre>";

        print_R($pathFile);

        $fh = fopen($pathFile, "r");

        if ($fh) {
            while (($linha = fgets($fh)) !== false) {
                $contador++;

                echo "<pre>";

                // print_R($linha);
            }
        }

        echo "<pre>";
        echo "MINHA QUANTIDADE\n";

        print_R($contador);
    }
}
