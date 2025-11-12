<?php


class Arquivos
{

	protected $utils;
	protected $tratamento;

	protected $MontaJsonConfigEHeadersDaConsultas;
	protected $GravaTransacao;
	protected $GravaRespostaPlugin;
	protected $teste;
	public function __construct()
	{

		$this->utils = new Instance();
		require_once 'MontaJsonConfigEHeadersDaConsulta.php';
		$this->MontaJsonConfigEHeadersDaConsultas = new MontaJsonConfigEHeadersDaConsulta();

		require_once __DIR__ . '/../models/GravaTransacao.php';
		$this->GravaTransacao = new GravaTransacao();
		//  $this->GravaTransacao = $this->utils = new GravaTransacao();

		require_once __DIR__ . '/../models/GravaRespostaPlugin.php';
		$this->GravaRespostaPlugin = new GravaRespostaPlugin();

		require_once __DIR__ . '/../models/process.php';
		$this->teste = new process();

		require_once __DIR__ . '/../models/process.php';
		$this->teste = new process();
	}
	
	public function get_dados_id($dados)
	{
		//vou procurar os dados 
		foreach ($dados as $key => $values) {

			// print_r($this->utils->findById($values['processo_id']));
			$dados[$key]['resultado'] = $this->utils->findById($values['processo_id'], $values['transacao_id']);
		}

		// print_r($dados);
		
		$retorno = self::tratamento_dados($dados);

	}

	public function tratamento_dados($row_data)
	{
		$dados_filtrados = array_values(array_filter($row_data, function ($row) {
			return !empty($row['resultado']);
		}));
		
		$cacheCns = [];
		$transacoes = [];
		$GrespostaPlugin = [];
		$GtransacaoSuceso = [];
		$sucessTruegravaTransacao = [];
		$inicio = microtime(true);

		foreach ($dados_filtrados as $r) {
		    
			$cod = $r['codcns'];

			if (!empty($r['resultado'])) {

				foreach ($r['resultado'] as $values) {

					list($camposAquisicao, $jsonRespostas) = [$values->campo_aquisicao, null];
					
					$transacoes[] = [
						'processo_id' => $r['processo_id'],
						'camposAquisicao' => $camposAquisicao,
						'status' => 5,
						'sucesso' => null,
						'resposta' => null,
						'json_resposta' => $jsonRespostas ?? null,
					];
					// $this->GravaTransacao->execute($r['processo_id'], $camposAquisicao, 5, 0, null, $jsonRespostas);


					$plgsConfigurados = self::getPluginsConfigDB($r['configuracao_json']); // array com codigos dos plugins da configuracao 

					list($camposAquisicao, $jsonResposta) = [$values->campo_aquisicao,  $values->resposta_json];

					$jsonObjProscore = self::getObjectJson($jsonResposta); // object da resposta premium json

					$linhaSaidaHorizontal = ""; // linha arquivo principal

					$success = false;
					if ($jsonObjProscore) {
					   if ($jsonObjProscore->registro) {
						
						 $success = true;

							// PARA CADA REGISTRO/PLUGIN do JSON PREMIUM
							$registros = self::getRegistrosPlugins($jsonObjProscore, $plgsConfigurados);

							
							foreach ($registros as $plugin => $arrayValues) {

								$configPlugin = self::getConfObjectByPluginDB($r['configuracao_json'], $plugin);
                        
								if (!$configPlugin) { // config nao encontrada para o plugin
									continue;
								}
								
								if ($configPlugin->separar) { // SAIDA ARQUIVO A PARTE - GRAVA CADA LINHA
			
									if (!isset($cacheCns[$cod])) {
										
										$conf = $this->MontaJsonConfigEHeadersDaConsultas->execute($cod);
										$cacheCns[$cod] = is_array($conf) ? $conf : [];
									}

									$confCns = $cacheCns[$cod];

									$confCns = is_array($confCns) ? $confCns : [];
									$header = isset($confCns['header_' . $plugin]) ? $confCns['header_' . $plugin] : '-';

									
									$linhasSaidaVertical = self::montaLinhaRegistroVertical($arrayValues, $configPlugin); // array com linhas de saida
				
									foreach ($linhasSaidaVertical as $linhaSaidaVertical) { // PARA CADA OCORRENCIA do PLUGIN

										if ($linhaSaidaVertical != "") {

										
											// GRAVA RESPOSTA PLUGIN TRANSACAO
											$GrespostaPlugin[] = [
												'plugin' => $plugin,
												'resposta' => $camposAquisicao . ";" . $linhaSaidaVertical,
												'transacaoId' => $values->transacao_id,
												'header' => $header
												];

									
												// $this->GravaRespostaPlugin->execute($plugin, $camposAquisicao . ";" . $linhaSaidaVertical, $values->transacao_id, $header);
										}
									}
								} else { // concatena na linha do arquivo principal

									// MONTA LINHA MAIN
									if ($linhaSaidaHorizontal == "") {
										$linhaSaidaHorizontal .= "$camposAquisicao"; // inicializa linha com campos de aquisicao
									}

									$linhaSaidaHorizontal .= ";" . self::montaLinhaRegistroHorizontal($arrayValues, $configPlugin);
								}
							}
						}
					}

					if (!$success) { // saida de ERRO

						// GRAVA RESPOSTA TRANSACAO SUCESSO
		
						$GtransacaoSuceso[] =[
							'processo_id' => $r['processo_id'],
							'camposAquisicao' => $camposAquisicao,
							'status' => 3,
							'sucesso'=> 0,
							'resposta'=> null, 
							'respostaJson' =>null
						];
 
						// $this->GravaTransacao->execute($r['processo_id'], $camposAquisicao, 3, 0, null, null);

					} else {

						// SAIDA PRINCIPAL

						// linha arquivo main preenchida
						if ($linhaSaidaHorizontal != "") {

							// GRAVA RESPOSTA TRANSACAO SUCESSO
						   $sucessTruegravaTransacao[] = [
                             'processo_id' => $r['processo_id'],
							'camposAquisicao' => $camposAquisicao,
							'status' => 3,
							'sucesso'=> 1,
							'resposta'=> $linhaSaidaHorizontal , 
							'respostaJson' =>null

						  ]; 

							// $this->GravaTransacao->execute($r['processo_id'], $camposAquisicao, 3, 1, $linhaSaidaHorizontal, $jsonResposta);
						}
					}

					$linhaSaidaHorizontal = ""; // zera linha
				}
			} else {
				// echo "<br>***<br>";
			}
		}



		if(!empty($transacoes)){
			$this->GravaTransacao->insertBatch($transacoes);
		}
		
		if(!empty($GrespostaPlugin)){
			$this->GravaRespostaPlugin->insert_all_Respost_pluglin($GrespostaPlugin);
		}

		if(!empty($GtransacaoSuceso)){
		    $this->GravaTransacao->insertBatch($GtransacaoSuceso);
		}

		if(!empty($sucessTruegravaTransacao)){
		   $this->GravaTransacao->insertBatch($sucessTruegravaTransacao);
		}
	}

