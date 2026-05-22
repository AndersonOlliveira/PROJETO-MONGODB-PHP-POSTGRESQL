<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passíveis de Suspensão · Gestão de Tratativas</title>
    <link href="../css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="../css/flatpickr/flatpickr.min.css" rel="stylesheet">
    <link href="../css/Relatorio/viewSups.css?v= <?= time(); ?>" rel="stylesheet">
</head>

<body>

    <?php $tctrid = isset($_GET['tctrid']) ? $_GET['tctrid'] : 417039; ?>
    <?php $tctraut = isset($_GET['tctraut']) ? $_GET['tctraut'] : 'a14beaccd7f530ea7e7c8847d35cd0af'; ?>

    <div class="topbar">
        <div class="topbar-brand">
            <div class="dot"></div>
            <div>
                <h5>Clientes Passíveis de Suspensão</h5>
                <small>Relatório de Clientes Suspensos ou Passíveis de Suspensão</small>
            </div>
        </div>
        <div class="topbar-actions">
            <button class="btn-top primary" onclick="window.location='?rota=index'">Voltar para Tratativas</button>
            <button class="btn-top danger">Sair</button>
        </div>
    </div>

    <div class="filterbar">
        <div class="fg">
            <label>Vencimento Inicial</label>
            <input type="date" id="f-data-ini">
        </div>
        <div class="fg">
            <label>Vencimento Final</label>
            <input type="date" id="f-data-fim">
        </div>
        <div class="fg">
            <label>Ordenação</label>
            <select id="f-ordenacao">
                <option value="tordctr">Documento Gerado</option>
                <option value="torddat">Data de Vencimento</option>
                <option value="tordnom">Nome do Cliente</option>
                <option value="tordvlr">Valor da Parcela</option>
            </select>
        </div>
        <button class="btn-buscar" id="btn-filtrar">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.35-4.35" />
            </svg>
            Filtrar Banco
        </button>
        <button class="btn-csv" id="btn-csv" title="Exportar registros filtrados para CSV">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <polyline points="7 10 12 15 17 10" />
                <line x1="12" y1="15" x2="12" y2="3" />
            </svg>
            Exportar CSV
        </button>
    </div>

    <div class="main-layout">
        <div class="table-wrap">
            <div class="table-inner">
                <div class="contagem" id="contagem-susp">Carregando...</div>
                <table id="table-suspensao">
                    <colgroup>
                        <col class="c-cliente">
                        <col class="c-perfil">
                        <col class="c-compra">
                        <col class="c-nnro">
                        <col class="c-venc">
                        <col class="c-valor">
                        <col class="c-docger">
                        <col class="c-suspenso">
                        <col class="c-contato">
                        <col class="c-vendedor">
                        <col class="c-acoes">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Perfil</th>
                            <th>Compra/Créd.</th>
                            <th>N.Nro</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                            <th>Doc. Ger.</th>
                            <th>Já Suspenso?</th>
                            <th>Contato / Telefone</th>
                            <th>Vendedor(es)</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="corpo-tabela-susp">
                        <tr class="loading-row">
                            <td colspan="11"><span class="spinner"></span>Processando análise de elegibilidade no banco...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="resumo-bar">
                <div class="resumo-item"><span class="resumo-label">Total de Parcelas:</span><span class="resumo-val" id="res-qtd">0</span></div>
                <div class="resumo-item"><span class="resumo-label">Somatória Total:</span><span class="resumo-val" id="res-total">R$ 0,00</span></div>
                <div class="resumo-item"><span class="resumo-label">Total Suspensos (Ativos):</span><span class="resumo-val" style="color:var(--danger)" id="res-ativos">R$ 0,00</span></div>
                <div class="resumo-item"><span class="resumo-label">Total Não Suspensos (Inativos):</span><span class="resumo-val" style="color:var(--success-text)" id="res-inativos">R$ 0,00</span></div>
            </div>
        </div>
    </div>


    <script type="text/javascript" src="../Scripts/jquery/jquery.min.js"></script>

    <!-- <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> -->

    <script src="../../Scripts/supsp.js?v=<?= time(); ?>"></script>


    <!-- Bootstrap -->
    <script type="text/javascript" src="../Scripts/propper/popper.min.js"></script>
    <!-- <script type="text/javascript" src="../Scripts/bootstrap/bootstrap.bundle.min.js"></script> -->
    <script type="text/javascript" src="../Scripts/bootstrap/bootstrap.min.js"></script>

    <!-- DataTables principal -->
    <script type="text/javascript" src="../Scripts/datatable/dataTables.js"></script>

    <!-- Extensão Buttons -->
    <script type="text/javascript" src="../Scripts/datatable/dataTablesbuttons.js"></script>

    <!-- Dependências de exportação -->
    <script type="text/javascript" src="../Scripts/jszip/jszip.min.js"></script>
    <!-- <script type="text/javascript" src="../Scripts/pdfMake/pdfmake.min.js"></script> -->
    <script type="text/javascript" src="../Scripts/pdfMake/vfs_fonts.js"></script>

    <!-- Botões HTML5 -->
    <script type="text/javascript" src="../Scripts/datatable/buttonshtml5.min.js"></script>

    <!-- Flatpickr -->
    <script type="text/javascript" src="../Scripts/flatpickr/flatpickr.js"></script>
    <script type="text/javascript" src="../Scripts/flatpickr/languageFlatPickr.js"></script>
    <!-- sweetAlert -->
    <script type="text/javascript" src="../Scripts/sweetAlert/sweetAlert.js"></script>
    <script type="text/javascript" src="../Scripts/flatpickr/flatpickr.js"></script>
    <script type="text/javascript" src="../Scripts/flatpickr/languageFlatPickr.js"></script>
</body>

</html>