<?php

class MontaJsonConfigEHeadersDaConsulta
{

	protected $utils;
	protected $tratamento;

	protected $MontaJsonConfigEHeadersDaConsultas;
	protected $capturaPlugins;
	protected $CapturaCamposDoPlugin;
	public function __construct()
	{

		// $this->utils = new Instance();
		// require_once 'MontaJsonConfigEHeadersDaConsulta.php';
		// $this->MontaJsonConfigEHeadersDaConsultas = new MontaJsonConfigEHeadersDaConsulta();

		require_once __DIR__ . '/../models/CapturaPluginsDaConsulta.php';
		$this->capturaPlugins = new CapturaPluginsDaConsulta();
		//  $this->GravaTransacao = $this->utils = new GravaTransacao();

		require_once __DIR__ . '/../models/CapturaCamposDoPlugin.php';
		$this->CapturaCamposDoPlugin = new CapturaCamposDoPlugin();
	}

	public function execute($codConsulta)
	{
        ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', 300); // 5 minutos

		$config = array();
		$return = array();

		// require_once __DIR__ . '/../models/CapturaPluginsDaConsulta.php';
		// require_once __DIR__ . '/../models/CapturaCamposDoPlugin.php';

		// $capturaPlugins = new CapturaPluginsDaConsulta();
		// $CapturaCamposDoPlugin = new CapturaCamposDoPlugin();

		$plugins = $this->capturaPlugins->execute($codConsulta);
		$header = "";
		foreach ($plugins as $plugin) {


			echo 'info do plugin: ' . $plugin['plugin'] . "<br>";

 			$campos = $this->CapturaCamposDoPlugin->execute($plugin['plugin']);

			$separar = (!empty($plugin['qt_ocorrencias']) && $plugin['qt_ocorrencias'] > 1);
			$key = 'header_' . $plugin['plugin'];

			if (!isset($return[$key])) {
				$return[$key] = '';
			}

			$i = 1;
			$camposPlg = [];

			foreach ($campos as $c) {
				$camposPlg[] = $i;

				if ($separar) {
					$return[$key] .= self::limpaNomeCampo($c['nome_campo']) . ";";
				} else {
					$header .= $c['nome_campo'] . " " . $plugin['plugin'] . ";";
				}

				$i++;
			}

			$arrPlugin = [
				"plugin" => $plugin['plugin'],
				"separar" => $separar,
				"ocorrencias" => $plugin['qt_ocorrencias'],
				"campos" => $camposPlg
			];

			$config[] = $arrPlugin;
		}

		$header = self::limpaNomeCampo($header);
		$return['json_config'] = json_encode($config, JSON_UNESCAPED_UNICODE);
		$return['header_arquivo_principal'] = $header;
		return $return;
	}

	private function limpaNomeCampo($header)
	{

		$header = preg_replace("/\;$/", "", $header);
		$header = preg_replace("/\n|\r|\t/", " ", $header);
		$header = preg_replace("/\s\s+/", " ", $header);

		return trim($header);
	}
}
