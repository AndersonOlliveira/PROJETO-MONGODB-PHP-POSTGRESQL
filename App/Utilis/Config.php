<?php

class Config
{

    public static function env($param)
    {

        $confContent = file_get_contents('C:/xampp_backup/htdocs/projeto74/mvc/App/env.json');
        $obj = json_decode($confContent, true);

        return $obj[$param];
    }
}
