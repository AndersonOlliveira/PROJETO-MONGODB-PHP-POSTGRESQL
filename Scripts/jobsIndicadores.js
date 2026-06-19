// ── ESTADO GLOBAL ─────────────────────────────────────────────
const App = {
    dados: [], // todos os registros da API
    data_global_contratros: [],
    dadosFiltrados: [], // subconjunto após filtros
    paginaAtual: 1,
    porPagina: 50,
    linhaSelecionada: null,
    drawerAberto: false,
    abaAtiva: 'tratativa',
    historicos: {}, // cache por n_nro
    crt: '',
    status: true,
    clientesSelecionado: '',
    perfilSelecionado: '',
    clienteTeste: 'novo',
    tipo: 9
};

// ── INICIALIZAÇÃO ─────────────────────────────────────────────
$(document).ready(function () {
    // PEGO O LOGIN E APROVEITO DE FORMA GLOBAL DENTRO DO CODIGO
    App.crt = $('#d-id').val();
    //   preencherDataHoje();
    //   listContato();
    //   list_action();


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
    $('#btn-csv').on('click', exportarCSV);

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

                App.dadosFiltrados = App.dadosFiltrados.map(item => ({
                    ...item,
                    solicitante: item.dados_solicitante?.n_nome_user ?? '-',
                    area_solicitante: item.dados_solicitante?.n_area ?? '-',
                    area_executor: item.dados_executor?.n_area ?? '-',
                    n_executor: item.dados_executor?.n_nome_user ?? '-',

                }));

                console.log('ESTOU CHAMANDO OS DADOS \n');
                console.log(App.dados);
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

function mostrarLoading() {
    $('#corpo-tabela').html('<tr class="loading-row"><td colspan="13"><span class="spinner"></span>Carregando dados do servidor...</td></tr>');
    $('#contagem').text('');
}

function mostrarErro(msg) {
    $('#corpo-tabela').html(`<tr><td colspan="13"><div class="error-state"><p>Erro ao carregar os dados</p><small>${msg}</small><br><button class="btn-retry" onclick="list_dados()">Tentar novamente</button></div></td></tr>`);
    $('#contagem').text('');
}


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
        scrollX: true,
        searching: true,
        ordering: true,
        responsive: false,
        language: {
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Nenhum registro encontrado",
            lengthMenu: "Mostrar _MENU_ registros",
            search: "Pesquisar:",
            searchPlaceholder: "Digite para pesquisar...",
            paginate: {
                previous: "Anterior",
                next: "Próximo"
            }
        },

        data: App.dadosFiltrados, //DADOS VINDO DA API

        columns: [

            {
                data: 'id_cadjob',
                defaultContent: '-'
            }, {
                data: 'titulo_email',
                defaultContent: '-'
            },
            {
                data: 'nome_cliente',
                defaultContent: '-'
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
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
   <div class="dropdown dropdown-dinamico">
        <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown" data-bs-flip="false">
            ${row.dados_executor.n_nome_user || 'Sem nome'}
        </button>
            <ul class="dropdown-menu p-2">
              <input type="hidden" value="${row.id_cadjob}" class="tabela-row">
            <li><h6 class="dropdown-header fw-bold text-dark px-2">Selecione Executor</h6></li>
            <li><a class="dropdown-item item-executor" href="#" data-id="0">Todos</a></li>
            <li><hr class="dropdown-divider"></li>
            <div class="lista-executantes">
                <li class="text-muted small px-2">Carregando...</li>
            </div>
        </ul>
    </div>
 
        `;
                }
            },


            {
                data: 'area_executor',
                defaultContent: '-'
            },
            {
                data: 'n_perfil',
                defaultContent: '-'
            },
            {
                data: 'n_status',
                render: function (data, type, row) {

                    let classe = 'badge bg-secondary';

                    switch (data) {
                        case 'PAUSADO':
                            classe = 'badge bg-warning';
                            break;

                        case 'FINALIZADO':
                            classe = 'badge bg-success';
                            break;

                        case 'EM ANDAMENTO':
                            classe = 'badge bg-primary';
                            break;
                    }

                    return `
                     <div class="dropdown dropdown-dinamico-status">
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
            },
            {
                data: null,
                render: function (data, type, row) {
                    return renderData(data, type, row);

                    // if (!data) return '-';

                    // return data;
                }
            },
            {
                data: 'data_inicio',
                render: function (data, type, row) {
                    return renderDataIncio(data, type, row);
                    // if (!data) return '-';

                    // return data;
                }
            },
            {
                data: 'data_fim',
                render: function (data, type, row) {
                    return renderDataFim(data, type, row);
                    // if (!data) return '-';

                    // return data;
                }
            },
            {
                data: 'detalhamento',
                defaultContent: '-'
            }, {
                data: null,
                render: function (data, type, row) {


                    return `
                    <div class="d-flex flex-row mb-3">
                      <div class="p-2 pg-file-section">
                        <button type="button" class="custom-file-label btn-editar"> Editar </button> 
                        </div>
                         <div class="p-2 pg-file-section">
                            <button type="button" class="custom-file-label btn-status-action" data-tipo="" data-dados-id=""> 
                            Cancelar
                            </button>
                            </div>  
                            
                            
                            <div class="p-2 pg-file-section">
                            <button type="button" class="custom-file-label btn-status-action" data-tipo="" data-dados-id=""> 
                            Detalhes
                            </button>
                            </div> 
                        </div>`;

                },
            },
        ]
    });

    $('#contagem').html(
        `${App.dadosFiltrados.length} registro(s) encontrado(s)`
    );
}


