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


	public function teste ()
	{
        
		echo "<pre>";

		print_r('acessei esta pagina');
		$postgresData = $this->teste->list_processo();
		

		$mongoData = $this->utils->listarDadosDosProcessos();
		

    

    // --- JUNÇÃO ---
      $dadosCombinados = self::joinMongoPostgres($postgresData, $mongoData, 'processo_id', 'id_processo');
 print_r($dadosCombinados);
		
     return $dadosCombinados;
	
	}


	public function get_dados_id($dados)
	{
		//vou procurar os dados 
		foreach ($dados as $key => $values) {

			// print_r($this->utils->findById($values['processo_id']));
			$dados[$key]['resultado'] = $this->utils->findById($values['processo_id']);
		}



		$retorno = self::tratamento_dados($dados);

		//    print_r($retorno);
	}


public	function joinMongoPostgres(array $pgData, array $mongoData, string $keyPg, string $keyMongo): array
{
    // Converter objetos stdClass do MongoDB para arrays associativos
  $mongoMap = [];

foreach ($mongoData as $doc) {
    $doc = (array) $doc;

    if (!isset($doc[$keyMongo])) {
        continue;
    }

    $valorChave = $doc[$keyMongo];

    // Se for ObjectId ou objeto, converte pra string
    if ($valorChave instanceof MongoDB\BSON\ObjectId) {
        $valorChave = (string) $valorChave;
    }
    // Se for array, tenta extrair algum campo interno (oid, id, etc)
    elseif (is_array($valorChave)) {
        $valorChave = $valorChave['oid'] ?? json_encode($valorChave);
    }
    // Se for outro tipo complexo, converte para JSON string
    elseif (!is_scalar($valorChave)) {
        $valorChave = json_encode($valorChave);
    }

    // Agora garantimos que é string/int seguro pra usar como índice
    $mongoMap[(string)$valorChave][] = $doc;
}
    // Fazer o merge com base na chave
    $merged = [];
    foreach ($pgData as $pgRow) {
        $pgKey = $pgRow[$keyPg] ?? null;
        $mongoRow = $mongoMap[$pgKey] ?? [];

        // Unir os arrays — dados do Mongo substituem chaves iguais do Postgres
        $merged[] = array_merge($pgRow, $mongoRow);
    }

    return $merged;
}
	public function tratamento_dados($row_data)
	{
		$dados_filtrados = array_values(array_filter($row_data, function ($row) {
			return !empty($row['resultado']);
		}));
		echo "<pre>";

		print_r($dados_filtrados);
		echo "</pre>";


		foreach ($dados_filtrados as $r) {

		     $confCns = $this->MontaJsonConfigEHeadersDaConsultas->execute($r['codcns']);


			if (!empty($r['resultado'])) {

				foreach ($r['resultado'] as $values) {
					
					list($camposAquisicao, $jsonRespostas) = [$values->campo_aquisicao, null];
					// $this->utils->gr
					$this->GravaTransacao->execute($r['processo_id'], $camposAquisicao, 5, 0, null, $jsonRespostas);


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


								
									// echo "Entrou em MontaJsonConfigEHeadersDaConsulta<br>";
									// debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
									

									echo "<pre>";
									echo "meu header do plugin";
									print_r($confCns['header_' . (string)$plugin]);

									$confCns = is_array($confCns) ? $confCns : [];
									$header = isset($confCns['header_' . $plugin]) ? $confCns['header_' . $plugin] : '-';
									$linhasSaidaVertical = self::montaLinhaRegistroVertical($arrayValues, $configPlugin); // array com linhas de saida
									echo "<pre>";

									foreach ($linhasSaidaVertical as $linhaSaidaVertical) { // PARA CADA OCORRENCIA do PLUGIN

										if ($linhaSaidaVertical != "") {

											// GRAVA RESPOSTA PLUGIN TRANSACAO

											echo "<pre>";
											echo "o que tenho aqui";

											print_r($plugin . $camposAquisicao . ";" . $linhaSaidaVertical .  $values->transacao_id .  $header);



											$this->GravaRespostaPlugin->execute($plugin, $camposAquisicao . ";" . $linhaSaidaVertical, $values->transacao_id, $header);
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

						echo "<pre>";
						echo "o que tenho aqui";

						print_r($r['processo_id'] . $camposAquisicao . ";" . 3);

						$this->GravaTransacao->execute($r['processo_id'], $camposAquisicao, 3, 0, null, null);
					} else {

						// SAIDA PRINCIPAL

						// linha arquivo main preenchida
						if ($linhaSaidaHorizontal != "") {

							// GRAVA RESPOSTA TRANSACAO SUCESSO
							echo "<pre>";
							echo "o que tenho aqui";
							echo "***";

							print_r($r['processo_id'] . "***" . $camposAquisicao . "***" . 3 . "***" .  1 . "***" .  $linhaSaidaHorizontal . "***" . $jsonResposta);
							echo "</pre>";

							$this->GravaTransacao->execute($r['processo_id'], $camposAquisicao, 3, 1, $linhaSaidaHorizontal, null);
						}
					}

					$linhaSaidaHorizontal = ""; // zera linha
				}
			} else {
				echo "<br>***<br>";
			}
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
				$retPlg[$indice] = preg_replace("/\;/", " ", $retPlg[$indice]); // limpa ponto-e-virgula de valores
				$linha .= $retPlg[$indice] . ";";
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
				$retPlg[$indice] = preg_replace("/\;/", " ", $retPlg[$indice]); // limpa ponto-e-virgula de valores
				$linha .= $retPlg[$indice] . ";";
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
