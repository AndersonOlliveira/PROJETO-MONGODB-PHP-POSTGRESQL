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
