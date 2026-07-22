
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

        $campo = $this->ajustar->convertToLatin1($campo);

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

    public function cad_informacoes_obs($dados, $config)
    {
        extract($dados);

        $sql = "";
        $error = [];
        $msg = [];

        $tabela = $config['tabela'];
        $campoObs = $config['campos'][0];
        $campoId    = $config['campos'][1];
        $campo = strtoupper($campos);


        $sql = "INSERT INTO {$tabela} ({$campoObs}, crt_interno_obs, {$campoId})
            VALUES (:campo_inserir,:crt_interno_obs,:id_inserir)";

        $campo = $this->ajustar->convertToLatin1($campo);


        try {

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':campo_inserir', $campo, PDO::PARAM_STR);
            $stmt->bindParam(':crt_interno_obs', $ctr, PDO::PARAM_INT);
            $stmt->bindParam(':id_inserir', $id, PDO::PARAM_INT);
            $stmt->execute();
            #lista de erros 
            return  $stmt->rowCount() > 0 ? $msg[] = ["MSG" => "Sucesso em inserir o dado informado"] : $error[] = ["error" => "Dados informado já inserido na {$tabela}"];
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

        $id_cliid = explode('$', $dados['n_cliente']);

        $tratado  = $id_cliid[0] == 'novo' ? null : $id_cliid[0];

        $titulo = $this->ajustar->convertToLatin1($dados['titulo_email']);
        $detalhamento = $this->ajustar->convertToLatin1($detalhamento);



        $sql = "INSERT INTO {$tabela} (solicitante_id, job_id, nome_cliente, status_id, perfil_id, data_solicitacao, titulo_email, detalhamento, ctr_interno_cad,cliid_id)
        VALUES (:solicitante_id, :job_id,:nome_cliente, :status_id, :perfil_id, :data_solicitacao, :titulo_email, :detalhamento, :ctr_interno_cad,:cliid_id);";
        // VALUES (:executante_id, :job_id, :area_id, :nome_cliente, :status_id, :perfil_id, :data_solicitacao, :titulo_email, :detalhamento, :ctr_interno_cad);";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':solicitante_id', $dados['id_solicitante']);
            $stmt->bindParam(':job_id', $dados['tipoJob']);
            // $stmt->bindParam(':area_id', $dados['area']);
            $stmt->bindParam(':nome_cliente', $id_cliid[1]);
            $stmt->bindParam(':status_id', $dados['s_tatus']);
            $stmt->bindParam(':perfil_id', $dados['perfil']);
            $stmt->bindParam(':data_solicitacao', $dados['d_soliciticao']);
            $stmt->bindParam(':titulo_email', $titulo);
            $stmt->bindParam(':detalhamento', $detalhamento);
            $stmt->bindParam(':ctr_interno_cad', $dados['ctr']);
            $stmt->bindParam(':cliid_id', $tratado);

            if ($stmt->execute()) {

                return $stmt->rowCount() > 0 ? $msg[] = ["MSG" => "Sucesso em Cadastrar Job"] : $error[] = ["error" => "Falha ao inserir verifique campos enviados!"];
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


        $sql = "WITH job_perfil as (
                SELECT
                        jperfil.n_perfil,
                        jperfil.id_perfil
                    FROM cadastro_job.jobperfil jperfil
                ),
                job_status AS (
                    SELECT
                        jstatus.n_status,
                        jstatus.id_status
                    FROM cadastro_job.jobstatus jstatus
                )
                SELECT
                    cadjobs.id_cadjob,  -- PRECISA ALTERAR O NOME DA COLUNA
                    cadjobs.solicitante_id,
                    cadjobs.executante_id,
                     tstatus.id_status,
                    tstatus.n_status,
                    cadjobs.data_inicio,
                    cadjobs.data_fim,
                    cadjobs.titulo_email,
                    cadjobs.detalhamento,
                    cadjobs.data_cad_job,
                    cadjobs.nome_cliente,
                    cadjobs.data_solicitacao,
                    perjobs.n_perfil

                FROM cadastro_job.jobcadjobs cadjobs
                INNER JOIN job_perfil perjobs
                    ON perjobs.id_perfil = cadjobs.perfil_id
                    INNER JOIN job_status tstatus
                    ON tstatus.id_status = cadjobs.status_id ORDER BY cadjobs.id_cadjob DESC LIMIT 2;";

        try {

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


                    $newDate = new DateTime($row['data_solicitacao']);
                    $newDateInicio = !empty($row['data_inicio']) && $row['data_inicio'] != null ? new DateTime($row['data_inicio']) : $row['data_inicio'];
                    $newDateFim = !empty($row['data_fim']) && $row['data_fim'] != null ? new DateTime($row['data_fim']) : $row['data_fim'];
                    $row['data_solicitacao'] = $newDate->format('d-m-Y');
                    $row['n_perfil'] = strtoupper($row['n_perfil']);
                    $row['data_inicio'] =  $newDateInicio != null ? $newDateInicio->format('d-m-Y') : $newDateInicio;
                    $row['data_fim'] =  $newDateFim != null ? $newDateFim->format('d-m-Y') : $newDateFim;
                    $dataAtual = date('Y/m/d');
                    $dataAtual =  new DateTime($dataAtual);
                    // if ($row['id_cadjob'] == 34) {
                    //     $row['data_inicio'] = '10-07-2026';
                    //     $row['data_fim'] = '30-07-2026';
                    // }
                    $data_inicio = new DateTime($row['data_inicio']);
                    $data_fim_projeto = new DateTime($row['data_fim']);
                    $intervalo_dias_de_inicio_data_solicitacao = $row['data_inicio'] != null ? $newDate->diff($data_inicio) : null;
                    $intervalod_fim_projeto = $row['data_fim'] != null ? $newDate->diff($data_fim_projeto)->days : 0; // DIFERENÇA ENTRE DATA SOLICITACAO A DATA FINAL DO JOBS PEGANDO OS DIAS
                    $intervalo = $dataAtual->diff($newDate); // CALCULO A DIFERENÇA DOS DIAS DA SOLICITACAO A DATA ATUAL
                    $row['qta_dias_solicitacao_jobs'] = $intervalo->days; //QTA DO DIA ATUAL JUNTO COM A SOLICITACAO EXEMPLO: SOLICITADO A 5 
                    $row['diferencias_em_horas_solicitacao'] = $intervalo->days * 8; //HORAS DE TRABALHO 8 HORAS DIAS   
                    $row['qta_diferencias_em_dias_inicio_solicitacao'] = $intervalo_dias_de_inicio_data_solicitacao != null ? $intervalo_dias_de_inicio_data_solicitacao->days : 'SEM DATA DE INÍCIO ATRIBUIDA'; //DIFERENÇA DATA DA SOLICITACAO E DATA DO INICIO DO JOB
                    $row['qta_dias_jobs_finalizado'] =  $intervalod_fim_projeto;
                    $row['qt_dias_jobs_horas_finalizado'] = $intervalod_fim_projeto * 8 . ':Hs';
                    $row['dados_solicitante'] =  self::users($row['solicitante_id']);
                    $row['dados_executor'] =  self::users($row['executante_id']);

                    $registros[] = $row;

                    // $registros[] = $row;
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

    public function diasParaHoras($dias)
    {
        return $dias * 8;
    }
    public function lista_jobs_atualizacoes($idTabela)
    {
        $error = [];
        $msg = [];
        $registros = [];


        $sql = "WITH job_perfil as (
            SELECT
                    jperfil.n_perfil,
                    jperfil.id_perfil
                FROM cadastro_job.jobperfil jperfil
            ),
            job_status AS (
                SELECT
                    jstatus.n_status,
                    jstatus.id_status
                FROM cadastro_job.jobstatus jstatus
            )
            SELECT
                cadjobs.cad_idjob,
                cadjobs.solicitante_id,
                cadjobs.executante_id,
                tstatus.id_status,
                cadjobs.data_cad_hist,
                tstatus.n_status,
                cadjobs.data_inicio,
                cadjobs.data_fim,
                cadjobs.data_cad_job,
                perjobs.n_perfil

            FROM cadastro_job.jobhistorico cadjobs
            INNER JOIN job_perfil perjobs
                ON perjobs.id_perfil = cadjobs.perfil_id
                INNER JOIN job_status tstatus
                ON tstatus.id_status = cadjobs.status_id
                WHERE cadjobs.cad_idjob = :id_busca";

        try {

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id_busca", $idTabela, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


                    // $newDate = new DateTime($row['data_solicitacao']);
                    $newDateInicio = !empty($row['data_inicio']) ? new DateTime($row['data_inicio']) : $row['data_inicio'];
                    // $row['data_solicitacao'] = $newDate->format('d-m-Y');

                    $row['data_inicio'] =  $newDateInicio != null ? $newDateInicio->format('d-m-Y') : $newDateInicio;

                    $row['dados_solicitante'] =  self::users($row['solicitante_id']);
                    $row['dados_executor'] =  self::users($row['executante_id']);

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
    public function lista_jobs_obs($idTabela)
    {
        $error = [];
        $msg = [];
        $registros = [];


        $sql = "SELECT obs,data_cadastro,job_cad_id as tabela
        FROM cadastro_job.jobobservacoes
        WHERE job_cad_id = :id_busca";

        try {

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id_busca", $idTabela, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


                    // $newDate = new DateTime($row['data_solicitacao']);
                    $newDateInicio = !empty($row['data_cadastro']) ? new DateTime($row['data_cadastro']) : $row['data_cadastro'];
                    // $row['data_solicitacao'] = $newDate->format('d-m-Y');

                    $row['data_cadastro'] =  $newDateInicio != null ? $newDateInicio->format('d-m-Y') : $newDateInicio;
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
    public function lista_jobs_old()
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
                    ON perjobs.id_perfil = cadjobs.perfil_id ORDER BY ;";




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
    public function get_propesct()
    {


        $error = [];

        $sql = "SELECT cliid,
                COALESCE(NULLIF(clinomraz, ''), clinomfan) AS cli_propect
                FROM cli_prospect limit 10;";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            if ($stmt->execute()) {

                return  $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC)  : $error[] = ['error' => "Nenhum dados encontrado"];
            }
        } catch (PDOException $e) {
            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );

            return $error[] = ['error' => "Falha em Solicitar os dados propesct, cod error: {$e->getCode()}"];
        }
    }

    public function get_user_area()
    {

        $sql = "";
        $error = [];


        $sql = "SELECT jbus.id as usuario_id,jbus.status as status_user, 
              CONCAT(jbus.n_nome_user, '-', jba.n_area) AS user_area,
              jbus.n_nome_user 
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

            return $error[] = ['error' => "Falha em Solicitar os dados area, cod error: {$e->getCode()}"];
        }
    }
    public function get_tipo_job()
    {
        $error = [];
        $sql = "";


        $sql = "SELECT 
              id_job, n_tipo
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

            return $error[] = ['error' => "Falha em Solicitar os dados area, cod error: {$e->getCode()}"];
        }
    }

    public function get_tipo_perfil()
    {
        $error = [];
        $sql = "";


        $sql = "SELECT 
              id_perfil, n_perfil
            FROM cadastro_job.jobperfil;";

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

            return $error[] = ['error' => "Falha em Solicitar os dados area, cod error: {$e->getCode()}"];
        }
    }

    public function get_tipo_status()
    {
        $error = [];
        $sql = "";


        $sql = "SELECT id_status , n_status 
        FROM cadastro_job.jobstatus;";

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

            return $error[] = ['error' => "Falha em Solicitar os dados area, cod error: {$e->getCode()}"];
        }
    }


    public function get_list_clients()
    {
        // $conexaobd = $this->conection->getInstance();

        $sql = "";
        $result = [];


        $sql = "SELECT cliid,clinomraz  FROM 
                 cli, ctr 
                 --WHERE cliid = ctrcli AND cliatv = 'S' AND ctratv = 'S' AND ctrint = 'N'
                  WHERE cliid = ctrcli AND cliatv = 'S' AND ctratv = 'S'
                 GROUP BY cliid,clinomraz";

        try {

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {

                $a = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($a as  $key => $items) {
                    $items['ctrid'] = self::get_list_clients_conctract($items['cliid']);

                    $result[] = $items;
                }
                // return $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            }
        } catch (PDOException $e) {
            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );

            return $error[] = ['error' => "Falha em Solicitar os dados Clientes, cod error: {$e->getCode()}"];
        }
    }

    public function get_list_clients_conctract($id)
    {
        // $conexaobd = $this->conection->getInstance();

        $sql = "";


        $sql = "SELECT ctrid  FROM 
                 cli, ctr 
                 WHERE cliid = ctrcli AND cliid =:id_buscado";

        try {

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_buscado', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {

                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                return $result['ctrid'];
            } else {
                return null;
            }
        } catch (PDOException $e) {
            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );

            return $error[] = ['error' => "Falha em Solicitar os dados Clientes, cod error: {$e->getCode()}"];
        }
    }
    public function users($id)
    {
        $sql = "";

        $sql = "SELECT jbarea.id_area,
          juser.id,
          trim(jbarea.n_area) as n_area,   
          trim(juser.n_nome_user) as n_nome_user
	      FROM cadastro_job.jobusuarios juser
	      INNER JOIN cadastro_job.jobarea jbarea on (jbarea.id_area = juser.id_area)
          WHERE juser.id = :id";

        $stmt = $this->db->prepare($sql);

        try {
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetch();
        } catch (PDOException $e) {

            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );

            return $error[] = ['error' => "Falha em Solicitar os dados Clientes, cod error: {$e->getCode()}"];
        }
    }
    public function get_jobs_inserts($id)
    {


        $sql = "";

        $sql = "SELECT * FROM cadastro_job.jobcadjobs jb WHERE jb.id_cadjob = :id";

        $stmt = $this->db->prepare($sql);

        try {
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );

            return $error[] = ['error' => "Falha em Solicitar os dados Clientes, cod error: {$e->getCode()}"];
        }
    }


    public function up_dados_jobs($dados, $config)
    {
        $sql = "";
        $msg = [];
        $error = [];

        //busco os dados da tabela pelo o id 

        $busca_tabela = self::get_jobs_inserts($dados['tabela']);

        try {

            $this->db->beginTransaction();

            $tabela =  $config['tabela'];

            $sql = "UPDATE {$tabela} SET ";

            $sets = [];
            $params = [];

            foreach ($config['campos'] as $campo) {
                $sets[] = "$campo = :$campo";
                $params[":$campo"] = $dados[$campo];
            }

            $sql .= implode(', ', $sets);
            $sql .= " WHERE id_cadjob = :id_cadjob";

            $params[':id_cadjob'] = $dados['tabela'];


            $id_cadjob = isset($busca_tabela['id_cadjob']) ? $busca_tabela['id_cadjob'] : null;

            if ($id_cadjob) {

                $valores_insert[] = "({$id_cadjob}, "
                    . (isset($busca_tabela['executante_id']) ? $busca_tabela['executante_id'] : "NULL") . ", 
                    " . (isset($busca_tabela['job_id']) ? $busca_tabela['job_id'] : '') . ", 
                    '" . (isset($busca_tabela['nome_cliente']) ? $busca_tabela['nome_cliente'] : '') . "', 
                    " . (isset($busca_tabela['status_id']) ? $busca_tabela['status_id'] : '') . ",
                    " . (isset($busca_tabela['perfil_id']) ? $busca_tabela['perfil_id'] : '') . ", 
                    " . (isset($busca_tabela['data_solicitacao']) ? "'" . $busca_tabela['data_solicitacao'] . "'" : "NOW()") . ",
                    " . (isset($busca_tabela['data_inicio']) ? "'" . $busca_tabela['data_inicio'] . "'" : "NULL") . ", 
                    " . (isset($busca_tabela['data_fim']) ? "'" . $busca_tabela['data_fim'] . "'" : "NULL") . ", 
                    '" . (isset($busca_tabela['titulo_email']) ? $busca_tabela['titulo_email'] : '') . "', 
                    '" . (isset($busca_tabela['detalhamento']) ? $busca_tabela['detalhamento'] : '') . "',
                    " . (isset($busca_tabela['ctr_interno_cad']) ? $busca_tabela['ctr_interno_cad'] : '') . ", 
                    '" . (isset($busca_tabela['data_cad_job']) ? $busca_tabela['data_cad_job'] : '') . "',
                    " . (isset($busca_tabela['solicitante_id']) ? $busca_tabela['solicitante_id'] : "NULL") . ", 
                    " . (isset($busca_tabela['cliid_id']) ? $busca_tabela['cliid_id'] : "NULL") . ", 
                    " . (isset($busca_tabela['alter_id']) ? $busca_tabela['alter_id'] : $dados["crt"]) . ")";
            }


            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            // Se atualizou, grava o histórico
            if ($stmt->rowCount() > 0 && !empty($valores_insert)) {
                $insertHistorico = "INSERT INTO cadastro_job.jobhistorico 
                (cad_idjob, executante_id, job_id, nome_cliente, status_id, perfil_id, data_solicitacao, data_inicio, data_fim, titulo_email, detalhamento, ctr_interno_cad, data_cad_job, solicitante_id, cliid_id, alter_id) VALUES ";
                $stmt = $this->db->prepare($insertHistorico . implode(', ', $valores_insert));

                $stmt->execute();
            }
            $this->db->commit();
            return $stmt->rowCount() > 0 ? $msg[] = ["MSG" => "Sucesso em Atualizar Job"] : $error[] = ["error" => "Falha ao inserir verifique campos enviados!"];
        } catch (PDOException $e) {

            $this->db->rollBack();

            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );

            return $error[] = ['error' => "Falha em atualizar os dados , cod error: {$e->getCode()}"];
        }
    }
}

?>