	function getConfObjectByPluginDB($configuracaoJson, $cod)
	{

		$confObj = false;
		try {
			$confObj = json_decode($configuracaoJson);
		} catch (Exception $e) {
			echo " *********** ERR01: ERRO AO ABRIR ARQUIVO DE CONFIGURACAO *********** ";
			die();
		}

		if ($confObj) {
			foreach ($confObj as $plugin) {
				if (intval($cod) == intval($plugin->plugin)) {
					return $plugin;
				}
			}
		}

		//echo " *********** ERR02: CONFIGURACAO NAO ENCONTRADA PARA PLUGIN $cod *********** ";
		//die();
		return false;
	}

	function getObjectJson($json)
	{
		if ($json) {
			if (trim($json) != "" and trim($json) != "{{{### ###}}}") {
				// padrao json
				if ((preg_match("/^(\{|\[)/", $json)) and (preg_match("/(\}|\])$/", $json))) {
					$object = json_decode($json);
					return $object;
				}
			}
		}
		return false;
	}

	function getPluginsConfigDB($configuracaoJson)
	{
		$result = array();

		$confObj = false;
		try {
			$confObj = json_decode($configuracaoJson);
		} catch (Exception $e) {
			echo " *********** ERR01: ERRO AO ABRIR ARQUIVO DE CONFIGURACAO *********** ";
			die();
		}

		if ($confObj) {
			foreach ($confObj as $plugin) {
				$result[] = $plugin->plugin;
			}
		}

		return $result;
	}
	/**
	 * Retorna array com registros do json premium separado pela chave do plugin  
	 * 
	 * @param object $obj
	 * @param array $arrayPluginsConf
	 * @return array
	 */
	function getRegistrosPlugins($obj, $arrayPluginsConf)
	{

		$return = array();

		// inicializa array de retorno com o codigo de cada plugin da configuracao
		foreach ($arrayPluginsConf as $codPlgConf) {
			$return[$codPlgConf] = array(); // para que os registros que não estejam no retorno premium sejam incluidos com campos vazios e não quebre o layout
		}

		// para cada registro inclui no array de retorno
		foreach ($obj->registro as $registro) {

			if (!isset($return[$registro->numero_plugin])) {
				$return[$registro->numero_plugin] = array();
			}
			$return[$registro->numero_plugin][] = $registro;
		}
		return $return;
	}
	/**
	 * Monta linha do registro de saida horizontal
	 * 
	 * @param array $arrValores
	 * @param object $configuracao
	 * @param int $numeroPlg
	 * @return string
	 */
	function montaLinhaRegistroHorizontal($arrValores, $configuracao)
	{

		$linha = "";

		$i = 0;
		foreach ($arrValores as $retPlg) { // cada ocorrencia do registro/plugin

			$retPlg = self::transformaArrayPlgEmSimples($retPlg);

			// inclui campos na linha de acordo com a configuracao
			foreach ($configuracao->campos as $indice) {

				if (isset($retPlg[$indice])) {

					$valor = preg_replace("/\;/", " ", $retPlg[$indice]); // limpa ponto-e-virgula de valores

				} else {
					$valor = '';
				}
				$linha .= $valor . ";";
			}
			$i++;

			if ($configuracao->ocorrencias == $i) { // limite de ocorrencias atingido
				break; // para de gravar
			}
		}
		$linha = preg_replace("/;$/", "", trim($linha)); // retira ponto-e-virgula extra do final

		// completa quantidade de campos na linha de acordo com a configuracao
		$qtCamposLinha = count(explode(";", $linha));
		$qtConfigurada = $configuracao->ocorrencias * count($configuracao->campos);
		if ($qtCamposLinha < $qtConfigurada) {
			$qtIncluir = ($qtConfigurada - $qtCamposLinha);
			$linha = self::incluiCamposVazios($linha, $qtIncluir);
		}

		return $linha;
	}
	/**
	 * Monta linha do registro de saida vertical (arquivo a parte)
	 * 
	 * @param array $arrValores
	 * @param object $configuracao
	 * @param int $numeroPlg
	 * @return array
	 */
	function montaLinhaRegistroVertical($arrValores, $configuracao)
	{

		$linha = "";

		

		$i = 0;
		foreach ($arrValores as $retPlg) { // cada ocorrencia do registro/plugin

			$retPlg = self::transformaArrayPlgEmSimples($retPlg);



			

			// inclui campos na linha de acordo com a configuracao
			foreach ($configuracao->campos as $indice) {
						
				if (isset($retPlg[$indice])) {

					$valor = preg_replace("/\;/", " ", $retPlg[$indice]); // limpa ponto-e-virgula de valores

				} else {
					$valor = '';
				}
				$linha .= $valor . ";";
			}
			$i++;

			if ($configuracao->ocorrencias == $i) { // limite de ocorrencias atingido
				break; // para de gravar
			}

			$linha = preg_replace("/;$/", "|", trim($linha)); // delimita linhas por pipe
		}
		$linha = preg_replace("/\|$/", "", trim($linha)); // retira pipe extra do final

		return explode("|", $linha);
	}
	/**
	 * Transforma o array com keys do json de retorno do registro em array simples sem keys
	 * 
	 * @param array $registro
	 * @return array[]
	 */
	function transformaArrayPlgEmSimples($registro)
	{
		$camposPlg = array();
		foreach ($registro as $campoPlg) {
			$camposPlg[] = $campoPlg;
		}
		return $camposPlg;
	}
	/**
	 * Inclui campos vazios (;) na string da linha
	 *  
	 * @param string $linha
	 * @param int $qtd
	 * @return string
	 */
	function incluiCamposVazios($linha, $qtd)
	{

		for ($i = 0; $i < $qtd; $i++) {
			$linha .= ";";
		}
		return $linha;
	}
}
