<?php


class ProcessController extends Controller
{

    protected $utilis_process;
    protected $utilis_processs;

    public function __construct()
    {
        $this->utilis_process = $this->Utilis('Process_Utilis');
        $this->utilis_processs = $this->Utilis('teste');
    }

    public function get_all_query()
    {

        $return = $this->utilis_process->get_query();

        echo "<pre>";

        print_r($return);

        return $this->view('Query_active');
    }
    public function get_all_teste()
    {

        $return = $this->utilis_processs->lista_testessss();



        return $this->view('listar_teste');
    }

    public function cpu_server()
    {

        try {
            $cpu_load = self::get_cpu_usage();

            http_response_code(200);
            ob_clean();
            echo json_encode(
                [
                    'status' => 2,
                    'messsage' => 'sucesso em solicitar',
                    'data' => $cpu_load
                ]
            );
        } catch (Exception $e) {
            echo "Falha ao obter CPU: " . $e->getMessage();
        }
    }

    function get_cpu_usage()
    {
        $processes = shell_exec("ps aux --sort=-%cpu | head -10");
        return $processes;
    }
}
