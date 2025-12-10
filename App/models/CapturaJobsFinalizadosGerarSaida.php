<?php

class CapturaJobsFinalizadosGerarSaida extends Model
{

	public function execute($id)
	{

		// processo finalizado que ainda não gerou arquivo de saida
		$sql =
			"SELECT
			p.nome_arquivo,
			p.header_arquivo,
			p.contrato,
			p.campos_aquisicao
		FROM
			progestor.processo p
		WHERE
			p.processo_id = ? 
		LIMIT 1;";


		$dados = [$id];

		$result = $this->db->prepare($sql);
		$result->execute($dados);

		$registros = array();
		if ($row = $result->fetch(PDO::FETCH_ASSOC)) {

			return $row;
		}

		return false;
	}
}
