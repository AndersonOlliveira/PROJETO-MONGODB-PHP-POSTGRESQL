// ── ESTADO GLOBAL ─────────────────────────────────────────────
const App = {

    tipo: 'n_cliente',
    tctrid: '',
    tctraut: '',
    tipo_busca_c: '',
    dados: JSON.parse(localStorage.getItem('dados_clientes')) || [] //LISTA COM O RETORNO
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