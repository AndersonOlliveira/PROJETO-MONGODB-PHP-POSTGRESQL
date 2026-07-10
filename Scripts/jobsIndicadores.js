// ── ESTADO GLOBAL ─────────────────────────────────────────────
const App = {
    dados: [], // todos os registros da API
    dadosObs: [], // todos os registros da API
    data_global_contratros: [],
    dadosFiltrados: [], // subconjunto após filtros
    dadosFiltradosHistorico: [], // subconjunto após filtros historico
    dadosFiltradosHistoricoObs: [], // subconjunto após filtros OBS
    paginaAtual: 1,
    porPagina: 50,
    linhaSelecionada: null,
    drawerAberto: false,
    abaAtiva: 'tratativa',
    historicos: {}, // cache por n_nro
    crt: '',
    status: true,
    clientesSelecionado: '',
    clientesSelecionadoPropesct: '',
    perfilSelecionado: '',
    clienteTeste: 'novo',
    clienteProscore: 'PROSCORE',
    tipo: 9,
    tipoJobs: 1,
    filtrosExecutane: [],
    filtrosAreas: []
};

// ── INICIALIZAÇÃO ─────────────────────────────────────────────
$(document).ready(function () {
    // PEGO O LOGIN E APROVEITO DE FORMA GLOBAL DENTRO DO CODIGO
    App.crt = $('#d-id').val();

    listArchivesJobs();

    //CHAMA FUNCAO PARA LISTA AS AREAS DISPONIVEIS
    get_lista_area();
    //PENSAR EM CHAMAR NA HORA QUE TIVER O CLICK DENTRO DO CADASTRO
    //CHAMA FUNCAO PARA LISTA AS AREAS DISPONIVEIS
    get_lista_user_area();

    //CHAMA FUNCAO PARA LISTA AS CLIENTES DISPONIVEIS
    get_lista_cliente();

    //CHAMA FUNCAO PARA LISTA DE TIPOS DISPONIVEIS
    get_lista_tipo();

    //CHAMA FUNCAO PARA LISTA DE STATUS DISPONIVEIS
    get_lista_status();

    //CHAMA FUNCAO PARA LISTA DE PERFIL P/JOB DISPONIVEIS
    get_lista_perfil();
    //   buscar_Info_Responsavel_Logado();

    $('#btn-buscar').on('click', aplicarFiltros);
    $('#f-busca').on('keypress', e => {
        if (e.which === 13) aplicarFiltros();
    });
    $('#btn-fechar-drawer').on('click', fecharDrawer);
    $(document).on('click', '.drawer-tab', function () {
        mudarAba($(this).data('tab'));
    });

    //PADRAO O INPUT FICA ESCONDIDO 
    $("#clientes_inputs").hide();

    //   $('#btn-salvar-tratativa').on('click', salvarTratativa);
    // $('#btn-csv').on('click', exportarCSV);

});

// ── ESTADO CHAMA TABELA ─────────────────────────────────────────────


// ── FETCH DE DADOS ────────────────────────────────────────────
// GET ?rota=index&acao=listRelatorio
// Retorna: { sucesso: true, dados: [...] }
function listArchivesJobs() {
    mostrarLoading();


    $.ajax({
        url: '/api/ListJobs',
        type: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        dataType: 'json',
        success: function (resp) {
            if (resp.sucesso && Array.isArray(resp.dados)) {
                App.dados = resp.dados;
                App.dadosFiltrados = [...resp.dados];


                App.filtrosAreas = App.dadosFiltrados.map(item => ({
                    ...item,
                    solicitante: item.dados_solicitante?.n_nome_user ?? '-',
                    area_solicitante: item.dados_solicitante?.n_area ?? '-',
                    area_executor: item.dados_executor?.n_area ?? '-',
                    n_executor: item.dados_executor?.n_nome_user ?? '-',

                }));



                App.filtrosExecutane = App.dadosFiltrados.map(item => ({
                    ...item,
                    solicitante: item.dados_solicitante?.n_nome_user ?? '-',
                    area_solicitante: item.dados_solicitante?.n_area ?? '-',
                    area_executor: item.dados_executor?.n_area ?? '-',
                    n_executor: item.dados_executor?.n_nome_user ?? '-',

                }));

                App.dadosFiltrados = App.dadosFiltrados.map(item => ({
                    ...item,
                    solicitante: item.dados_solicitante?.n_nome_user ?? '-',
                    area_solicitante: item.dados_solicitante?.n_area ?? '-',
                    area_executor: item.dados_executor?.n_area ?? '-',
                    n_executor: item.dados_executor?.n_nome_user ?? '-',

                }));


                renderTabela();
            } else {
                // clearFiltro();
                //RESPSTA ESTA DENTRO DE DADOS
                mostrarErro(resp.dados.msg || 'Resposta inesperada do servidor.');
            }
        },
        error: function (xhr) {
            mostrarErro('Falha de conexão. (HTTP ' + xhr.status + ')');
        }
    });
}

function listArchivesJobsHistorico(id) {
    mostrarLoading();

    if (App.dadosFiltradosHistorico.length > 0) {
        App.dadosFiltradosHistorico = [];
    }

    $('#historico-lista-apresentar').html(`
        <div class="loading-row" style="text-align:center; padding:40px; color:var(--muted); font-size:14px;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="margin-right: 8px;"></span>
            Carregando dados...
        </div>
    `);

    $.ajax({
        url: '/api/ListJobsHis',
        data: {
            'tabela': id,
        },
        type: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        dataType: 'json',
        success: function (resp) {
            if (resp.sucesso && Array.isArray(resp.dados)) {
                App.dados = resp.dados;
                App.dadosFiltradosHistorico = [...resp.dados];

                App.dadosFiltradosHistorico = App.dadosFiltradosHistorico.map(item => ({
                    ...item,
                    solicitante: item.dados_solicitante?.n_nome_user ?? '-',
                    area_solicitante: item.dados_solicitante?.n_area ?? '-',
                    area_executor: item.dados_executor?.n_area ?? '-',
                    n_executor: item.dados_executor?.n_nome_user ?? '-',

                }));


                renderTabelaHistorico();
            } else {
                // clearFiltro();
                //RESPSTA ESTA DENTRO DE DADOS
                $('#apresentar_msg').empty();
                $('#historico-lista-apresentar').html(`
                <div class="loading-row" style="text-align:center; padding:40px; color:var(--muted); font-size:14px;">
                    <span style="margin-right: 8px;">${resp.dados}</span>
                    
                </div>
    `);

                mostrarErro(resp.dados.msg || 'Resposta inesperada do servidor.');
            }
        },
        error: function (xhr) {
            mostrarErro('Falha de conexão. (HTTP ' + xhr.status + ')');
        }
    });
}

function listArchivesJobsHistoricoObs(id) {
    mostrarLoading();

    if (App.dadosFiltradosHistoricoObs.length > 0) {
        App.dadosFiltradosHistoricoObs = [];
    }

    $('#historico-lista-apresentar-observacoes').html(`
        <div class="loading-row" style="text-align:center; padding:40px; color:var(--muted); font-size:14px;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="margin-right: 8px;"></span>
            Carregando dados...
        </div>`);

    $.ajax({
        url: '/api/ListJobsHisObs',
        data: {
            'tabela': id,
        },
        type: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        dataType: 'json',
        success: function (resp) {
            if (resp.sucesso && Array.isArray(resp.dados)) {
                App.dadosFiltradosHistoricoObs = [...resp.dados];
                renderTabelaHistoricoObs();

            } else {
                //RESPSTA ESTA DENTRO DE DADOS
                $('#apresentar_msg').empty();
                $('#historico-lista-apresentar-observacoes').html(`
                <div class="loading-row" style="text-align:center; padding:40px; color:var(--muted); font-size:14px;">
                    <span style="margin-right: 8px;">${resp.dados}</span></div>`);

                mostrarErro(resp.dados.msg || 'Resposta inesperada do servidor.');
            }
        },
        error: function (xhr) {
            mostrarErro('Falha de conexão. (HTTP ' + xhr.status + ')');
        }
    });
}

