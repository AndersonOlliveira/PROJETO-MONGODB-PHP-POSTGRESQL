<?php



class process extends Model {
 
     public function list_processo()
    {
        
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
			p.pause
		FROM 
			progestor.processo as p 
		WHERE 
			p.finalizado = true 
			and 
			p.processo_id = 353
	
		LIMIT 
			1000;";

          $result = $this->db->query($sql);

          return $result->fetchAll(PDO::FETCH_ASSOC);
         
    }
}