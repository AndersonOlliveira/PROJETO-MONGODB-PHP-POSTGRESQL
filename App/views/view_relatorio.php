<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
    <title>Bootstrap demo</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Parcelas em Atraso</title>

    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="../css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="../css/flatpickr/flatpickr.min.css" rel="stylesheet">
    <link href="../css/Relatorio/viewRelatorio.css?v= <?= time(); ?>" rel="stylesheet">
</head>

<body>

    <!-- ID VINDO DO BACKEND -->
    <input type="hidden" id="d-id" value="417039">

    <!-- topbar -->
    <div class="topbar">
        <div class="topbar-brand">
            <div class="dot"></div>
            <div>
                <h5>Relatório de parcelas em atraso</h5>
                <small>Parcelas pendentes ou vencidas com gestão de tratativas</small>
            </div>
        </div>
        <div class="topbar-actions">
            <button class="btn-top primary">Menu de serviços</button>
            <button class="btn-top">Início</button>
            <button class="btn-top danger">Sair</button>
        </div>
    </div>

    <!-- filtros (aplicados no front sobre os dados já carregados) -->
    <div class="filterbar">
        <div class="fg">
            <label>Data inicial</label>
            <input type="date" id="range">
        </div>
        <div class="fg">
            <label>Data final</label>
            <input type="date" id="range_ate">
        </div>
        <div class="fg">
            <label>Status</label>
            <select id="f-status">
                <option value="">Todos</option>
                <option value="0">Pendente</option>
                <option value="1">Em andamento</option>
                <option value="2">Sem retorno</option>
                <option value="3">Resolvido</option>
                <option value="4">Cancelado</option>
            </select>
        </div>
        <div class="fg">
            <label>Dias de atraso</label>
            <select id="f-dias">
                <option value="">Todos</option>
                <option value="1-10">1 a 10 dias</option>
                <option value="11-30">11 a 30 dias</option>
                <option value="31+">Acima de 30</option>
            </select>
        </div>
        <div class="fg fg-busca">
            <label>Buscar</label>
            <input type="text" id="f-busca" placeholder="Cliente, parcela, doc...">
        </div>
        <button class="btn-buscar" id="btn-buscar">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.35-4.35" />
            </svg>
            Buscar
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

    <!-- layout: tabela + drawer -->
    <div class="main-layout" id="main-layout">

        <!-- tabela -->
        <div class="table-wrap" id="table-wrap">
            <div class="table-inner">
                <div class="contagem" id="contagem"></div>
                <table id="table-relatorio">
                    <colgroup>
                        <col class="c-cliente">
                        <col class="c-perfil">
                        <col class="c-compra">
                        <col class="c-nnro">
                        <col class="c-venc">
                        <col class="c-valor">
                        <col class="c-docger">
                        <col class="c-dias">
                        <col class="c-vendedor">
                        <col class="c-ultima">
                        <col class="c-status">
                        <col class="c-proxacao">
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
                            <th>Dias atraso</th>
                            <th>Vendedor</th>
                            <th class="col-ocultavel">Última tratativa</th>
                            <th>Status</th>
                            <th class="col-ocultavel">Próxima ação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="corpo-tabela">
                        <tr class="loading-row">
                            <td colspan="13"><span class="spinner"></span>Carregando dados...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="paginacao" id="paginacao">
                <span id="pag-info">—</span>
                <div class="pg-btns" id="pg-btns"></div>
            </div>
        </div>

        <!-- drawer lateral: 3 abas — Nova tratativa | Histórico | Detalhes -->
        <div class="drawer" id="drawer">
            <div class="drawer-header">
                <div>
                    <h6 id="drawer-cliente-nome">—</h6>
                    <small id="drawer-parcela-info">—</small>
                </div>
                <button class="btn-fechar-drawer" id="btn-fechar-drawer" title="Fechar painel">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="drawer-tabs">
                <div class="drawer-tab ativo" data-tab="tratativa">Nova tratativa</div>
                <div class="drawer-tab" data-tab="historico">Histórico</div>
                <div class="drawer-tab" data-tab="detalhes">Detalhes</div>
            </div>

            <div class="drawer-body">

                <!-- aba: nova tratativa -->
                <div class="tab-pane ativo" id="tab-tratativa">
                    <div class="form-row-d">
                        <div class="form-group-d">
                            <label class="form-label-d">Tipo de contato</label>
                            <select class="form-control-d" id="d-tipo-contato">
                                <!-- <option value="WhatsApp">WhatsApp</option>
                                <option value="E-mail">E-mail</option>
                                <option value="Ligação">Ligação</option>
                                <option value="Visita">Visita</option>
                                <option value="SMS">SMS</option> -->
                            </select>
                        </div>
                        <!-- <div class="form-group-d">
                            <label class="form-label-d">Data da tratativa</label>
                            <input type="date" class="form-control-d" id="d-data-tratativa">
                        </div> -->
                    </div>
                    <!-- <div class="form-row-d"> -->
                    <div class="form-group-d">
                        <label class="form-label-d">Responsável</label>
                        <!-- <input type="text" class="form-control-d" id="d-responsavel" placeholder="Nome"> -->
                        <input type="text" class="form-control-d" id="d-responsavel" disabled>
                        <input type="hidden" value="" id="d-responsavel-id">
                        <!-- </div> -->
                        <!-- <div class="form-group-d">
                            <label class="form-label-d">Responsável</label>

                            <input type="text" class="form-control-d" id="d-responsavel" disabled>
                            <input type="hidden" value="" id="d-responsavel-id">
                        </div> -->
                    </div>
                    <div class="form-group-d">
                        <label class="form-label-d">Status da tratativa</label>
                        <select class="form-control-d" id="d-status-tratativa">
                            <option value="0">Pendente</option>
                            <option value="1">Em andamento</option>
                            <option value="3">Resolvido</option>
                            <option value="2">Sem retorno</option>
                            <option value="4">Cancelado</option>
                        </select>
                    </div>
                    <div class="form-group-d">
                        <label class="form-label-d">Descrição / Observação</label>
                        <textarea class="form-control-d" id="d-descricao" placeholder="Descreva o contato realizado..."></textarea>
                    </div>
                    <div class="form-group-d">
                        <label class="form-label-d">Próxima ação</label>
                        <select class="form-control-d" id="d-proxima-acao">
                            <option value="">Carregando...</option>
                        </select>
                    </div>
                    <button class="btn-salvar" id="btn-salvar-tratativa">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                            <polyline points="7 3 7 8 15 8" />
                        </svg>
                        Salvar tratativa
                    </button>
                </div>

                <!-- aba: histórico -->
                <div class="tab-pane" id="tab-historico">
                    <div id="historico-lista">
                        <div style="text-align:center;padding:40px;color:var(--muted);font-size:12px">
                            Selecione uma parcela para ver o histórico.
                        </div>
                    </div>
                </div>

                <!-- aba: detalhes -->
                <div class="tab-pane" id="tab-detalhes">
                    <div class="det-section">
                        <div class="det-section-title">Dados da parcela</div>
                        <div class="det-row"><span class="det-label">Cliente</span> <span class="det-val" id="det-cliente">—</span></div>
                        <div class="det-row"><span class="det-label">Perfil</span> <span class="det-val" id="det-perfil">—</span></div>
                        <div class="det-row"><span class="det-label">Compra / Crédito</span><span class="det-val" id="det-compra">—</span></div>
                        <div class="det-row"><span class="det-label">N.Nro</span> <span class="det-val" id="det-nnro">—</span></div>
                        <div class="det-row"><span class="det-label">Vencimento</span> <span class="det-val" id="det-venc">—</span></div>
                        <div class="det-row"><span class="det-label">Valor</span> <span class="det-val" id="det-valor">—</span></div>
                        <div class="det-row"><span class="det-label">Doc. Gerado</span> <span class="det-val" id="det-docger">—</span></div>
                        <div class="det-row"><span class="det-label">Dias em atraso</span><span class="det-val red" id="det-dias">—</span></div>
                    </div>
                    <div class="det-section">
                        <div class="det-section-title">Tratativa atual</div>
                        <div class="det-row"><span class="det-label">Vendedor</span> <span class="det-val" id="det-vendedor">—</span></div>
                        <div class="det-row"><span class="det-label">Status</span> <span class="det-val" id="det-status">—</span></div>
                        <div class="det-row"><span class="det-label">Última tratativa</span> <span class="det-val" id="det-ultima">—</span></div>
                        <div class="det-row"><span class="det-label">Próxima ação</span> <span class="det-val" id="det-proxacao">—</span></div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <div class="toast-wrapper" id="toast-wrapper"></div>



    <!-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script> -->


    <script type="text/javascript" src="../Scripts/jquery/jquery.min.js"></script>

    <!-- <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> -->

    <script src="../../Scripts/tratativa.js?v=<?= time(); ?>"></script>


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