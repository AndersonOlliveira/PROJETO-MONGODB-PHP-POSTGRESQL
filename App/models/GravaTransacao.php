<?php

class GravaTransacao extends Model {
	
	
	public function execute($processoId, $campoAquisicao, $status=0, $sucesso=true, $resposta=null, $respostaJson=null) {
		
			ini_set('memory_limit', '1024M');
		
		$sql = "INSERT INTO progestor.log_transacao (
				id_processo, campo_aquisicao, status, sucesso, resposta, resposta_json)
				VALUES (?, ?, ?, ?, ?, ?);";
		
		$dados = array ();
		$dados[] = $processoId;
		$dados[] = $campoAquisicao;
		$dados[] = $status;
		$dados[] = $sucesso;
		$dados[] = $resposta;
		$dados[] = $respostaJson;
		
		$result = $this->db->prepare( $sql );

    
		$result->execute( $dados );

		 echo "<pre>";
        echo "Meu ultimo id " . $this->db->lastInsertId();
	}
	public function insertBatch(array $registros)
{
    if (empty($registros)) {
        return false;
    }

    // Inicia a transação (insert em lote)
    $this->db->beginTransaction();

    try {
        $sql = "INSERT INTO progestor.log_transacao (
				id_processo, campo_aquisicao, status, sucesso, resposta, resposta_json)
				VALUES ";

        $values = [];
        $params = [];

        foreach ($registros as $i => $r) {
            // Define valores padrão (como o execute)
            $processoId     = $r['processo_id']     ?? null;
            $campoAquisicao = $r['camposAquisicao'] ?? null;
            $status         = $r['status']          ?? 0;
            $sucesso        = $r['sucesso']         ?? true;
            $resposta       = $r['resposta']        ?? null;
            $respostaJson   = $r['resposta_json']   ?? null;

            $values[] = "(?, ?, ?, ?, ?, ?)";
            array_push($params, $processoId, $campoAquisicao, $status, $sucesso, $resposta, $respostaJson);
        }

        // Monta a query completa
        $sql .= implode(", ", $values);

        // Executa em um único INSERT
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}
}

?>