<?php

class Crc_tratativas extends Model
{

    protected $arquivoLog;

    protected $ajustar;


    public function __construct()
    {
        parent::__construct();


        $this->arquivoLog = $_SERVER['DOCUMENT_ROOT'] . '../error/errorTratativa.txt';

        require_once __DIR__ . "/../Utilis/validaCampos.php";

        $this->ajustar = new validaCampos();


        $diretorio = dirname($this->arquivoLog);

        // Se a pasta não existir, cria ela com permissão de leitura e escrita
        if (is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        if (file_exists($this->arquivoLog)) {
            touch($this->arquivoLog);
            chmod($this->arquivoLog, 0664); // Dá permissão de leitura/escrita para o arquivo
        }

        set_error_handler([$this, 'manipuladorDeErros']);
    }


    public function listTipoContrato($cod = null)
    {
        $sql = "SELECT * FROM public.crc_tratativa_tipo where cod_tipo_tratativa not in (6) ";


        $parametro = [];

        if ($cod) {
            $sql .= " AND cod_tipo_tratativa = :cod_tipo_tratativa";
            $parametro[':cod_tipo_tratativa'] = $cod;
        }

        $sql .= " order by tipo_tratativa asc;";

        try {
            $stmt = $this->db->prepare($sql);

            foreach ($parametro as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            self::manipuladorDeErros(10, 'Erro na busca public.crc_tratativa_tipo: ' . $e->getMessage(), __FILE__, __LINE__);

            echo "ERRO: " . $e->getMessage();
        }
    }

    public function listTipoAcoes($cod = null)
    {


        $sql = "SELECT * FROM public.crc_tipo_acoes  where cod_acao not in (6) ";
        // $sql = $this->db->prepare($sql);

        $parametro = [];
        if ($cod) {
            $sql .= " AND cod_acao = :cod";
            $parametro[':cod'] = $cod;
        }

        $sql .= " order by acao_descricao asc;";
        try {
            $stmt = $this->db->prepare($sql);


            foreach ($parametro as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            self::manipuladorDeErros(10, 'Erro na busca das açoes public.crc_tipo_acoes : ' . $e->getMessage(), __FILE__, __LINE__);

            echo "ERRO: " . $e->getMessage();
        }
    }

    public function getRelatorio($mes = null, $data_inicio = null, $data_fim = null)
    {


        //	-- 1. Criar dados virtuais em lote usando generate_series dentro das CTEs

        $sql = "WITH perfilcob_ficticio AS (
            SELECT 1 AS perfilcobid, 'Boleto bancário' AS perfilcobtipo UNION ALL
            SELECT 2, 'Cartão de Crédito' AS perfilcobtipo UNION ALL
            SELECT 3, 'PIX Mensal' AS perfilcobtipo
        ),
        cli_ficticio AS (
            SELECT 
                i AS cliid,
                '(11) 9' || LPAD((i * 13)::text, 8, '0') AS clicobtel,
                'Contato Financeiro ' || i AS clicomctt,
                CASE WHEN i % 10 = 0 THEN 'S' ELSE 'N' END AS clissp,
                'Empresa Cliente ' || i || ' LTDA' AS clinomraz,
                (i % 3) + 1 AS cliperfilcobid
            FROM generate_series(1, 200) s(i)
        ),
        crc_ficticio AS (
            SELECT 
                700906 + i AS crcid,
                CAST('2026-05-01' AS DATE) + (i % 16) AS crcdatvct,
                'DOC-' || LPAD(i::text, 3, '0') AS crcdocger,
                (i * 45.50) + 100.00 AS crcvlr,
                i AS crccli,
                1 AS crcfil,
                'N' AS crcbxd,
                false AS crcprepago
            FROM generate_series(1, 200) s(i)
        ),
        ven_ficticio AS (
            SELECT 1 AS venid, 'Carlos Vendedor' AS venean UNION ALL
            SELECT 2, 'Roberto Comercial' AS venean UNION ALL
            SELECT 3, 'Ana Consultora' AS venean
        ),
        vencli_ficticio AS (
            SELECT i AS venclicli, (i % 3) + 1 AS vencliven FROM generate_series(1, 200) s(i)
            UNION ALL
            SELECT i AS venclicli, 2 AS vencliven FROM generate_series(1, 200) s(i) WHERE i % 4 = 0
        )

        SELECT  
            crc.crcid as N_Nro,               
            crc.crcdatvct as Vencimento,
            crc.crcdocger as Doc_Ger,
            crc.crcvlr as valor,
            cli.clicobtel as telefone,
            cli.clicomctt as Contato_financeiro,
            upper(cli.clissp) as Suspenso,
            cli.clinomraz as cliente,
            cli.cliid,
            array_to_string(array_agg(cast(ven.venean as text)),', ') as vendedor,
            perfilcob.perfilcobtipo, 
            crc.crcprepago,
            real_mov.crc_tratativas_crcid as movivementacao,
            real_mov.crc_tratativa_tipo_id as idTipo,
            real_mov.crc_tipo_acoes_id as idAcoes,
            real_mov.descricao_movimentacao as descricao_mov, 
            real_mov.ctr_interno as contratoResponsavel,
            tipo.tipo_tratativa as status,
            ac.acao_descricao as descricao_acao,             
            st.crc_tratativas_id as ultima_info,
            st.ultima_consulta,
            st.cod_status 
        FROM 
            cli_ficticio cli 
        INNER JOIN crc_ficticio crc ON crc.crccli = cli.cliid 
        LEFT JOIN vencli_ficticio vencli ON vencli.venclicli = cli.cliid
        LEFT JOIN ven_ficticio ven ON vencli.vencliven = ven.venid 
        LEFT JOIN perfilcob_ficticio perfilcob ON perfilcob.perfilcobid = cli.cliperfilcobid
        INNER JOIN (
            SELECT *,
                ROW_NUMBER() OVER (PARTITION BY crc_tratativas_crcid ORDER BY id_crc_tratativas DESC) as rn_mov
            FROM public.crc_tratativas_movimentacao
        ) real_mov ON real_mov.crc_tratativas_crcid = crc.crcid AND real_mov.rn_mov = 1
        INNER JOIN public.crc_tratativa_tipo tipo ON tipo.id_crc_tratativa_tipo = real_mov.crc_tratativa_tipo_id
        INNER JOIN public.crc_tipo_acoes ac on ac.cod_acao = real_mov.crc_tipo_acoes_id
        INNER JOIN (
            SELECT 
                crc_tratativas_id,
                cod_status,
                data_cadastro as ultima_consulta,
                ROW_NUMBER() OVER (PARTITION BY crc_tratativas_id ORDER BY data_cadastro DESC ) as rn 
            FROM 
                public.crc_tratativas_status
        ) st ON st.crc_tratativas_id = real_mov.id_crc_tratativas AND st.rn = 1
        WHERE 
            crc.crcfil = 1 AND
            upper(crc.crcbxd) = 'N' AND
            crc.crcvlr > 0.00 AND
            crc.crcprepago = false ";


        $filtros = [];
        $params = [];

        // if (!empty($numeroCobranca)) {
        //     $filtros[] = " crc.crcid = :numeroCobranca";
        //     $params[':numeroCobranca'] = $numeroCobranca;
        // }

        // Cenário A: Filtro por período (Data Início e Fim)
        if (!empty($data_inicio) && !empty($data_fim)) {

            $filtros[] = " crc.crcdatvct::date BETWEEN :data_inicio AND :data_fim";
            $data_inicio_obj = self::converterData($data_inicio);
            $data_fim_obj = self::converterData($data_fim);

            if ($data_inicio_obj && $data_fim_obj) {
                $data_inicio = $data_inicio_obj->format('Y-m-d');
                $data_fim = $data_fim_obj->format('Y-m-d');

                $params[':data_inicio'] = $data_inicio;
                $params[':data_fim'] = $data_fim;
            } else {

                return ['msg' => 'DATAS ENVIADAS NO FORMATO INVALIDO.'];
            }
        }
        // Cenário B: Filtro por Mês/Ano (Caso não tenha o período completo)
        elseif (!empty($mes)) {
            $dados_mes = explode('/', $mes);
            if (count($dados_mes) == 2) {

                $filtros[] = " EXTRACT(MONTH FROM crc.crcdatvct) = :mes";
                $filtros[] = " EXTRACT(YEAR FROM crc.crcdatvct) = :ano";
                $params[':mes'] = (int)$dados_mes[0];
                $params[':ano'] = (int)$dados_mes[1];
            }
        }
        // Se houver filtros, aplica-os à consulta
        if (!empty($filtros)) {
            $sql .= " AND " . implode(" AND ", $filtros);
        }

        // 3. Recoloca o GROUP BY e ORDER BY no final de tudo
        $sql .= " GROUP BY 
                crc.crcid, crc.crcdatvct, crc.crcdocger, crc.crcvlr, cli.clicobtel,
                cli.clicomctt, cli.clissp, cli.clinomraz, cli.cliid, perfilcob.perfilcobtipo,
                crc.crcprepago,
                real_mov.crc_tratativas_crcid,
                real_mov.crc_tratativa_tipo_id,
                real_mov.crc_tipo_acoes_id,
                real_mov.descricao_movimentacao,
                real_mov.ctr_interno,
                tipo.tipo_tratativa,
                ac.acao_descricao,
                st.crc_tratativas_id,
                st.ultima_consulta,
                st.cod_status
                        ORDER BY 
                            ultima_info DESC,
                st.crc_tratativas_id DESC limit 1";

        try {

            $constul = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $constul->bindValue($key, $value);
            }

            $constul->execute();

            if ($constul->rowCount() > 0) {
                $result = [];

                $a = $constul->fetchAll(PDO::FETCH_ASSOC);

                foreach ($a as  $key => $items) {

                    $a['perfilcobtipo'] = self::converterData($a['perfilcobtipo']);

                    if (isset($items['contratoresponsavel']) && !empty($items['contratoresponsavel'])) {
                        //PEGO O NOME DO USUARIOS RESPONSAVEL PELA INSERÇÃO DO DADO   
                        $items['res'] = self::info_responsavel($items['contratoresponsavel']);
                    } else {
                        $items['res'] = 'SISTEMA';
                    }

                    $result[] = $items;
                }

                return $result;
            } else {

                return ['msg' => 'Nenhum resultado encontrado para os filtros informados.'];
            }
        } catch (PDOException $e) {
            self::manipuladorDeErros(10, 'Erro na busca do relatori: ' . $e->getMessage(), __FILE__, __LINE__);
        }
    }
    public function getRelatorioAll($numeroCobranca)
    {


        //	-- 1. Criar dados virtuais em lote usando generate_series dentro das CTEs

        $sql = "  WITH perfilcob_ficticio AS (
            SELECT 1 AS perfilcobid, 'Boleto bancário' AS perfilcobtipo UNION ALL
            SELECT 2, 'Cartão de Crédito' AS perfilcobtipo UNION ALL
            SELECT 3, 'PIX Mensal' AS perfilcobtipo
        ),
        cli_ficticio AS (
            SELECT 
                i AS cliid,
                '(11) 9' || LPAD((i * 13)::text, 8, '0') AS clicobtel,
                'Contato Financeiro ' || i AS clicomctt,
                CASE WHEN i % 10 = 0 THEN 'S' ELSE 'N' END AS clissp,
                'Empresa Cliente ' || i || ' LTDA' AS clinomraz,
                (i % 3) + 1 AS cliperfilcobid
            FROM generate_series(1, 200) s(i)
        ),
        crc_ficticio AS (
            SELECT 
                700906 + i AS crcid,
                CAST('2026-05-01' AS DATE) + (i % 16) AS crcdatvct,
                'DOC-' || LPAD(i::text, 3, '0') AS crcdocger,
                (i * 45.50) + 100.00 AS crcvlr,
                i AS crccli,
                1 AS crcfil,
                'N' AS crcbxd,
                false AS crcprepago
            FROM generate_series(1, 200) s(i)
        ),
        ven_ficticio AS (
            SELECT 1 AS venid, 'Carlos Vendedor' AS venean UNION ALL
            SELECT 2, 'Roberto Comercial' AS venean UNION ALL
            SELECT 3, 'Ana Consultora' AS venean
        ),
        vencli_ficticio AS (
            SELECT i AS venclicli, (i % 3) + 1 AS vencliven FROM generate_series(1, 200) s(i)
            UNION ALL
            SELECT i AS venclicli, 2 AS vencliven FROM generate_series(1, 200) s(i) WHERE i % 4 = 0
        )

        SELECT  
            crc.crcid as N_Nro,               
            crc.crcdatvct as Vencimento,
            crc.crcdocger as Doc_Ger,
            crc.crcvlr as valor,
            cli.clicobtel as telefone,
            cli.clicomctt as Contato_financeiro,
            upper(cli.clissp) as Suspenso,
            cli.clinomraz as cliente,
            cli.cliid,
            array_to_string(array_agg(cast(ven.venean as text)),', ') as vendedor,
            perfilcob.perfilcobtipo, 
            crc.crcprepago,
            real_mov.crc_tratativas_crcid as movivementacao,
            real_mov.crc_tratativa_tipo_id as idTipo,
            real_mov.crc_tipo_acoes_id as idAcoes,
            real_mov.descricao_movimentacao as descricao_mov, 
            real_mov.ctr_interno as contratoResponsavel,
            tipo.tipo_tratativa as status,
            ac.acao_descricao as descricao_acao,             
            st.crc_tratativas_id as ultima_info,
            st.ultima_consulta,
            st.cod_status 
        FROM 
            cli_ficticio cli 
        INNER JOIN crc_ficticio crc ON crc.crccli = cli.cliid 
        LEFT JOIN vencli_ficticio vencli ON vencli.venclicli = cli.cliid
        LEFT JOIN ven_ficticio ven ON vencli.vencliven = ven.venid 
        LEFT JOIN perfilcob_ficticio perfilcob ON perfilcob.perfilcobid = cli.cliperfilcobid
        INNER JOIN (
            SELECT *,
                ROW_NUMBER() OVER (PARTITION BY crc_tratativas_crcid ORDER BY id_crc_tratativas DESC) as rn_mov
            FROM public.crc_tratativas_movimentacao
        ) real_mov ON real_mov.crc_tratativas_crcid = crc.crcid
        INNER JOIN public.crc_tratativa_tipo tipo ON tipo.id_crc_tratativa_tipo = real_mov.crc_tratativa_tipo_id
        INNER JOIN public.crc_tipo_acoes ac on ac.cod_acao = real_mov.crc_tipo_acoes_id
        INNER JOIN (
            SELECT 
                crc_tratativas_id,
                cod_status,
                data_cadastro as ultima_consulta,
                ROW_NUMBER() OVER (PARTITION BY crc_tratativas_id ORDER BY data_cadastro DESC ) as rn 
            FROM 
                public.crc_tratativas_status
        ) st ON st.crc_tratativas_id = real_mov.id_crc_tratativas AND st.rn = 1
        WHERE 
            crc.crcfil = 1 AND
            upper(crc.crcbxd) = 'N' AND
            crc.crcvlr > 0.00 AND
            crc.crcprepago = false 
			AND  crc.crcid = :numeroCobranca
			
			GROUP BY 
                crc.crcid, crc.crcdatvct, crc.crcdocger, crc.crcvlr, cli.clicobtel,
                cli.clicomctt, cli.clissp, cli.clinomraz, cli.cliid, perfilcob.perfilcobtipo,
                crc.crcprepago,
                real_mov.crc_tratativas_crcid,
                real_mov.crc_tratativa_tipo_id,
                real_mov.crc_tipo_acoes_id,
                real_mov.descricao_movimentacao,
                real_mov.ctr_interno,
                tipo.tipo_tratativa,
                ac.acao_descricao,
                st.crc_tratativas_id,
                st.ultima_consulta,
                st.cod_status
                        ORDER BY 
                            ultima_info DESC,
                st.crc_tratativas_id DESC; ";


        try {
            $constul = $this->db->prepare($sql);
            $constul->bindValue(':numeroCobranca', $numeroCobranca);
            $constul->execute();

            if ($constul->rowCount() > 0) {
                $result = [];

                $a = $constul->fetchAll(PDO::FETCH_ASSOC);

                foreach ($a as  $key => $items) {

                    if (isset($items['contratoresponsavel']) && !empty($items['contratoresponsavel'])) {
                        //PEGO O NOME DO USUARIOS RESPONSAVEL PELA INSERÇÃO DO DADO   
                        $items['res'] = self::info_responsavel($items['contratoresponsavel']);
                    } else {
                        $items['res'] = 'SISTEMA';
                    }

                    $result[] = $items;
                }

                return $result;
            } else {

                return ['msg' => 'Nenhum resultado encontrado para os filtros informados.'];
            }
        } catch (PDOException $e) {

            self::manipuladorDeErros(10, 'Erro na busca do relatorioAll: ' . $e->getMessage(), __FILE__, __LINE__);
        }
    }
    public function getRelatorio_ultima()
    {

        //	-- 1. Criar dados virtuais em lote usando generate_series dentro das CTEs

        $sql = "WITH perfilcob_ficticio AS (
            SELECT 1 AS perfilcobid, 'Boleto bancário' AS perfilcobtipo UNION ALL
            SELECT 2, 'Cartão de Crédito' AS perfilcobtipo UNION ALL
            SELECT 3, 'PIX Mensal' AS perfilcobtipo
        ),
        cli_ficticio AS (
            SELECT 
                i AS cliid,
                '(11) 9' || LPAD((i * 13)::text, 8, '0') AS clicobtel,
                'Contato Financeiro ' || i AS clicomctt,
                CASE WHEN i % 10 = 0 THEN 'S' ELSE 'N' END AS clissp,
                'Empresa Cliente ' || i || ' LTDA' AS clinomraz,
                (i % 3) + 1 AS cliperfilcobid
            FROM generate_series(1, 200) s(i)
        ),
        crc_ficticio AS (
            SELECT 
                700906 + i AS crcid,
                CAST('2026-05-01' AS DATE) + (i % 16) AS crcdatvct,
                'DOC-' || LPAD(i::text, 3, '0') AS crcdocger,
                (i * 45.50) + 100.00 AS crcvlr,
                i AS crccli,
                1 AS crcfil,
                'N' AS crcbxd,
                false AS crcprepago
            FROM generate_series(1, 200) s(i)
        ),
        ven_ficticio AS (
            SELECT 1 AS venid, 'Carlos Vendedor' AS venean UNION ALL
            SELECT 2, 'Roberto Comercial' AS venean UNION ALL
            SELECT 3, 'Ana Consultora' AS venean
        ),
        vencli_ficticio AS (
            SELECT i AS venclicli, (i % 3) + 1 AS vencliven FROM generate_series(1, 200) s(i)
            UNION ALL
            SELECT i AS venclicli, 2 AS vencliven FROM generate_series(1, 200) s(i) WHERE i % 4 = 0
        )

