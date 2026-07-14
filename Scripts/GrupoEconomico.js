// ── ESTADO GLOBAL ─────────────────────────────────────────────
const App = {

    tipo: 'n_cliente',
    tctrid: '',
    tctraut: '',
    tipo_busca_c: '',
    dados: JSON.parse(localStorage.getItem('dados_clientes')) || [], //LISTA COM O RETORNO
    selecionados: []
}

$(document).ready(function () {

    //INICIA O CAMPO ESCODINDO
    $('#n_clientes').hide();

    //INICIO A VARIAVEL 
    App.tctrid = $("#d-id").val();
    App.tctraut = $("#d-tctraut").val();

});

let ultimoMarcado = null;
// TRATAR O CHECKBOX PARA LIMPAR 
$('input[name="tipoPesquisa"]').click(function () {
    // Se o botão clicado já era o último marcado, desmarca ele
    if (ultimoMarcado === this) {
        this.checked = false;
        ultimoMarcado = null;
        console.log('Botão desmarcado');
        $('#inputDados').show();
        $('#n_clientes').hide();


        $('#n_clientes').select2('destroy');

    } else {
        ultimoMarcado = this;
        console.log('Botão marcado:', this.value);
        $('#inputDados').show();
    }

    // Só carrega a lista quando o radio está marcado (checked)
    if (this.checked && this.id == App.tipo) {
        get_lista_cliente();
        $('#inputDados').hide();
        $('#n_clientes').show();
    } else {
        $('#inputDados').show();
        // Limpa e esconde o select quando desmarcado
        if ($('#n_clientes').hasClass("select2-hidden-accessible")) {
            $('#n_clientes').select2('destroy');
        }
        $('#n_clientes').val(null).trigger('change');
        $('#n_clientes').hide();
    }

});

// INFO DO FORMULARIO PARA ENVIO DO SUBMIT
$('#pesquisar').submit(function (event) {
    event.preventDefault();

    console.log('estou sendo clicado');

    const resultCheckd = verify_checkBox();
    const list = {
        ctr: App.tctrid, //CONTRATO LOGADO
        tcrt: App.tctraut,
        c_cliente_search: $('#inputDados').val(),
        tipo_busca: App.tipo_busca_c
    }

    if (!resultCheckd) {
        return;
    }

    forms_submit(list);
});



// VERIFICAR SE OS INPUTS FORAM MARCADOS
function verify_checkBox() {

    const selectedRadio = document.querySelector('input[name="tipoPesquisa"]:checked');


    if (!selectedRadio) {

        toast(`Selecione ao menos uma opção!!.`, 'error');
        return false;
    } else {

        App.tipo_busca_c = switchTipo(selectedRadio.id);
    }

    const opcaoSelecionada = selectedRadio.value;


    return opcaoSelecionada;
}

function toast(msg, tipo = '') {
    const el = $(`<div class="toast-item ${tipo}">${msg}</div>`);
    $('#toast-wrapper').append(el);
    setTimeout(() => el.fadeOut(300, () => el.remove()), 3500);
}


function switchTipo(statusLimpo) {
    let retorno_tipo_value = null;

    switch (statusLimpo) {
        case 'c_contrato':
            classe = 1; //contrato
            break;

        case 'c_rede':
            classe = 2; //cod rede
            break;

        case 'n_cliente':
            classe = 1; //CLIENTE SELECT 
            break;
        case 'radio-todos':
            classe = 3;
            break;
        case 'radio-selecionado':
            classe = 4;
            break;
        default:
            classe = null;
            break;
    }

    return classe;
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


                if ($('#n_clientes').hasClass("select2-hidden-accessible")) {
                    $('#n_clientes').select2('destroy');
                }


                $('#n_clientes').html('<option></option>');


                $('#n_clientes').select2({
                    placeholder: 'Selecione um cliente',
                    width: '100%',
                    data: dados
                });
            }
        }
    });
}

