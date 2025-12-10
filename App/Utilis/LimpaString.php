<?php

class LimpaString {

	public static function limpaConteudoArquivo($string) {

		$string = preg_replace('/[^a-zA-Z0-9_ %\[\]\(\n)\.\;\(\)%&-]/s', '', $string);
		return $string;
	}

	
}