        SELECT  
            crc.crcid as N_Nro,               
            crc.crcdatvct as Vencimento,
            crc.crcdocger as Doc_Ger,
            crc.crcvlr as valor,
            cli.clicobtel as telefone,
            cli.clicomctt as Contato_financeiro,
            upper(cli.clissp) as Suspenso,
            cli.clinomraz as cliente,
            cli.cliid,
            array_to_string(array_agg(cast(ven.venean as text)),', ') as vendedor,
            perfilcob.perfilcobtipo, 
            crc.crcprepago,
            real_mov.crc_tratativas_crcid as movivementacao,
            real_mov.crc_tratativa_tipo_id as idTipo,
            real_mov.crc_tipo_acoes_id as idAcoes,
            real_mov.descricao_movimentacao as descricao_mov, 
            real_mov.ctr_interno as contratoResponsavel,
            tipo.tipo_tratativa as status,
            ac.acao_descricao as descricao_acao,             
            st.crc_tratativas_id as ultima_info,
            st.ultima_consulta,
            st.cod_status 
        FROM 
            cli_ficticio cli 
        INNER JOIN crc_ficticio crc ON crc.crccli = cli.cliid 
        LEFT JOIN vencli_ficticio vencli ON vencli.venclicli = cli.cliid
        LEFT JOIN ven_ficticio ven ON vencli.vencliven = ven.venid 
        LEFT JOIN perfilcob_ficticio perfilcob ON perfilcob.perfilcobid = cli.cliperfilcobid
        INNER JOIN (
            SELECT *,
                ROW_NUMBER() OVER (PARTITION BY crc_tratativas_crcid ORDER BY id_crc_tratativas DESC) as rn_mov
            FROM public.crc_tratativas_movimentacao
        ) real_mov ON real_mov.crc_tratativas_crcid = crc.crcid AND real_mov.rn_mov = 1
        INNER JOIN public.crc_tratativa_tipo tipo ON tipo.id_crc_tratativa_tipo = real_mov.crc_tratativa_tipo_id
        INNER JOIN public.crc_tipo_acoes ac on ac.cod_acao = real_mov.crc_tipo_acoes_id
        INNER JOIN (
            SELECT 
                crc_tratativas_id,
                cod_status,
                data_cadastro as ultima_consulta,
                ROW_NUMBER() OVER (PARTITION BY crc_tratativas_id ORDER BY data_cadastro DESC ) as rn 
            FROM 
                public.crc_tratativas_status
        ) st ON st.crc_tratativas_id = real_mov.id_crc_tratativas AND st.rn = 1
        WHERE 
            crc.crcfil = 1 AND
            upper(crc.crcbxd) = 'N' AND
            crc.crcvlr > 0.00 AND
            crc.crcprepago = false 
        GROUP BY 
            crc.crcid, crc.crcdatvct, crc.crcdocger, crc.crcvlr, cli.clicobtel,
            cli.clicomctt, cli.clissp, cli.clinomraz, cli.cliid, perfilcob.perfilcobtipo,
            crc.crcprepago,
            real_mov.crc_tratativas_crcid,
            real_mov.crc_tratativa_tipo_id,
            real_mov.crc_tipo_acoes_id,
            real_mov.descricao_movimentacao,
            real_mov.ctr_interno,
            tipo.tipo_tratativa,
            ac.acao_descricao,
            st.crc_tratativas_id,
            st.ultima_consulta,
            st.cod_status
            
