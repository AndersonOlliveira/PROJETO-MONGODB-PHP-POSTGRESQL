<?php

class process extends Model
{

	public function list_processo($idProcesso, $qtLimit, $info = false)
	{
		logInfo(date('Y-m-d H:i:s') . " - Iniciando list_processo com idProcessos:  {$idProcesso}  \n");
		logInfo(date('Y-m-d H:i:s') . " - Iniciando list_processo com x linhas : {$qtLimit} \n");


		$sql = "";
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
			t.status in (12,3) AND 
			p.contrato = 417039 AND
			 p.finalizado = false AND
			 p.error = false";


		// echo "meu info enviado\n";

		// var_dump($info);
		// echo "vem vazio?\n";
		// print_r(empty($info));

		// var_dump($idProcesso);


		$params = [];
		if ($idProcesso !== null) {
			$sql .= " AND p.processo_id = ?";
			$params[] = $idProcesso;
		}


		$sql .= " AND p.pause = ?";
		$params[] = $info ? 1 : 0;;

		// if ($info !== null) {

		// 	echo "<pre>";
		// 	echo "E DIFERENTE DE NULL\n";
		// 	var_dump($info);
		// 	$sql .= " AND p.pause = ?";
		// 	$params[] = (bool) true;
		// } else {
		// 	echo "<pre>";
		// 	echo "E NULL\n";
		// 	var_dump($info);
		// 	$sql .= " AND p.pause = ?";
		// 	$params[] =  (bool)false;
		// }

		if ($qtLimit !== null) {
			$qtLimit = (int)$qtLimit; // garante que é inteiro
			$sql .= " ORDER BY random() LIMIT $qtLimit;";
		} else {
			$sql .= " ORDER BY random() LIMIT 10;";
		}


		// echo "<pre>";
		// echo "MEUS PARAM MONTADO\n";

		// print_r($sql);


		$results = $this->db->prepare($sql);
		$results->execute($params);
		// $result = $this->db->query($sql);

		return $results->fetchAll(PDO::FETCH_ASSOC);
	}

