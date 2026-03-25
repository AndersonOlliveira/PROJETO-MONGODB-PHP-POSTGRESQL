<?php


class PrepagoController extends Controller
{


    protected $utilis_process;
    protected $utilis_processs;
    protected $utilis_processs_teste_arquivos;
    protected $utilis_processs_teste_arquivos_;
    protected $utilis_processs_teste_arquivos_json;
    protected $utilis_processs_teste;
    protected $utilis_processs_prepago;
    protected $utilis_processs_new;
    protected $utilis_process_valida;
    protected $BuscaValorLotePorConsulta;
    protected $CapturaRedeLojaDoContrato;
    protected $arquivos_json;

    public function __construct()
    {
        $this->utilis_process = $this->Utilis('Process_Utilis');
        $this->utilis_processs = $this->Utilis('teste');
        $this->utilis_processs_teste = $this->Utilis('Arquivo_testes');
        $this->utilis_processs_prepago = $this->Utilis('process_prepago');
        // $this->utilis_processs_teste = $this->Utilis('Arquivo_testes_copys');
        $this->utilis_processs_new = $this->Utilis('new_arquivo');
        $this->utilis_process_valida = $this->Utilis('ArquivoValida');
        $this->utilis_processs_teste_arquivos_ = $this->Utilis_arquivo('CONSULTA_PREPAGO');

        $this->utilis_processs_teste_arquivos_json = $this->Utilis_arquivo_json('meu_arquivo');
        // $this->utilis_processs_teste_arquivos_ = $this->Utilis_arquivo('CONSULTA-BASE');

        require_once __DIR__ . '/../models/BuscaValorLotePorConsulta.php';
        $this->BuscaValorLotePorConsulta = new BuscaValorLotePorConsulta();

        require_once __DIR__ . '/../models/CapturaRedeLojaDoContrato.php';
        $this->CapturaRedeLojaDoContrato = new CapturaRedeLojaDoContrato();


        require_once __DIR__ . '/../Utilis/Configs.php';
        $this->arquivos_json = new Configs();
    }

    public function ler_prePago()
    {

        $retorno_query = $this->utilis_processs_prepago->busca_dados_prepago();

        echo "<pre>";
        print_R($retorno_query);
    }
}
