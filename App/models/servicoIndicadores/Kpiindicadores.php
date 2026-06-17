
<?php

use App\core\AppManipularError;


class Kpiindicadores extends Model
{



    protected $arquivoLog;

    protected $ajustar;
    protected $functions;
    private  $errorHandler;


    public function __construct()
    {
        parent::__construct();


        // $this->arquivoLog = $_SERVER['DOCUMENT_ROOT'] . '../error/errorKpi.txt';

        require_once __DIR__ . "/../../Utilis/validaCampos.php";

        $this->ajustar = new validaCampos();

        $this->arquivoLog =  __DIR__ . '/../../../error/errorKpi.txt';
        //    C:\xampp_backup\htdocs\projeto74\mvc\App\core\AppManipularError.php
        require_once __DIR__ . '/../../core/AppManipularError.php';

        $this->errorHandler = new AppManipularError($this->arquivoLog);
        $diretorio  = $this->arquivoLog;

        $diretorio = dirname($this->arquivoLog);
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        if (!file_exists($this->arquivoLog)) {
            touch($this->arquivoLog);
            chmod($this->arquivoLog, 0664);
        }
    }


    public function cad_jobs($registros)
    {

        echo "<pre>";

        print_R("CHANDO NA TELA DE MODEL\n");

        print_R($registros);

        try {

            $nivel  = 10;
            $mensagem = 'Erro na busca das açoes ';
            $arquivo = __FILE__;
            $linha = __LINE__;

            set_error_handler(function ($nivel, $mensagem, $arquivo, $linha) {
                $this->errorHandler->manipuladorDeErros(
                    $nivel,
                    $mensagem,
                    $arquivo,
                    $linha,
                    $this->arquivoLog
                );
            });
        } catch (Exception $e) {

            echo "meu catch" . $e->getCode();
        }
        try {

            throw new Exception('Teste');
        } catch (Exception $e) {

            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );
        }

