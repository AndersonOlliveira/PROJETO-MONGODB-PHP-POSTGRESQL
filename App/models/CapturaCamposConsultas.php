<?php


class CapturaCamposConsultas extends Model
{


    public function Consultation_description($description)
    {
        // $placeholders = implode(',', array_fill(0, count($description), '?'));

        $description = array_map('trim', explode(',', $description));
        $placeholders = implode(',', array_fill(0, count($description), '?'));
        echo "<pre>";
        echo "meu pla";

        print_r($placeholders);

        $cpovar = [];


        $orderByCase = "CASE cpovar ";
        $i = 1;
        $paramIndex = 1;

        foreach ($description as $value) {
            $orderByCase .= "WHEN $" . $paramIndex . " THEN $i ";
            $i++;
            $paramIndex++;
        }
        $orderByCase .= "END";

        echo "<pre>";
        echo "meur";

        print_r($orderByCase);
        $sql = "";
        $sql = "SELECT
        	DISTINCT  cpodsc, 
            $orderByCase as ordem
            FROM
            rdecns inner join 
            rdecnsreg on rdecnsid = rdecnsregrdecns inner join 
            regcpo on rdecnsregreg = regcporeg  inner join
            cpo on cpoid = regcpocpo
            inner join
            reg on regid = rdecnsregreg
            where
            cpovar in ($placeholders)
            order by ordem;";

        try {

            echo "<pre>";
            echo "meu select\n";

            print_r($sql);


            $results = $this->db->prepare($sql);

            $results->execute($description);

            if ($results->rowCount() > 0) {

                while ($row = $results->fetch(PDO::FETCH_ASSOC)) {


                    $cpovar[] = self::limparCampo(mb_convert_encoding($row['cpodsc'], 'UTF-8', 'ISO-8859-1'));

                    echo "<pre>";
                }
            }
        } catch (Exception $e) {

            return $e->getMessage();
        }


        return $cpovar;
    }

