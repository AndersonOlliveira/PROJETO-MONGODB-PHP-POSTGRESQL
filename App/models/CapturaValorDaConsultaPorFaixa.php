<?php

class CapturaValorDaConsultaPorFaixa extends Model
{

	public function execute($codConsulta, $rede, $quantidade)
	{



		$sql = "SELECT rdefxacnsvlr FROM rdefxacns WHERE rdefxacnsrdecns = ? AND rdefxacnsrde = ? AND rdefxacnsini <= ? AND  rdefxacnsfim >= ?";

		$dados = array();
		$dados[] = $codConsulta;
		$dados[] = $rede;
		$dados[] = $quantidade;
		$dados[] = $quantidade;

		$result = $this->db->prepare($sql);
		$result->execute($dados);

		$valor = false;
		if ($row = $result->fetch(PDO::FETCH_ASSOC)) {

			$valor = $row['rdefxacnsvlr'];
		}

		return $valor;
	}
}
