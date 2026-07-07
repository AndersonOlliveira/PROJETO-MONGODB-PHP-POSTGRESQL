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
    <link href="../../css/bootstrap/docs.css" rel="stylesheet">
    <link href="../../css/flatpickr/flatpickr.min.css" rel="stylesheet">
    <link href="../../css/select/select2.min.css" rel="stylesheet">
    <link href="../../css/Relatorio/viewindicadores.css?v= <?= time(); ?>" rel="stylesheet">
</head>

<body>

    <?php $tctrid = isset($_GET['tctrid']) ? $_GET['tctrid'] : 417039; ?>
    <?php $tctraut = isset($_GET['tctraut']) ? $_GET['tctraut'] : 'a14beaccd7f530ea7e7c8847d35cd0af'; ?>
    <!-- ID VINDO DO BACKEND -->
    <input type="hidden" id="d-id" value="<?= $tctrid ?>">
    <input type="hidden" id="d-tctraut" value="<?= $tctraut ?>">

    <!-- topbar -->
    <div class="topbar ttitpg">
        <div class="topbar-brand">
            <!-- <div class="dot"></div> -->
            <td valign="top" class="ttitpg" width="40%">

                <div class="lkpr">
                    <font class="ctitpg">Relatório de Kpis indicadores </font>

                    <small>Indicadores</small>
                </div>
            </td>
        </div>


        <div class="topbar-actions">

            <a class="btn-top primary tacn"
                href="/srv/srvcns.chp?tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>"
                title="Serviços">
                <div class="lkpr">Menu de serviços</div>

            </a>

            <a class="btn-top danger tacn"
                href="/srv/srvsai.chp?tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>"
                title="Sair do sistema">
                <div class="lkpr">Sair</div>
            </a>
        </div>
    </div>
    <!-- PARA EXIBIR OS BOTOES -->
    <div class="container-fluid">
        <div class="grid text-center">
            <!-- <div class="g-col-6 g-col-md-4 thlp"> -->
            <button class="g-col-6 g-col-md-4 thlp" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCadastrarUser" aria-expanded="false" aria-controls="collapseCadastrarUser">
                <span class="lkpr">Cadastar Usuários</span>
            </button>
            <!-- </div> -->
            <!-- <div class="g-col-6 g-col-md-4 thlp"> -->
            <button class="g-col-6 g-col-md-4 thlp" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCadastrarStatus" aria-expanded="false" aria-controls="collapseCadastrarStatus">
                <span class="lkpr">Cadastrar Status </span>
            </button>
            <!-- </div> -->
            <!-- <div class="g-col-6 g-col-md-4 thlp"> -->
            <button class="g-col-6 g-col-md-4 thlp" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCadastrarAreas" aria-expanded="false" aria-controls="collapseCadastrarAreas">
                <span class="lkpr">Cadastrar Área </span>
            </button>
            <!-- </div> -->

            <!-- <div class="g-col-6 g-col-md-4 thlp"> -->
            <button class="g-col-6 g-col-md-4 thlp" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCadastrarPerfil" aria-expanded="false" aria-controls="collapseCadastrarPerfil">
                <span class="lkpr">Cadastrar Pérfil </span>
            </button>
            <!-- </div> -->

            <!-- <div class="g-col-6 g-col-md-4 thlp"> -->
            <button class="g-col-6 g-col-md-4 thlp" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCadastrarSolicitacao" aria-expanded="false" aria-controls="collapseCadastrarSolicitacao">
                <span class="lkpr">Cadastrar Solicitação </span>
            </button>
            <!-- </div> -->
        </div>
    </div><!-- container -->

    <!-- teste para abir o coollpaces -->

    <div class="container-fluid">
        <div id="acordionFecharAll">
            <div class="collapse" id="collapseCadastrarUser" data-bs-parent="#acordionFecharAll">
                <div class="card card-body">
                    <form id="cad_usuario">
                        <div class="mb-3 form-check">
                            <label for="n_usuario" class="form-label">Nome:</label>
                            <input type="text" class="form-control" id="n_usuario" aria-describedby="name_help">
                            <div id="name_help" class="form-text"></div>
                        </div>
                        <div class="mb-3 form-check">
                            <label class="form-check-label" for="d-tipo-area">Selecione Área</label>
                            <select class="form-control" id="d-tipo-area"></select>
                        </div>
                        <div class="mb-3 form-check">
                            <button type="submit" class="btn btn-primary bot btn-salvar-area" id="btn-salvar-usuarios">
                                Salvar Usuário
                            </button>
                            <input type="hidden" value="usuarios" id="d-usuario">
                        </div>
                    </form>
                </div>
            </div>

            <div class="collapse" id="collapseCadastrarStatus" data-bs-parent="#acordionFecharAll">
                <div class="card card-body">
                    <form id="cad_status">
                        <div class="mb-3 form-check">
                            <label for="n_status" class="form-label">Nome Status:</label>
                            <input type="text" class="form-control" id="n_status" aria-describedby="status_help">
                            <input type="hidden" class="form-control" id="status-tipo" value="2">
                            <input type="hidden" value="status" id="d-status">
                        </div>
                        <div class="mb-3 form-check">
                            <button type="submit" class="btn btn-primary bot btn-salvar-area" id="btn-salvar-area">
                                Salvar Status
                            </button>
                        </div>
                        <input type="hidden" value="usuarios" id="d-usuarios-area">
                    </form>
                </div>
            </div>
            <div class="collapse" id="collapseCadastrarAreas" data-bs-parent="#acordionFecharAll">
                <div class="card card-body">
                    <form id="cad_area">
                        <div class="mb-3 form-chec">
                            <label for="n_area" class="form-label">Nome área</label>
                            <input type="text" class="form-control" id="n_area" aria-describedby="area_help">
                            <input type="hidden" class="form-control" id="data-tipo" value="0">
                            <input type="hidden" value="area" id="d-area">
                        </div>
                        <div class="form-check">
                            <button type="submit" class="btn btn-primary bot btn-salvar-area" id="btn-salvar-area">
                                Salvar Área
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="collapse" id="collapseCadastrarPerfil" data-bs-parent="#acordionFecharAll">
                <div class="card card-body">
                    <form id="cad_perfil">
                        <div class="mb-3 form-chec">
                            <label for="n_perfil" class="form-label">Nome Perfil</label>
                            <input type="text" class="form-control" id="n_perfils" aria-describedby="perfil_help">
                            <input type="hidden" class="form-control" id="data-perfil" value="3">
                            <input type="hidden" value="perfil" id="d-perfil">
                        </div>
                        <div class="form-check">
                            <button type="submit" class="btn btn-primary bot btn-salvar-area" id="btn-salvar-area">
                                Salvar Perfil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="collapse" id="collapseCadastrarSolicitacao" data-bs-parent="#acordionFecharAll">
                <div class="card card-body">
                    <form id="cad_job">
                        <div class="mb-3 form-check">
                            <label class="form-check-label" for="d-tipo-user-area-solicitante">Selecione Solicitante</label>
                            <select class="form-control" id="d-tipo-user-area-solicitante"></select>
                            <span>ao selecionar o solicitante já tem o vinculo solicite a área</span>
                        </div>

                        <div class="mb-3 form-check">
                            <label class="form-check-label" for="d-tipo-job">Selecione Tipo Job:</label>
                            <select class="form-control" id="d-tipo-job"></select>
                            <input type="hidden" value="" id="cliente_padrao">
                            <span>ao selecionar interno por padrão fica cliente Proscore</span>
                        </div>

                        <div class="mb-3">
                            <label for="n_cliente" class="form-label">Selecione Cliente</label>
                            <select id="n_cliente" class="form-control" style="width: 100%;">
                                <option></option>
                            </select>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="myCheckbox" value="novo">
                            <label class="form-check-label" for="myCheckbox">Cliente Novo</label>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="myCheckboxProspect" value="prospect">
                            <label class="form-check-label" for="myCheckboxProspect">Cliente Prospect</label>


                            <div class="mb-3" id="container_prospect" style="display: none;">
                                <select id="n_clientes_inputs_prospect" class="form-control" style="width: 100%;">
                                    <option></option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="text" class="form-control" id="clientes_inputs" placeholder="Informe o nome do cliente...">
                        </div>
                        <div class="mb-3 form-check">
                            <label class="form-check-label" for="d-tipo-job-status">Selecione Tipo Status:</label>
                            <select class="form-control" id="d-tipo-job-status"></select>
                        </div>

                        <div class="mb-3 form-check">
                            <label for="n_perfil">Selecione Perfil do Job:</label>
                            <input type="text" id="n_perfil" list="dl_perfil" class="form-control" autocomplete="off">
                            <datalist id="dl_perfil"></datalist>
                        </div>

                        <div class="col col-lg-2 form-check">
                            <label for="range" class="form-label">Data Solicitação</label>
                            <input type="date" class="form-control" id="range">
                        </div>

                        <div class="mb-3 form-check">
                            <label for="titulo_email" class="form-label">Informe o Titulo do e-mail</label>
                            <input type="text" class="form-control" id="titulo_email" placeholder="Informe o titulo do e-mail...">
                        </div>

                        <div class="mb-3 form-check">
                            <label for="detalhamento_email" class="form-label">Detalhamento do e-mail</label>
                            <textarea id="detalhamento_email" class="form-control" name="story" rows="5" placeholder="Informe o detalhamento.."></textarea>
                        </div>
                        <div class="mb-3 form-check teste">
                            <button type="submit" class="btn btn-primary bot btn-salvar-area" id="btn-salvar-job">
                                Salvar Solicitação
                            </button>
                        </div>
                        <input type="hidden" class="form-control" id="data-tipo-cadastro" value="0">
                    </form>
                </div>
            </div>
        </div> <!-- acordionFecharAll -->
    </div><!-- container -->

    <!-- <div class="container-fluid">
        <div class="d-flex flex-row flex-wrap gap-3">

            <div class="accordion accordion-flush" id="accordionFlushflow"> -->

    <!-- CADASTRAR USUÁRIOS -->
    <!-- <div class="accordion-item tacn">
                    <div class="accordion-header tacn" id="flush-headinExecutor">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseExecutor" aria-expanded="false" aria-controls="flush-collapseExecutor">
                            <div class="lkpr"> Cadastrar Usuário</div>
                        </button>
                    </div>
                    <div id="flush-collapseExecutor" class="accordion-collapse collapse" aria-labelledby="flush-headinExecutor" data-bs-parent="#accordionFlushflow">
                        <div class="accordion-body">
                            <form id="cad_usuario">
                                <div class="mb-3 form-check">
                                    <label for="n_usuario" class="form-label">Nome:</label>
                                    <input type="text" class="form-control" id="n_usuario" aria-describedby="name_help">
                                    <div id="name_help" class="form-text"></div>
                                </div>
                                <div class="mb-3 form-check">
                                    <label class="form-check-label" for="d-tipo-area">Selecione Área</label>
                                    <select class="form-control" id="d-tipo-area"></select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-salvar-area" id="btn-salvar-usuarios">
                                    <svg xmlns="http://w3.org" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                        <polyline points="17 21 17 13 7 13 7 21" />
                                        <polyline points="7 3 7 8 15 8" />
                                    </svg>
                                    Salvar Usuário
                                </button>
                                <input type="hidden" value="usuarios" id="d-usuario">
                            </form>
                        </div>
                    </div>
                </div> -->
    <!-- 
                <div class="accordion-item tacn">
                    <div class="accordion-header tacn" id="flush-headingStatus">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseStatus" aria-expanded="false" aria-controls="flush-collapseStatus">
                            <div class="lkpr">Cadastrar Status </div>
                        </button>
                    </div>
                
                    <div id="flush-collapseStatus" class="accordion-collapse collapse" aria-labelledby="flush-headingStatus" data-bs-parent="#accordionFlushflow">

                        <div class="accordion-body">

                            <form id="cad_status">
                                <div class="mb-3">
                                    <label for="n_status" class="form-label">Nome Status</label>
                                    <input type="text" class="form-control" id="n_status" aria-describedby="status_help">
                                    <input type="hidden" class="form-control" id="status-tipo" value="2">
                                    <input type="hidden" value="status" id="d-status">
                                </div>
                                <button type="submit" class="btn btn-primary btn-salvar-area" id="btn-salvar-area">
                                    <svg xmlns="http://w3.org" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                        <polyline points="17 21 17 13 7 13 7 21" />
                                        <polyline points="7 3 7 8 15 8" />
                                    </svg>
                                    Salvar Status
                                </button>
                            </form>
                        </div>
                    </div>
                </div> -->


    <!-- CADASTRAR ÁREA -->
    <!-- <div class="accordion-item tacn">
                    <div class="accordion-header tacn" id="flush-headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                            <div class="lkpr"> Cadastrar Área</div>
                        </button>
                    </div>



                    <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushflow">
                        <div class="accordion-body">

                            <form id="cad_area">
                                <div class="mb-3">
                                    <label for="n_area" class="form-label">Nome área</label>
                                    <input type="text" class="form-control" id="n_area" aria-describedby="area_help">
                                    <input type="hidden" class="form-control" id="data-tipo" value="0">
                                    <input type="hidden" value="area" id="d-area">
                                </div>
                                <button type="submit" class="btn btn-primary btn-salvar-area" id="btn-salvar-area">
                                    <svg xmlns="http://w3.org" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                        <polyline points="17 21 17 13 7 13 7 21" />
                                        <polyline points="7 3 7 8 15 8" />
                                    </svg>
                                    Salvar Área
                                </button>
                            </form>
                        </div>
                    </div>
                </div> -->

    <!-- <div class="accordion-item tacn">
                    <div class="accordion-header tacn" id="flush-headingPerfil">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapsePerfil" aria-expanded="false" aria-controls="flush-collapsePerfil">
                            <div class="lkpr"> Cadastrar Perfil </div>
                        </button>
                    </div>


                    CADASTARA PERFIL
                    <div id="flush-collapsePerfil" class="accordion-collapse collapse" aria-labelledby="flush-headingPerfil" data-bs-parent="#accordionFlushflow">

                        <div class="accordion-body">

                            <form id="cad_perfil">
                                <div class="mb-3">
                                    <label for="n_perfil" class="form-label">Nome Perfil</label>
                                    <input type="text" class="form-control" id="n_perfils" aria-describedby="perfil_help">
                                    <input type="hidden" class="form-control" id="data-perfil" value="3">
                                    <input type="hidden" value="perfil" id="d-perfil">
                                </div>
                                <button type="submit" class="btn btn-primary btn-salvar-area" id="btn-salvar-area">
                                    <svg xmlns="http://w3.org" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                        <polyline points="17 21 17 13 7 13 7 21" />
                                        <polyline points="7 3 7 8 15 8" />
                                    </svg>
                                    Salvar Perfil
                                </button>
                            </form>
                        </div>
                    </div>
                </div> -->


    <!--REGISTRAR SOLICITAÇÃO -->
    <!-- <div class="accordion-item tacn">
                    <div class="accordion-header tacn" id="flush-headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                            <div class="lkpr"> Registrar Solicitação</div>
                        </button>
                    </div>



                    <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushflow">
                        <div class="accordion-body">

                            <form id="cad_job">
                                <div class="mb-3">
                                    <label class="form-check-label" for="d-tipo-user-area-solicitante">Selecione Solicitante</label>
                                    <select class="form-control" id="d-tipo-user-area-solicitante"></select>
                                    <span>ao selecionar o solicitante já tem o vinculo solicite a área</span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-check-label" for="d-tipo-job">Selecione Tipo Job:</label>
                                    <select class="form-control" id="d-tipo-job"></select>
                                    <input type="hidden" value="" id="cliente_padrao">
                                    <span>ao selecionar interno por padrão fica clinte Proscore</span>
                                </div>

                                <div class="mb-3">
                                    <label for="n_cliente">Selecione Cliente</label>
                                    <input type="text" id="n_cliente" list="dl_cliente" class="form-control" autocomplete="off">
                                    <datalist id="dl_cliente"></datalist>
                                </div>

                                <div class="mb-3">
                                    <input type="checkbox" class="form-check-input" id="myCheckbox" value="novo">
                                    <label class="form-check-label" for="myCheckbox">Cliente Novo</label>
                                </div>

                                <div class="mb-3">
                                    <input type="checkbox" class="form-check-input" id="myCheckboxProspect" value="prospect">
                                    <label class="form-check-label" for="myCheckboxProspect">Cliente Prospect</label>


                                    <div class="mb-3" id="container_prospect" style="display: none;">
                                        <select id="n_clientes_inputs_prospect" class="form-control" style="width: 100%;">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <input type="text" class="form-control" id="clientes_inputs" placeholder="Informe o nome do cliente...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-check-label" for="d-tipo-job-status">Selecione Tipo Status:</label>
                                    <select class="form-control" id="d-tipo-job-status"></select>
                                </div>

                                <div class="mb-3">
                                    <label for="n_perfil">Selecione Perfil do Job:</label>
                                    <input type="text" id="n_perfil" list="dl_perfil" class="form-control" autocomplete="off">
                                    <datalist id="dl_perfil"></datalist>
                                </div>

                                <div class="col col-lg-2">
                                    <label for="range" class="form-label">Data Solicitação</label>
                                    <input type="date" class="form-control" id="range">
                                </div>

                                <div class="mb-3">
                                    <label for="titulo_email" class="form-label">Informe o Titulo do e-mail</label>
                                    <input type="text" class="form-control" id="titulo_email" placeholder="Informe o titulo do e-mail...">
                                </div>

                                <div class="mb-3">
                                    <label for="detalhamento_email" class="form-label">Detalhamento do e-mail</label>
                                    <textarea id="detalhamento_email" class="form-control" name="story" rows="5" placeholder="Informe o detalhamento.."></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary btn-salvar-area" id="btn-salvar-job">
                                    <svg xmlns="http://w3.org" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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
 -->




    <!-- </div> accordion accordion-flush  finish -->
    <!-- </div>container-fluid finish -->
    <!-- </div>container-fluid finish -->

    <div class="container-fluid">
        <div class="filterbar">
            <div class="fg thlp">
                <!-- <span class="lkpr"></span> -->
                <span class="lkpr">Status</span>

                <select id="f-status" class="form-control selectControll pda"> </select>
            </div>
            <div class="fg thlp">
                <span class="lkpr">Executante</span>
                <select id="f-executante" class="form-control selectControllExecutante pda"> </select>
            </div>
            <div class="fg thlp">
                <span class="lkpr">Área</span>
                <select id="f-area" class="form-control selectControllArea pda"> </select>
            </div>
        </div>

        <!-- Tabela envolvida pela div de scroll -->
        <div class="table-wrap" id="table-wrap">
            <div class="contagem" id="contagem"></div>

            <table id="table-relatorio-indicadores">

                <thead>
                    <tr>
                        <th>id</th>
                        <th>Título</th>
                        <th>Cliente</th>
                        <th>Solicitante</th>
                        <th>Área</th>
                        <th>Executor</th>
                        <th>Área Executor</th>
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
                        <td colspan="14"><span class="spinner"></span>Carregando dados...</td>
                    </tr>
                </tbody>
            </table>
        </div> <!-- Fim da table-wrap -->
    </div>





    <!-- ============================================================
                             MODAL PARA APRESENTAÇÃO DOS DADOS DO JOB
                        ============================================================ -->
    <div class="modal fade" id="modalDados" tabindex="-1" aria-labelledby="exampleModalDados" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalDados">Lista de dados</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="pg-dash-wrapper">
                    <div class="pg-year-metrics-card">
                        <div class="pg-table-section">
                            <div id="teste" class="row">
                                <div class="d-flex">

                                    <!---->
                                    <div id="acordionPai" class="d-flex w-100">

                                        <!-- Menu Lateral Principal -->
                                        <div class="sidebar bg-dark text-white p-3 thlp" style="width: 250px; min-height: 100vh;">

                                            <!-- Botão 1 -->
                                            <div class="menu-item position-relative mb-2">
                                                <a class="custom-file-label w-100 mb-2" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                                                    Observações
                                                </a>
                                            </div>

                                            <!-- Botão 2 -->
                                            <div class="menu-item position-relative mb-2">
                                                <a class="custom-file-label w-100 mb-2 btn-listar-historico-obs" data-bs-toggle="collapse" href="#collapseObservacoes" role="button" aria-expanded="false" aria-controls="collapseObservacoes">
                                                    Listar Observações
                                                </a>
                                            </div>

                                            <!-- Botão 3 -->
                                            <div class="menu-item position-relative mb-2">
                                                <a class="custom-file-label w-100 mb-2 btn-listar-historico" data-bs-toggle="collapse" href="#collapseExampleAtualizacoes" role="button" aria-expanded="false" aria-controls="collapseExampleAtualizacoes">
                                                    Listar atualizações
                                                </a>
                                                <input type="hidden" id="d-id-tabela-historico" value="">
                                            </div>

                                        </div> <!-- /sidebar -->

                                        <!-- Área de Conteúdo Principal -->
                                        <div class="content flex-grow-1 p-3">
                                            <div id="apresentar_msg">
                                                <div style="text-align:center;padding:40px;color:var(--muted);font-size:12px">
                                                    Selecione Alguma opção para ver os dados!
                                                </div>
                                            </div>

                                            <!-- Painel 1 -->
                                            <div class="collapse" id="collapseExample" data-bs-parent="#acordionPai">
                                                <div class="card card-body">
                                                    <form id="obs_job">
                                                        <div class="mb-3 form-check">
                                                            <textarea id="info_job" class="form-control tacn" name="story" rows="20" cols="100" placeholder="Informe algo importantes sobre o o job ...."></textarea>
                                                            <input type="hidden" value="obs" id="d-obs">
                                                            <input type="hidden" value="" id="d-id-tabela">
                                                        </div>
                                                        <div class="form-check">
                                                            <button class="btn btn-primary bot btn-salvar-obs" id="btn-salvar-usuarios">
                                                                Salvar Observação </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Painel 2: Listar Observações  -->
                                            <div class="collapse" id="collapseObservacoes" data-bs-parent="#acordionPai">
                                                <div id="historico-lista-apresentar-observacoes" class="card card-body">
                                                    Conteúdo da lista de observações aqui...
                                                </div>
                                            </div>

                                            <!-- Painel 3 -->
                                            <div class="collapse" id="collapseExampleAtualizacoes" data-bs-parent="#acordionPai">
                                                <div id="historico-lista-apresentar" class="card card-body">
                                                    Conteúdo do histórico de atualizações...
                                                </div>
                                            </div>

                                        </div> <!-- /content -->

                                    </div> <!-- /ARCORDION-PAi -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bot" data-bs-dismiss="modal">Fechar</button>
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