function renderRowSolicitante(row, type, data) {

    console.log(row['dados_solicitante']);
    $.each(row['dados_solicitante'], function (key, value) {
        return key == 'n_nome_user' ?? value;

    });


}

function renderData(row, type, data) {
    let date = row['data_solicitacao'];
    let infoDate = calcularDias(date);
    let content = `<span style="display:flex;flex-direction:column;line-height:1.10;">
                    <span class="infodiasdate"> Solicitado a ${infoDate || ''}D</span>
                </span>`;
    if (!date) return '-';

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
    toast(`
    $ {
        App.dadosFiltrados.length
    }
    registro(s) encontrado(s)
    `);
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
    return Math.floor((hoje - venc) / 86400000);
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

        case "perfil":
            content = 3
        case "status":
            content = 2
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
                // toast('Área salva com sucesso!', 'success');
            } else {

                //   toast('Erro ao salvar: ' + (resp.dados.error[0] || 'tente novamente'), 'error');
                var erros = JSON.stringify(resp.mensagem) ?? JSON.stringify(resp.dados);

                toast('Erro ao salvar: ' + (erros || 'tente novamente'), 'error');
            }
        },
        error: function () {
            toast('Falha de conexão ao salvar. Tente novamente.', 'error');
        },
        complete: function () {
            $('#btn-salvar-area').prop('disabled', false).html(`
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg> Salvar Área..`);
        }
    });
}

// PARA SALVAMENTO ATUALIZAR OS DADOS 
function UpDados(playload, botao) {

    // $(`#btn-salvar-${botao}`).prop('disabled', true).text('Salvando...');

    const playloadJson = JSON.stringify(playload);

    $.ajax({
        url: '/api/UpdadosJobs',
        type: 'PUT',
        data: playloadJson,
        dataType: 'json',
        success: function (resp) {
            if (resp.sucesso) {
                // _atualizarEstadoLocal(id, payloadtext);
                // toast('Área salva com sucesso!', 'success');
            } else {

                //   toast('Erro ao salvar: ' + (resp.dados.error[0] || 'tente novamente'), 'error');
                var erros = JSON.stringify(resp.mensagem) ?? JSON.stringify(resp.dados);

                toast('Erro ao salvar: ' + (erros || 'tente novamente'), 'error');
            }
        },
        error: function () {
            toast('Falha de conexão ao salvar. Tente novamente.', 'error');
        },
        complete: function () {
            $('#btn-salvar-area').prop('disabled', false).html(`
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg> Salvar Área..`);
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
                    return {
                        id: item.id_perfil,
                        text: item.n_perfil
                    };
                });

                $('#n_perfil').select2({
                    placeholder: 'Selecione um Perfil',
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

                $('#n_cliente').select2({
                    placeholder: 'Selecione um cliente',
                    data: dados
                });

            }

        }
    });

}