    public function Consultation_header_new($dados, $headers)
    {
        // echo "<pre>";

        // print_r('estou chamando dentro do dados');
        // print_r($dados);


        $description = array_map('trim', explode(';', $headers));
        // echo "<pre>";

        // print_r('estou chamando dentro do headers');
        // print_r($description);
        $orderBy = "CASE cpovar ";
        foreach ($description as $i => $campo) {
            $campo = addslashes($campo);
            $orderBy .= "WHEN '{$campo}' THEN {$i} ";
        }
        $orderBy .= "END";

        $ids = $dados;
        $idConsultation = array_map('intval', (array)$dados);
        $ids = implode(',', $ids);
        $placeholders = implode(',', array_fill(0, count($idConsultation), '?'));

        $sql = "";
        $sql = "SELECT
        	DISTINCT cpovar, cpodsc, regcod,
             {$orderBy} AS ordem
            FROM
            rdecns inner join 
            rdecnsreg on rdecnsid = rdecnsregrdecns inner join 
            regcpo on rdecnsregreg = regcporeg  inner join
            cpo on cpoid = regcpocpo
            inner join
            reg on regid = rdecnsregreg
            WHERE rdecnsid IN ($placeholders)
            AND cpo.cpovar IN (" . implode(',', array_fill(0, count($description), '?')) . ")
            ORDER BY ordem, regcod";

        try {

            $params = array_merge(
                $idConsultation,
                $description
            );


            echo "<pre>";
            echo "meus PARAMETROS";

            var_dump($params);

            $results = $this->db->prepare($sql);
            $results->execute($params);

            $cpovar = [];
            $dados_geral = [];
            $cpodsc = [];
            $plugin = [];



            if ($results->rowCount() > 0) {

                while ($row = $results->fetch(PDO::FETCH_ASSOC)) {



                    $cpovar[] = trim($row['cpovar']);

                    $dados_geral[] = [
                        $row['cpovar'],
                        $row['regcod'],
                        mb_convert_encoding($row['cpodsc'], 'UTF-8', 'ISO-8859-1')
                    ];
                    $plugin[] = $row['regcod'];
                    $cpodsc[] = mb_convert_encoding($row['cpodsc'], 'UTF-8', 'ISO-8859-1');
                }

                $headersNew = [];
                foreach ($idConsultation as $id) {
                    $headersNew[$id] = self::heades($id, $headers);
                }

                $result = [
                    'cpovars' => array_unique($cpovar),
                    // 'plugin' => $plugin,
                    'descriptions' => array_unique($cpodsc),
                    'geral' => $dados_geral,
                    'headersNew' => $headersNew
                ];


                $retorno_consulta =  $this->consult_header_plugin($result);

                return $retorno_consulta;
            }
            return false;
        } catch (PDOException $e) {
            return 'Erro ao buscar consultas: ' . $e->getMessage();
            var_dump('Erro ao buscar consultas: ' . $e->getMessage());
        }
    }
    public function heades($codConsulta)
    {


        echo "<pre>";
        echo "meu header enviado";

        print_R($codConsulta);




        echo "<pre>";
        echo "MEU COND DE CONSULTA \n";

        print_R($codConsulta);
        $ids = $codConsulta;

        $sql = "";
        $sql = "SELECT
        	DISTINCT cpovar, cpodsc, regcod
            FROM
            rdecns inner join 
            rdecnsreg on rdecnsid = rdecnsregrdecns inner join 
            regcpo on rdecnsregreg = regcporeg  inner join
            cpo on cpoid = regcpocpo
            inner join
            reg on regid = rdecnsregreg
            WHERE rdecnsid = ?;";


        try {

            $dadoss = [];
            $dadoss[] = $codConsulta;
            // echo "<pre>";
            // echo "dados para consultas";
            // print_R($ids);
            // echo "<pre>";
            // echo "id vindo do consultation passando para a variavle dados";
            // print_R($dadoss);

            $results = $this->db->prepare($sql);
            $results->execute([$codConsulta]);

            $cpovar = [];
            $cpovarCod = [];
            $dados_geral = [];
            $cpodsc = [];
            $plugin = [];


            if ($results->rowCount() > 0) {

                while ($row = $results->fetch(PDO::FETCH_ASSOC)) {


                    // echo "<pre>";
                    // echo "meu resultado da consulta\n";
                    // print_r($row);

                    $cpovar[] = trim($row['cpovar']);
                    // $cpovar[] = implode(',',$row['cpovar']);
                    // Use $codConsulta as the key, since $dadoss is an array and cannot be used as an array key
                    // $cpovarCod[] = $row['cpovar'];
                    // $dados_geral[] = [
                    //     $row['cpovar'],
                    //     $row['regcod'],
                    //     mb_convert_encoding($row['cpodsc'], 'UTF-8', 'ISO-8859-1')
                    // ];
                    // $plugin[] = $row['regcod'];
                    // $cpodsc[] = mb_convert_encoding($row['cpodsc'], 'UTF-8', 'ISO-8859-1');
                }

                return [
                    $codConsulta => [
                        'cpovars'       => array_values(array_unique($cpovar)),
                        'tes'       => trim(implode(',', array_unique($cpovar))),
                        // 'descriptions' => array_values(array_unique($cpodsc)),
                        // 'geral'        => $dados_geral
                    ]
                ];


                // echo "<pre>";
                // echo "meu resultado da consulta\n";
                // print_r($result);


                // $retorno_consulta =  $this->consult_header_plugin($result);

                // return $retorno_consulta;
            }
            return false;
        } catch (PDOException $e) {
            // Loga ou exibe o erro (não exiba em produção!)

            error_log('Erro ao buscar consultas: ' . $e->getMessage());
            return 'Erro ao buscar consultas: ' . $e->getMessage();
        }
    }

