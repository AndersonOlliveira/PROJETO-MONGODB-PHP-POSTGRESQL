
<?php

use App\core\AppManipularError;


class GrupoEconomico extends Model
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

                $sql = "SELECT * FROM cli, ctr 
                    WHERE cliid = ctrcli  AND ctrid = :contrato ";
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
}