        ORDER BY 
            ultima_info DESC,
            st.crc_tratativas_id DESC LIMIT 1;";

        try {
            $sql = $this->db->prepare($sql);
            $sql->execute();

            $result = [];

            $a = $sql->fetchAll(PDO::FETCH_ASSOC);

            foreach ($a as  $key => $items) {

                if (isset($items['contratoresponsavel']) && !empty($items['contratoresponsavel'])) {
                    //PEGO O NOME DO USUARIOS RESPONSAVEL PELA INSERÇÃO DO DADO   
                    $items['res'] = self::info_responsavel($items['contratoresponsavel']);
                } else {
                    $items['res'] = 'SISTEMA';
                }

                $result[] = $items;
            }


            return $result;
        } catch (PDOException $e) {
            self::manipuladorDeErros(10, 'Erro na busca do getRelatorio_ultima: ' . $e->getMessage(), __FILE__, __LINE__);
        }
    }
    public function getRelatorio_old()
    {

        //	-- 1. Criar dados virtuais em lote usando generate_series dentro das CTEs

        $sql = "WITH perfilcob_ficticio AS (
                    SELECT 1 AS perfilcobid, 'Boleto bancário' AS perfilcobtipo UNION ALL
                    SELECT 2, 'Cartão de Crédito' AS perfilcobtipo UNION ALL
                    SELECT 3, 'PIX Mensal' AS perfilcobtipo
                ),
                cli_ficticio AS (
                    SELECT 
                        i AS cliid,
                        '(11) 9' || LPAD((i * 13)::text, 8, '0') AS clicobtel,
                        'Contato Financeiro ' || i AS clicomctt,
                        CASE WHEN i % 10 = 0 THEN 'S' ELSE 'N' END AS clissp, -- 10% suspensos
                        'Empresa Cliente ' || i || ' LTDA' AS clinomraz,
                        (i % 3) + 1 AS cliperfilcobid -- Alterna entre os 3 perfis
                    FROM generate_series(1, 100) s(i)
                ),
                crc_ficticio AS (
                    SELECT 
                        -- 1000 + i AS crcid,
                         700907  AS crcid,
                        CAST('2026-05-01' AS DATE) + (i % 14) AS crcdatvct, -- Datas entre 01/05 e 14/05
                        'DOC-' || LPAD(i::text, 3, '0') AS crcdocger,
                        (i * 45.50) + 100.00 AS crcvlr, -- Valores acima de zero
                        i AS crccli, -- Vincula 1 para 1 com os clientes
                        1 AS crcfil, -- Filial 1 fixa para passar no filtro
                        'N' AS crcbxd, -- Não baixado
                        false AS crcprepago -- Não pré-pago
                    FROM generate_series(1, 100) s(i)
                ),
                ven_ficticio AS (
                    SELECT 1 AS venid, 'Carlos Vendedor' AS venean UNION ALL
                    SELECT 2, 'Roberto Comercial' AS venean UNION ALL
                    SELECT 3, 'Ana Consultora' AS venean
                ),
                vencli_ficticio AS (
                    -- Vincula cada cliente a pelo menos 1 vendedor principal
                    SELECT i AS venclicli, (i % 3) + 1 AS vencliven FROM generate_series(1, 100) s(i)
                    UNION ALL
                    -- Adiciona um segundo vendedor para alguns clientes (para testar o array_agg)
                    SELECT i AS venclicli, 2 AS vencliven FROM generate_series(1, 100) s(i) WHERE i % 4 = 0
                )

                -- 2. Sua consulta original adaptada para ler as tabelas virtuais acima
                SELECT  
                    crcid as N_Nro,
                    crcdatvct as Vencimento,
                    crcdocger as Doc_Ger,
                    crcvlr as valor,
                    clicobtel as telefone,
                    clicomctt as Contato_financeiro,
                    upper(clissp) as Suspenso,
                    clinomraz as cliente,
                    cliid,
                    array_to_string(array_agg(cast(venean as text)),', ') as vendedor,
                    perfilcobtipo, 
                    crcprepago
                FROM 
                    cli_ficticio cli 
                INNER JOIN crc_ficticio crc ON crccli = cliid 
                LEFT JOIN vencli_ficticio vencli ON vencli.venclicli = cli.cliid
                LEFT JOIN ven_ficticio ven ON vencli.vencliven = ven.venid 
                LEFT JOIN perfilcob_ficticio perfilcob ON perfilcobid = cliperfilcobid
                WHERE 
                    crcfil = 1 AND
                    upper(crcbxd) = 'N' AND
                    crcvlr > 0.00 AND
                    crcprepago = false
                GROUP BY 
                    crcid, crcdatvct, crcdocger, crcvlr, clicobtel, clicomctt, clissp, clinomraz, cliid, perfilcobtipo, crcprepago
                ORDER BY 
                    vencimento ASC";
        $sql = $this->db->prepare($sql);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);

        // while()
    }


    public function info_responsavel($contrato)
    {


        $sql = "";
        $sql = "SELECT ctrapl FROM ctr where ctrid = :ctrid";


        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':ctrid', $contrato);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return trim($result['ctrapl']);
        } catch (PDOException $e) {
            self::manipuladorDeErros(8, 'Erro na busca do ctr: ' . $e->getMessage(), __FILE__, __LINE__);
        }
    }


    public function getRelatorio_origim($idCobranca = null)
    {

        //	-- 1. Criar dados virtuais em lote usando generate_series dentro das CTEs

        $sql = "SELECT  
                crcid as N_Nro,
                crcdatvct as Vencimento,
                crcdocger as Doc_Ger,crcvlr as valor,
                clicobtel as telefone,
                clicomctt as Contato_financeiro,
                upper(clissp) as Suspenso,
                clinomraz as cliente,
                cliid,
                array_to_string(array_agg(cast(venean as text)),', ') as vendedor,
                perfilcobtipo, 
                crcprepago FROM 
                cli INNER JOIN crc ON crccli = cliid 
                LEFT JOIN vencli ON vencli.venclicli = cli.cliid
                LEFT JOIN ven ON vencli.vencliven = ven.venid 
                LEFT JOIN perfilcob ON perfilcobid = cliperfilcobid
                where 
                crcfil =  1 and
                --crcdatvct between '2026-05-01' and  '2026-05-14-' AND
                upper(crcbxd) = 'N' AND
                crcvlr > '0.00' AND
                crcprepago = false ";

        $params = [];

        if ($idCobranca) {
            $sql .= " AND crcid = :crcid";
            $params[':crcid'] = $idCobranca;
        }

        $sql .= "  group by crcid ,clicobtel,clicomctt,clissp,clinomraz,cliid,venean,perfilcobtipo,crcprepago
                ORDER BY vencimento asc;";


        try {
            $sql = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $sql->bindValue($key, $value);
            }
            $sql->execute();

            return $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            self::manipuladorDeErros(10, 'Erro na busca do getRelatorio_origim: ' . $e->getMessage(), __FILE__, __LINE__);
            echo "ERRO: " . $e->getMessage();
        }
    }

    public function verifry_cobraca($idCobranca, $dados = null)
    {

        $sql = "SELECT EXISTS(
                    SELECT 1
                    FROM public.crc_tratativas_movimentacao
                    WHERE crc_tratativas_crcid = :id
                    LIMIT 1
                )";

        try {

            if (!empty($idCobranca)) {
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':id', $idCobranca, PDO::PARAM_INT);
                $stmt->execute();

                $resultado = $stmt->fetchColumn();

                if (!$resultado) {

                    // registro não existe: criar movimentação pendente
                    return  self::insertMovimentacao($idCobranca, null);
                    // return false;
                }
            }

            if (isset($dados)) {


                return  self::insertMovimentacao(null, $dados);
            }
        } catch (PDOException $e) { // Melhor usar PDOException para erros do que Exception
            error_log('Erro verifry_cobraca: ' . $e->getMessage());
            self::manipuladorDeErros(10, 'Erro verifry_cobraca:  ' . $e->getMessage(), __FILE__, __LINE__);

            return  ['status' => 'error', 'message' => 'Erro verifry_cobraca: ' . $e->getMessage()];
        }
    }

    public function insertMovimentacao($idCobranca, $dados = null)
    {

        if (!empty($dados)) {

            extract($dados);
        }



        $sql = "INSERT INTO public.crc_tratativas_movimentacao(
                    crc_tratativas_crcid, crc_tratativa_tipo_id, crc_tipo_acoes_id, descricao_movimentacao, ctr_interno)
                    VALUES (:cobranca, :crc_tratativa_tipo_id, :crc_tipo_acoes_id, :descricao_movimentacao, :ctr_interno)
                    RETURNING id_crc_tratativas;";

        $sqlStatus = "INSERT INTO public.crc_tratativas_status (crc_tratativas_id, cod_status, status_descricao) 
                      VALUES (:crc_tratativas_id, :cod_status, :status_descricao);";

        try {

            $crc_tratativa_tipo_id = isset($dados) && !empty($dados)  ? $status_tratativa : 6;  // Ex: ID do WHATS na tabela 'crc_tratativa_tipo' // AGUARDAR INICIO 5
            $crc_tipo_acoes_id = isset($dados) && !empty($dados)  ? $tipo_acoes : 6;     // Ex: ID do Pendente na tabela 'crc_tipo_acoes' // AGUARDAR INICIO 5 
            $descricao_movimentacao = isset($dados) && !empty($dados) ? $descricao : 'AGUARDA INICIO';
            $ctr_interno = isset($dados) && !empty($dados) ? $tctrid : null;
            $idCobranca = isset($dados) && !empty($dados)  ? $numeroCobranca : $idCobranca;

            $cliIds = self::getRelatorio_origim($idCobranca);
            $tpos =  self::tipo_tratativa(); //PEGO O TIOPO DA TRA

            $new_tipo = self::listTipoContrato($crc_tratativa_tipo_id);
            $new_acoes = self::listTipoAcoes($crc_tipo_acoes_id);
            $nome = empty($ctr_interno) ? "INSERIDO SISTEMA" :  $ctr_interno . " - " . self::info_responsavel($ctr_interno);

            $new_ocorrencia =  $this->ajustar->convertEncode('Foi inserido a movimentacao da cobrança ' . $idCobranca . ' com o  tipo ' . $new_tipo[0]['tipo_tratativa'] . ' com a ação ' . $new_acoes[0]['acao_descricao'] . ' com a seguinte observação ' . $descricao_movimentacao . ' inserido pelo o contrato ' . $ctr_interno);

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cobranca', $idCobranca);
            $stmt->bindParam(':crc_tratativa_tipo_id', $crc_tratativa_tipo_id);
            $stmt->bindParam(':crc_tipo_acoes_id', $crc_tipo_acoes_id);
            $stmt->bindParam(':descricao_movimentacao', $descricao_movimentacao);
            $stmt->bindParam(':ctr_interno', $ctr_interno);

            $stmt->execute();

            // Recupera o ID gerado usando o FETCH do RETURNING (Postgres)
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $idTratativa = $resultado['id_crc_tratativas'];

            $cod_status = isset($dados) && !empty($dados) ? $status_tratativa : 1;  //# dados;


            $status_descricao = $descricao_movimentacao;

            $stmt = $this->db->prepare($sqlStatus);
            $stmt->bindParam(':crc_tratativas_id', $idTratativa);
            $stmt->bindParam(':cod_status', $cod_status);
            $stmt->bindParam(':status_descricao', $status_descricao);
            // $stmt->execute();

            if ($stmt->execute()) {
                // Se a inserção do status for bem-sucedida, registra a ocorrência
                self::registrarOcorrencia($cliIds[0]['cliid'], $tpos[0]['tpoid'], $new_ocorrencia, $nome);

                return ['status' => 'success', 'message' => 'Movimentação e Status inseridos com sucesso!'];
            }
        } catch (PDOException $e) {
            self::manipuladorDeErros(11, 'Erro ao inserir movimentação  public.crc_tratativas_status public.crc_tratativas_movimentacao : ' . $e->getMessage(), __FILE__, __LINE__);

            return ['status' => 'error', 'message' => 'Erro ao inserir movimentação: ' . $e->getMessage()];
        }
    }


    public function registrarOcorrencia($cliId, $tpos, $descricao, $nome)
    {
        $sql = "INSERT INTO public.cliocr(
                     cliocrcli, cliocrtpo, cliocrant, cliocrrsp)
                    VALUES (:cliocrcli, :cliocrtpo, :cliocrant, :cliocrrsp);";

        try {
            $stmt = $this->db->prepare($sql);
            // $stmt->bindParam(':cliocrid', $descricao);
            $stmt->bindParam(':cliocrcli', $cliId);
            $stmt->bindParam(':cliocrtpo', $tpos);
            // $stmt->bindParam(':cliocrdat', $descricao);
            $stmt->bindParam(':cliocrant', $descricao);
            $stmt->bindParam(':cliocrrsp', $nome);
            // $stmt->bindParam(':cliocrcad', $descricao);
            // $stmt->bindParam(':cliocruploadname', $descricao);
            // $stmt->bindParam(':cliocruploadformat', $descricao);
            // $stmt->bindParam(':cliocruploadpath', $descricao);
            // $stmt->bindParam(':cliocrvlrcnsold', $descricao);
            $stmt->execute();
        } catch (PDOException $e) {
            self::manipuladorDeErros(11, 'Erro ao registrar ocorrência public.cliocr: ' . $e->getMessage(), __FILE__, __LINE__);
            error_log('Erro ao registrar ocorrência: ' . $e->getMessage());
        }
    }

    public function tipo_tratativa()
    {
        $sql = "SELECT tpoid FROM public.tpo WHERE tpodsc = trim('TRATATIVA COBRANCA') ;";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->execute();

            // print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            self::manipuladorDeErros(11, 'Erro ao listar tipo de contrato: ' . $e->getMessage(), __FILE__, __LINE__);
            error_log('Erro ao listar tipo de contrato: ' . $e->getMessage());
            // return [];
        }
    }


    public function return_dados_data($mes = null, $data_inicio = null, $data_fim = null)
    {


        //	-- 1. Criar dados virtuais em lote usando generate_series dentro das CTEs

        $sql = "WITH perfilcob_ficticio AS (
            SELECT 1 AS perfilcobid, 'Boleto bancário' AS perfilcobtipo UNION ALL
            SELECT 2, 'Cartão de Crédito' AS perfilcobtipo UNION ALL
            SELECT 3, 'PIX Mensal' AS perfilcobtipo
        ),
        cli_ficticio AS (
            SELECT 
                i AS cliid,
                '(11) 9' || LPAD((i * 13)::text, 8, '0') AS clicobtel,
                'Contato Financeiro ' || i AS clicomctt,
                CASE WHEN i % 10 = 0 THEN 'S' ELSE 'N' END AS clissp,
                'Empresa Cliente ' || i || ' LTDA' AS clinomraz,
                (i % 3) + 1 AS cliperfilcobid
            FROM generate_series(1, 200) s(i)
        ),
        crc_ficticio AS (
            SELECT 
                700906 + i AS crcid,
                CAST('2026-05-01' AS DATE) + (i % 16) AS crcdatvct,
                'DOC-' || LPAD(i::text, 3, '0') AS crcdocger,
                (i * 45.50) + 100.00 AS crcvlr,
                i AS crccli,
                1 AS crcfil,
                'N' AS crcbxd,
                false AS crcprepago
            FROM generate_series(1, 200) s(i)
        ),
        ven_ficticio AS (
            SELECT 1 AS venid, 'Carlos Vendedor' AS venean UNION ALL
            SELECT 2, 'Roberto Comercial' AS venean UNION ALL
            SELECT 3, 'Ana Consultora' AS venean
        ),
        vencli_ficticio AS (
            SELECT i AS venclicli, (i % 3) + 1 AS vencliven FROM generate_series(1, 200) s(i)
            UNION ALL
            SELECT i AS venclicli, 2 AS vencliven FROM generate_series(1, 200) s(i) WHERE i % 4 = 0
        )

        SELECT  
            crc.crcid as N_Nro,               
            crc.crcdatvct as Vencimento,
            crc.crcdocger as Doc_Ger,
            crc.crcvlr as valor,
            cli.clicobtel as telefone,
            cli.clicomctt as Contato_financeiro,
            upper(cli.clissp) as Suspenso,
            cli.clinomraz as cliente,
            cli.cliid,
            array_to_string(array_agg(cast(ven.venean as text)),', ') as vendedor,
            perfilcob.perfilcobtipo, 
            crc.crcprepago,
            real_mov.crc_tratativas_crcid as movivementacao,
            real_mov.crc_tratativa_tipo_id as idTipo,
            real_mov.crc_tipo_acoes_id as idAcoes,
            real_mov.descricao_movimentacao as descricao_mov, 
            real_mov.ctr_interno as contratoResponsavel,
            tipo.tipo_tratativa as status,
            ac.acao_descricao as descricao_acao,             
            st.crc_tratativas_id as ultima_info,
            st.ultima_consulta,
            st.cod_status 
        FROM 
            cli_ficticio cli 
        INNER JOIN crc_ficticio crc ON crc.crccli = cli.cliid 
        LEFT JOIN vencli_ficticio vencli ON vencli.venclicli = cli.cliid
        LEFT JOIN ven_ficticio ven ON vencli.vencliven = ven.venid 
        LEFT JOIN perfilcob_ficticio perfilcob ON perfilcob.perfilcobid = cli.cliperfilcobid
        INNER JOIN (
            SELECT *,
                ROW_NUMBER() OVER (PARTITION BY crc_tratativas_crcid ORDER BY id_crc_tratativas DESC) as rn_mov
            FROM public.crc_tratativas_movimentacao
        ) real_mov ON real_mov.crc_tratativas_crcid = crc.crcid AND real_mov.rn_mov = 1
        INNER JOIN public.crc_tratativa_tipo tipo ON tipo.id_crc_tratativa_tipo = real_mov.crc_tratativa_tipo_id
        INNER JOIN public.crc_tipo_acoes ac on ac.cod_acao = real_mov.crc_tipo_acoes_id
        INNER JOIN (
            SELECT 
                crc_tratativas_id,
                cod_status,
                data_cadastro as ultima_consulta,
                ROW_NUMBER() OVER (PARTITION BY crc_tratativas_id ORDER BY data_cadastro DESC ) as rn 
            FROM 
                public.crc_tratativas_status
        ) st ON st.crc_tratativas_id = real_mov.id_crc_tratativas AND st.rn = 1
        WHERE 
            crc.crcfil = 1 AND
            upper(crc.crcbxd) = 'N' AND
            crc.crcvlr > 0.00 AND
            crc.crcprepago = false ";


        $filtros = [];
        $params = [];

        // Cenário A: Filtro por período (Data Início e Fim)
        if (!empty($data_inicio) && !empty($data_fim)) {
            $filtros[] = " crc.crcdatvct::date BETWEEN :data_inicio AND :data_fim";

            $data_inicio_obj = self::converterData($data_inicio);
            $data_fim_obj = self::converterData($data_fim);

            if ($data_inicio_obj && $data_fim_obj) {

                $data_inicio = $data_inicio_obj->format('Y-m-d');
                $data_fim = $data_fim_obj->format('Y-m-d');

                $params[':data_inicio'] = $data_inicio;
                $params[':data_fim'] = $data_fim;
            }
            // Cenário B: Filtro por Mês/Ano (Caso não tenha o período completo)
            elseif (!empty($mes)) {
                $dados_mes = explode('/', $mes);
                if (count($dados_mes) == 2) {

                    $filtros[] = " EXTRACT(MONTH FROM crc.crcdatvct) = :mes";
                    $filtros[] = " EXTRACT(YEAR FROM crc.crcdatvct) = :ano";
                    $params[':mes'] = (int)$dados_mes[0];
                    $params[':ano'] = (int)$dados_mes[1];
                }
            }
            // Se houver filtros, aplica-os à consulta
            if (!empty($filtros)) {
                $sql .= " AND " . implode(" AND ", $filtros);
            }

            // 3. Recoloca o GROUP BY e ORDER BY no final de tudo
            $sql .= " GROUP BY 
                crc.crcid, crc.crcdatvct, crc.crcdocger, crc.crcvlr, cli.clicobtel,
                cli.clicomctt, cli.clissp, cli.clinomraz, cli.cliid, perfilcob.perfilcobtipo,
                crc.crcprepago,
                real_mov.crc_tratativas_crcid,
                real_mov.crc_tratativa_tipo_id,
                real_mov.crc_tipo_acoes_id,
                real_mov.descricao_movimentacao,
                real_mov.ctr_interno,
                tipo.tipo_tratativa,
                ac.acao_descricao,
                st.crc_tratativas_id,
                st.ultima_consulta,
                st.cod_status
                        ORDER BY 
                            ultima_info DESC,
                st.crc_tratativas_id DESC limit 1";




            $constul = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $constul->bindValue($key, $value);
            }

            $constul->execute();

            if ($constul->rowCount() > 0) {
                // echo "<pre>";
                // echo "DADOS LOCALIZADOS! Total de linhas: " . $constul->rowCount() . "\n";


                $result = [];

                $a = $constul->fetchAll(PDO::FETCH_ASSOC);

                foreach ($a as  $key => $items) {

                    if (isset($items['contratoresponsavel']) && !empty($items['contratoresponsavel'])) {
                        //PEGO O NOME DO USUARIOS RESPONSAVEL PELA INSERÇÃO DO DADO   
                        $items['res'] = self::info_responsavel($items['contratoresponsavel']);
                    } else {
                        $items['res'] = 'SISTEMA';
                    }

                    $result[] = $items;
                }


                return $result;
            } else {

                return ['msg' => 'Nenhum resultado encontrado para os filtros informados.'];
            }
        }
    }

    function converterData($data)
    {
        // tenta ano com 4 dígitos
        $date = DateTime::createFromFormat('d/m/Y', $data);

        // se falhar tenta com 2 dígitos
        if (!$date) {
            $date = DateTime::createFromFormat('d/m/y', $data);
        }

        return $date;
    }





    public function manipuladorDeErros($nivel, $mensagem, $arquivo, $linha)
    {
        $dataHora = date('Y-m-d H:i:s');

        // Formata o texto do erro
        $linhaDoErro = "[{$dataHora}] Nível: {$nivel} | Erro: {$mensagem} | Arquivo: {$arquivo} | Linha: {$linha}" . PHP_EOL;

        // Grava no arquivo usando a propriedade da classe
        error_log($linhaDoErro, 3, $this->arquivoLog);

        // Retorne false para que o PHP também mostre o erro na tela (se estiver em desenvolvimento)
        return false;
    }


    // BUSCAR SUSPENSA 
    public function list_supensao($dataIncial, $datafinal)
    {

        $sql = "";
        $sql = "SELECT  
                crcid as N_Nro,
                crcdatvct as Vencimento,
                crcdocger as Doc_Ger,crcvlr as valor,
                clicobtel as telefone,
                clicomctt as Contato_financeiro,
                upper(clissp) as Suspenso,
                clinomraz as cliente,
                cliid,
                array_to_string(array_agg(cast(venean as text)),', ') as vendedor,
                perfilcobtipo, 
                crcprepago FROM 
                cli INNER JOIN crc ON crccli = cliid 
                LEFT JOIN vencli ON vencli.venclicli = cli.cliid
                LEFT JOIN ven ON vencli.vencliven = ven.venid 
                LEFT JOIN perfilcob ON perfilcobid = cliperfilcobid
                where 
                crcfil =  1 and
                crcdatvct between :data_inicial and  :data_final AND
                upper(crcbxd) = 'N' AND
                crcvlr > '0.00' AND
                ( SELECT COUNT(*) FROM crc 
                WHERE crccli = cli.cliid and  crcbxd = 'N'  
                    and
                    crcdatvct <= :crcdatvctFinal ) > 3
                    AND
                    ( SELECT COUNT(*) FROM crc
                    WHERE  crccli = cli.cliid  and 
                    crcbxd = 'S'  and 
                    crcdatvct <= :crcdatvctMenor 
                    )  > 0 AND
                crcprepago = false
                group by crcid ,clicobtel,clicomctt,clissp,clinomraz,cliid,venean,perfilcobtipo,crcprepago
                ORDER BY vencimento asc;";

        try {

            $constul = $this->db->prepare($sql);


            $params = [];

            $params[':data_inicial'] = $dataIncial;
            $params[':data_final'] =   $datafinal;
            $params[':crcdatvctFinal'] = $datafinal;
            $params[':crcdatvctMenor'] = $datafinal;

            foreach ($params as $key => $value) {
                $constul->bindValue($key, $value);
            }


            if ($constul->rowCount() > 0) {

                return $constul->fetchAll(PDO::FETCH_ASSOC);
            } else {

                return ['msg' => 'Nenhum resultado encontrado para os filtros informados.'];
            }
        } catch (PDOException $e) {

            self::manipuladorDeErros(11, 'Erro ao listar DADOS CONTRATO : ' . $e->getMessage(), __FILE__, __LINE__);
        }
    }

    function removerAcentos($string)
    {
        // Normaliza os caracteres acentuados separando a letra do acento
        $normalizado = Normalizer::normalize($string, Normalizer::FORM_D);
        // Remove os acentos utilizando regex
        return preg_replace('/[\x{0300}-\x{036F}]/u', '', $normalizado);
    }
}
