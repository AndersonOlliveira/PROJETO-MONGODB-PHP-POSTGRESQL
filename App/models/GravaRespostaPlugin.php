<?php

class GravaRespostaPlugin extends Model {
	
	public  function execute($plugin, $resposta, $transacaoId, $header="") {
			ini_set('memory_limit', '1024M');
		if (!self::existe($plugin, $resposta, $transacaoId)) { // grava se não existir no banco

			
			$sql = "INSERT INTO progestor.respostas_plugin (
					id_transacao, plugin, resposta, header_arquivo)
					VALUES (?, ?, ?, ?);";
			
			$dados = array ();
			$dados[] = $transacaoId;
			$dados[] = $plugin;
			$dados[] = $resposta;
			$dados[] = $header;
			
			$result = $this->db->prepare( $sql );
			
			$result->execute( $dados );
		}
	}

	private  function existe($plugin, $resposta, $transacaoId) {
		
		
		$sql = "SELECT respostas_id FROM progestor.respostas_plugin WHERE id_transacao = ? and plugin = ? and resposta = ? LIMIT 1";
		
		$dados = array();
		$dados[] = $transacaoId;
		$dados[] = $plugin;
		$dados[] = $resposta;

		$result = $this->db->prepare( $sql );

		$result->execute($dados);
		
		$existe=false;
		if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			
			if (!is_null($row['respostas_id'])) {
				$existe=true;	
			}
		}
		
		return $existe;
	}

    public function insert_all_Respost_pluglin(array $registros){
   
   
        if (empty($registros)) {
           return false;
    
          }


    // Inicia a transação (insert em lote)
    $this->db->beginTransaction();

    try {
        $sql = "INSERT INTO progestor.respostas_plugin (
					id_transacao, plugin, resposta, header_arquivo)
					VALUES ";

        $values = [];
        $params = [];

        foreach ($registros as $i => $r) {
        
        if (!self::existe($r['plugin'], $r['resposta'], $r['transacaoId'])) { // grava se não existir no banco
            // Define valores padrão (como o execute)

            $id_transacao = $r['transacaoId']  ?? null;
            $plugin =  $r['plugin'] ?? null;
            $resposta   = $r['resposta']  ?? null;
            $header_arquivo  = $r['header']   ?? null;
          

            $values[] = "(?, ?, ?,?)";
            array_push($params, $id_transacao, $plugin, $resposta, $header_arquivo);
       
     } 
    }

    echo "estou passando do existe";

   
        // Monta a query completa
        // $sql .= implode(", ", $values) . " ON CONFLICT (id_transacao, plugin) DO NOTHING";
        $sql .= implode(", ", $values);

        // Executa em um único INSERT
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        echo "<pre>";
        echo "*********-----*** <br>";

        echo $this->db->lastInsertId();

        print_r('RESPOSTA DO INSERT');

        ECHO "</pre>";

        $this->db->commit();
        return true;
    
    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
   }
}
	
?>