<?php


class BuscaValorLotePorConsulta  extends Model
{

	public function calcula($codConsulta, $rede, $quantidade)
	{

		require_once 'CapturaValorDaConsulta.php';
		require_once 'CapturaValorDaConsultaPorFaixa.php';
		require_once 'CapturaModulo.php';

		$CapturaModulo = new CapturaModulo();

		$result = $CapturaModulo->resultModulo($codConsulta);

		if ($result) {

			$valor = $CapturaModulo->CaputuraValor($codConsulta);
			if ($valor) {
				return [$valor * $quantidade, $result];
			}
		}

		$capturaValor = new CapturaValorDaConsulta();
		$valor = $capturaValor->execute($codConsulta);
		if ($valor) {
			return [$valor * $quantidade, $result];
		}

		$capturaValorFaixa = new CapturaValorDaConsultaPorFaixa();
		$valor = $capturaValorFaixa->execute($codConsulta, $rede, $quantidade);
		if ($valor) {
			return [$valor * $quantidade, $result];
		}

		return [0, $result];
	}
}