function forms_submit(solicit) {

    const playloadJson = JSON.stringify(solicit);

    $.ajax({
        url: '/api/searchClientes',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        data: {
            playloadJson
        },
        dataType: 'json',
        success: function (resp) {
            if (resp.sucesso) {
                localStorage.setItem('dados_clientes', JSON.stringify(resp.dados));
                window.location.href = resp.rota;

            } else {
                //   toast('Erro ao salvar: ' + (resp.dados.error[0] || 'tente novamente'), 'error');
                var erros = JSON.stringify(resp.mensagem) ?? JSON.stringify(resp.dados);
                toast('Erro ao buscar : ' + (erros || 'tente novamente'), 'error');

            }
        },
        error: function () {
            toast('Falha de conexão ao salvar. Tente novamente.', 'error');
            // atualizar_botao(botao);
        },
        complete: function () {
            // atualizar_botao(botao);
        }
    });

}


console.warn(App.dados);

// PARA TRATAAMENTO DOS DADOS VINDO DEPOIS DA BUSCA

$(document).ready(function () {

    //monta a tabela
    create_table();



});


function create_table() {

    tabela_indicadores = $('#dados_redes').DataTable({
        destroy: true,
        processing: true,
        // select: true,
        paging: true,
        pagingType: "full_numbers",
        scrollX: true,
        searching: true,
        ordering: true,
        responsive: false,
        select: {
            style: 'multi',
            selector: 'td:first-child .select-checkbox'
        },
        layout: {
            top: {
                search: {
                    placeholder: 'Digite para pesquisar...'
                }
            },
            topEnd: [{
                buttons: [
                    'excel',
                    {
                        text: 'Marcar todos',
                        action: function () {

                            tabela_indicadores.rows().nodes().to$().find('input[type="checkbox"]').each(function () {

                                console.log($(this)).val();
                                App.selecionados.push($(this).val());
                            });

                            console.log(App.selecionados);
                        }
                    }
                ]
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
        data: App.dados,


        columns: [{

                orderable: false,
                render: function (data, type, row, meta) {

                    return rendeRCheckbox(data, type, row, meta);
                },
            },

            {
                data: 'rdeid',
                defaultContent: '-'
            },
            {
                data: 'rdeljactr',
                defaultContent: '-',

            },
            {
                data: 'rdenom',
                defaultContent: '-',

            },
            {
                data: null,
                defaultContent: '-',

            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {

                    return renderActions(data, type, row);

                }
            }

        ],
        drawCallback: function (settings) {
            let api = this.api();
            api.rows().every(function () {
                let row = this.data();
                let cell = $(this.node()).find('td').eq(4); /// coluna limite de dados

                cell.text("Carregando!...");
                // $('#process_id').val(row.processo_id);

                get_limit_contrato(row.rdeljactr).then(dados => {
                    if (dados == null || dados === '') {
                        cell.text('-');
                        return;
                    }

                    cell.html(dados);

                }).catch(err => {
                    console.error(err);
                    cell.text('-');
                });
                // 
                // }
            });

        }
    });

    //PEGO O CORPO DO TABELA
    $('#dados_redes tbody').on('change', 'input.select-checkbox', function () {
        const filtroSelecionado = this.value;
        const rowData = tabela_indicadores.row($(this).closest('tr')).data();
        const checked = this.checked;

        if (checked) {
            if (!App.selecionados.includes(rowData.rdeljactr)) {
                App.selecionados.push(rowData.rdeljactr);
            }
        } else {
            App.selecionados = App.selecionados.filter(item => item !== rowData.rdeljactr);
        }
        console.log(App.selecionados, ' IDS SELECIONADOS');
    });
}



function renderActions(data, type, row) {

    const [textoBotao, tipo, idActiton, infoAction] = row.data_cadastro ? ['Desativar', 2, row.id, 'deativate'] : ['Remover', 1, row.id, 'bot'];

    return `
    <div class="acoes-cell">
        <button type="button" class=" dt-button btn-editar"> Alterar </button> 
        <button type="button" class="dt-button" data-tipo=${tipo} data-dados-id=${idActiton}> 
            ${textoBotao} 
        </button> 
    </div>
`;

}

function rendeRCheckbox(data, type, row, meta) {

    return `
        <input type="checkbox" class="select-checkbox" value="${row.rdeid}" data-rdeid="${row.rdeid}">
        <input type="hidden" name="dd" value="${row.rdeid}">
    `;
}


function get_limit_contrato(id_contrato) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '/api/search_limite_nivel',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            data: {
                id_contrato
            },
            dataType: 'json',
            success: function (resp) {
                if (resp.sucesso) {

                    try {
                        var retorno_valor_nivel = resp.dados;
                        // console.log(retorno_valor_nivel[0].nivel_atual);
                        resolve(retorno_valor_nivel[0].nivel_atual);
                    } catch (e) {
                        reject(e);
                    }

                } else {
                    //   toast('Erro ao salvar: ' + (resp.dados.error[0] || 'tente novamente'), 'error');
                    var erros = JSON.stringify(resp.mensagem) ?? JSON.stringify(resp.dados);
                    // toast('Erro ao buscar : ' + (erros || 'tente novamente'), 'error');
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status == 422 || xhr.status == 500) {

                    console.warn(xhr.responseText)

                    try {
                        const resp = JSON.parse(xhr.responseText);
                        console.warn("Erro: " + resp.message);
                    } catch (e) {
                        console.warn("Erro inesperado: " + xhr.responseText);
                    }
                }
                reject(error);
            }
        });
    });
}


