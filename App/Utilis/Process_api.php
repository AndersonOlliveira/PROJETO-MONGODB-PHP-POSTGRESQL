<?php


class Process_api
{

    protected $utils;

    public function __construct()
    {
        $this->utils = new Instance();
    }

    public function index($dados)
    {

        $retorno = $this->utils->insert_all_paralizar($dados);

        if ($retorno['success'] > 1) {

            return true;
        }
    }
}
