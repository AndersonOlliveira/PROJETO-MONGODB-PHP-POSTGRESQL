<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
    <title>Bootstrap demo</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous"> -->
    <meta name="viewport" content="width=device-width" />
    <title>Relatório de Parcelas em Atraso</title>

    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="../../css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/flatpickr/flatpickr.min.css" rel="stylesheet">
    <link href="../../css/select/select2.min.css" rel="stylesheet">
    <link href="../../css/Relatorio/viewindicadores.css?v= <?= time(); ?>" rel="stylesheet">
</head>

<body>

    <?php $tctrid = isset($_GET['tctrid']) ? $_GET['tctrid'] : 417039; ?>
    <?php $tctraut = isset($_GET['tctraut']) ? $_GET['tctraut'] : 'a14beaccd7f530ea7e7c8847d35cd0af'; ?>
    <!-- ID VINDO DO BACKEND -->
    <input type="hidden" id="d-id" value="<?= $tctrid ?>">

    <!-- topbar -->
    <div class="topbar">
        <div class="topbar-brand">
            <div class="dot"></div>
            <div>
                <h5>Relatório de Kpis indicadores </h5>
                <small>Indicadores</small>
            </div>
        </div>

        <div class="topbar-actions">

            <a class="btn-top primary"
                href="/srv/srvcns.chp?tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>"
                title="Serviços">Menu de serviços
            </a>


            <a class="btn-top"
                href="relatz.chp?r=viewSups&tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>"
                title="Serviços">
                Passíveis de suspensão
            </a>


            <a class="btn-top danger"
                href="/srv/srvsai.chp?tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>"
                title="Sair do sistema">
                <svg xmlns="http://www.w3.org/2000/svg" width="15.765" height="16" viewBox="0 0 15.765 16">
                    <g id="Group" transform="translate(-1237 -36)">
                        <path id="Vector" d="M.36.232A1,1,0,0,1,1.768.36l2.083,2.5A1,1,0,0,1,2.315,4.14L.232,1.64A1,1,0,0,1,.36.232Z" transform="translate(1248.667 40.5)" fill="#4b4b4b" fill-rule="evenodd" />
                        <path id="Vector-2" d="M.36,4.286A1,1,0,0,1,.231,2.878L2.315.378a1,1,0,1,1,1.536,1.28l-2.083,2.5A1,1,0,0,1,.36,4.286Z" transform="translate(1248.667 42.982)" fill="#4b4b4b" fill-rule="evenodd" />
                        <path id="Vector-3" d="M14.5,8a1,1,0,0,1-1,1H7A1,1,0,1,1,7,7h6.5a1,1,0,0,1,1,1ZM0,1A1,1,0,0,1,1,0h9a1,1,0,1,1,0,2H1A1,1,0,0,1,0,1ZM0,15a1,1,0,0,1,1-1h9a1,1,0,1,1,0,2H1a1,1,0,0,1-1-1Z" transform="translate(1237 36)" fill="#4b4b4b" fill-rule="evenodd" />
                        <path id="Vector-4" d="M10,0a1,1,0,0,1,1,1V5A1,1,0,1,1,9,5V1a1,1,0,0,1,1-1Zm0,10a1,1,0,0,1,1,1v4a1,1,0,1,1-2,0V11a1,1,0,0,1,1-1ZM1,0A1,1,0,0,1,2,1V15a1,1,0,1,1-2,0V1A1,1,0,0,1,1,0Z" transform="translate(1237 36)" fill="#4b4b4b" fill-rule="evenodd" />
                    </g>
                </svg>
                Sair
            </a>
        </div>
    </div>

    <!-- filtros (aplicados no front sobre os dados já carregados) -->


    <!-- TESTE PARA MELHOR  OPÇÃO ABAS OU  ACORDIAN -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Home</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Profile</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Contact</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="disabled-tab" data-bs-toggle="tab" data-bs-target="#disabled-tab-pane" type="button" role="tab" aria-controls="disabled-tab-pane" aria-selected="false" disabled>Disabled</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">...</div>
        <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">...</div>
        <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">...</div>
        <div class="tab-pane fade" id="disabled-tab-pane" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">...</div>
    </div>

    <!-- accordion accordion-flush -->
    <div class="accordion accordion-flush" id="accordionFlushflow">
        <div class="accordion-item">
            <h2 class="accordion-header" id="flush-headinExecutor">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseExecutor" aria-expanded="false" aria-controls="flush-collapseOne">
                    Cadastrar usuário
                </button>
            </h2>
            <div id="flush-collapseExecutor" class="accordion-collapse collapse" aria-labelledby="flush-headinExecutor" data-bs-parent="#accordionFlushflow">
                <div class="accordion-body">FLUXO PARA APRESENTACAO DE FORMULADOR PARA CADASTRO DO EXECUTOR

                    <form id="cad_usuario">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Nome:</label>
                            <input type="text" class="form-control" id="n_usuario" aria-describedby="name_help">
                            <div id="emailHelp" class="form-text"></div>
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Password</label>
                            <input type="password" class="form-control" id="exampleInputPassword1">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="exampleCheck1">
                            <label class="form-check-label" for="exampleCheck1">Check me out</label>
                        </div>

                        <div class="mb-3 form-check">
                            <label class="form-check-label" for="exampleCheck1">Selecione Área</label>
                            <select class="form-control-d" id="d-tipo-area"></select>

                        </div>
                        <button class="btn-salvar-area" id="btn-salvar-usuarios">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                <polyline points="17 21 17 13 7 13 7 21" />
                                <polyline points="7 3 7 8 15 8" />
                            </svg>
                            Salvar tratativa
                        </button>
                        <input type="hidden" value="usuarios" id="d-usuario">
                    </form>
                </div>


            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="flush-headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                    Cadastrar área
                </button>
            </h2>
            <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">FLUXO PARA APRESENTACAO DE FORMULADOR PARA CADASTRO DO areas

                    <form id="cad_area">
                        <div class="mb-3">
                            <label for="areaInput" class="form-label">Nome área</label>
                            <input type="text" class="form-control" id="n_area" aria-describedby="area_help">
                            <input type="hidden" class="form-control" id="data-tipo" value="0">
                            <input type="hidden" value="area" id="d-area">
                        </div>

                        <button class="btn-salvar-area" id="btn-salvar-area">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                <polyline points="17 21 17 13 7 13 7 21" />
                                <polyline points="7 3 7 8 15 8" />
                            </svg>
                            Salvar tratativa
                        </button>
                    </form>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="flush-headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                        Registrar Solicitação
                    </button>
                </h2>
                <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">FLUXO PARA APRESENTACAO DE FORMULADOR PARA CADASTRO DOS JOBS
                        <form id="cad_job">
                            <div class="mb-3 form-check">
                                <label class="form-check-label" for="exampleCheck1">Selecione Solicitante</label>
                                <select class="form-control-d" id="d-tipo-user-area-solicitante"></select>

                            </div>
                            <span>ao selecionaar o solicitante já tem o vinculo solicite a área</span>
                            <div class="mb-3">
                                <!-- <label for="areaInput" class="form-label">Selecione Cliente</label> -->
                                <label for="cliente">Selecione Cliente</label>

                                <input type="text" id="n_cliente" list="n_cliente" class="form-control" autocomplete="off">

                                <datalist id="n_cliente"></datalist>

                            </div>
                            <label>
                                <input type="checkbox" id="myCheckbox">
                                Cliente Novo ou Teste
                            </label>
                            <input type="text" id="clientes_inputs" placeholder="Type here...">
                            <div class="mb-3 form-check">
                                <label class="form-check-label" for="exampleCheck1">Selecione Tipo Job:</label>
                                <select class="form-control-d" id="d-tipo-job"></select>

                            </div>

                            <div class="mb-3 form-check">
                                <label class="form-check-label" for="exampleCheck1">Selecione Tipo Status:</label>
                                <select class="form-control-d" id="d-tipo-job-status"></select>
                            </div>

                            <div class="mb-3">
                                <!-- <label for="areaInput" class="form-label">Selecione Cliente</label> -->
                                <label for="cliente">Selecione Perfil do Job:</label>

                                <input type="text" id="n_perfil" list="n_perfil" class="form-control" autocomplete="off">

                                <datalist id="n_perfil"></datalist>

                            </div>
                            <div class="mb-3">
                                <div class="fg">
                                    <label>Data Solicitação</label>
                                    <input type="date" id="range">
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="fg">
                                    <label>Informe o Titulo do e-mail</label>
                                    <input type="text" id="titulo_email" placeholder="Type here...">
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="fg">
                                    <label>Detalahamento do e-mail</label>


                                    <textarea id="detalhamento_email" name="story" rows="5" cols="33">
                                        It was a dark and stormy night...</textarea>
                                </div>
                            </div>

                            <button class="btn-salvar-area" id="btn-salvar-area">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                    <polyline points="17 21 17 13 7 13 7 21" />
                                    <polyline points="7 3 7 8 15 8" />
                                </svg>
                                Salvar Solicitação
                            </button>
                            <input type="hidden" class="form-control" id="data-tipo-cadastro" value="0">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- accordion accordion-flush  finish-->


        <div class="filterbar">
            <!-- <div class="fg">
            <label>Data inicial</label>
            <input type="date" id="range">
        </div>
        <div class="fg">
            <label>Data final</label>
            <input type="date" id="range_ate">
        </div> -->
            <div class="fg">
                <label>Status</label>
                <select id="f-status">
                    <option value="">Todos</option>
                    <option value="1">Pendente</option>
                    <option value="2">Em andamento</option>
                    <option value="3">Sem retorno</option>
                    <option value="4">Resolvido</option>
                    <option value="5">Cancelado</option>
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
            <button class="btn-buscar" id="btn-limpar">
                Limpar filtros
            </button>
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
                    <table id="table-relatorio-indicadores">
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
                                <th>Título</th>
                                <th>Cliente</th>
                                <th>Solicitante</th>
                                <th>Área</th>
                                <th>Executor</th>
                                <th>Perfil</th>
                                <th>Status</th>
                                <th>Data Solicitação</th>
                                <th>Data Início</th>
                                <th>Data Fim</th>
                                <th>Detalhamento</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="corpo-tabela-indicadores">
                            <tr class="loading-row">
                                <td colspan="13"><span class="spinner"></span>Carregando dados...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex flex-wrap align-items-center justify-content-right gap-3 mb-2 border-bottom pb-2" style="font-size: 11px; color: var(--muted);">
                    <div id="contagem" class="m-0">Carregando contagem...</div>

                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div>
                            <span>Total de Parcelas:</span>
                            <strong class="text-dark" id="resumo-qtd-parcelas">0</strong>
                        </div>
                        <div class="border-start ps-3">
                            <span>Somatória Total:</span>
                            <strong class="text-dark valor-mono" id="resumo-valor-total">R$ 0,00</strong>
                        </div>
                        <div class="border-start ps-3">
                            <span>Total Suspensos (Ativos):</span>
                            <strong class="text-danger valor-mono" id="resumo-suspenso-sim">R$ 0,00</strong>
                        </div>
                        <div class="border-start ps-3">
                            <span>Total Não Suspensos (Inativos):</span>
                            <strong class="text-success valor-mono" id="resumo-suspenso-nao">R$ 0,00</strong>
                        </div>
                    </div>
                </div>
                <div class="paginacao" id="paginacao">
                    <span id="pag-info">—</span>
                    <div class="pg-btns" id="pg-btns"></div>
                </div>
            </div>


            <div class="tab-pane fade" id="nav-profile-tab" role="tabpanel" aria-labelledby="nav-profile-tab">
                <p>SEU E-MAIL</p>

                <div class="pg-dash-wrapper">
                    <div class="pg-year-metrics-card">
                        <div class="pg-table-section">

                            <form class="col-sm-12">
                                <span>MEUS DADOS</span>

                                <!-- <table id="listagem_arquivos" class="text-center text-center tableJobs"
                                style=" font-size:10px; width:100%; border-radius:0;">
                                <thead>
                                    <tr class="list-jobs">
                                        <th>id</th>
                                        <th>Nome Arquivo</th>
                                        <th>Data Arquivo</th>
                                    </tr>
                                </thead>
                            </table> -->
                            </form>

                        </div><!-- /pg-table-section -->
                    </div>
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
                                <option value="1">Pendente</option>
                                <option value="2">Em andamento</option>
                                <option value="3">Resolvido</option>
                                <option value="4">Sem retorno</option>
                                <option value="5">Cancelado</option>
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



        <script type="text/javascript" src="../../Scripts/jquery/jquery.min.js"></script>

        <!-- SELECT BIBLIOTECA -->
        <script type="text/javascript" src="../../Scripts/select/select2.min.js"></script>
        <!-- <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> -->

        <script src="../../../Scripts/jobsIndicadores.js?v=<?= time(); ?>"></script>


        <!-- Bootstrap -->
        <script type="text/javascript" src="../../Scripts/propper/popper.min.js"></script>
        <!-- <script type="text/javascript" src="../Scripts/bootstrap/bootstrap.bundle.min.js"></script> -->
        <script type="text/javascript" src="../../Scripts/bootstrap/bootstrap.min.js"></script>

        <!-- DataTables principal -->
        <script type="text/javascript" src="../../Scripts/datatable/dataTables.js"></script>

        <!-- Extensão Buttons -->
        <script type="text/javascript" src="../../Scripts/datatable/dataTablesbuttons.js"></script>

        <!-- Dependências de exportação -->
        <script type="text/javascript" src="../../Scripts/jszip/jszip.min.js"></script>
        <!-- <script type="text/javascript" src="../Scripts/pdfMake/pdfmake.min.js"></script> -->
        <script type="text/javascript" src="../../Scripts/pdfMake/vfs_fonts.js"></script>

        <!-- Botões HTML5 -->
        <script type="text/javascript" src="../../Scripts/datatable/buttonshtml5.min.js"></script>

        <!-- Flatpickr -->
        <script type="text/javascript" src="../../Scripts/flatpickr/flatpickr.js"></script>
        <script type="text/javascript" src="../../Scripts/flatpickr/languageFlatPickr.js"></script>
        <!-- sweetAlert -->
        <script type="text/javascript" src="../../Scripts/sweetAlert/sweetAlert.js"></script>
        <script type="text/javascript" src="../../Scripts/flatpickr/flatpickr.js"></script>
        <script type="text/javascript" src="../../Scripts/flatpickr/languageFlatPickr.js"></script>

</body>

</html>