    public function consult_header_plugin($dados_consult)
    {
        $plugins_ids = [];
        $plugins_nomes = [];

        foreach ($dados_consult['geral'] as $values) {

            if (isset($values[1])) {
                $plugins_ids[] = $values[1];
            }
            if (isset($values[2])) {
                $plugins_nomes[] = $values[2];
            }
        }

        if (empty($plugins_ids)) {
            return [];
        }

        if (empty($plugins_nomes)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($plugins_ids), '?'));
        $sql = "";
        $sql = "SELECT
                *
            FROM progestor.plugin_campos_input
            WHERE numero_plugin IN ($placeholders);";
        try {

            $results = $this->db->prepare($sql);

            $results->execute($plugins_ids);

            $all_rows = $results->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {

            return $e->getMessage();
        }



        $dados_pesquisas_limpos = array_map(function ($item) {
            return self::limparCampo($item);
        }, $plugins_nomes);

        $pluginFiltrado = array_filter($all_rows, function ($item) use ($plugins_ids) {
            return in_array($item['numero_plugin'], $plugins_ids);
        });

        $pluginFiltrado = array_values($pluginFiltrado);



        $campoExiste = false;


        $camposObrigatoriosPorPlugin = [];

        foreach ($pluginFiltrado as $plugin) {


            $camposObrigatoriosPorPlugin[$plugin['numero_plugin']] =
                self::camposObrigatoriosDoPlugin($plugin, $dados_pesquisas_limpos);
        }



        $camposObrigatoriosTotais = [];

        foreach ($camposObrigatoriosPorPlugin as $listaCampos) {
            $camposObrigatoriosTotais = array_merge($camposObrigatoriosTotais, $listaCampos);
        }


        $camposObrigatoriosTotais = array_unique($camposObrigatoriosTotais);

        //Agora marca no $dados_consult['descriptions']
        if (!empty($camposObrigatoriosTotais) && !empty($dados_consult['descriptions'])) {

            foreach ($dados_consult['descriptions'] as $key => $descricaoOriginal) {

                // Limpa a descrição para poder comparar
                $descricaoLimpa = (self::limparCampo($descricaoOriginal));

                // Se for obrigatório 
                if (in_array($descricaoLimpa, $camposObrigatoriosTotais)) {
                    $dados_consult['descriptions'][$key] = rtrim($descricaoOriginal) . ';obrigatorio';
                    $dados_consult['campos'][$key] = $dados_consult['cpovars'][$key];
                }
            }
        }

        if (!$campoExiste) {

            return $dados_consult;

            // return $camposObrigatoriosPorPlugin;
        }



        //     $ids_unicos = array_unique($plugins_nomes);



        //     $in  = implode(',', array_fill(0, count($plugins_nomes), '?'));


        //     $parametros_not_null = [];

        //     // foreach ($plugins_nomes as $valor) {
        //     // foreach ($parametros as $campo) {
        //     //     // monta condição com parênteses corretos
        //     //     $parametros_not_null[] = " ( $campo LIKE TRIM('%$in%'))";
        //     // }
        //     // } 
        //     foreach ($parametros as $campo) {
        //         // monta condição com parênteses corretos
        //         $parametros_not_null[] = " $campo is not null";
        //     }


        //     $parametros_condition = implode(' and ', $parametros_not_null);

        //     $sql = "SELECT
        //         *
        //     FROM progestor.plugin_campos_input
        //     WHERE numero_plugin IN ($placeholders);";



        //     //    -- AND ($parametros_condition);";

        //     // echo "<pre>";
        //     // print_r($sql);

        //     $dados_pesquisas = ['UF', 'CRM', 'Nome'];

        //    echo "MEU RETORNO\n";
        //     var_dump(array_filter($dados_pesquisas, function (string $value) {
        //         return  'UF';
        //     }));

        //     try {

        //         $results = $this->db->prepare($sql);

        //         $results->execute($plugins_ids);


        //         $all_rows = $results->fetchAll(PDO::FETCH_ASSOC);

        //         echo "<pre>";


        //         // print_R($all_rows);


        //         $dados_pesquisas = ['UF', 'CRM', 'Nome'];
        //         echo "minha Busca \n";
        //         print_R($dados_pesquisas);

        //         $deve_incluir = false;

        //         foreach ($all_rows as $valor) {

        //             // echo "<pre>";
        //             // echo "meus retorno de dados vindo da consulta.\n";
        //             // print_r($valor);
        //             for ($i = 1; $i <= 10; $i++) {
        //                 $nome_coluna = 'parametro' . $i;
        //                 // echo "<pre>";
        //                 // echo "Valor da minha coluna.\n";
        //                 // print_R($nome_coluna);

        //                 if (isset($valor[$nome_coluna])) {
        //                     $valor_coluna = trim($valor[$nome_coluna]);

        //                     // echo "<pre>";
        //                     // echo "Valor da minha coluna preenchida.\n";
        //                     // print_R($valor_coluna);
        //                     //     $valor_coluna = trim($valor[$nome_coluna]);

        //                     foreach ($dados_pesquisas as $filtro) {
        //                         $valor_filtro = trim($filtro);
        //                         // echo "<pre>";
        //                         // echo "Tenho resultado do filtro.\n";
        //                         // print_R($valor_filtro);


        //                         if (strpos($valor_filtro, $valor_coluna) !== false) {
        //                             echo "Existe testar na string.\n";

        //                             $deve_incluir = true;
        //                             break;
        //                         }
        //                     }
        //                 }

        //                 if ($deve_incluir) {
        //                     $resultados_filtrados[] = ['valor' => $valor_coluna];
        //                 }
        //             }
        //         }

        //         // }


        //         echo "<pre>";
        //         echo "tenho os resultados aqui.\n";
        //         $resultados_filtrados = array_unique($resultados_filtrados);

        //         print_r($resultados_filtrados);

        //         //         // if (in_array($valor, $valor_coluna)) {

        //         //         //     echo "tenho dados aqui";
        //         //         // }

        //         // $deve_incluir = false;
        //         // foreach ($all_rows as $row) {
        //         //     // Itera sobre as colunas parametro1 até parametro10
        //         //     for ($i = 1; $i <= 10; $i++) {
        //         //         $nome_coluna = 'parametro' . $i;

        //         //         if (isset($row[$nome_coluna])) {
        //         //             $valor_coluna = trim($row[$nome_coluna]);

        //         //             foreach ($ids_unicos as $filtro) {
        //         //                 $valor_filtro = trim($filtro);
        //         //                 echo "<pre>";
        //         //                 print_R($valor_filtro);


        //         //                 if (strpos($valor_coluna, $valor_filtro) !== false) {
        //         //                     $deve_incluir = true;
        //         //                     break;
        //         //                 }
        //         //             }
        //         //         }
        //         //     }

        //         //     if ($deve_incluir) {
        //         //         $resultados_filtrados[] = $row;
        //         //     }
        //         // }
        //         // echo "<pre>";

        //         // print_r($resultados_filtrados);



        //         return $resultados_filtrados; // Retorna o array para uso posterior

        //     } catch (\Exception $e) {

        //         print_r($e->getMessage());
        //         // Log ou tratamento de erro
        //         return $e->getMessage();
        //     }
    }

