<?php


class BuscaValorLotePorConsulta  extends Model
{

	public function calcula($codConsulta, $rede, $quantidade)
	{

		require_once 'CapturaValorDaConsulta.php';
		require_once 'CapturaValorDaConsultaPorFaixa.php';

		$capturaValor = new CapturaValorDaConsulta();
		$valor = $capturaValor->execute($codConsulta);
		if ($valor) {
			return $valor * $quantidade;
		}

		$capturaValorFaixa = new CapturaValorDaConsultaPorFaixa();
		$valor = $capturaValorFaixa->execute($codConsulta, $rede, $quantidade);
		if ($valor) {
			return $valor * $quantidade;
		}

		return 0;
	}
}
