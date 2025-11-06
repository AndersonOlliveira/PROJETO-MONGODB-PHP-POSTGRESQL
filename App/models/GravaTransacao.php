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
	}
	
}

?>