<?php

class validacoes {

	public function validarParametrosDados($parametros,$chaves){

		foreach($chaves as $chave){
			
			if(!isset($parametros[$chave])){
				throw new Exception("$chave nao foi informado!");
			}
			$parametros[$chave] = trim(strip_tags($parametros[$chave]));
		}

		return $parametros;
	}

	public function formatarStringUtf8($string){
		if (!mb_check_encoding ( $string, "UTF-8" ) ){
			$string = utf8_encode( $string );
		}
		return $string;
	}

	public function validarEstadoCivil($estado_civil,$campo){
		$estado_civil_elementos = array("casado","uniao estavel","solteiro","separado","divorciado","viuvo");

		foreach($estado_civil_elementos as $key => $val){
			$estado_civil_elementos[$key] = strtoupper(trim($val));
		}

		$estado_civil = strtoupper(trim($estado_civil));

		if(array_search($estado_civil,$estado_civil_elementos) === false){
			return false;
		}

		return $estado_civil;
	}

	public function validarUfEstado($uf,$campo){
		$ufs = array("ac","al","am","ap","ba","ce","df","es","go","ma","mt","ms","mg","pa","pb","pr","pe","pi","rj","rn","ro","rs","rr","sc","se","sp","to");

		foreach($ufs as $key => $val){
			$ufs[$key] = strtoupper(trim($val));
		}

		$uf = strtoupper(trim($uf));

		if(array_search($uf,$ufs) === false){
			return false;
		}

		return $uf;
	}

	public function validarData($data,$is_nascimento=true){
		$data = trim($data);
		if(!preg_match("#^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$#", $data)){
			return false;
		}

		

		if(!$this->isDataValida($data)){
			return false;
		}

		if($is_nascimento){
			$aux = explode("/",$data);
			$y = $aux[2];
			if($aux[2] > date("Y")){
				return false;
			}

			$time_inicial = $this->geraTimestamp(date("d/m/Y"));
			$time_final = $this->geraTimestamp($data);
			$diferenca = $time_final - $time_inicial;
			$dias = (int)floor( $diferenca / (60 * 60 * 24));
			if($dias > 0){
				return false;
			}
		}

		return true;
	}

	public function isDataValida($dat){
		$data = explode("/",$dat);
		$d = $data[0];
		$m = $data[1];
		$y = $data[2];

		$res = checkdate($m,$d,$y);
		if ($res == 1){
			return true;
		} else {
			return false;
		}
	}

	public function geraTimestamp($data) {
		$partes = explode('/', $data);
		return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
	}

	public function validarTamanhoMinimoMaximo($string,$min,$max){
		$string = trim($string);

		if(strlen($string) < $min){
			return false;
		}

		if(strlen($string) > $max){
			return false;
		}

		return true;
	}

	public function validarCnpj($cnpj){
		$cnpj = preg_replace("~\D~","", $cnpj);

		if(strlen($cnpj) <> 14){
			return false;
		}

		if (($cnpj == '00000000000000') ||
		($cnpj == '11111111111111') ||
		($cnpj == '22222222222222') ||
		($cnpj == '33333333333333') ||
		($cnpj == '44444444444444') ||
		($cnpj == '55555555555555') ||
		($cnpj == '66666666666666') ||
		($cnpj == '77777777777777') ||
		($cnpj == '88888888888888') ||
		($cnpj == '99999999999999')
		) {
			return false;
		}

		$fncres1	= 0;
		$fncsum1	= 0;
		$fncdiv1	= 0;
		$fncmul1	= 5;
		$fncdig1	= '';

		$fncres2	= 0;
		$fncsum2	= 0;
		$fncdiv2	= 0;
		$fncmul2	= 6;
		$fncdig2	= '';

		for ($i = 0; $i < 12; $i++){
			$fncsum1 = $fncsum1 + (strval($cnpj[$i]) * $fncmul1);
			$fncsum2 = $fncsum2 + (strval($cnpj[$i]) * $fncmul2);

			$fncmul1--;
			$fncmul2--;

			if ($fncmul1 < 2){
				$fncmul1 = 9;
			}

			if ($fncmul2 < 2){
				$fncmul2 = 9;
			}
		}

		$fncdiv1 = $fncsum1 / 11;
		$fncmul1 = intval($fncdiv1) * 11;
		$fncres1 = $fncsum1 - $fncmul1;

		if ($fncres1 == 0 or $fncres1 == 1){
			$fncdig1 = 0;
		}else{
			$fncdig1 = 11 - $fncres1;
		}

		$fncsum2 = $fncsum2 + ($fncdig1 * 2);
		$fncdiv2 = $fncsum2 / 11;
		$fncmul2 = intval($fncdiv2) * 11;
		$fncres2 = $fncsum2 - $fncmul2;

		if ($fncres2 == 0 or $fncres2 == 1){
			$fncdig2 = 0;
		}else{
			$fncdig2 = 11 - $fncres2;
		}

		if ($fncdig1 == $cnpj[12] and $fncdig2 == $cnpj[13]){
			return true;
		}
		
		return false;
	}

