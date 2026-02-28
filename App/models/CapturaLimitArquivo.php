<?php

class CapturaLimitArquivo extends Model
{
	protected $conection;
	protected $infoArquivojson;
	protected $alterProcess;


	public function __construct()
	{
		// require_once 'ConexaoBd.php';
		// $this->conection = new conexaoBd();

		// require_once 'Config_arquivo.php';
		// $this->infoArquivojson = new Config_arquivo();

		// require_once 'AlterarJobsProcessNew.php';
		// $this->alterProcess = new AlterarJobsProcessNew();
	}
	public function limitArquivo($contrato)
	{


		// $contrato = 352249;

		$sql = "";
		$sql = "SELECT limite_uso
		FROM
		progestor.config_limit_progestor
		WHERE
		ctr_cliente = ?
		AND limite_uso > 0
		ORDER BY
		data_configuracao
		DESC limit 1;";

		$Newregistros = [];
		$dados = [$contrato];

		try {
			$result = $this->db->prepare($sql);
			$result->execute($dados);
		} catch (PDOException $e) {
			print_R("Erro na consulta: " . $e->getMessage());
			return null;
		}

		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

			$Newregistros = $row;
		}

		return $Newregistros;
	}
}
