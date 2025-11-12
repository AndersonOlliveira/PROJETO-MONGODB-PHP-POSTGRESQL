<?php

class process extends Model
{

	public function list_processo($idProcesso, $qtLimit)
	{
		logInfo(date('Y-m-d H:i:s') . " - Iniciando list_processo com idProcessos:  {$idProcesso}  \n");
		logInfo(date('Y-m-d H:i:s') . " - Iniciando list_processo com x linhas : {$qtLimit} \n");


		$sql = "SELECT p.processo_id, p.contrato,
			p.rede,
			p.codcns,
			p.nome_arquivo,
			p.aceite_execucao,
			p.mensagem_alerta,
			p.data_cadastro,
			p.configuracao_json,
			p.campos_aquisicao,
			p.loja,
			p.finalizado,
			p.data_finalizacao,
			p.pause,
			t.transacao_id,
			t.id_processo
			FROM
		   progestor.transacao t INNER JOIN 
			progestor.processo p ON p.processo_id = t.id_processo 
		WHERE 
			t.status = 2 AND 
			t.sucesso = true AND 
			p.finalizado = false AND 
			p.pause = false";

		$params = [];
		if ($idProcesso !== null) {
			$sql .= " AND p.processo_id = ?";
			$params[] = $idProcesso;
		}

		if ($qtLimit !== null) {
			$qtLimit = (int)$qtLimit; // garante que é inteiro
			$sql .= " ORDER BY random() LIMIT $qtLimit;";
		}else{
			$sql .= " ORDER BY random() LIMIT 10;";
		}

		$results = $this->db->prepare($sql);
		$results->execute($params);

		

		// $result = $this->db->query($sql);

		return $results->fetchAll(PDO::FETCH_ASSOC);
	}


	public function get_query_all()
	{
		ini_set('memory_Limit', '1024M');


		$query = "SELECT *
		FROM
        pg_stat_activity;";

		$result = $this->db->query($query);

		if (!$result) {
			echo "Ocorreu um erro na consulta.\n";
			exit;
		} else {

			return $result->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	public function get_all_teste()
	{

		return "estou aquiiii!!!!";
	}
}
