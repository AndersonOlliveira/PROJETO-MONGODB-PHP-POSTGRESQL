<?php


class BuscaValorLotePorConsulta  extends Model
{

	public function calcula($codConsulta, $rede, $quantidade)
	{

		require_once 'CapturaValorDaConsulta.php';
		require_once 'CapturaValorDaConsultaPorFaixa.php';
		require_once 'CapturaModulo.php';
		require_once 'CapturaVolumetria.php';

		$CapturaModulo = new CapturaModulo();
		$CapturaVolumetria = new CapturaVolumetria();

		$result = $CapturaModulo->resultModulo($codConsulta);

		if ($codConsulta == 265919) {
			$result = true;
		}
		if ($codConsulta == 283092) {
			$rede = 5290;
			$codConsulta = 280968;
		}

		$volumetria = $CapturaVolumetria->captura($rede, $codConsulta);

		$temFaixa = (is_array($volumetria) && isset($volumetria['rdefxacnsvlr']));
		$valorFaixa = $temFaixa ? $volumetria['rdefxacnsvlr'] : null;


		if ($result && !$temFaixa) {

			$valor = $CapturaModulo->CaputuraValor($codConsulta);
			if ($valor) {
				return [0.98 * $quantidade, $result];
			}
		}

		$capturaValor = new CapturaValorDaConsulta();
		$valor = !$valorFaixa ? $capturaValor->execute($codConsulta) : $valorFaixa;
		if ($valor) {
			return [$valor * $quantidade, $volumetria];
		}

		$capturaValorFaixa = new CapturaValorDaConsultaPorFaixa();
		$valor = !$valorFaixa ?  $capturaValorFaixa->execute($codConsulta, $rede, $quantidade) : $valorFaixa;
		if ($valor) {
			return [$valor * $quantidade, $volumetria];
		}

		return [0, $result];
	}
}