function mostrarLoading() {
    $('#corpo-tabela').html('<tr class="loading-row"><td colspan="13"><span class="spinner"></span>Carregando dados do servidor...</td></tr>');
    $('#contagem').text('');
}

function mostrarErro(msg) {
    $('#corpo-tabela').html(`<tr><td colspan="13"><div class="error-state"><p>Erro ao carregar os dados</p><small>${msg}</small><br><button class="btn-retry" onclick="list_dados()">Tentar novamente</button></div></td></tr>`);
    $('#contagem').text('');
}

console.log($('.dt-info').length);
console.log($('.dataTables_info').length);
// ── RENDERIZAÇÃO ──────────────────────────────────────────────
function renderTabela() {
    atualizarCardsResumoJobs();

    if ($.fn.DataTable.isDataTable('#table-relatorio-indicadores')) {
        $('#table-relatorio-indicadores').DataTable().clear().destroy();
    }

    tabela_indicadores = $('#table-relatorio-indicadores').DataTable({
        destroy: true,
        processing: true,
        // select: true,
        paging: true,
        pagingType: "full_numbers",
        scrollX: true,
        searching: true,
        ordering: true,
        responsive: false,

        // info: true,
        layout: {
            top: {
                search: {
                    placeholder: 'Digite para pesquisar...'
                }
            },
            topEnd: [{
                buttons: [
                    ['excel'],
                ],
            }],
        },
        language: {
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Nenhum registro encontrado",
            lengthMenu: "Mostrar _MENU_ registros",
            search: "Pesquisar:",
            searchPlaceholder: "Digite para pesquisar...",
            paginate: {
                first: "|<<",
                previous: "<",
                next: ">",
                last: ">>|"
            }
        },
        "order": [
            [0, "desc"]
        ],
        data: App.dadosFiltrados,


        columns: [

            {
                data: 'id_cadjob',
                defaultContent: '-'
            }, {
                data: 'titulo_email',
                defaultContent: '-',
                render: function (data, type, row) {
                    return quebrarTexto(data, type, row);

                }
            },
            {
                data: 'nome_cliente',
                defaultContent: '-',
                render: function (data, type, row) {
                    return quebrarTexto(data, type, row);

                }
            },
            {
                data: 'solicitante',
                defaultContent: '-',

            },
            {
                data: 'area_solicitante',
                defaultContent: '-'
            },
            {
                data: 'dados_executor.n_nome_user',
                orderable: false,
                searchable: true,
                render: function (data, type, row) {

                    const executorName = (row.dados_executor?.n_nome_user || 'Sem nome').trim();

                    if (type !== 'display')
                        return executorName;
                    return `
                    
                            <div class="dropdown dropdown-dinamico">
                                    <button class="btn btn-sm btn-light dropdown-toggle tacn tam" data-bs-toggle="dropdown" data-bs-flip="false">
                                        ${executorName}
                                    </button>
                                        <ul class="dropdown-menu p-2">
                                        <input type="hidden" value="${row.id_cadjob}" class="tabela-row">
                                        <li><p class="dropdown-header fw-bold text-dark">Selecione Executor</p></li>
                                        <li><a class="dropdown-item item-executor" href="#" data-id="0">Todos</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <div class="lista-executantes">
                                            <li class="text-muted small px-2">Carregando...</li>
                                        </div>
                                    </ul>
                                </div> `;
                }
            },


            {
                data: 'area_executor',
                defaultContent: '-'
            },
            {
                data: 'n_perfil',
                defaultContent: '-',
                render: function (data, type, row) {
                    return data.toUpperCase();
                }
            },
            {
                data: 'n_status',
                render: function (data, type, row) {

                    return renderOptionStatus(data, type, row);
                }
            },
            {
                data: 'data_solicitacao',
                defaultContent: '-',
                render: function (data, type, row) {
                    return renderData(data, type, row);
                }
            },
            {
                data: 'data_inicio',
                render: function (data, type, row) {
                    return renderDataIncio(data, type, row);
                }
            },
            {
                data: 'data_fim',
                render: function (data, type, row) {
                    return renderDataFim(data, type, row);
                }
            },
            {
                data: 'detalhamento',
                defaultContent: '-',
                render: function (data, type, row) {
                    return quebrarTexto(data, type, row);

                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `
                    <div class="d-flex flex-row mb-3">
                      <div class="p-2 pg-file-section">
                          <button type="button" class="btn-action btn-list-dados" data-id-tabela="${row.id_cadjob}" aria-label="Ver detalhes">
                              <img src="img/em-formacao.png" alt="Ver detalhes">
                          </button>
                      </div>
                    </div>`;
                },
            },
        ]
    });



    $('#f-status').on('change', function () {
        const filtroSelecionado = this.value.trim();

        if (filtroSelecionado && filtroSelecionado != 0) {

            tabela_indicadores
                .column(8)
                .search(filtroSelecionado)
                .draw();

        } else {

            tabela_indicadores
                .column(8)
                .search('')
                .draw();
        }
    });

    $('#f-executante').on('change', function () {
        const filtroExecutor = this.value;

        if (filtroExecutor && filtroExecutor != 0) {

            tabela_indicadores
                .column(5)
                .search(filtroExecutor)
                .draw();


        } else {

            tabela_indicadores
                .column(5)
                .search('')
                .draw();
        }
    });
    $('#f-area').on('change', function () {
        const filtroArea = this.value;
        if (filtroArea && filtroArea != 0) {
            tabela_indicadores
                .column(6)
                .search(filtroArea)
                .draw();


        } else {

            tabela_indicadores
                .column(6)
                .search('')
                .draw();
        }
    });
    $('#f-mes').on('change', function () {
        let filtroMes = this.value;

        if (filtroMes && filtroMes != 0) {
            filtroMes = filtroMes.padStart(2, '0');
            console.warn('filtro selecionado depois do padStart');
            console.warn(filtroMes);
            // const regexMes = `^\\s*\\d{2}[-\\/]${filtroMes}[-\\/]\\d{4}\\s*$`;
            const regexMes = `[-\\/]${filtroMes}[-\\/]`;

            console.warn('Regex gerada:', regexMes);

            tabela_indicadores
                .column(9)
                // .search(regexMes, true, false, true)
                .search(regexMes, true, false)
                .draw();
        } else {
            tabela_indicadores
                .column(9)
                .search('')
                .draw();
        }
    });


    const dadosFiltradosVisiveis = tabela_indicadores.rows({
        search: 'applied',
        page: 'current'
    }).data().toArray();

    console.log(App.filtrosAreas, 'DADOS PARA OS FILTROS');

    listaOption(dadosFiltradosVisiveis);
    listaOptionExecutante(App.filtrosExecutane);
    listaOptionArea(App.filtrosAreas);

    listaOptionMeses(App.dadosFiltrados);



    $('#contagem').html(
        `${App.dadosFiltrados.length} registro(s) encontrado(s)`
    );
}

function corteEstrito(texto, limite = 10) {

    return texto.match(new RegExp('.{1,' + limite + '}', 'g')).join('\n');
}

function quebrarTexto(texto, limite = 20) {
    if (!texto) return ''; // Evita erros se o campo vier vazio ou nulo

    const palavras = texto.split(' ');
    let linhaAtual = '';
    const resultado = [];

    palavras.forEach(palavra => {

        const espaco = linhaAtual === '' ? '' : ' ';
        if ((linhaAtual + espaco + palavra).length <= limite) {
            linhaAtual += espaco + palavra;
        } else {
            if (linhaAtual !== '') resultado.push(linhaAtual);
            linhaAtual = palavra;
        }
    });

    if (linhaAtual !== '') resultado.push(linhaAtual);


    return resultado.join('<br>');


}

function renderDetalhamento(data, type, row) {
    const retorno_texto = quebrarTexto(data, 20);
    return retorno_texto;
}


// APRESEMTAR O OPTION 