	public function list_processo_alert($idProcesso, $qtLimit)
	{

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
			t.status in (2,12) AND 
		    p.mensagem_alerta = '1' and
			t.sucesso = true AND 
			p.finalizado = true AND 
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


	public function list_processo_modulo($idProcesso, $qtLimit)
	{

		logInfo(date('Y-m-d H:i:s') . " - Iniciando Pesquisa modulo com x linhas : {$qtLimit} \n");


		$sql = "SELECT 
		    p.processo_id,
			p.contrato,
			p.rede,
			p.codcns,
			p.data_cadastro,
			p.data_finalizacao,
			p.valor_total
			FROM
			progestor.processo p 
		WHERE 
		    p.status_output = 2 and 
		    p.finalizado = true AND
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


	public function count_new_quantidade_OLD($idProcesso, $qtLimit)
	{

		logInfo(date('Y-m-d H:i:s') . " - Iniciando Pesquisa modulo com x linhas : {$qtLimit} \n");



		$sql = "";
		$sql = "SELECT 
		    p.processo_id,
			p.valor_total,
			p.contrato,
			p.codcns,
			 COALESCE(SUM(CASE WHEN  t.status != 6 or t.status != 17 AND t.campo_aquisicao IS NOT NULL  THEN 1 ELSE 0 END), 0) AS qtd_registros
			FROM
			progestor.processo p 
			inner join 
			progestor.transacao as t on 
		    (p.processo_id = t.id_processo)
		WHERE 
		    p.status_output = 2 and 
			-- p.processo_id = 51 AND
		    p.finalizado = true AND
		    p.pause = false
			group by p.processo_id ";



		$params = [];
		// if ($idProcesso !== null) {
		// 	$sql .= " AND p.processo_id = ?";
		// 	$params[] = $idProcesso;
		// }


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


	public function push_value_modulo($rede, $codConsulta, $dataInicio, $dataFim, $qtLimit)
	{

		logInfo(date('Y-m-d H:i:s') . " - Iniciando Pesquisa modulo com x linhas : {$qtLimit} \n");

		// (4061, 266982, '2025-12-03 00:00:00','2025-12-03 23:59:59');";
		$sql = "
		SELECT * 
		FROM
		progestor.fnc_extrato_modulos (
		:rede,
        :codConsulta,
        :data_inicio,
        :data_fim);";


		$stmt = $this->db->prepare($sql);

		$stmt->bindValue(':rede', $rede, PDO::PARAM_INT);
		$stmt->bindValue(':codConsulta', $codConsulta, PDO::PARAM_INT);
		$stmt->bindValue(':data_inicio', $dataInicio);
		$stmt->bindValue(':data_fim', $dataFim);

		$stmt->execute();

		$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $resultado;
	}

	public function list_processo_qta_process()
	{

		logInfo(date('Y-m-d H:i:s') . " - Iniciando processo com status mensagem 1 \n");


		$sql = "SELECT COALESCE(SUM(CASE WHEN t.status in (2,5) THEN 1 ELSE 0 END), 0) AS qta_processar,
		p.processo_id,
		p.mensagem_alerta as info
		FROM progestor.transacao as t
		inner join progestor.processo as p on (p.processo_id = t.id_processo)
		where p.mensagem_alerta ='1'
		group by p.mensagem_alerta, p.processo_id;";


		$results = $this->db->prepare($sql);
		$results->execute();
		// $result = $this->db->query($sql);

		return $results->fetchAll(PDO::FETCH_ASSOC);
	}


	public  function list_data_modulo($idConsultation)
	{

		$sql = "";
		$sql = "SELECT rdecnsmod FROM rdecns WHERE rdecnsid = ? LIMIT 1";

		try {
			$stmt = $this->db->prepare($sql);
			$stmt->execute([$idConsultation]);

			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$row) {

				return false;
			}

			//forco o retorno ser um boleano
			return filter_var($row['rdecnsmod'], FILTER_VALIDATE_BOOLEAN);
		} catch (\Exception $e) {

			return false;
		}
	}



	public function finish_process_die($id_process)
	{
		$erros = [];

		try {
			//recebe 6 
			$sql = "UPDATE progestor.processo SET finalizado = ?, data_finalizacao = ?, mensagem_alerta = ? WHERE processo_id = ? ";
			// $sql = "UPDATE progestor.processo SET valor_total = ?, finalizado = ?, data_finalizacao = ? WHERE processo_id = ? ";

			// $dados =  [$value, true, date("Y-m-d H:i:s"), $id_process];
			$dados =  [true, date("Y-m-d H:i:s"), 0, $id_process];
			$result = $this->db->prepare($sql);
			$result->execute($dados);
		} catch (\Exception $e) {

			echo $e->getMessage();


			$erros[] = [
				'msg' =>  $e->getMessage()
			];
		}


		return [
			'erros' => empty($erros) ? [] : $erros,
			'status' => empty($erros) ? 2 : 0,

		];
	}
	public function atualizarValorJobs($id_process, $contrato, $valor)
	{
		$erros = [];

		try {
			//atualizo o valor correto.
			$sql = "UPDATE progestor.processo SET valor_total = ? WHERE processo_id = ? and contrato = ? ";


			$dados =  [$valor, $id_process, $contrato];
			$result = $this->db->prepare($sql);
			$result->execute($dados);
		} catch (\Exception $e) {

			echo $e->getMessage();


			$erros[] = [
				'msg' =>  $e->getMessage()
			];
		}


		return [
			'erros' => empty($erros) ? [] : $erros,
			'status' => empty($erros) ? 2 : 0,

		];
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

	public function up_valor_modules($dados)
	{

		echo "<pre>";
		echo "MEUS DADOS PARA SER ATUALIZAD NO POSTEGREE\n";

		$sql = "UPDATE progestor.processo SET valor_total = ? where processo_id = ?;";

		try {
			$dadosTransacao = [$dados['valor_geral'], $dados['processo_id']];
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

	public function count_new_quantidade()
	{



		$sql = "";
		$sql = "SELECT 
    p.processo_id,
    p.valor_total,
    p.contrato,
    p.codcns,
    COALESCE(
        SUM(
            CASE 
                WHEN t.status != 6 AND t.status != 17
                     AND t.campo_aquisicao IS NOT NULL 
                THEN 1 
                ELSE 0 
            END
        ), 0
    ) AS qtd_registros
FROM progestor.processo p
INNER JOIN progestor.transacao t 
    ON p.processo_id = t.id_processo
WHERE 
    p.status_output = 2 
	AND p.finalizado = true 
    AND p.pause = false
GROUP BY 
    p.processo_id,
    p.valor_total,
    p.contrato,
    p.codcns
HAVING 
    p.valor_total < COALESCE(
        SUM(
            CASE 
                WHEN t.status != 6 and t.status != 17
                     AND t.campo_aquisicao IS NOT NULL 
                THEN 1 
                ELSE 0 
            END
        ), 0
    );
";

		try {
			$results = $this->db->prepare($sql);
			$results->execute();
			return $results->fetchAll(PDO::FETCH_ASSOC);
		} catch (\Exception $e) {

			print_r($e->getMessage());
		}
	}

	//funcao para pegar a quantidade de dia que estes processo esta paralisado
	public function get_count_day_process()
	{

		$sql = "";
		$sql = "";
	}

	public function get_limit_day_contrato($contrato)
	{

		$sql = "";
		$sql = "SELECT limite_dias
			FROM
			progestor.config_limit_dias
			WHERE
			ctr_cliente = ?
			AND limite_dias > 0
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
			return $e->getMessage();
		}


		$row = $result->fetch(PDO::FETCH_ASSOC);

		if (!$row) {
			return false; //
		}

		return (int) $row['limite_dias'];
	}


	public function breack_process_paralizar($id_process, $id_status)
	{

		$erros = [];


		try {
			//recebe 6 
			$sql = "UPDATE progestor.transacao SET status = ? WHERE id_processo = ? and status not in (12,3);";
			// $sql = "SELET progestor.transacao SET status = ? WHERE id_processo = ? ";

			$dados = array();
			$dados[] = $id_status;
			$dados[] = $id_process;

			$result = $this->db->prepare($sql);

			$result->execute($dados);
		} catch (\Exception $e) {

			$this->db->rollback();

			$erros[] = [
				'msg' =>  $e->getMessage()
			];
		}

		return [
			'erros' => empty($erros) ? [] : $erros,
			'status' => empty($erros) ? 2 : 0,
			'qta_afetadas' => $result->rowCount()

		];
	}


	public function lista_data_paralisados($idProcesso)
	{
		$sql =  "";
		$sql =  "SELECT transacao_id,id_processo
	           FROM PROGESTOR.TRANSACAO 
			   where id_processo = ?
			   AND campo_aquisicao is not null 
			   AND status NOT IN (3,12,17);";

		try {

			$Newregistros = [];
			$dados = [$idProcesso];
			$result = $this->db->prepare($sql);
			$result->execute($dados);
		} catch (PDOException $e) {
			print_R("Erro na consulta: " . $e->getMessage());
			return null;
		}

		if ($result->rowCount() == 0) {
			return null;
		}

		while ($row = $result->fetchall(PDO::FETCH_ASSOC)) {
			$Newregistros = [
				'ids' => $row,
				'linhas_afetadas' => $result->rowCount()
			];
		}

		// echo "<pre>";
		// echo "meus registros\n";

		// print_r($Newregistros);
		// die();


		return $Newregistros;
	}

	//pega a quantidade para realizar o update
	public function count_process_finalizado_paralizado($idProcesso)
	{
		$sql =  "";
		$sql =  "SELECT count(*) as total,
		  p.contrato,
		  p.processo_id,p.rede, codcns as consultas,p.mensagem_alerta,
		  SUM(CASE WHEN t.status = 3
                     AND t.campo_aquisicao IS NOT NULL  THEN 1 ELSE 0 END) AS finalizados
	      FROM
		    progestor.transacao t INNER JOIN 
			  progestor.processo p ON p.processo_id = t.id_processo 
			   where id_processo = ?
			   AND campo_aquisicao is not null 
			   AND status IN (3,12)
			--    AND p.menssagem_alerta is null
			   group by  p.contrato, p.processo_id,p.mensagem_alerta;";

		try {

			$Newregistros = [];
			$dados = [$idProcesso];
			$result = $this->db->prepare($sql);
			$result->execute($dados);
		} catch (PDOException $e) {
			print_R("Erro na consulta: " . $e->getMessage());
			return null;
		}

		if ($result->rowCount() == 0) {
			return null;
		}

		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$Newregistros = $row;
		}

		return $Newregistros;
	}

	public function alter_valores_process_paralizar($id_process, $value)
	{
		$erros = [];

		try {

			$sql = "UPDATE progestor.processo SET valor_total = ?, mensagem_alerta = ?, pause = ? WHERE processo_id = ? ";
			$dados =  [$value, '1', 0, $id_process];
			$result = $this->db->prepare($sql);
			$result->execute($dados);

			echo '<pre>';
			echo "MEU SQL MONTATO PARA UPDATE\n";

			var_dump($sql);

			print_r('ESTOU SAINDO AQUI\n');
		} catch (\Exception $e) {

			echo "<pre>";
			echo "erro vindo aqui\n";
			print_r($e->getMessage());

			$erros[] = [
				'msg' =>  $e->getMessage()
			];
		}


		return [
			'erros' => empty($erros) ? [] : $erros,
			'status' => empty($erros) ? 2 : 0,

		];
	}
}
