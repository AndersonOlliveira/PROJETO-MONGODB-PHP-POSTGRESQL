<?php

class CapturaRespostasPluginsTransacao extends Model
{

	public  function execute($idTransacao)
	{


		$sql =
			"SELECT
			r.header_arquivo,
			r.plugin,
			r.resposta 
		FROM
			progestor.respostas_plugin r
		WHERE
			r.id_transacao = ? 
		ORDER BY 
			r.plugin";

		$dados = array();
		$dados[] = $idTransacao;

		$result = $this->db->prepare($sql);
		$result->execute($dados);

		$registros = array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

			$registros[] = $row;
		}

		if (count($registros) == 0) {
			return false;
		}

		return $registros;
	}
	
	
	public function execute_old($idTransacao) {

		// require_once 'ConexaoBd.php';
		// $conexaoBd = conexaoBd::getInstance();
		
		$sql ="WITH nome_plugin AS (
			SELECT  
			reg.regdsc AS nome_campo,
				reg.regcod AS codigo_plugin
			FROM reg 
			INNER JOIN regstr ON reg.regid = regstr.regstrreg 
			WHERE regstr.regstrord >= 2
		)
		SELECT DISTINCT
			r.header_arquivo,
			r.plugin, 
			r.resposta,
			p.nome_campo
		FROM progestor.respostas_plugin r
		INNER JOIN nome_plugin p ON r.plugin = p.codigo_plugin
		WHERE r.id_transacao = ?
		ORDER BY r.plugin;";
		
		$dados = array();
		$dados[] = $idTransacao;
		
		$result = $this->db->prepare($sql);
		$result->execute($dados);
		
		$registros = array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			
			$registros[] = $row;
		}

		if (count($registros)==0) { 
			return false;
		}
		
		return $registros;
		}
	}