function listaOption(dadosFiltradosVisiveis) {

    const statusSelects = document.getElementById('f-status');

    statusSelects.innerHTML = 'Carregando..';
    const todosOption = document.createElement("option");
    todosOption.value = "0";
    todosOption.className = "form-control";
    todosOption.text = "Todos";
    statusSelects.appendChild(todosOption);

    //mapea para não repedir os dados que vem da consulta que já existe,pois fica inteirando
    const unicos = [...new Set(dadosFiltradosVisiveis.map(el => el.n_status))];

    unicos.forEach(element => {

        const options = document.createElement("option");
        options.className = 'form-control selectControll';

        options.value = removerAcentos(element).toUpperCase().trim();
        options.text = element.toUpperCase().trim();
        statusSelects.appendChild(options);

    });
}

function listaOptionExecutante(dadosFiltradosVisiveis) {

    const statusSelects = document.getElementById('f-executante');

    statusSelects.innerHTML = 'Carregando..';
    const todosOption = document.createElement("option");
    todosOption.value = "0";
    todosOption.className = "form-control";
    todosOption.text = "Todos";
    statusSelects.appendChild(todosOption);


    const dadosUnicos = [...new Map(dadosFiltradosVisiveis.map(el => [el.dados_executor.n_nome_user, el.dados_executor])).values()];

    dadosUnicos.forEach(element => {
        var dlistaN = element.n_nome_user ? element.n_nome_user : 'Sem nome';

        const options = document.createElement("option");
        options.value = dlistaN;
        options.text = dlistaN.toUpperCase();
        statusSelects.appendChild(options);
    });
}

function listaOptionArea(dadosFiltradosAreas) {

    const statusSelects = document.getElementById('f-area');

    statusSelects.innerHTML = 'Carregando...';
    const todosOption = document.createElement("option");
    todosOption.value = "0";
    todosOption.className = "form-control selectControllArea pda";
    todosOption.text = "Todos";
    statusSelects.appendChild(todosOption);

    const dadosUnicos = [...new Map(dadosFiltradosAreas.map(el => [el.dados_executor.n_area, el.dados_executor])).values()];

    dadosUnicos.forEach(element => {
        var dlistaN = element.n_area ? element.n_area : '-';

        const options = document.createElement("option");
        options.className = "area";
        options.value = dlistaN;
        options.text = dlistaN.toUpperCase();
        statusSelects.appendChild(options);
    });
}


function listaOptionMeses(dadosFiltradosAreas) {
    const statusSelects = document.getElementById('f-mes');

    statusSelects.innerHTML = 'Carregando...';
    const todosOption = document.createElement("option");
    todosOption.value = "0";
    todosOption.className = "form-control selectControllMes pda";
    todosOption.text = "Todos";
    statusSelects.appendChild(todosOption);

    const mesesUnicos = new Set();
    dadosFiltradosAreas.forEach(el => {
        if (el.data_solicitacao) {
            console.log(el.data_solicitacao);
            const mes = el.data_solicitacao.split("-")[1];
            mesesUnicos.add(mes);
        }
    });


    Array.from(mesesUnicos).sort().forEach(mes => {
        console.log(mes);
        const options = document.createElement("option");
        options.className = "mes";
        options.value = mes;
        options.text = lista_meses([mes]);
        statusSelects.appendChild(options);
    });
}

// ── RENDERIZAÇÃO  HISTORICO──────────────────────────────────────────────
function renderTabelaHistorico() {
    console.log('ACESSANDO A ROTA');


    const apresentar_lista = $('#historico-lista-apresentar');

    apresentar_lista.empty();
    $('#apresentar_msg').empty();


    if (!App.dadosFiltradosHistorico || App.dadosFiltradosHistorico.length === 0) {
        apresentar_lista.append(`<div style="text-align:center;padding:40px;color:var(--muted);font-size:12px">Nenhum histórico encontrado para esta parcela.</div>`);
        return apresentar_lista;
    }


    App.dadosFiltradosHistorico.forEach(function (item) {

        let newName_status = item.n_status;
        const statusLimpo = removerAcentos(newName_status).toUpperCase().trim();
        let classeBadge = switchStatus(statusLimpo);


        apresentar_lista.append(`
            <div class="hist-item" style="border-left: 4px solid var(--primary); padding: 10px; margin-bottom: 10px; background: #f8f9fa; border-radius: 4px;">
                <div class="hist-meta" style="display:flex; justify-content:space-between; align-items:center;">
                    <span class="hist-data" style="font-size:12px; font-weight:bold;">ID: ${item.cad_idjob} - Data: ${item.data_cad_hist}</span>
                    <span class="badge ${classeBadge}" style="font-size:10px; padding: 4px 8px;">${item.n_status ?? 'SEM STATUS'}</span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:5px; font-size:12px;">
                    <span class="hist-tipo"><b>Perfil:</b> ${item.n_perfil}</span>
                    <span class="hist-resp"><b>Executor:</b> ${item.dados_executor?.n_nome_user || 'Sem nome'}</span>
                </div>
                <div class="hist-desc" style="font-size:11px; color:#555; margin-top:5px;">
                    <b>Solicitante:</b> ${item.solicitante} (${item.area_solicitante})
                </div>
            </div>
        `);
    });

    return apresentar_lista;
}


function switchStatus(statusLimpo) {

    let classe = 'badge bg-secondary';
    switch (statusLimpo) {
        case 'NAO INICIADO':
            classe = 'badge bg-warning text-dark';
            break;

        case 'EM ANDAMENTO':
            classe = 'badge bg-primary';
            break;

        case 'EM REVISAO':
            classe = 'badge bg-info text-dark';
            break;

        case 'PAUSADO':
            classe = 'badge bg-warning text-dark';
            break;

        case 'IMPEDIMENTO':
            classe = 'badge bg-danger';
            break;

        case 'FINALIZADO':
            classe = 'badge bg-success';
            break;

        case 'CANCELADO':
            classe = 'badge bg-dark';
            break;

        default:
            classe = 'badge bg-secondary';
            break;
    }

    return classe;
}

function renderOptionStatus(data, type, row) {

    let classe = 'badge bg-secondary';

    const statusLimpo = removerAcentos(data).toUpperCase().trim();

    classe = switchStatus(statusLimpo);


    return `<div class="dropdown dropdown-dinamico-status">
                      <button class="btn btn-sm btn-light ${classe}  dropdown-toggle" data-bs-toggle="dropdown" data-bs-flip="false">
                         ${data ?? '-'}
                    </button>
                      <ul class="dropdown-menu p-2">
                            <input type="hidden" value="${row.id_cadjob}" class="tabela-row">
                            <li><h6 class="dropdown-header fw-bold text-dark px-2">Selecione Status</h6></li>
                            <li><a class="dropdown-item items-status" href="#" data-id="0">Todos</a></li>
                             <li><hr class="dropdown-divider"></li>
                            <div class="lista-status">
                                <li class="text-muted small px-2">Carregando...</li>
                            </div>
                        </ul>
                    </div>`;
}


// ── RENDERIZAÇÃO  HISTORICO OBSERVACOES──────────────────────────────────────────────
function renderTabelaHistoricoObs() {

    const apresentar_lista = $('#historico-lista-apresentar-observacoes');

    apresentar_lista.empty();
    $('#apresentar_msg_obs').empty();


    if (!App.dadosFiltradosHistoricoObs || App.dadosFiltradosHistoricoObs.length === 0) {
        apresentar_lista.append(`<div style="text-align:center;padding:40px;color:var(--muted);font-size:12px">Nenhum histórico encontrado para esta parcela.</div>`);
        return apresentar_lista;
    }


    App.dadosFiltradosHistoricoObs.forEach(function (item) {


        console.log(item.n_status);

        let classeBadge = switchStatus(item.n_status);


        console.log(classeBadge);

        apresentar_lista.append(`
            <div class="hist-item" style="border-left: 4px solid var(--primary); padding: 10px; margin-bottom: 10px; background: #f8f9fa; border-radius: 4px;">
                <div class="hist-meta" style="display:flex; justify-content:space-between; align-items:center;">
                    <span class="hist-data" style="font-size:12px; font-weight:bold;">ID: ${item.tabela} -</span>
                    <span class="badge ${classeBadge}" style="font-size:10px; padding: 4px 8px;">Data inserida ${item.data_cadastro}</span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:5px; font-size:12px;">
                    <span class="hist-tipo"><b>Obs Inserida:</b> ${item.obs}</span>
                  
                </div>
            </div>
        `);
    });

    return apresentar_lista;
}