        set_exception_handler(function ($exception) {
            error_log(
                $exception->getMessage(),
                3,
                $this->arquivoLog
            );
        });
    }

    public function cad_informacoes($dados, $config)
    {
        extract($dados);

        $sql = "";
        $error = [];
        $msg = [];


        $tabela = $config['tabela'];
        $campoPrincipal = $config['campos'][0];
        $campoStatus    = $config['campos'][1];
        $campo = strtoupper($campos);
        if (isset($config['campos'][3])) {
            $campoId = $config['campos'][3];
            $campoStatus  = $config['campos'][2];
            $sql = "INSERT INTO {$tabela}
            ({$campoPrincipal}, ctr_interno_cad, {$campoStatus}, {$campoId})
             SELECT
                    :campo_inserir,
                    :ctr_interno_cad,
                    :status,
                    :id_inserir
                    WHERE NOT EXISTS (
                    SELECT 1
                    FROM {$tabela}
                    WHERE UPPER({$campoPrincipal}) = :campo_check
                )";
        } else {
            $sql = "INSERT INTO {$tabela} ({$campoPrincipal}, ctr_interno_cad, {$campoStatus})
                    SELECT
                    :campo_inserir,
                    :ctr_interno_cad,
                    :status
                    WHERE NOT EXISTS (
                    SELECT 1
                    FROM {$tabela}
                    WHERE UPPER({$campoPrincipal}) = :campo_check)";
        }



        try {

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':campo_inserir', $campo, PDO::PARAM_STR);
            $stmt->bindParam(':ctr_interno_cad', $ctr, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_BOOL);
            $stmt->bindParam(':campo_check', $campo, PDO::PARAM_STR);

            if (isset($config['campos'][3])) {
                $stmt->bindParam(':id_inserir', $id, PDO::PARAM_STR);
            }
            $stmt->execute();

            #lista de erros 
            return  $stmt->rowCount() >  0 ? $msg[] = ["MSG" => "Sucesso em inserir o dado informado"] : $error[] = ["error" => "Dados informado já inserido na {$tabela}"];
        } catch (Exception $e) {
            //CRIA O 
            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );

            return $error[] = ['error' => "Falha em inserir dados na tabela {$tabela},cod error: {$e->getCode()}"];
        }
    }

    public function vinculador($dados, $config)
    {
        extract($dados);

        $sql = "";
        $error = [];
        $msg = [];

        $tabela = $config['tabela'];


        $campoA = $config['campos'][0];
        $campoB    = $config['campos'][1];


        if ((isset($dados['tipo']) ? $dados['tipo'] : null) == 6) {
            $campo_a_inserir = isset($dados['executor_id']) ? $dados['executor_id'] : null;
            $campo_b_inserir = isset($dados['area_id']) ? $dados['area_id'] : null;
        } else {
            $campo_a_inserir = isset($dados['id_area_solicitante']) ? $dados['id_area_solicitante'] : null;
            $campo_b_inserir = isset($dados['id_solicitante']) ? $dados['id_solicitante'] : null;
        }

        echo "<pre>";
        echo "MEUS DADOS\n";
        echo $campoA;
        echo "-------\n";


        print_r($campo_a_inserir);
        echo "-------\n";
        print_r($campo_b_inserir);





        $sql = "INSERT INTO {$tabela} ($campoA, $campoB)
                SELECT
                    :campo_vincular,
                    :campo_solicitante
                    WHERE NOT EXISTS (
                    SELECT 1
                    FROM {$tabela}
                    WHERE $campoA = :campo_check
                )
            ";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':campo_vincular', $campo_a_inserir, PDO::PARAM_INT);
            $stmt->bindParam(':campo_solicitante', $campo_b_inserir, PDO::PARAM_INT);
            $stmt->bindParam(':campo_check', $campo_a_inserir, PDO::PARAM_INT);
            $stmt->execute();
            #lista de erros 
            return  $stmt->rowCount() >  0 ? $msg[] = ["MSG" => "Sucesso ao vincular dados"] : $error[] = ["error" => "Falha ao inserir verifique campos enviados, ou executante já esta vinculado"];
        } catch (Exception $e) {


            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );

            return $error[] = ['error' => "Falha ao Vincular os dados, cod error: {$e->getCode()}"];
        }
    }
    public function cadastrar_jobs($dados, $config)
    {
        $tabela = $config['tabela'];
        $error = [];
        $msg = [];
        $detalhamento =  !empty($dados['detalhamento']) ? $dados['detalhamento'] : null;

        $sql = "INSERT INTO {$tabela} (executante_id, job_id, area_id, nome_cliente, status_id, perfil_id, data_solicitacao, titulo_email, detalhamento, ctr_interno_cad)
        VALUES (:executante_id, :job_id, :area_id, :nome_cliente, :status_id, :perfil_id, :data_solicitacao, :titulo_email, :detalhamento, :ctr_interno_cad);";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':executante_id', $dados['executor']);
            $stmt->bindParam(':job_id', $dados['tipoJob']);
            $stmt->bindParam(':area_id', $dados['area']);
            $stmt->bindParam(':nome_cliente', $dados['n_cliente']);
            $stmt->bindParam(':status_id', $dados['s_tatus']);
            $stmt->bindParam(':perfil_id', $dados['perfil']);
            $stmt->bindParam(':data_solicitacao', $dados['d_soliciticao']);
            $stmt->bindParam(':titulo_email', $dados['titulo_email']);
            $stmt->bindParam(':detalhamento', $detalhamento);
            $stmt->bindParam(':ctr_interno_cad', $dados['ctr']);

            if ($stmt->execute()) {

                return $stmt->rowCount() > 1 ? $msg[] = ["MSG" => "Sucesso em Cadastrar Job"] : $error[] = ["error" => "Falha ao inserir verifique campos enviados!"];
            }
        } catch (PDOException $e) {

            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );

            return $error[] = ['error' => "Falha em Cadastrar Job, cod error: {$e->getCode()}"];
        }
    }
    public function lista_jobs()
    {
        $error = [];
        $msg = [];
        $registros = [];


        $sql = "WITH executor AS (
                    SELECT
                        jobs.id_job_executor AS id_executor,
                        ex.n_executor,
                        jbarea.n_area
                    FROM cadastro_job.executorjobs jobs
                    INNER JOIN cadastro_job.jobexecutor ex
                        ON ex.id_executor = jobs.executor_id
                    INNER JOIN cadastro_job.jobarea jbarea
                        ON jbarea.id_area = jobs.area_id
                ),
                job_tipo AS (
                    SELECT
                        jbsolicitante.id_solicitante,
                        jbsolicitante.n_solicitante AS solicitante,
                        jbarea.n_area
                    FROM cadastro_job.executorareasolicitante exSolicite
                    INNER JOIN cadastro_job.jobsolicitante jbsolicitante
                        ON jbsolicitante.id_solicitante = exSolicite.id_solicitante
                    INNER JOIN cadastro_job.jobarea jbarea
                        ON jbarea.id_area = exSolicite.area_solicitante_id
                ),
                job_status AS (
                    SELECT
                        jstatus.n_status,
                        jstatus.id_status
                    FROM cadastro_job.jobstatus jstatus
                ),
                job_perfil as (
                SELECT
                        jperfil.n_perfil,
                        jperfil.id_perfil
                    FROM cadastro_job.jobperfil jperfil
                )
                SELECT
                    exc.id_executor,
                    exc.n_executor,
                    tp.id_solicitante,
                    tp.solicitante ,
                    tp.n_area as area_solicitante,
                    tstatus.n_status,
                    cadjobs.data_inicio,
                    cadjobs.data_fim,
                    cadjobs.titulo_email,
                    cadjobs.detalhamento,
                    cadjobs.data_cad_job,
                    cadjobs.nome_cliente,
                    perjobs.n_perfil
                    
                FROM cadastro_job.jobcadjobs cadjobs
                INNER JOIN executor exc
                    ON exc.id_executor = cadjobs.executante_id
                INNER JOIN job_tipo tp
                    ON tp.id_solicitante = cadjobs.job_id
                INNER JOIN job_status tstatus
                    ON tstatus.id_status = cadjobs.status_id
                INNER JOIN job_perfil perjobs
                    ON perjobs.id_perfil = cadjobs.perfil_id;";




        try {

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


                    $newDate = new DateTime($row['data_cad_job']);
                    $row['data_cad_job'] = $newDate->format('d-m-Y');
                    $registros[] = $row;
                }
                return $registros;
            } else {

                return $error[] = ['error' => 'Dados Não localizados'];
            }
        } catch (PDOException $e) {

            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );

            return $error[] = ['error' => "Falha em Solicitar os dados Job, cod error: {$e->getCode()}"];
        }
    }

    // SOLICITAR AREA 
    public function get_area()
    {


        $error = [];

        $config = $this->ajustar->getDados_tabela(0);

        $sql = "SELECT id_area, n_area FROM {$config['tabela']}";


        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );

            return $error[] = ['error' => "Falha em Solicitar os dados area, cod error: {$e->getCode()}"];
        }
    }

    public function get_user_area()
    {

        $sql = "";


        $sql = "SELECT jbus.id as usuario_id,jbus.status as status_user, 
              CONCAT(jbus.n_nome_user, '-', jba.n_area) AS user_area
              FROM cadastro_job.jobusuarios jbus
              INNER JOIN cadastro_job.jobarea jba ON (jba.id_area = jbus.id_area)";

        try {

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );
        }
    }
    public function get_tipo_job()
    {

        $sql = "";


        $sql = "SELECT 
              id_job, n_job
              FROM cadastro_job.jobtipo";

        try {

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );
        }
    }
}

?>