// PEGANDO O BOTÃO

$('#cadastrar-limite').on('submit', function (e) {
    e.preventDefault();
    // e.stopPropagation();
    var botaoClicado = $(document.activeElement);

    console.log(App.selecionados);

    //pegar o tipo do checkBox

    var checkBoxSelect = verify_checkBox_incluir();
    console.log(checkBoxSelect);


    var configLimit = $('#limit_config').val();

    var clasClicado = botaoClicado.attr('class');
    var idBotao = botaoClicado.attr('id');
    var textoBotao = $(this).text();

    console.log('BOTÃO CLICADO:', {
        classe: clasClicado,
        id: idBotao,
        // texto: textoBotao
        value_limite: configLimit,
        contratos_afetar: App.selecionados
    });

    // Aqui você pode definir a ação baseada no botão clicado
    // if (clasClicado) {
    //     console.log('Ação para o botão com classe:', clasClicado);
    //     console.log('Ação para o botão com classe:', botaoClicado);
    // }
});

let checkBoxMarcado = null;
// TRATAR O CHECKBOX PARA LIMPAR 
$('input[name="incluir_contrato"]').click(function () {
    // Se o botão clicado já era o último marcado, desmarca ele
    if (ultimoMarcado === this) {
        this.checked = false;
        ultimoMarcado = null;
        console.log('Botão desmarcado ....');


    } else {
        ultimoMarcado = this;
        console.log('Botão marcado:', this.value);
        // $('#inputDados').show();
    }

    // Só carrega a lista quando o radio está marcado (checked)
    if (this.checked && this.id == App.tipo) {
        // get_lista_cliente();
        // $('#inputDados').hide();
        // $('#n_clientes').show();
    } else {
        // $('#inputDados').show();
        // Limpa e esconde o select quando desmarcado
        // if ($('#n_clientes').hasClass("select2-hidden-accessible")) {
        //     $('#n_clientes').select2('destroy');
        // }
        // $('#n_clientes').val(null).trigger('change');
        // $('#n_clientes').hide();
    }

});

function verify_checkBox_incluir() {

    const selectedRadio = document.querySelector('input[name="incluir_contrato"]:checked');


    if (!selectedRadio) {

        toast(`Selecione ao menos uma opção!!.`, 'error');
        return false;
    } else {

        App.tipo_busca_c = switchTipo(selectedRadio.id);
    }

    const opcaoSelecionada = selectedRadio.value;


    return switchTipo(selectedRadio.id);
}