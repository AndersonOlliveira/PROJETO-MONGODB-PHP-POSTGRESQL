<?php

class Crc_tratativas extends Model
{

    public function __construct()
    {
        parent::__construct();
    }


    public function get_lista_tratativas()
    {


        $sql = "SELECT * FROM crc_tratativa_tipo";
        $sql = $this->db->prepare($sql);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRelatorio()
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
            FROM generate_series(1, 200) s(i)
        ),
        crc_ficticio AS (
            SELECT 
                700906 + i AS crcid,
                CAST('2026-05-01' AS DATE) + (i % 16) AS crcdatvct, -- Datas entre 01/05 e 16/05
                'DOC-' || LPAD(i::text, 3, '0') AS crcdocger,
                (i * 45.50) + 100.00 AS crcvlr, -- Valores acima de zero
                i AS crccli, -- Vincula 1 para 1 com os clientes
                1 AS crcfil, -- Filial 1 fixa para passar no filtro
                'N' AS crcbxd, -- Não baixado
                false AS crcprepago -- Não pré-pago
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

        -- 2. Sua consulta original adaptada e corrigida
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
        INNER JOIN public.crc_tratativas_movimentacao real_mov ON real_mov.crc_tratativas_crcid = crc.crcid
        INNER JOIN public.crc_tratativa_tipo tipo ON tipo.id_crc_tratativa_tipo = real_mov.crc_tratativa_tipo_id
        INNER JOIN public.crc_tipo_acoes ac on ac.cod_acao = real_mov.crc_tipo_acoes_id
        INNER JOIN (
            SELECT 
                crc_tratativas_id,
                cod_status,
                data_cadastro as ultima_consulta,
                ROW_NUMBER() OVER (PARTITION BY crc_tratativas_id ORDER BY data_cadastro DESC) as rn
            FROM 
                public.crc_tratativas_status
        ) st ON st.crc_tratativas_id = real_mov.id_crc_tratativas AND st.rn = 1 -- Filtra apenas a linha mais recente
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
            st.crc_tratativas_id DESC";
        $sql = $this->db->prepare($sql);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
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
                    echo "VENHO AQUI?\n";
                    // registro não existe: criar movimentação pendente
                    //c
                    return false;
                }
            }

            if ($dados) {
                echo "<pre>";
                echo "DADOS ENVIADOS\n";
                // extract($dados);


                var_dump($dados);
                foreach ($dados as $items) {

                    echo "<pre>";

                    print_r($items);
                }
                // tratamento adicional se dados estiverem presentes
            }

            return true;
        } catch (PDOException $e) { // Melhor usar PDOException para erros do que Exception
            error_log('Erro verifry_cobraca: ' . $e->getMessage());
            return false;
        }
    }

    public function insertMovimentacao($idCobranca)
    {

        $sql = "INSERT INTO public.crc_tratativas_movimentacao(
                    crc_tratativas_crcid, crc_tratativa_tipo_id, crc_tipo_acoes_id, descricao_movimentacao, ctr_interno)
                    VALUES (:cobranca, :crc_tratativa_tipo_id, :crc_tipo_acoes_id, :descricao_movimentacao, :ctr_interno)
                    RETURNING id_crc_tratativas;";

        $sqlStatus = "INSERT INTO public.crc_tratativas_status (crc_tratativas_id, cod_status, status_descricao) 
                      VALUES (:crc_tratativas_id, :cod_status, :status_descricao);";

        try {

            $crc_tratativa_tipo_id = isset($dados) ? $dados['tipo_trativa'] : 5;  // Ex: ID do WHATS na tabela 'crc_tratativa_tipo' // AGUARDAR INICIO 5
            $crc_tipo_acoes_id = 5;     // Ex: ID do Pendente na tabela 'crc_tipo_acoes' // AGUARDAR INICIO5 


            $descricao_movimentacao = 'AGUARDA INICIO';
            $ctr_interno = 417039;

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

            $cod_status = 0; # -- Código interno do sistema para Pendente
            $status_descricao = 'INFORMOU QUE NAO LEMBRA DESTA CONTA';

            $stmt = $this->db->prepare($sqlStatus);
            $stmt->bindParam(':crc_tratativas_id', $idTratativa);
            $stmt->bindParam(':cod_status', $cod_status);
            $stmt->bindParam(':status_descricao', $status_descricao);
            $stmt->execute();

            echo "Movimentação e Status inseridos com sucesso!";
        } catch (PDOException $e) {
            echo "<pre>";
            echo "TENHO ERROS NESTA BUSCA:\n";
            print_r($e->getMessage());
            echo "</pre>";
        }
    }
}