	public function validarCpf($cpf){
		$cpf = preg_replace("~\D~","", $cpf);

		if(strlen($cpf) <> 11){
			return false;
		}

		if (($cpf == '00000000000') ||
		($cpf == '11111111111') ||
		($cpf == '22222222222') ||
		($cpf == '33333333333') ||
		($cpf == '44444444444') ||
		($cpf == '55555555555') ||
		($cpf == '66666666666') ||
		($cpf == '77777777777') ||
		($cpf == '88888888888') ||
		($cpf == '99999999999')
		) {
			return false;
		}

		$fncres1	= 0;
		$fncsum1	= 0;
		$fncdiv1	= 0;
		$fncmul1	= 10;

		$fncres2	= 0;
		$fncsum2	= 0;
		$fncdiv2	= 0;
		$fncmul2	= 11;

		for ($i = 0; $i < 9; $i++) {
			$fncsum1 = $fncsum1 + (strval($cpf[$i]) * $fncmul1);
			$fncsum2 = $fncsum2 + (strval($cpf[$i]) * $fncmul2);
			$fncmul1--;
			$fncmul2--;
		}

		$fncdiv1 = $fncsum1 / 11;
		$fncmul1 = intval($fncdiv1) * 11;
		$fncres1 = $fncsum1 - $fncmul1;

		if ($fncres1 == 0 or $fncres1 == 1) {
			$fncdig1 = 0;
		} else {
			$fncdig1 = 11 - $fncres1;
		}

		$fncsum2 = $fncsum2 + ($fncdig1 * 2);
		$fncdiv2 = $fncsum2 / 11;
		$fncmul2 = intval($fncdiv2) * 11;
		$fncres2 = $fncsum2 - $fncmul2;

		if ($fncres2 == 0 or $fncres2 == 1) {
			$fncdig2 = 0;
		} else {
			$fncdig2 = 11 - $fncres2;
		}

		if ($fncdig1 == $cpf[9] and $fncdig2 == $cpf[10]) {
			return true;
		}

		return false;
	}

	public function validarNumerico($numerico,$tamanho){
		$numerico = trim($numerico);

		if(preg_match("#\,#", $numerico)){
			$numerico = preg_replace("#\.#", "", $numerico);
			$numerico = preg_replace("#\,#", ".", $numerico);
		}

		if(!preg_match("#^[0-9]{1,$tamanho}\.[0-9]{2}$#", $numerico)){
			return false;
		}

		return $numerico;
	}