// PEGAR OS DADOS
$('#n_cliente').on('select2:select', function (e) {
    App.clientesSelecionado = e.params.data
    const cliente = e.params.data;

    console.log(cliente.id);
    console.log(cliente.text);

});

$('#n_perfil').on('select2:select', function (e) {
    const perId = e.params.data;
    console.warn(perId.id);
    App.perfilSelecionado = perId;
});


const checkbox = document.getElementById('myCheckbox');
const input = document.getElementById('n_cliente');

checkbox.addEventListener('change', function () {
    input.disabled = checkbox.checked;
    //SE MARCAR MOSTRO, CASO NÃO OCULTO
    checkbox.checked ? $('#clientes_inputs').show() : $('#clientes_inputs').hide();
    // $('#n_cliente').val(null).trigger('change');
    // App.clientesSelecionado = null;
});




function tipos(tipos) {

    var content = '';
    // 0- araea,1-jobtipo,2-jobstatus,3-jobperfil,4-jobexecutor,5-jobsolicitante,10-jobusuarios
    switch (tipos) {
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
        default:
            content = null;
    }

    return content;
}


// CAPUTRA O FORM DO FORMULARIO DO CAD 
$("#cad_job").submit(function (event) {
    event.preventDefault();
    console.log(App.perfilSelecionado, ' PERFIL SELECIONADO');
    const clienteInput = $('#clientes_inputs').val() ? $('#clientes_inputs').val().trim() : '';
    const selectPerfil = App.perfilSelecionado ? App.perfilSelecionado.id : '';
    const usandoInput = checkbox.checked; // SE MARCA O CHECK EU PEGO DAQUI
    let n_info_cliente = '';
    let n_info_perfil = '';

    console.log(selectPerfil, ' PERFIL SELECIONADO');

    n_info_cliente = validarCliente(usandoInput, clienteInput);
    // n_info_cliente = validarPerfil(clienteInput);


    const listaCadastro = {
        id_solicitante: $('#d-tipo-user-area-solicitante').val(),
        n_cliente: n_info_cliente,
        tipoJob: $('#d-tipo-job').val(),
        perfil: selectPerfil,
        s_tatus: $('#d-tipo-job-status').val(),
        d_soliciticao: $('#range').val(),
        titulo_email: $('#titulo_email').val(),
        detalhamento: $('#detalhamento_email').val(),
        ctr: $('#d-id').val(),
        // tcrid falta
        tipo: App.tipo

    }


    enviarSolicitacao(listaCadastro);

});


function enviarSolicitacao(listaCadastro) {
    const playloadJson = JSON.stringify(listaCadastro);

    $.ajax({
        url: '/api/CadJob',
        type: 'POST',
        data: playloadJson,
        dataType: 'json',
        success: function (resp) {
            if (resp.sucesso) {
                // _atualizarEstadoLocal(id, payloadtext);
                // toast('Área salva com sucesso!', 'success');
            } else {

                //   toast('Erro ao salvar: ' + (resp.dados.error[0] || 'tente novamente'), 'error');
                var erros = JSON.stringify(resp.mensagem) ?? JSON.stringify(resp.dados);

                toast('Erro ao salvar: ' + (erros || 'tente novamente'), 'error');
            }
        },
        error: function () {
            toast('Falha de conexão ao salvar. Tente novamente.', 'error');
        },
        complete: function () {
            $('#btn-salvar-area').prop('disabled', false).html(`
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg> Salvar Área..`);
        }
    });

}


