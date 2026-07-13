<?php


class grupoEconomicoUtilis extends Controller
{


    protected $modelGrupo;
    protected $functions;


    ##[Override]
    public function __construct()
    {
        require_once __DIR__ . '../../../models/grupoEconomico/grupoEconomico.php';
        $this->modelGrupo = new grupoEconomico();

        require_once __DIR__ . '../../validaCampos.php';
        $this->functions = new validaCampos();
        return parent::__construct();
    }


    public function search($dados)
    {


        extract($dados);


        return $this->modelGrupo->lista_rde_loja($c_cliente_search, $tipo_busca);
    }
}
