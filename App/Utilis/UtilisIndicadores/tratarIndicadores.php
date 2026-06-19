<?php

// namespace App\Core\Controller;
// use App\core\Controller;

class tratarIndicadores extends Controller
{

    protected $modelKpi;
    protected $functions;


    ##[Override]
    public function __construct()
    {
        require_once __DIR__ . '../../../models/servicoIndicadores/Kpiindicadores.php';
        $this->modelKpi = new Kpiindicadores();

        require_once __DIR__ . '../../validaCampos.php';
        $this->functions = new validaCampos();
        return parent::__construct();
    }

    public function tratar_dados($dados)
    {

        #AQUI PEGO OS CAMPOS TIPO PARA PEGAR OS CAMPOS QUE VOU INSERIR PRA FICAR DE UMA FORMA FALICITADA
        $retorno_tabela = $this->functions->getDados_tabela($dados['tipo']);

        if (!isset($retorno_tabela)) {
            return $retorno_tabela;
        }

        $result_insert = $this->modelKpi->cad_informacoes($dados, $retorno_tabela);

        return $result_insert;
    }

    public function tratar_vinculo($dados)
    {

        $retorno_tabela = $this->functions->getDados_tabela($dados['tipo']);

        if (isset($retorno_tabela['error'])) {
            return $retorno_tabela;
        }

        $result_insert = $this->modelKpi->vinculador($dados, $retorno_tabela);

        return $result_insert;
    }

    public function cadatrarJobs($dadosCadastros)
    {

        $error = [];


        $retorno_tabela = $this->functions->getDados_tabela($dadosCadastros['tipo']);
        if (isset($retorno_tabela['error'])) {
            return $retorno_tabela;
        }

        $error = $this->functions->validarParametrosDados($dadosCadastros, $retorno_tabela['campos']);

        if (isset($error['error'])) {
            return $error;
        }

        // $dadosCadastros = $this->functions->convertEncode($dadosCadastros);

        $result_insert = $this->modelKpi->cadastrar_jobs($dadosCadastros, $retorno_tabela);

        return $result_insert;
    }

    public function get_jobs()
    {
        $result_insert = $this->modelKpi->lista_jobs();

        return $result_insert;
    }

    public function lista_area()
    {
        $result_get_area = $this->modelKpi->get_area();

        return $result_get_area;
    }

    public function lista_user_area()
    {
        $result_get_user_area = $this->modelKpi->get_user_area();

        return $result_get_user_area;
    }
    public function lista_tipo_solicitacao()
    {
        $result_get_tipo = $this->modelKpi->get_tipo_job();

        return $result_get_tipo;
    }

    public function lista_status_jobs()
    {
        $result_get_status = $this->modelKpi->get_tipo_status();

        return $result_get_status;
    }
    public function lista_perfil_jobs()
    {
        $result_get_perfil = $this->modelKpi->get_tipo_perfil();

        return $result_get_perfil;
    }

    public function lista_clientes()
    {
        $result_get_cliente = $this->modelKpi->get_list_clients();

        return $result_get_cliente;
    }

    public function atualizar_dados($dados)
    {
        $retorno_tabela = $this->functions->getDados_atualiza_jobs($dados['tipo']);

        if (isset($retorno_tabela['error'])) {
            return $retorno_tabela;
        }
        $result_get_cliente = $this->modelKpi->up_dados_jobs($dados, $retorno_tabela);

        return $result_get_cliente;
    }
}