function validarCliente(usandoInput, clienteInput) {
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
        // mode: "multiple",
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
function redimensionarTdDropdown($divDropdown) {
    var $td = $divDropdown.closest('td');
    var $menu = $divDropdown.find('.dropdown-menu');

    $td.css('position', 'relative');

    var menuHeight = $menu.outerHeight();
    var menuWidth = $menu.outerWidth();

    // altura do botão para somar no cálculo real
    var buttonHeight = $divDropdown.outerHeight() || 35;


    var paddingBottomValue = Math.max(40, buttonHeight + menuHeight + 20) + 'px';
    var paddingRightValue = Math.max(40, menuWidth + 20) + 'px';

    $divDropdown.css({
        'position': 'absolute',
        'top': '5px',
        'left': '5px'
    });

    $td.css({
        'padding-top': '0px',
        'padding-left': '0px',
        'padding-bottom': paddingBottomValue,
        'padding-right': paddingRightValue
    });
}

$(document).ready(function () {

    $(document).on('show.bs.dropdown', '.dropdown-dinamico', function () {
        var $divDropdown = $(this);
        var $containerLista = $divDropdown.find('.lista-executantes');

        //Executa o cálculo inicial(para o estado atual / carregando)
        setTimeout(function () {
            redimensionarTdDropdown($divDropdown);
        }, 10);


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
                    }, 50); //Delay 
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
        var $td = $(this).closest('td');
        $td.css({
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
        console.log("ID Selecionado:", idSelecionado);
        console.log("Nome Selecionado:", nomeSelecionado);
        console.log("Nome tabela_row:", tabela_row);

        const playload = {
            tabela: tabela_row,
            executante_id: idSelecionado,
            tipo: 4,
            crt: App.crt


        }

        console.log(playload);
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
        'left': '5px'
    });

    $td.css({
        'padding-top': '0px',
        'padding-left': '0px',
        'padding-bottom': paddingBottomValue,
        'padding-right': paddingRightValue
    });
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

    $(document).on('hide.bs.dropdown', '.dropdown-dinamico-status', function () {
        var $td = $(this).closest('td');
        $td.css({
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
        console.log("ID Selecionado:", idSelecionado);
        console.log("Nome Selecionado:", nomeSelecionado);
        console.log("Nome tabela_row:", tabela_row);

        const playload = {
            tabela: tabela_row,
            status_id: idSelecionado,
            tipo: 3,
            crt: App.crt


        }

        console.log(playload);
        UpDados(playload);

    });
});

$(document).on('change', '.d-data-inicio', function (e) {

    e.preventDefault();

    var $input = $(this);
    var idSelecionadoTabela = $input.data('id-tabela');
    var dataSelecionada = $input.val();
    console.log('TENHO O CLICK VINDO PARA PEGAR A DATA');
    console.log("ID Selecionado:", idSelecionadoTabela);
    console.log("Nome Selecionado:", dataSelecionada);

    const playload = {
        tabela: idSelecionadoTabela,
        data_inicio: dataSelecionada,
        tipo: 1,
        crt: App.crt


    }

    console.log(playload);
    UpDados(playload);

});

$(document).on('change', '.d-data-fim', function (e) {

    e.preventDefault();

    var $input = $(this);
    var idSelecionadoTabela = $input.data('id-tabela');
    var dataSelecionada = $input.val();
    console.log('TENHO O CLICK VINDO PARA PEGAR A DATA FIM');
    console.log("ID Selecionado:", idSelecionadoTabela);
    console.log("Nome Selecionado:", dataSelecionada);

    const playloads = {
        tabela: idSelecionadoTabela,
        data_fim: dataSelecionada,
        tipo: 2,
        crt: App.crt
    }

    console.log(playloads);
    UpDados(playloads);

});