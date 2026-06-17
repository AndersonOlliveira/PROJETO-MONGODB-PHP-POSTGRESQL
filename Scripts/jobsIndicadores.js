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
    status: true
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
    //   buscar_Info_Responsavel_Logado();

    $('#btn-buscar').on('click', aplicarFiltros);
    $('#f-busca').on('keypress', e => {
        if (e.which === 13) aplicarFiltros();
    });
    $('#btn-fechar-drawer').on('click', fecharDrawer);
    $(document).on('click', '.drawer-tab', function () {
        mudarAba($(this).data('tab'));
    });
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

        columns: [{
                data: 'titulo_email',
                defaultContent: '-'
            },
            {
                data: 'nome_cliente',
                defaultContent: '-'
            },
            {
                data: 'solicitante',
                defaultContent: '-'
            },
            {
                data: 'area_solicitante',
                defaultContent: '-'
            },
            {
                data: 'n_executor',
                defaultContent: '-'
            },
            {
                data: 'n_perfil',
                defaultContent: '-'
            },
            {
                data: 'n_status',
                render: function (data) {

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

                    return `<span class="${classe}">${data ?? '-'}</span>`;
                }
            },
            {
                data: 'data_cad_job',
                render: function (data) {

                    if (!data) return '-';

                    return data;
                }
            },
            {
                data: 'data_inicio',
                render: function (data) {

                    if (!data) return '-';

                    return data;
                }
            },
            {
                data: 'data_fim',
                render: function (data) {

                    if (!data) return '-';

                    return data;
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
                        </div>`;

                },
            },
        ]
    });

    $('#contagem').html(
        `${App.dadosFiltrados.length} registro(s) encontrado(s)`
    );
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
    toast(`${App.dadosFiltrados.length} registro(s) encontrado(s)`);
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

    const escapar = v => {
        const s = String(v);
        return (s.includes(';') || s.includes('"') || s.includes('\n')) ? `"${s.replace(/"/g,'""')}"` : s;
    };
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
    const venc = new Date(vencimento + 'T00:00:00');
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

function get_lista_tipo() {

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
            content = 3
        case "status":
            content = 2
        default:
            content = null;
    }

    return content;


}