function renderRowSolicitante(row, type, data) {
    $.each(row['dados_solicitante'], function (key, value) {
        return key == 'n_nome_user' ?? value;

    });
}
// TRATAMENTO DO TEXTOS
function removerAcentos(texto) {
    return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
}

function renderData(data, type, row) {
    const date = data;
    if (!date) return '-';
    const infoDate = calcularDias(date);
    const content = `<span style="display:flex;flex-direction:column;line-height:1.10;">
                    <span class="infodiasdate"> Solicitado a ${infoDate}D</span>
                </span>`;
    return date + content;
}

function renderDataIncio(data, type, row) {

    let content = `<span style="display:flex;flex-direction:column;line-height:1.10;">
                    <span class="infodiasdate"> Informe data de inicio</span>
                </span>`;

    let divDate = `<input type="date" class="form-control-d items-date d-data-inicio" data-id-tabela="${row['id_cadjob']}" >`;


    let date = calcularDias(data);
    let contentInfodias = `<span style="display:flex;flex-direction:column;line-height:1.10;">
                    <span class="infodiasdate"> Iniciado a ${date}D </span>
                </span>`;

    if (!data) return divDate + content;

    return data + contentInfodias;
}

function renderDataFim(data, type, row) {
    let content = `<span style="display:flex;flex-direction:column;line-height:1.10;">
                    <span class="infodiasdate"> Informe data de termino</span>
                </span>`;

    let divDate = `<input type="date" class="form-control-d items-date d-data-fim" data-id-tabela="${row['id_cadjob']}" >`;


    let date = calcularDias(data);
    let contentInfodias = `<span style="display:flex;flex-direction:column;line-height:1.10;">
                    <span class="infodiasdate"> Finaliza em  ${date}D </span>
                </span>`;

    if (!data) return divDate + content;

    return data + contentInfodias;
}



function atualizarCardsResumoJobs() {
    let qtdParcelas = App.dadosFiltrados.length;
    let valorTotal = 0;
    let suspensoSim = 0;
    let suspensoNao = 0;
    App.dadosFiltrados.forEach(r => {
        const valorFlutuante = parseFloat(r.valor || 0);
        valorTotal += valorFlutuante;

        // Verifica a suspensão (ajuste r.suspensa conforme o retorno real do seu JSON)
        if (r.suspensa === 'SIM' || r.suspensa === 'S' || r.suspensa === 1) {
            suspensoSim += valorFlutuante;
        } else {
            suspensoNao += valorFlutuante;
        }
    });

    // Atualiza os elementos discretos na tela
    $('#resumo-qtd-parcelas').text(qtdParcelas);
    $('#resumo-valor-total').text(formatarValor(valorTotal));
    $('#resumo-suspenso-sim').text(formatarValor(suspensoSim));
    $('#resumo-suspenso-nao').text(formatarValor(suspensoNao));
}

// ── FILTROS (aplicados no front, sem nova requisição) ─────────
function aplicarFiltros() {
    const busca = $('#f-busca').val().toLowerCase().trim();
    const status = $('#f-status').val();
    const dias = $('#f-dias').val();
    const dIni = $('#f-data-ini').val();
    const dFim = $('#f-data-fim').val();

    App.dadosFiltrados = App.dados.filter(r => {

        if (busca) {
            const campos = [r.cliente, r.n_nro, r.doc_ger, r.vendedor].join(' ').toLowerCase();
            if (!campos.includes(busca)) return false;
        }
        if (status !== '' && String(r.cod_status) !== String(status)) return false;
        if (dias) {
            const d = calcularDias(r.vencimento);
            if (dias === '1-10' && !(d >= 1 && d <= 10)) return false;
            if (dias === '11-30' && !(d >= 11 && d <= 30)) return false;
            if (dias === '31+' && d <= 30) return false;
        }
        if (dIni && r.vencimento < dIni) return false;
        if (dFim && r.vencimento > dFim) return false;
        return true;
    });

    App.paginaAtual = 1;
    renderTabela();
    toast(`${App.dadosFiltrados.length}registro(s) encontrado(s)`);
}


function fecharDrawer() {
    $('#drawer').removeClass('aberto');
    $('#main-layout').removeClass('drawer-open');
    $('#corpo-tabela tr').removeClass('ativo');
    App.drawerAberto = false;
    App.linhaSelecionada = null;
}

