<?php

class CapturaValorDaConsulta extends Model
{

	public  function execute($codConsulta)
	{


		$sql = "SELECT rdecnsvlr FROM rdecns WHERE rdecnsid = ? AND rdecnsvlr > 0.00 AND rdecnsmod IS FALSE limit 1";

		$dados = array();
		$dados[] = $codConsulta;

		$result = $this->db->prepare($sql);
		$result->execute($dados);

		$valor = false;
		if ($row = $result->fetch(PDO::FETCH_ASSOC)) {

			$valor = $row['rdecnsvlr'];
		}

		return $valor;
	}
}
