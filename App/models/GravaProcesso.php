<?php

class GravaProcesso extends Model
{

	public function execute($contrato, $rede, $loja, $codConsulta, $nomeArquivo, $aceite = true, $mensagem = "", $configuracaoJson, $camposAquisicao, $header, $valortotal, $fingers)
	{
		// public static function execute($contrato, $rede, $loja, $codConsulta, $nomeArquivo, $aceite=true, $mensagem="", $configuracaoJson="", $camposAquisicao="tcpfcnpj", $header="",$valortotal) {


		$sql = "INSERT INTO progestor.processo(
			            contrato, rede, codcns, nome_arquivo, aceite_execucao, 
			            mensagem_alerta, configuracao_json, campos_aquisicao, 
			            loja, header_arquivo,valor_total, finger)
			    VALUES (?, ?, ?, ?, ?, 
			            ?, ?, ?, 
			            ?, ?,?,?);";

		$dados = array();
		$dados[] = $contrato;
		$dados[] = $rede;
		$dados[] = $codConsulta;
		$dados[] = $nomeArquivo;
		$dados[] = $aceite;
		$dados[] = $mensagem;
		$dados[] = $configuracaoJson;
		$dados[] = $camposAquisicao;
		$dados[] = $loja;
		$dados[] = $header;
		$dados[] = $valortotal;
		$dados[] = $fingers;

		try {

			$result =  $this->db->prepare($sql);

			$result->execute($dados);

			echo "<pre>";
			echo "Lista de dados a ser inserido\n";


			print_r($dados);

			$idProcesso = $this->db->lastInsertId();

			return $idProcesso;
		} catch (\Exception $e) {

			return $e->getMessage();
		}
	}
}