function exportarCSV() {
    if (App.dadosFiltrados.length === 0) {
        toast('Nenhum dado para exportar.', 'error');
        return;
    }

    const cabecalho = ['Cliente', 'Perfil', 'Compra/Cred.', 'N.Nro', 'Vencimento', 'Valor (R$)', 'Doc. Ger.', 'Dias Atraso', 'Vendedor', 'Ultima Tratativa', 'Status', 'Proxima Acao'];
    const linhas = App.dadosFiltrados.map(r => [
        r.cliente, r.perfil || '', r.compra_credito || '', r.n_nro,
        formatarData(r.vencimento),
        parseFloat(r.valor || 0).toFixed(2).replace('.', ','),
        r.doc_ger || '', calcularDias(r.vencimento), r.vendedor || '',
        r.descricao_mov || '', STATUS_LABEL[r.cod_status] || r.cod_status || '',
        r.descricao_acao || '',
    ]);

    //     const escapar = v => {
    //         const s = String(v);
    //         return (s.includes(';') || s.includes('"') || s.includes('\n')) ? `
    //     "${s.replace(/" / g, '""')
    // }
    // "` : s;
    //     };
    const conteudo = [cabecalho, ...linhas].map(row => row.map(escapar).join(';')).join('\r\n');

    const blob = new Blob(['\uFEFF' + conteudo], {
        type: 'text/csv;charset=utf-8;'
    });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    const data = new Date().toLocaleDateString('pt-BR').replace(/\//g, '-');
    link.href = url;
    link.download = `parcelas-atraso_${data}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    toast(`CSV exportado · ${App.dadosFiltrados.length} registros`, 'success');
}



// ── UTILITÁRIOS ───────────────────────────────────────────────
function calcularDias(vencimento) {
    if (!vencimento) return 0;
    const data = String(vencimento).trim();
    let venc;

    const isoMatch = data.match(/^(\d{4})-(\d{2})-(\d{2})$/);
    const brMatch = data.match(/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/);

    if (isoMatch) {
        venc = new Date(`${isoMatch[1]}-${isoMatch[2]}-${isoMatch[3]}T00:00:00`);
    } else if (brMatch) {
        venc = new Date(`${brMatch[3]}-${brMatch[2]}-${brMatch[1]}T00:00:00`);
    } else {
        venc = new Date(data);
    }

    if (Number.isNaN(venc.getTime())) return 0;

    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);

    const resultFlor = Math.floor((hoje - venc) / 86400000);

    return resultFlor;
}

function formatarValor(v) {
    return parseFloat(v || 0).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });
}

function formatarData(d) {
    if (!d) return '—';
    const [y, m, dd] = d.split('-');
    return `${dd}/${m}/${y}`;
}

function toast(msg, tipo = '') {
    const el = $(`<div class="toast-item ${tipo}">${msg}</div>`);
    $('#toast-wrapper').append(el);
    setTimeout(() => el.fadeOut(300, () => el.remove()), 3500);
}


function formaTdiasPesquisa(diasFormatados) {

    const rawDate = diasFormatados;

    const [year, month, day] = rawDate.split("-");
    const invertedDate = `${day}/${month}/${year}`;
    return invertedDate
}

// # PARA CADASTRAMENTO DA AREA

$(document).ready(function () {

    $('#cad_area').submit(function (event) {

        event.preventDefault();
        const tipo = tipos($("#d-area").val());
        const listaValida = {
            area: $('#n_area').val()
        }
        const infoErros = validarCamposInput(listaValida, tipo);
        if (infoErros) {
            listaValida.id = '';
            listaValida.campos = listaValida.area
            listaValida.tipo = tipo;
            listaValida.ctr = App.crt;
            listaValida.status = App.status;
            delete listaValida.area;
            salvarDados(listaValida, $("#d-area").val());
        }
    });

    $("#cad_usuario").submit(function (event) {
        event.preventDefault();

        const tipo = tipos($("#d-usuario").val());

        const listaValida = {
            nome: $('#n_usuario').val(),
            area: $("#d-tipo-area").val(),
        }


        const infoErros = validarCamposInput(listaValida, tipo);
        if (infoErros) {
            listaValida.campos = listaValida.nome
            listaValida.tipo = tipo;
            listaValida.id = listaValida.area
            listaValida.ctr = App.crt;
            listaValida.status = App.status;
            delete listaValida.nome;
            delete listaValida.area;
            salvarDados(listaValida, $("#d-usuario").val());
        }
    });
});


function validarCamposInput(dados, tipo) {

    switch (tipo) {
        case 10:

            for (const [chave, valor] of Object.entries(dados)) {

                if (!valor || valor == 0) {
                    toast(`Campo ${chave} não pode ser vazio!.`, 'error');
                    $('#btn-salvar-tratativa').prop('disabled', false);
                    return false; // Para a função aqui
                }
            }
            break;
        case 0:
            for (const [chave, valor] of Object.entries(dados)) {
                if (!valor || valor == 0) {
                    toast(`Campo ${chave} não pode ser vazio!.`, 'error');
                    $('#btn-salvar-tratativa').prop('disabled', false);
                    return false; // Para a função aqui
                }
            }
            break;
        case 11:
            for (const [chave, valor] of Object.entries(dados)) {
                if (!valor || valor == 0) {
                    toast(`Campo ${chave} não pode ser vazio!.`, 'error');
                    $('#btn-salvar-tratativa').prop('disabled', false);
                    return false; // Para a função aqui
                }
            }
            break;

        case 3:
            for (const [chave, valor] of Object.entries(dados)) {
                if (!valor || valor == 0) {
                    toast(`Campo ${chave} não pode ser vazio!.`, 'error');
                    $('#btn-salvar-tratativa').prop('disabled', false);
                    return false; // Para a função aqui
                }
            }
            break;
        case 2:
            for (const [chave, valor] of Object.entries(dados)) {
                if (!valor || valor == 0) {
                    toast(`Campo ${chave} não pode ser vazio!.`, 'error');
                    $('#btn-salvar-tratativa').prop('disabled', false);
                    return false; // Para a função aqui
                }
            }
            break;
        default:
            content = null;
    }

    return true;

    // console.log(campo);

    // if (!campo || campo == 0) {
    //     toast(`Campo Nome não pode ser vazio!.`, 'error');
    //     $('#btn-salvar-tratativa').prop('disabled', false);
    //     return false; // Para a função aqui
    // }

}


// if (!campo || campo == 0) {
//     toast(`Campo ${tipo} não pode ser vazio!.`, 'error');
//     $('#btn-salvar-tratativa').prop('disabled', false);
//     return false; // Para a função aqui
// }

// if (!statusTratativa || statusTratativa == 0) {
//     toast('Selecione o status da tratativa.', 'error');
//     $('#btn-salvar-tratativa').prop('disabled', false);
//     return false; // Para a função aqui
// }

// if (!proximaAcao || proximaAcao == 0) {

//     toast('Selecione a próxima ação.', 'error');
//     $('#btn-salvar-tratativa').prop('disabled', false);
//     return false; // Para a função aqui
// }


function toast(msg, tipo = '') {
    const el = $(`<div class="toast-item ${tipo}">${msg}</div>`);
    $('#toast-wrapper').append(el);
    setTimeout(() => el.fadeOut(300, () => el.remove()), 3500);
}


// ENVIAR DADOS PARA A API

function salvarDados(playload, botao) {

    $(`#btn-salvar-${botao}`).prop('disabled', true).text('Salvando...');

    const playloadJson = JSON.stringify(playload);

    $.ajax({
        url: '/api/Cadinformacoes',
        type: 'POST',
        data: playloadJson,
        dataType: 'json',
        success: function (resp) {
            if (resp.sucesso) {
                // _atualizarEstadoLocal(id, payloadtext);
                toast(`${botao} salva com sucesso!`, 'success');
                atualizar_botao(botao);
                //atualizar tabela
                ataulizar_tabela();

            } else {
                var erros = JSON.stringify(resp.mensagem) ?? JSON.stringify(resp.dados);
                toast('Erro ao salvar: ' + (erros || 'tente novamente'), 'error');
                atualizar_botao(botao);

            }
        },
        error: function () {
            toast('Falha de conexão ao salvar. Tente novamente.', 'error');
            atualizar_botao(botao);
        },
        complete: function () {

            atualizar_botao(botao);
        }
    });
}

function atualizar_botao(botao) {

    $(`#btn-salvar-${botao}`).prop('disabled', false).html(`
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg> Salvar ${botao}..`);
}

// PARA SALVAMENTO ATUALIZAR OS DADOS 
function UpDados(playload, botao) {

    $(`#btn-salvar-${botao}`).prop('disabled', true).text('Salvando...');

    const playloadJson = JSON.stringify(playload);

    $.ajax({
        url: '/api/UpdadosJobs',
        type: 'PUT',
        data: playloadJson,
        dataType: 'json',
        success: function (resp) {
            if (resp.sucesso) {
                // _atualizarEstadoLocal(id, payloadtext);
                var msg = JSON.stringify(resp.mensagem) ?? JSON.stringify(resp.dados);
                toast(`${msg}`, 'success');
                ataulizar_tabela();
            } else {


                var erros = JSON.stringify(resp.mensagem) ?? JSON.stringify(resp.dados);

                toast('Erro ao salvar: ' + (erros || 'tente novamente'), 'error');
                atualizar_botao(botao);
            }
        },
        error: function () {
            toast('Falha de conexão ao salvar. Tente novamente.', 'error');
        },
        complete: function () {
            atualizar_botao(botao);

        }
    });
}


//CHAMADA PARA A CRIACAO DO SELECT DA AREA
function get_lista_area() {

    $.ajax({
        url: '/api/ListArea',
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },

        success: function (resp) {

            if (resp.status == 2) {
                montar_select(resp.dados)
            } else {
                toast('Outro status:', resp.message, 'error');
            }
        },
        error: function (error) {
            console.error('Erro na requisição:', error);
        }
    });
}

function get_lista_user_area() {

    $.ajax({
        url: '/api/ListUserArea',
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },

        success: function (resp) {

            if (resp.status == 2) {
                montar_selec_user_area(resp.dados)

            } else {
                toast('Outro status:', resp.message, 'error');
            }
        },
        error: function (error) {
            console.error('Erro na requisição:', error);
        }
    });
}


function get_lista_status() {

    $.ajax({
        url: '/api/ListStatus',
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },

        success: function (resp) {

            if (resp.status == 2) {
                montar_select_status(resp.dados)
            } else {
                toast('Outro status:', resp.message, 'error');
            }
        },
        error: function (error) {
            console.error('Erro na requisição:', error);
        }
    });
}


function get_lista_perfil() {

    $.ajax({
        url: '/api/ListPerfil',
        type: 'GET',
        dataType: 'json',
        success: function (resp) {

            if (resp.status == 2) {

                const dados = resp.dados.map(function (item) {
                    const n_perfilUpper = item.n_perfil.toUpperCase();

                    return {
                        id: item.id_perfil,
                        text: n_perfilUpper
                    };
                });


                if ($('#n_perfil').hasClass("select2-hidden-accessible")) {
                    $('#n_perfil').select2('destroy');
                }


                $('#n_perfil').html('<option></option>');


                $('#n_perfil').select2({
                    placeholder: 'Selecione um Perfil',
                    // class: 'form-control',
                    width: '100%',
                    data: dados
                });
            }
        }
    });
}

function get_lista_tipo() {

    $.ajax({
        url: '/api/ListTipo',
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },

        success: function (resp) {

            if (resp.status == 2) {

                montar_selec_tipo(resp.dados)

            } else {
                toast('Outro status:', resp.message, 'error');
            }
        },
        error: function (error) {
            console.error('Erro na requisição:', error);
        }
    });
}



