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

    public function index_supspensao()
    {

        return $this->view('view_susp');
    }

    public function index_conection()
    {

        $t = $this->Utilis('TesteConection');

        if (is_string($t) && class_exists($t)) {
            $t = new $t();
        }

        $dd = $t->conection();

        return $this->view('view_conection');
    }
}
