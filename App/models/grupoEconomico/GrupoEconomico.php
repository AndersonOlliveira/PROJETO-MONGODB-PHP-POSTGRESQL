
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

        require_once __DIR__ . "/../../Utilis/validaCamposEconomico.php";

        $this->ajustar = new validaCamposEconomico();

        require_once __DIR__ . '/../../Utilis/GrupoEconomico/AuxiliaresGrupo.php';


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

            if (in_array($tipo, [
                AuxiliaresGrupo::ID_BUSCA_INFO_REDES_CONTRATOS,
                AuxiliaresGrupo::ID_BUSCA_INFO_REDES_CONTRATOS_FIVE
            ])) {

                $sql = "WITH REDE_LOJA AS (
			            SELECT rdenom,rdeid,rdeljaid,rdeljactr
						FROM  rdelja , rde where rdeljarde = rdeid 
			        )
					SELECT REDE_LOJA.rdenom,
					REDE_LOJA.rdeid,
					REDE_LOJA.rdeljaid,
					REDE_LOJA.rdeljactr,
					cli.cliid
					FROM cli INNER JOIN ctr ON cli.cliid = ctr.ctrcli
					INNER JOIN REDE_LOJA ON REDE_LOJA.rdeljactr = ctr.ctrid
					WHERE ctr.ctrid = :contrato ";
            } else {
                $sql = "SELECT rdenom,rdeid,rdeljaid,rdeljactr
                       FROM  rdelja , rde where 
                       rdeid = :rede and rdeljarde = rdeid limit 3 ";
            }


            $stmt = $this->db->prepare($sql);
            if (in_array($tipo, [
                AuxiliaresGrupo::ID_BUSCA_INFO_REDES_CONTRATOS,
                AuxiliaresGrupo::ID_BUSCA_INFO_REDES_CONTRATOS_FIVE
            ])) {
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
    public function tipo_tratativa_grupo()
    {

        $sql = "SELECT tpoid FROM public.tpo WHERE UPPER(unaccent(tpodsc))  =  UPPER(unaccent(trim('" . AuxiliaresGrupo::BUSCA_ID_GRUPO_ECONOMICO . "')))";

        try {
            $stmt = $this->db->prepare($sql);
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
        }
    }
    public function registrarOcorrencia($cliId, $tpos, $descricao, $nome)
    {



        $sql = "INSERT INTO public.cliocr(
                     cliocrcli, cliocrtpo, cliocrant, cliocrrsp)
                    VALUES (:cliocrcli, :cliocrtpo, :cliocrant, :cliocrrsp);";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cliocrcli', $cliId);
            $stmt->bindParam(':cliocrtpo', $tpos);
            $stmt->bindParam(':cliocrant', $descricao);
            $stmt->bindParam(':cliocrrsp', $nome);


            $stmt->execute();
        } catch (PDOException $e) {
            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );
            error_log('Erro ao registrar ocorrência: ' . $e->getMessage());
        }
    }


    public function nivel_cliente_grupo($contrato)
    {


        $sql = "";
        // $sql = "SELECT CASE 
        // WHEN limite_nivel IS NULL THEN '-' 
        // ELSE limite_nivel::TEXT END AS nivel_atual FROM grupo_economico.config_limite
        // WHERE contrato_cliente = :contrato_busca";
        $sql = "SELECT CASE 
        WHEN limite_nivel IS NULL THEN 'VAZIO'
        ELSE limite_nivel::TEXT END AS nivel_atual
        FROM grupo_economico.config_limite  
        WHERE contrato_cliente = :contrato_busca";

        try {

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':contrato_busca', $contrato, PDO::PARAM_INT);

            $rr  = [];
            if ($stmt->execute() && $stmt->rowCount() > 0) {

                return  $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    public function info_responsavel($contrato)
    {


        $sql = "";
        $sql = "SELECT ctrapl FROM ctr where ctrid = :ctrid";


        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':ctrid', $contrato);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return trim($result['ctrapl']);
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
        $error = [];
        $tabela = $config['tabela'];

        try {
            switch ($tipo) {
                case 1: // INSERT
                    $contrato_cliente   = $config['campos'][0];
                    $limite_nivel       = $config['campos'][1];
                    $contrato_interno   = $config['campos'][2];

                    $sql = "INSERT INTO {$tabela} ({$contrato_cliente}, {$limite_nivel}, {$contrato_interno})
                        VALUES (:input_contrato, :input_limite, :input_contrato_interno) RETURNING id";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(':input_contrato', $contrato_afetar, PDO::PARAM_INT);
                    $stmt->bindParam(':input_limite', $limite, PDO::PARAM_INT);
                    $stmt->bindParam(':input_contrato_interno', $c_interno, PDO::PARAM_INT);
                    break;

                case 2: // dois para TODOS MAS PRECISO VER O O TIPO 1 
                    $contrato_cliente   = $config['campos'][0];
                    $limite_nivel       = $config['campos'][1];
                    $contrato_interno   = $config['campos'][2];

                    $sql = "INSERT INTO {$tabela} ({$contrato_cliente}, {$limite_nivel}, {$contrato_interno})
                        VALUES (:input_contrato, :input_limite, :input_contrato_interno) RETURNING id";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(':input_contrato', $contrato_afetar, PDO::PARAM_INT);
                    $stmt->bindParam(':input_limite', $limite, PDO::PARAM_INT);
                    $stmt->bindParam(':input_contrato_interno', $c_interno, PDO::PARAM_INT);
                    break;
                case 4: // UPDATE tipo 4
                    $limite_nivel       = $config['campos'][0];
                    $contrato_interno   = $config['campos'][1];
                    $contratoGrupo      = $config['campos'][2];

                    $sql = "UPDATE {$tabela} 
                        SET {$limite_nivel} = :NEW_LIMITE, {$contrato_interno} = :NEW_CONTRATO 
                        , regra_ativa = :regra
                        WHERE {$contratoGrupo} = :contrato_cliente_alter RETURNING id";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindValue(':NEW_LIMITE', NULL);
                    $stmt->bindParam(':NEW_CONTRATO', $c_interno, PDO::PARAM_INT);
                    $stmt->bindValue(':regra', false, PDO::PARAM_BOOL);
                    $stmt->bindParam(':contrato_cliente_alter', $contrato_afetar, PDO::PARAM_INT);
                    break;

                case 5: // UPDATE tipo 5
                    $limite_nivel       = $config['campos'][0];
                    $contrato_interno   = $config['campos'][1];
                    $contratoGrupo      = $config['campos'][2];

                    $sql = "UPDATE {$tabela} 
                        SET {$limite_nivel} = :NEW_LIMITE, {$contrato_interno} = :NEW_CONTRATO
                        , regra_ativa = :regra
                        WHERE {$contratoGrupo} = :contrato_cliente_alter RETURNING id";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(':NEW_LIMITE', $limite, PDO::PARAM_INT);
                    $stmt->bindParam(':NEW_CONTRATO', $c_interno, PDO::PARAM_INT);
                    $stmt->bindValue(':regra', true, PDO::PARAM_BOOL);
                    $stmt->bindParam(':contrato_cliente_alter', $contrato_afetar, PDO::PARAM_INT);
                    break;

                default:
                    throw new InvalidArgumentException("Tipo {$tipo} inválido para operação.");
            }

            if ($stmt && $stmt->execute()) {

                self::registrarOcorrencia(
                    self::lista_rde_loja($contrato_afetar, AuxiliaresGrupo::ID_BUSCA_INFO_REDES_CONTRATOS)[0]['cliid'],
                    self::tipo_tratativa_grupo()['tpoid'],
                    $this->ajustar->convertToLatin1(AuxiliaresGrupo::TEXTOS_INSERIR_OCORRENCIAS[$tipo]['TEXTO'] .  $contrato_afetar),
                    self::info_responsavel($c_interno)
                );

                return AuxiliaresGrupo::TEXTOS_INSERIR[$tipo]['TEXTO'] . ' CONTRATO: ' . $contrato_afetar;
            }
        } catch (\PDOException $e) {

            $this->errorHandler->manipuladorDeErros(
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $this->arquivoLog
            );

            return ['error' => "Falha em realizar alteração ou inserção dos dados, cod: error: {$e->getCode()},  contrato: {$contrato_afetar}"];
        }
    }
}