    // function campoExisteNosParametros(array $plugin, array $campos): bool
    // {

    //     $registroObrigatorio = [];
    //     for ($i = 1; $i <= 10; $i++) {
    //         $param = trim($plugin["parametro{$i}"]);

    //         if ($param !== '' && in_array($param, $campos)) {
    //             $registroObrigatorio[] = $param;
    //         }
    //     }

    //     // return array_unique($registroObrigatorio); // evita repetição
    // }


    public static function limparCampo($string)
    {
        $string = trim($string);
        $string = preg_replace('/^\d+\s*-\s*/', '', $string); // remove "01 - "
        $string = str_replace(['-', '/'], [' ', ' '], $string); // troca - e /
        $string = preg_replace('/\s+/', ' ', $string); // remove espaços duplicados
        $string = mb_strtoupper($string); // deixa tudo em maiúsculo


        return $string;
    }
    public static function camposObrigatoriosDoPlugin(array $plugin, array $campos)
    {
        $camposEncontrados = [];
        for ($i = 1; $i <= 10; $i++) {
            $param = isset($plugin["parametro{$i}"]) ? trim($plugin["parametro{$i}"]) : '';

            $param =  mb_convert_encoding($param, 'UTF-8', 'ISO-8859-1');
            $paramLimpo = self::limparCampo($param);

            if ($param != '' &&  in_array($paramLimpo, $campos)) {
                $camposEncontrados[] = $paramLimpo;
            }
        }
        return array_unique($camposEncontrados);
    }






    public  function Consultation_header_tdados($idConsultation)
    {

        $sql = " SELECT DISTINCT TRIM(cpovar) as cpovar
    FROM rdecns
    INNER JOIN rdecnsreg ON rdecnsid = rdecnsregrdecns
    INNER JOIN regcpo     ON rdecnsregreg = regcporeg
    INNER JOIN cpo        ON cpoid = regcpocpo
    WHERE rdecnsid = ?
      AND TRIM(cpovar) = 'tcpfcnpj'
    LIMIT 1;";

        $dados = [];
        $dados[] = $idConsultation;

        $result = $this->db->prepare($sql);
        $result->execute($dados);

        $dadosRetorno = [];

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $dadosRetorno[] = trim($row['cpovar']);
        }

        if (empty($dadosRetorno)) {
            return false;
        }

        return $dadosRetorno;
    }
}
