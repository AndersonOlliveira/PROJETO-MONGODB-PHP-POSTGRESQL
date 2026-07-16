
<?php

use App\core\AppManipularError;


class GrupoEconomico extends Model
{
    protected $arquivoLog;

    protected $ajustar;
    protected $functions;
    private  $errorHandler;

    const TIPO  = [1, 2, 4, 5];
    public function __construct()
    {
        parent::__construct();

        // $this->arquivoLog = $_SERVER['DOCUMENT_ROOT'] . '../error/errorKpi.txt';

        require_once __DIR__ . "/../../Utilis/validaCampos.php";

        $this->ajustar = new validaCampos();

        $this->arquivoLog =  __DIR__ . '/../../../error/errorGrupoEconimco.txt';
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

    public function buscar_clientes()
    {

        print_r('estou chegando até aqui');
    }



    //LISTA TOODS OS CLIENTE POR REDE
    public function lista_rde_loja($c_rede, $tipo)
    {

        $sql = "";

        try {

            if ($tipo == 1) {

                $sql = "WITH REDE_LOJA AS (
			            SELECT rdenom,rdeid,rdeljaid,rdeljactr
						FROM  rdelja , rde where rdeljarde = rdeid 
			        )
					SELECT REDE_LOJA.rdenom,
					REDE_LOJA.rdeid,
					REDE_LOJA.rdeljaid,
					REDE_LOJA.rdeljactr
					FROM cli INNER JOIN ctr ON cli.cliid = ctr.ctrcli
					INNER JOIN REDE_LOJA ON REDE_LOJA.rdeljactr = ctr.ctrid
					WHERE ctr.ctrid = :contrato ";
            } else {

                $sql = "SELECT rdenom,rdeid,rdeljaid,rdeljactr
            
             FROM  rdelja , rde where 
                    rdeid = :rede and rdeljarde = rdeid limit 2 ";
            }


            $stmt = $this->db->prepare($sql);
            if ($tipo == 1) {
                $stmt->bindParam(':contrato', $c_rede, PDO::PARAM_INT);
            } else {

                $stmt->bindParam(':rede', $c_rede, PDO::PARAM_INT);
            }

            if ($stmt->execute()) {

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return false;
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

    public function nivel_cliente_grupo($contrato)
    {


        $sql = "";
        $sql = "SELECT CASE 
        WHEN limite_nivel IS NULL THEN '-' 
        ELSE limite_nivel::TEXT END AS nivel_atual FROM grupo_economico.config_limite
        WHERE contrato_cliente = :contrato_busca";

        try {

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':contrato_busca', $contrato, PDO::PARAM_INT);

            $rr  = [];
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                // while ($row =  $stmt->fetchAll(PDO::FETCH_ASSOC));


                // $row['nivel_atual'] = 20;

                // $rr[] = $row;
                return  $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return "-";
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

    // $retorno_tipo,  $contrato_afetar, $dados['value_limite'], $dados['id']
    public function Upsert_grupo_OLD($config, $contrato_afetar, $limite, $tipo, $c_interno)
    {

        $sql = "";
        $msg = [];
        $error = [];

        $tabela = $config['tabela'];

        echo "<pre>";
        print_R($config);

        echo "<pre>";
        echo "dados para pequissa\n";
        print_R($tipo);


        print_r(GrupoEconomico::TIPO);
        print_r(in_array($tipo, GrupoEconomico::TIPO) . ' QUE RESULADO TENHO AQUI!!');


        die();
        try {

            if (in_array($tipo, GrupoEconomico::TIPO)) {
                $contrato_cliente = $config['campos'][0];
                $limite_nivel    = $config['campos'][1];
                $contrato_interno    = $config['campos'][2];
                $sql = "INSERT INTO {$tabela} ({$contrato_cliente}, {$limite_nivel}, {$contrato_interno})
                VALUES (:input_contrato,
                    :input_limite,
                    :input_contrato_interno)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':input_contrato', $contrato_afetar, PDO::PARAM_INT);
                $stmt->bindParam(':input_limite', $limite, PDO::PARAM_INT);
                $stmt->bindParam(':input_contrato_interno', $c_interno, PDO::PARAM_INT);
            } elseif (in_array($tipo, GrupoEconomico::TIPO)) {
                $limite_nivel = $config['campos'][0];
                $contrato_interno    = $config['campos'][1];
                $contratoGrupo    = $config['campos'][2];

                $sql = "UPDATE {$tabela} SET {$limite_nivel} = :NEW_LIMITE , {$contrato_interno} = :NEW_CONTRATO WHERE  {$contratoGrupo} = :contrato_cliente_alter";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':NEW_LIMITE', $limite, PDO::PARAM_INT);
                $stmt->bindParam(':NEW_CONTRATO', $c_interno, PDO::PARAM_INT);
                $stmt->bindParam(':contrato_cliente_alter', $contrato_afetar, PDO::PARAM_INT);
            } elseif (in_array($tipo, GrupoEconomico::TIPO)) {
                $limite_nivel = $config['campos'][0];
                $contrato_interno    = $config['campos'][1];
                $contratoGrupo    = $config['campos'][2];

                $sql = "UPDATE {$tabela} SET {$limite_nivel} = :NEW_LIMITE , {$contrato_interno} = :NEW_CONTRATO WHERE  {$contratoGrupo} = :contrato_cliente_alter";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':NEW_LIMITE', $limite, PDO::PARAM_INT);
                $stmt->bindParam(':NEW_CONTRATO', $c_interno, PDO::PARAM_INT);
                $stmt->bindParam(':contrato_cliente_alter', $contrato_afetar, PDO::PARAM_INT);
            }


            echo "<pre>";
            echo "MEU NEW LIMITE ENVIADO\n";
            print_r($limite);
            echo "MEU NEW contrato_afetar ENVIADO\n";
            print_r($contrato_afetar);
            echo "MEU NEW c_interno ENVIADO\n";
            print_r($c_interno);


            // print_r('variavel stmt');

            echo "<pre>";
            print_r($stmt);


            if ($stmt->execute()) {

                echo "<pre>";

                print_r('FOI INSERIDO O LIMITE');
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

    public function Upsert_grupo($config, $contrato_afetar, $limite, $tipo, $c_interno)
    {
        $sql = "";
        $stmt = null;
        $tabela = $config['tabela'];

        try {
            switch ($tipo) {
                case 1: // INSERT
                    $contrato_cliente   = $config['campos'][0];
                    $limite_nivel       = $config['campos'][1];
                    $contrato_interno   = $config['campos'][2];

                    $sql = "INSERT INTO {$tabela} ({$contrato_cliente}, {$limite_nivel}, {$contrato_interno})
                        VALUES (:input_contrato, :input_limite, :input_contrato_interno)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(':input_contrato', $contrato_afetar, PDO::PARAM_INT);
                    $stmt->bindParam(':input_limite', $limite, PDO::PARAM_INT);
                    $stmt->bindParam(':input_contrato_interno', $c_interno, PDO::PARAM_INT);
                    break;

                case 2: // UPDATE tipo 2
                case 4: // UPDATE tipo 4
                    $limite_nivel       = $config['campos'][0];
                    $contrato_interno   = $config['campos'][1];
                    $contratoGrupo      = $config['campos'][2];

                    $sql = "UPDATE {$tabela} 
                        SET {$limite_nivel} = :NEW_LIMITE, {$contrato_interno} = :NEW_CONTRATO 
                        WHERE {$contratoGrupo} = :contrato_cliente_alter";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindValue(':NEW_LIMITE', NULL);
                    $stmt->bindParam(':NEW_CONTRATO', $c_interno, PDO::PARAM_INT);
                    $stmt->bindParam(':contrato_cliente_alter', $contrato_afetar, PDO::PARAM_INT);
                    break;

                case 5: // UPDATE tipo 5
                    $limite_nivel       = $config['campos'][0];
                    $contrato_interno   = $config['campos'][1];
                    $contratoGrupo      = $config['campos'][2];

                    $sql = "UPDATE {$tabela} 
                        SET {$limite_nivel} = :NEW_LIMITE, {$contrato_interno} = :NEW_CONTRATO 
                        WHERE {$contratoGrupo} = :contrato_cliente_alter";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(':NEW_LIMITE', $limite, PDO::PARAM_INT);
                    $stmt->bindParam(':NEW_CONTRATO', $c_interno, PDO::PARAM_INT);
                    $stmt->bindParam(':contrato_cliente_alter', $contrato_afetar, PDO::PARAM_INT);
                    break;

                default:
                    throw new InvalidArgumentException("Tipo {$tipo} inválido para operação.");
            }

            echo "<pre>";
            print_r($stmt);
            if ($stmt && $stmt->execute()) {
                echo "Operação realizada com sucesso!";
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
