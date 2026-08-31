
<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// use core\Controller\Controller;
class DecisonController extends Controller
{
    protected $process_dados;
    protected $validaCampos;

    public function __construct()
    {
        $this->process_dados = $this->Utilis('Prodecison/ProUtilis');

        $this->validaCampos = $this->Utilis('ValidarCamposProDecicison');
    }
    public function index_descision()
    {
        $page = 'index'; 
        $dados = [
            'page' => $page,
          
        ];

        // CRIADO ESTRUTURA PARA O VIEW
        return $this->view('decision/view_pro', $dados);
    }
    public function index_acao()
    {
        $page = 'acao'; 
        $dados = [
            'page' => $page,
          
        ];

        // CRIADO ESTRUTURA PARA O VIEW
        return $this->view('decision/view_pro_acao',$dados);
    } 
    
    public function index_comite()
    {
        $page = 'comite'; 
        $dados = [
            'page' => $page,
          
        ];

        // CRIADO ESTRUTURA PARA O VIEW
        return $this->view('decision/view_pro_comite',$dados);
    } 
    public function index_status()
    {
        $page = 'status'; 
        $dados = [
            'page' => $page,
          
        ];

        // CRIADO ESTRUTURA PARA O VIEW
        return $this->view('decision/view_pro_status',$dados);
    } 
}