// LISTA DE CLIENTES
function get_lista_cliente() {

    $.ajax({
        url: '/api/ListCliente',
        type: 'GET',
        dataType: 'json',
        success: function (resp) {
            if (resp.status == 2) {

                const dados = resp.dados.map(function (item) {
                    return {
                        id: item.cliid,
                        text: item.clinomraz
                    };
                });


                if ($('#n_cliente').hasClass("select2-hidden-accessible")) {
                    $('#n_cliente').select2('destroy');
                }


                $('#n_cliente').html('<option></option>');


                $('#n_cliente').select2({
                    placeholder: 'Selecione um cliente',
                    // class: 'form-control',
                    width: '100%',
                    data: dados
                });
            }
        }
    });
}

// LISTA DE CLIENTES PROPESCT
function get_lista_cliente_propesct() {

    $.ajax({
        url: '/api/ListPropesct',
        type: 'GET',
        dataType: 'json',
        success: function (resp) {

            if (resp.status == 2) {

                const dados = resp.dados.map(function (item) {
                    return {
                        id: item.cliid,
                        text: item.cli_propect
                    };
                });

                if ($('#n_clientes_inputs_prospect').hasClass("select2-hidden-accessible")) {
                    $('#n_clientes_inputs_prospect').select2('destroy');
                }


                $('#n_clientes_inputs_prospect').html('<option></option>');


                $('#n_clientes_inputs_prospect').select2({
                    placeholder: 'Selecione um cliente Prospect',
                    allowClear: true,
                    data: dados
                });
            }
        }
    });
}


// PEGAR OS DADOS
$('#n_clientes_inputs_prospect').on('select2:select', function (e) {
    App.clientesSelecionadoPropesct = e.params.data
    const proPesct = e.params.data;
});

$('#n_cliente').on('select2:select', function (e) {
    App.clientesSelecionado = e.params.data
    const cliente = e.params.data;
});

$('#n_perfil').on('select2:select', function (e) {
    const perId = e.params.data;
    App.perfilSelecionado = perId;
});


const checkbox = document.getElementById('myCheckbox');
const input = document.getElementById('n_cliente');

checkbox.addEventListener('change', function () {
    input.disabled = checkbox.checked;
    //SE MARCAR MOSTRO, CASO NÃO OCULTO
    checkbox.checked ? $('#clientes_inputs').show() : $('#clientes_inputs').hide();
    checkbox.checked ? $('#myCheckboxProspect').prop('disabled', true) : $('#myCheckboxProspect').prop('disabled', false);
});


// PARA TRATAMENTO DO CHECKBOX AO CLIENTE PROPESCT
$('#myCheckboxProspect').on('change', function () {
    const isChecked = this.checked;
    $('#container_prospect').toggle(isChecked);

    if (isChecked) {
        get_lista_cliente_propesct();
        $('#myCheckbox').prop('disabled', true);
        $('#n_cliente').prop('disabled', true).hide();

    } else {

        $('#n_clientes_inputs_prospect').val(null).trigger('change');
        $('#cliente_padrao').val('');
        $('#myCheckbox').prop('disabled', false);
        $('#n_cliente').prop('disabled', false).show();
    }
});




function tipos(tiposNumber) {

    var content = '';
    // 0- araea,1-jobtipo,2-jobstatus,3-jobperfil,4-jobexecutor,5-jobsolicitante,10-jobusuarios
    switch (tiposNumber) {
        case "usuarios":
            content = 10;
            break;

        case "area":
            content = 0;
            break;

        case "perfil":
            content = 3;
            break;
        case "status":
            content = 2;
            break;
        case "obs":
            content = 11;
            break;
        default:
            content = null;
    }

    return content;
}

$(document).on('change', '#d-tipo-job', function () {

    const idjob = $('#d-tipo-job').val()
    validarInterno(idjob, App.tipoJobs);

    //VERIFICAR O INPUT
});


// CAPUTRA O FORM DO FORMULARIO DO CAD 
$("#cad_job").submit(function (event) {
    event.preventDefault();
    const selectPerfil = App.perfilSelecionado ? App.perfilSelecionado.id : '';


    var valoresSelecionados = $('input:checkbox:checked').map(function () {
        App.clientesSelecionado = '';
        return $(this).val();
    }).get();


    const padrao = $('#cliente_padrao').val();

    let n_info_cliente = '';
    let n_info_perfil = '';

    n_info_cliente = padrao ? padrao : validarCliente(valoresSelecionados[0]);

    const retorno_data_atual = current();
    const data_atual_selecionada = $('#range').val();

    const partesFim = formaTdiasPesquisa(data_atual_selecionada);


    if (partesFim > current()) {

        toast('Escolha data menor que á a data atual', 'error');
        return
    }


    const listaCadastro = {
        id_solicitante: $('#d-tipo-user-area-solicitante').val(),
        n_cliente: n_info_cliente,
        tipoJob: $('#d-tipo-job').val(),
        perfil: selectPerfil,
        s_tatus: $('#d-tipo-job-status').val(),
        d_soliciticao: $('#range').val(),
        titulo_email: $('#titulo_email').val(),
        detalhamento: $('#detalhamento_email').val(),
        tctrid: App.crt,
        ctr: App.crt,
        tctraut: App.tctraut,
        tipo: App.tipo

    }

    enviarSolicitacao(listaCadastro, 'job');

});


function enviarSolicitacao(listaCadastro, botao) {


    $(`#btn-salvar-${botao}`).prop('disabled', true).text('Salvando...');
    let playloadJson = JSON.stringify(listaCadastro);

    $.ajax({
        url: '/api/CadJob',
        type: 'POST',
        data: playloadJson,
        dataType: 'json',
        success: function (resp) {
            if (resp.sucesso) {
                // _atualizarEstadoLocal(id, payloadtext);
                toast(`${botao} com sucesso!`, 'success');

                ataulizar_tabela();
                // if ($.fn.DataTable.isDataTable("#table-relatorio-indicadores")) {
                //     $("#table-relatorio-indicadores").DataTable().destroy();
                // }
                // limpar payload e campos após sucesso para evitar reenvio do mesmo dado
                playloadJson = null;
                try {
                    $('#titulo_email').val('');
                    $('#detalhamento_email').val('');
                    $('#range').val('');
                    $('#d-id').val('');
                    $('#d-tipo-job').val($('#d-tipo-job option:first').val());
                    $('#d-tipo-job-status').val($('#d-tipo-job-status option:first').val());
                    $('#d-tipo-user-area-solicitante').val($('#d-tipo-user-area-solicitante option:first').val());
                } catch (e) {
                    /* ignora se elementos não existirem */
                }
                // listArchivesJobs();

                atualizar_botao(botao);
            } else {

                //   toast('Erro ao salvar: ' + (resp.dados.error[0] || 'tente novamente'), 'error');
                var erros = JSON.stringify(resp.mensagem) ?? JSON.stringify(resp.dados);

                toast('Erro ao salvar: ' + (erros || 'tente novamente'), 'error');
                atualizar_botao(botao);
            }
        },
        error: function () {
            toast('Falha de conexão ao salvar. Tente novamente.', 'error');
            atualizar_botao(botao);
        },
        complete: function () {
            atualizar_botao(botao);
        }
    });

}

function ataulizar_tabela() {

    if ($.fn.DataTable.isDataTable("#table-relatorio-indicadores")) {
        $("#table-relatorio-indicadores").DataTable().destroy();
    }
    listArchivesJobs();
}

function validarInterno(usandoInput, tipo) {


    const jobId = Number(usandoInput);
    const tipoId = Number(tipo);

    if (!jobId) {
        alert('Informe o Tipo de Job.');
        $('#myCheckbox').prop('disabled', false);
        $('#cliente_padrao').val('');
        $('#myCheckboxProspect').prop('disabled', false).show();
        return;
    }

    if (tipoId === jobId) {
        const result = App.clienteTeste + '$' + App.clienteProscore;
        $('#cliente_padrao').val(result);
        $('#myCheckbox').prop('disabled', true);
        $('#n_cliente').prop('disabled', true).hide();
        $('#myCheckboxProspect').prop('disabled', true);

    } else {
        $('#cliente_padrao').val('');
        $('#myCheckbox').prop('disabled', false);
        $('#n_cliente').prop('disabled', false).show();
        $('#myCheckboxProspect').prop('disabled', false);
    }
}

