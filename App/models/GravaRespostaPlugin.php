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
	
}

?>