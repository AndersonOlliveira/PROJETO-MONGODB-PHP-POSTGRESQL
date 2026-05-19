<?php


class process_Trativas extends Controller
{

    protected $gravaProcess;
    protected $RemoveProcesso;
    protected $GravaTransacao;
    protected $CapturaRedeLojaDoContrato;
    protected $CapturaCamposConsultas;
    protected $ValidaCpf;
    protected $ValidaCnpj;
    protected $MontaJsonConfigEHeadersDaConsulta;
    protected $CapturaJobsFinalizadosGerarSaida;
    protected $CapturaDadosTransacoesJob;
    protected $CapturaRespostasPluginsTransacao;
    protected $db;
    protected $limpaString;
    protected $Config;
    protected $enconde;
    protected $mongo;
    protected $arquivo_teste;
    protected $soaps;

    protected $filtros;
    protected $utilis_pgadmin;

    public function __construct()
    {


        $model = new Model();
        $this->db = $model->getConnection();

        require_once __DIR__ . '/../models/GravaProcesso.php';
        $this->gravaProcess = new GravaProcesso();
        require_once __DIR__ . '/../models/RemoveProcesso.php';
        $this->RemoveProcesso = new RemoveProcesso();
        require_once __DIR__ . '/../models/GravaTransacao.php';
        $this->GravaTransacao = new GravaTransacao();
        require_once __DIR__ . '/../models/CapturaRedeLojaDoContrato.php';
        $this->CapturaRedeLojaDoContrato = new CapturaRedeLojaDoContrato();
        require_once __DIR__ . '/../models/CapturaCamposConsultas.php';
        $this->CapturaCamposConsultas = new CapturaCamposConsultas();

        require_once __DIR__ . '/../models/CapturaDadosTransacoesJob.php';
        $this->CapturaDadosTransacoesJob = new CapturaDadosTransacoesJob();
        require_once __DIR__ . '/../models/CapturaJobsFinalizadosGerarSaida.php';
        $this->CapturaJobsFinalizadosGerarSaida = new CapturaJobsFinalizadosGerarSaida();


        require_once __DIR__ . '/../models/CapturaRespostasPluginsTransacao.php';
        $this->CapturaRespostasPluginsTransacao = new CapturaRespostasPluginsTransacao();


        require_once __DIR__ . '/../Utilis/MontaJsonConfigEHeadersDaConsulta.php';
        $this->MontaJsonConfigEHeadersDaConsulta = new MontaJsonConfigEHeadersDaConsulta();

        require_once __DIR__ . '/../Utilis/MontaJsonConfigEHeadersDaConsulta.php';
        $this->MontaJsonConfigEHeadersDaConsulta = new MontaJsonConfigEHeadersDaConsulta();

        require_once __DIR__ . '/../Utilis/Configs.php';
        $this->Config = new Configs();

        require_once __DIR__ . '/../Utilis/validaCampos.php';
        $this->enconde = new ValidaCampos();

        require_once __DIR__ . '/../Utilis/LimpaString.php';
        $this->limpaString = new LimpaString();

        require_once __DIR__ . '/../models/instance.php';
        $this->mongo = new instance();

        require_once __DIR__ . '/../Utilis/Arquivo_testes.php';
        $this->arquivo_teste = new Arquivo_testes();

        require_once __DIR__ . '/../Utilis/soaps.php';
        $this->soaps = new soaps();
        require_once __DIR__ . '/../models/process.php';
        $this->filtros = new process();


        require_once __DIR__ . '/../models/Crc_tratativas.php';
        $this->utilis_pgadmin = new Crc_tratativas();
    }



    public function trata_dados_trativa($dados)
    {

        echo '<pre>';
        echo "dados enviando via controller\n";
        // NULL POR CONTA QUE NÁO VOU ENVIAR O ID PARA CONSULTA
        $result_process = $this->utilis_pgadmin->verifry_cobraca(null, $dados);

        // var_dump($tipo_acoes);
    } 
    
    
    public function seachDataAll($dados)
    {
        
    if(isset($dados['tdataInicio']) && isset($dados['tdataFim'])){
            $tdataInicio =$dados['tdataInicio']; 
            $tdataFim =$dados['tdataFim']; 
            $result_process = $this->utilis_pgadmin->return_dados_data(null,$tdataInicio,$tdataFim);
      
            }else{
                
            $result_process = $this->utilis_pgadmin->return_dados_data($dados['mes'],null,null);    
       }

        if($result_process){

          return $result_process;
        }
    }
}
