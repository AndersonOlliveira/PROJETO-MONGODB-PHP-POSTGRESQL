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
			t.id_processo,
			t.resposta_json
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
		} else {
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

	public function filtros_data($ids)
	{
		$sql = "SELECT 
		 t.id_processo,
         t.transacao_id,
         t.status as new_status,
		 t.resposta_json,
		 t.sucesso,
		 t.campo_aquisicao
		FROM
		progestor.transacao t INNER JOIN 
		progestor.processo p ON p.processo_id = t.id_processo 
		WHERE 
		    t.status = 2 AND 
			t.sucesso = true AND 
			p.finalizado = false AND 
		    p.pause = false";

		$params = [];
		if ($ids != null) {
			$sql .= " AND t.transacao_id = ?;";
			$params[] = $ids;
		}

		$results = $this->db->prepare($sql);
		$results->execute($params);

		//ira receber um lote de ids para alterar para 0 resposta_json vazio
		$up_status_one = [];
		$up_mongo_data = [];

		while ($row = $results->fetchAll(PDO::FETCH_ASSOC)) {

			if (empty($row['resposta_json'])) {

				$up_status_one = $row[0];
			}
			if (!isset($row['resposta_json'])) {

				$up_mongo_data = $row[0];
			}
		}

		if (isset($up_mongo_data)) {

			return $up_mongo_data;
		}

		self::up_zero_status($up_status_one['transacao_id']);
	}

	public function up_zero_status($ids)
	{

		$sql = "UPDATE progestor.transacao SET status = ? , sucesso =? where transacao_id = ?;";

		try {
			$dadosTransacao = [0, 0, $ids];
			$results = $this->db->prepare($sql);
			$results->execute($dadosTransacao);

			echo "ok para atualizar\n";

			return true;
		} catch (\Exception $e) {

			return $e->getMessage();
		}
	}

	public function size_pgAdmin()
	{
		$sql = "SELECT 
        nspname || '.' || relname AS tabela,
        pg_size_pretty(pg_relation_size(c.oid)) AS tamanho_dados,
        pg_total_relation_size(c.oid) AS tamanho_total,
        ROUND(c.reltuples) AS estimated_rows
    FROM 
        pg_class c
    LEFT JOIN
        pg_namespace n ON n.oid = c.relnamespace
    WHERE
        relkind = 'r'
        AND nspname = 'progestor';
    ";

		$results = $this->db->prepare($sql);
		$results->execute();
		return $results->fetchAll(PDO::FETCH_ASSOC);
	}
}