	public function validarSenha($senha){
		if(strlen($senha) < 8){
			return false;
		}

		$wordlen = strlen($senha);
		$score = pow($wordlen, 1.4);

		$pontuacao = array(
		'lowercase'=> 1,
		'uppercase'=> 3,
		'one_number'=> 3,
		'three_numbers'=> 5,
		'one_special_char'=> 3,
		'two_special_char'=> 5,
		'upper_lower_combo'=> 2,
		'letter_number_combo'=> 2,
		'letter_number_char_combo'=> 2
		);

		$regexs = array(
		'lowercase'=> '/[a-z]/',
		'uppercase'=> '/[A-Z]/',
		'one_number'=> '/\d+/',
		'three_numbers'=> '/(.*[0-9].*[0-9].*[0-9])/',
		'one_special_char'=> '/.[!,@,#,$,%,\^,&,*,?,_,~]/',
		'two_special_char'=> '/(.*[!,@,#,$,%,\^,&,*,?,_,~].*[!,@,#,$,%,\^,&,*,?,_,~])/',
		'upper_lower_combo'=> '/([a-z].*[A-Z])|([A-Z].*[a-z])/',
		'letter_number_combo'=> '/([a-zA-Z])/',
		'letter_number_char_combo'=> '/([a-zA-Z0-9].*[!,@,#,$,%,\^,&,*,?,_,~])|([!,@,#,$,%,\^,&,*,?,_,~].*[a-zA-Z0-9])/'
		);

		foreach ($regexs as $key => $regex){
			if(preg_match($regex, $senha)){
				$score += $pontuacao[$key];
			}
		}

		if($score <= 30){
			return false;
		}

		return true;
	}

	public function validarNomeSobrenome($nome){
		if(preg_match("/[A-z]{3,}[ ][A-z]{1,}/", $nome)){
			return true;
		}
		return false;
	}

	public function validarCelular($celular){
		$celular = preg_replace("~\D~","", $celular);

		if(strlen($celular) == 11){
			return true;
		}
	
		return false;
	}

	public function validarTelefoneFixo($telefone){
		$telefone = preg_replace("~\D~","", $telefone);

		if(strlen($telefone) == 10){
			return true;
		}
	
		return false;
	}

	public function validarEmail($email) {
		$email = strtolower(trim($email));
		if(preg_match("/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/", $email)){
			return true;
		}
		return false;
	}

	public function validarString($campo,$string,$tamanho){

		$string = $this->txtacento($string);

		if($tamanho and strlen($string) > $tamanho){
			return false;
		}

		return $string;
	}

	public function txtacento( $texto ) {

		$texto = $this->formatarStringUtf8($texto);

		$array1 = array(   "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç"
		, "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç" );
		$array2 = array(   "a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c"
		, "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C" );

		foreach ($array1 as $key => $val){
			$array1[$key] = $this->formatarStringUtf8($val);
		}

		$texto = str_replace( $array1, $array2, $texto );

		$texto = $this->limpatxt($texto);

		$texto = preg_replace("#\s{1,}#", " ", $texto);

		return strtoupper(trim($texto));
	}

	public function limpatxt ( $locfnctxt ){
		//================================//	Declara variaveis locais
		$locfncchr	=	'';		//	Caracter atual da string
		$locfncstr	=	'';		//	string ~limpa~
		$locfnccnt	=	0;		//	Contador auxiliar de caracter
		//=================================//	Le caracter a caracter
		while ($locfnccnt <= StrLen( $locfnctxt )  )
		{

			$locfncchr	=	substr( $locfnctxt, $locfnccnt, 1 );

			if ( @strpos('1234567890-&qwertyuiop[]QWERTYUIOPasdfghjklASDFGHJKL:zxcvbnm,./ZXCVBNM',$locfncchr)=== false )
			{
				$locfncchr	=	' ' ;
			}


			$locfncstr	.=	$locfncchr;
			++$locfnccnt;
		}

		return $locfncstr;
	}

	public function formatCnpjCpf($value){
		  $cnpj_cpf = preg_replace("/\D/", '', $value);
		  
		  if (strlen($cnpj_cpf) === 11) {
		    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
		  } 
		  
		  return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
	}

	public function formatCepTelefone($value,$delimitador,$quantidade_1,$quantidade_2){
		$value = preg_replace("/\D/", '', $value);  
	
		return preg_replace("/(\d{{$quantidade_1}})(\d{{$quantidade_2}})/", "\$1{$delimitador}\$2", $value);
	}

}

?>