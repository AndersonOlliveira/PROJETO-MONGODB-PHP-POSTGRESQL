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
			p.pause,
			t.transacao_id,
			t.id_processo
			FROM
		   progestor.transacao t INNER JOIN 
			progestor.processo p ON p.processo_id = t.id_processo 
		WHERE 
			t.status = 2 AND 
			t.sucesso = true AND
			-- p.finalizado = false 
			
			p.processo_id = 360;";

          $result = $this->db->query($sql);

          return $result->fetchAll(PDO::FETCH_ASSOC);
         
    }
}