<?php


class process_prepago
{



    protected $utils;
    protected $tratamento;

    protected $MontaJsonConfigEHeadersDaConsultas;
    protected $GravaTransacao;
    protected $GravaRespostaPlugin;
    protected $GravaUpdateParalizar;
    protected $teste;
    protected $filtros;
    protected $instance;

    protected $CapturaRedeLojaDoContrato;
    protected $CapturaCamposConsultas;
    protected $BuscaValorLotePorConsulta;
    protected $GravarUpdateDieProcess;
    protected $arquivos_json;


    public function __construct()
    {

        $this->utils = new Instance();
        require_once 'MontaJsonConfigEHeadersDaConsulta.php';
        $this->MontaJsonConfigEHeadersDaConsultas = new MontaJsonConfigEHeadersDaConsulta();

        require_once __DIR__ . '/../models/GravaTransacao.php';
        $this->GravaTransacao = new GravaTransacao();
        //  $this->GravaTransacao = $this->utils = new GravaTransacao();

        require_once __DIR__ . '/../models/GravaRespostaPlugin.php';
        $this->GravaRespostaPlugin = new GravaRespostaPlugin();

        require_once __DIR__ . '/../models/GravaUpdateParalizar.php';
        $this->GravaUpdateParalizar = new GravaUpdateParalizar();

        require_once __DIR__ . '/../models/GravarUpdateDieProcess.php';
        $this->GravarUpdateDieProcess = new GravarUpdateDieProcess();

        require_once __DIR__ . '/../models/process.php';
        $this->teste = new process();

        require_once __DIR__ . '/../models/process.php';
        $this->filtros = new process();

        require_once __DIR__ . '/../models/instance.php';
        $this->instance = new instance();

        require_once __DIR__ . '/../models/CapturaRedeLojaDoContrato.php';
        $this->CapturaRedeLojaDoContrato = new CapturaRedeLojaDoContrato();
        require_once __DIR__ . '/../models/CapturaCamposConsultas.php';
        $this->CapturaCamposConsultas = new CapturaCamposConsultas();
        require_once __DIR__ . '/../models/BuscaValorLotePorConsulta.php';
        $this->BuscaValorLotePorConsulta = new BuscaValorLotePorConsulta();

        require_once __DIR__ . '/../Utilis/Config.php';
        $this->arquivos_json = new Config();
    }

    public function busca_dados_prepago()
    {


        $retorno_prepago = $this->instance->get_prePago_info();

        echo "<pre>";

        if (!isset($retorno_prepago)) {
            echo "<pre>";

            print_r('meus daddos vindo aquio');
        }

        var_dump($retorno_prepago);
    }
}
