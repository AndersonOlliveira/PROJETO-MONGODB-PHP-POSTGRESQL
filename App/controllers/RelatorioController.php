<?php



class RelatorioController extends Controller
{

    protected $utilis_processs_teste_arquivos_json;

    public function __construct()
    {

        // $this->utilis_processs_teste_arquivos_json = $this->Utilis_javaScript('tratativa');
    }
    public function index_relatorio()
    {

        return $this->view('view_relatorio');
    }
}
