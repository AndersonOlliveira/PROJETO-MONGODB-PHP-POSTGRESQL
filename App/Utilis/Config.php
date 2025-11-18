<?php

class Config
{

    public static function env($param)
    {

        $confContent = file_get_contents('/usr/chp/pub/prod/pag/progestor/env.json');
        $obj = json_decode($confContent, true);

        return $obj[$param];
    }
}