function validarCliente(usandoInput) {
    let result = '';


    const isClienteNovo = $('#myCheckbox').is(':checked');
    const isProspect = $('#myCheckboxProspect').is(':checked');

    if (usandoInput) {
        let clienteInput = '';

        if (isProspect) {

            clienteInput = (App.clientesSelecionadoPropesct && App.clientesSelecionadoPropesct.text) ?
                App.clientesSelecionadoPropesct.text.trim() :
                $('#n_clientes_inputs_prospect option:selected').text().trim();
        } else if (isClienteNovo) {

            clienteInput = ($('#clientes_inputs').val() || '').trim();
        } else {

            clienteInput = ($('#clientes_inputs').val() || '').trim();
        }

        if (!clienteInput || clienteInput === "") {
            alert('Informe o cliente ou selecione um Prospect.');
            return null;
        }

        result = (App.clienteTeste ? App.clienteTeste + '$' : '') + clienteInput;

    } else {

        if (!App.clientesSelecionado || !App.clientesSelecionado.id) {
            alert('Selecione um cliente.');
            return null;
        }

        result = App.clientesSelecionado.id + '$' + App.clientesSelecionado.text;
    }

    return result;
}


function validarPerfil(usandoInput, clienteInput) {
    let result = ''
    if (usandoInput) {
        if (!clienteInput) {
            alert('Informe o cliente.');
            return;
        }
        result = (App.clienteTeste ? App.clienteTeste + '$' : '') + clienteInput;
    } else {
        if (!App.clientesSelecionado || !App.clientesSelecionado.id) {
            alert('Selecione um cliente.');
            return;
        }
        result = App.clientesSelecionado.id + '$' + App.clientesSelecionado.text;
    }

    return result;
}

function datePicker() {

    flatpickr("#range", {
        locale: "pt",
        dateFormat: "Y-m-d",
        maxDate: new Date().toISOString().split("T")[0],
    });
}

// === AREA PARA MONTAR OS SELECT DINAMICOS  ========= ////
//MONTO SELECT PARA A VICULAR A AREA A PESSOA
function montar_select(dados) {
    const tipo_selects = document.getElementById('d-tipo-area');
    tipo_selects.innerHTML = '';
    const todosOption = document.createElement("option");
    todosOption.value = "0";
    todosOption.text = "Todos";
    tipo_selects.appendChild(todosOption);

    dados.forEach((items) => {
        const option = document.createElement("option");
        option.value = items.id_area;
        option.text = items.n_area;
        tipo_selects.appendChild(option);
    });

}

//MONTO SELECT PARA CADASTRO DO JOB VICULAR SOLICITANTE 
function montar_selec_user_area(dados) {
    const tipo_selects = document.getElementById('d-tipo-user-area-solicitante');
    tipo_selects.innerHTML = '';
    const todosOption = document.createElement("option");
    todosOption.value = "0";
    todosOption.className = "form-control";
    todosOption.text = "Todos";
    tipo_selects.appendChild(todosOption);

    dados.forEach((items) => {
        const option = document.createElement("option");
        option.value = items.usuario_id;
        option.text = items.user_area;
        tipo_selects.appendChild(option);
    });

}


//MONTO SELECT PARA CADASTRO DO TIPO VICULAR SOLICITANTE 
function montar_selec_tipo(dados) {
    const tipo_selects = document.getElementById('d-tipo-job');
    tipo_selects.innerHTML = '';
    const todosOption = document.createElement("option");
    todosOption.value = "0";
    todosOption.className = "form-control";
    todosOption.text = "Todos";
    tipo_selects.appendChild(todosOption);

    dados.forEach((items) => {
        const option = document.createElement("option");
        option.value = items.id_job;
        option.text = items.n_tipo;
        tipo_selects.appendChild(option);
    });

}
//MONTO SELECT PARA CADASTRO DO STATUS VICULAR SOLICITANTE 
function montar_select_status(dados) {
    const tipo_selects = document.getElementById('d-tipo-job-status');
    tipo_selects.innerHTML = '';
    const todosOption = document.createElement("option");
    todosOption.value = "0";
    todosOption.className = "form-control";
    todosOption.text = "Todos";
    tipo_selects.appendChild(todosOption);

    dados.forEach((items) => {
        const option = document.createElement("option");
        option.value = items.id_status;
        option.text = items.n_status;
        tipo_selects.appendChild(option);
    });

}


// === FECHAMENTO PARA MONTAR OS SELECT DINAMICOS  ========= ////

// === ABERTURA DO CÓDIGO PARA MONTAR OS SELECT DINAMICOS  E OPTIONS PRA SELECICONAR EXECUTANTE ========= ////
function montar_lista_user_area_datatable(dados, containerElement) {
    containerElement.innerHTML = '';
    if (dados.length === 0) {
        containerElement.innerHTML = '<li><span class="dropdown-item-text text-muted small">Nenhum encontrado</span></li>';
        return;
    }
    dados.forEach((items) => {
        const li = document.createElement("li");
        li.innerHTML = `<a class="dropdown-item item-executante" href="#" data-id-user="${items.usuario_id}">${items.user_area}</a>`;
        containerElement.appendChild(li);
    });
}

// Função TD
// function redimensionarTdDropdown($divDropdown) {
//     var $td = $divDropdown.closest('td');
//     var $menu = $divDropdown.find('.dropdown-menu');

//     if (!$td.data('padding-original')) {
//         $td.data('padding-original', {
//             top: $td.css('padding-top'),
//             right: $td.css('padding-right'),
//             bottom: $td.css('padding-bottom'),
//             left: $td.css('padding-left')
//         });
//     }

//     $td.css({
//         position: 'relative',
//         padding: '0',
//     });

//     $divDropdown.css({
//         position: 'absolute',
//         top: '4px',
//         left: '4px'
//     });
// }

$(document).ready(function () {

    $(document).on('show.bs.dropdown', '.dropdown-dinamico', function () {
        var $divDropdown = $(this);
        var $containerLista = $divDropdown.find('.lista-executantes');


        setTimeout(function () {
            redimensionarTdDropdown($divDropdown);
        }, 50);


        $.ajax({
            url: '/api/ListUserArea',
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            success: function (resp) {
                if (resp.status == 2) {

                    montar_lista_user_area_datatable(resp.dados, $containerLista[0]);

                    setTimeout(function () {
                        redimensionarTdDropdown($divDropdown);
                    }, 50); //delay 
                } else {
                    toast('Outro status:', resp.message, 'error');
                }
            },
            error: function (error) {
                console.error('Erro na requisição:', error);
                $containerLista.html('<li class="text-danger small px-2">Erro ao carregar</li>');
                redimensionarTdDropdown($divDropdown);
            }
        });
    });

    // Evento ao fechar o dropdown

    $(document).on('hide.bs.dropdown', '.dropdown-dinamico', function () {
        var $divDropdown = $(this);
        var $td = $divDropdown.closest('td');

        // remove inline styles added by redimensionarTdDropdown
        $divDropdown.css({
            'position': '',
            'top': '',
            'left': '',
            'z-index': ''
        });

        $td.css({
            'position': '',
            'padding-top': '',
            'padding-left': '',
            'padding-bottom': '',
            'padding-right': ''
        });
    });
    //Evento para quando o usuário clicar em um nome da lista 
    $(document).on('click', '.item-executor, .item-executante', function (e) {
        e.preventDefault();
        var $linkClicado = $(this);

        var idSelecionado = $(this).data('id-user');
        var nomeSelecionado = $(this).text();
        var tabela_row = $linkClicado.closest('.dropdown-menu').find('.tabela-row').val();


        const playload = {
            tabela: tabela_row,
            executante_id: idSelecionado,
            tipo: 4,
            crt: App.crt
        }
        UpDados(playload);

    });
});



