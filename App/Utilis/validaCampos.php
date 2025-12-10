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
            return trim(mb_convert_encoding($data, 'UTF-8', 'UTF-8'));
        }
        return $data;
      
    }
     public static function convertEncode($data)
    {
        $data_utf8 = self::utf8ize($data);
        return $data_utf8;
    }

    }