// === ABERTURA DO CÓDIGO PARA MONTAR OS SELECT DINAMICOS  E OPTIONS PRA SELECICONAR STATUS ========= ////
function montar_lista_user_status_datatable(dados, containerElement) {

    containerElement.innerHTML = '';
    if (dados.length === 0) {
        containerElement.innerHTML = '<li><span class="dropdown-item-text text-muted small">Nenhum status encontrado</span></li>';
        return;
    }
    dados.forEach((items) => {
        const li = document.createElement("li");
        li.innerHTML = `<a class="dropdown-item item-status" href="#" data-id-status="${items.id_status}">${items.n_status}</a>`;
        containerElement.appendChild(li);
    });
}


function redimensionarTdDropdown($divDropdown) {
    var $td = $divDropdown.closest('td');
    var $menu = $divDropdown.find('.dropdown-menu');

    $td.css('position', 'relative');

    var menuHeight = $menu.outerHeight();
    var menuWidth = $menu.outerWidth();

    var buttonHeight = $divDropdown.outerHeight() || 35;

    var paddingBottomValue = Math.max(40, buttonHeight + menuHeight + 20) + 'px';
    var paddingRightValue = Math.max(40, menuWidth + 20) + 'px';

    $divDropdown.css({
        'position': 'absolute',
        'top': '5px',
        'left': '5px',
        'z-index': 9999
    });

    console.log($td);

    $td.css({
        'padding-top': '10px',
        'padding-left': '10px',
        'padding-bottom': paddingBottomValue,
        'padding-right': paddingRightValue
    });

    console.warn('CLASSE TD', $td);
}
$(document).ready(function () {

    $(document).on('show.bs.dropdown', '.dropdown-dinamico-status', function () {
        var $divDropdown = $(this);
        var $containerLista = $divDropdown.find('.lista-status');


        setTimeout(function () {
            redimensionarTdDropdown($divDropdown);
        }, 10);


        $.ajax({
            url: '/api/ListStatus',
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            success: function (resp) {
                if (resp.status == 2) {

                    montar_lista_user_status_datatable(resp.dados, $containerLista[0]);
                    setTimeout(function () {
                        redimensionarTdDropdown($divDropdown);
                    }, 50);
                } else {
                    toast('Outro status:', resp.message, 'error');
                }
            },
            error: function (error) {
                console.error('Erro na requisição:', error);
                $containerLista.html('<li class="text-danger small px-2">Erro ao carregar</li>');
                redimensionarTdDropdown($divDropdown);
            }
        });
    });

    // Reset styles when dropdown is hidden so layout returns to normal
    $(document).on('hidden.bs.dropdown', '.dropdown-dinamico-status', function () {
        var $divDropdown = $(this);
        var $td = $divDropdown.closest('td');

        // remove inline styles added by redimensionarTdDropdown
        $divDropdown.css({
            'position': '',
            'top': '',
            'left': '',
            'z-index': ''
        });

        $td.css({
            'position': '',
            'padding-top': '',
            'padding-left': '',
            'padding-bottom': '',
            'padding-right': ''
        });
    });


    $(document).on('click', '.items-status, .item-status', function (e) {
        e.preventDefault();
        var $linkClicado = $(this);
        var idSelecionado = $(this).data('id-status');
        var nomeSelecionado = $(this).text();
        var tabela_row = $linkClicado.closest('.dropdown-menu').find('.tabela-row').val();

        const playload = {
            tabela: tabela_row,
            status_id: idSelecionado,
            tipo: 3,
            crt: App.crt
        }

        UpDados(playload);

    });
});

$(document).on('change', '.d-data-inicio', function (e) {

    e.preventDefault();

    var $input = $(this);
    var idSelecionadoTabela = $input.data('id-tabela');
    var dataSelecionada = $input.val();


    const playload = {
        tabela: idSelecionadoTabela,
        data_inicio: dataSelecionada,
        tipo: 1,
        crt: App.crt
    }


    UpDados(playload);

});

$(document).on('change', '.d-data-fim', function (e) {

    e.preventDefault();

    var $input = $(this);
    var idSelecionadoTabela = $input.data('id-tabela');
    var dataSelecionada = $input.val();

    const playloads = {
        tabela: idSelecionadoTabela,
        data_fim: dataSelecionada,
        tipo: 2,
        crt: App.crt
    }

    UpDados(playloads);

});


$(document).on('click', '.btn-list-dados', function (e) {

    e.preventDefault();

    $('#d-id-tabela').val($(this).data('id-tabela'));
    $('#d-id-tabela-historico').val($(this).data('id-tabela'));


    $('#modalDados').modal('show');

});

//    <!-- ============================================================
// CAPTURA FORMULARIO 
// ============================================================ -->
$("#obs_job").submit(function (event) {
    event.preventDefault();


    var obs = $('#info_job').val();

    var idSelecionadoTabela = $('#d-id-tabela').val();

    const tipo = tipos($("#d-obs").val());
    const listaValida = {
        obs: $('#info_job').val()
    }

    const infoErros = validarCamposInput(listaValida, tipo);

    if (infoErros) {
        listaValida.id = idSelecionadoTabela;
        listaValida.campos = listaValida.obs
        listaValida.tipo = tipo;
        listaValida.ctr = App.crt;
        listaValida.status = App.status;
        delete listaValida.obs;

        salvarDados(listaValida, $("#d-obs").val());
    }
});
//    <!-- ============================================================
// CAPTURA FORMULARIO  PERFIL
// ============================================================ -->

$("#cad_perfil").submit(function (event) {
    event.preventDefault();


    var perfilInformado = $('#n_perfils').val();


    console.log(perfilInformado);

    const tipo = tipos($("#d-perfil").val());
    const listaValida = {
        perfil: perfilInformado
    }

    const infoErros = validarCamposInput(listaValida, tipo);

    if (infoErros) {
        listaValida.id = '';
        listaValida.campos = listaValida.perfil
        listaValida.tipo = tipo;
        listaValida.ctr = App.crt;
        listaValida.status = App.status;
        delete listaValida.perfil;

        console.log(listaValida, 'RESULTADO');

        salvarDados(listaValida, $("#d-perfil").val());
    }
});

$(document).ready(function () {
    $('.btn-listar-historico').on('click', function (e) {

        var idSelecionadoTabela = $('#d-id-tabela-historico').val();

        listArchivesJobsHistorico(idSelecionadoTabela);

    });
});


$(document).ready(function () {
    $('.btn-listar-historico-obs').on('click', function (e) {


        var idSelecionadoTabela = $('#d-id-tabela-historico').val();

        listArchivesJobsHistoricoObs(idSelecionadoTabela);

    });
});

$(document).ready(function () {

    $('#modalDados').on('hidden.bs.modal', function () {
        App.dadosFiltradosHistorico = []; // Limpa o array de dados

        $("#historico-lista-apresentar").empty();

        // Volta a mensagem padrão
        $('#historico-lista').html(`
            <div style="text-align:center;padding:40px;color:var(--muted);font-size:12px">
                Selecione uma parcela para ver o histórico.
            </div>
        `);


        $('#collapseExampleAtualizacoes').collapse('hide');
    });
});


$('#cad_status').submit(function (e) {

    e.preventDefault();


    const tipo = tipos($("#d-status").val());
    const listaValida = {
        nome_status: $('#n_status').val()
    }

    const infoErros = validarCamposInput(listaValida, tipo);

    if (infoErros) {
        listaValida.id = '';
        listaValida.campos = listaValida.nome_status
        listaValida.tipo = tipo;
        listaValida.ctr = App.crt;
        listaValida.status = App.status;
        delete listaValida.nome_status;

        salvarDados(listaValida, 'Status');
    }
});




function current() {
    const date_day = new Date();
    const ano = date_day.getFullYear();
    const mes = String(date_day.getMonth() + 1).padStart(2, '0');
    const dia = String(date_day.getDate()).padStart(2, '0');

    return `${ano}-${mes}-${dia}`;

}


function lista_meses(mes) {
    const mesesMap = {
        '01': 'Janeiro',
        '02': 'Fevereiro',
        '03': 'Março',
        '04': 'Abril',
        '05': 'Maio',
        '06': 'Junho',
        '07': 'Julho',
        '08': 'Agosto',
        '09': 'Setembro',
        '10': 'Outubro',
        '11': 'Novembro',
        '12': 'Dezembro'
    };

    return mesesMap[mes];


}