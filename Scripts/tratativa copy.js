//chamanda para  carregamento dos dados
function carrregarDados() {
    return fetch('../json/lista.json')
        .then(response => response.json())
        .then(data => {
            console.log(data);
            return data;
        })
        .catch(error => {
            console.error('Erro:', error);
            throw error;
        });
}


$(document).ready(function () {
    fecthData();
    // list_dados();
    datePicker();

    optionAcoes(); //PARA GERAR OS DADOS DE

    listArchivesMonth();



});


function listArchivesMonth() {
    const anoAtual = new Date().getFullYear().toString();
    const mesAtual = (new Date().getMonth() + 1).toString();

    //pega o que foi aplicado dentro do filtro de selecionar
    $('#range, #range_ate').on('change', function () {

        const dataInicio = $('#range').val();
        const dataFim = $('#range_ate').val();

        // Verifica se as duas datas foram preenchidas
        if (dataInicio && dataFim) {

            // Exemplo formato esperado: 19/05/2026
            const partesInicio = formaTdiasPesquisa(dataInicio);
            const partesFim = formaTdiasPesquisa(dataFim);

            list_dados(null, partesInicio, partesFim);
        }

        // else {

        //     var mesAno = `${mesAtual}/${anoAtual}`;

        //     list_dados(mesAno, null, null);

        // }
    });

    var mesAno = `${mesAtual}/${anoAtual}`;

    console.log(mesAno);

    list_dados(mesAno, null, null);
}


function datePicker() {

    flatpickr("#range", {
        // mode: "multiple",
        locale: "pt",
        dateFormat: "Y-m-d",
        maxDate: new Date().toISOString().split("T")[0],
    });

    flatpickr("#range_ate", {
        // mode: "multiple",
        locale: "pt",
        dateFormat: "Y-m-d",
        maxDate: new Date().toISOString().split("T")[0],
    });
}

function clearFiltro() {

    $('#range')[0]._flatpickr.setDate([]);
    $('#range_ate')[0]._flatpickr.setDate([]);

}

//buscos os dasdos na api 
function fecthData() {

    $.ajax({
        url: '/api/getTratativas',
        type: 'GET',
        dataType: 'json', // Define que você espera JSON de retorno
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },

        success: function (response) {
            console.log('Resposta da api', response);
            if (response.status == 2) {
                console.warn('DADOS', response.dados);
            } else {
                console.log('Outro status:', response.message);
            }
        },
        error: function (error) {
            console.error('Erro na requisição:', error);
        }
    });

}


function list_dados(mes = null, tdataInicio = null, tdataFim = null) {


    console.log('DATAS ENVIADAS');
    console.log(mes);
    console.log(tdataInicio);
    console.log(tdataFim);

    var carregamento = document.getElementById('carregamento');

    carregamento.style.display = 'block';

    const convertDados = {
        "tdataInicio": tdataInicio,
        "tdataFim": tdataFim,
        "tctrid": 417039, //passar o contrato dinamico
        "mes": mes,
        // "tctraut": 
    }

    const convertidoJson = JSON.stringify(convertDados);

    $.ajax({
        url: '/api/listRelatorio',
        type: 'GET',
        dataType: 'json', // Define que você espera JSON de retorno
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        data: {
            convertidoJson
        },
        success: function (response) {
            console.log('Resposta da api vindo com a lista para criar a tabela dinamica', response);
            if (response.status == 2) {

                //depois do sucesso da consulta, caso tenha o filtro de datas limpo o range
                clearFiltro();

                carregamento.style.display = 'none';
                console.warn('DADOS criar a tabela dinamica', response.dados);

                toAssemble(response.dados);

            } else {


                //fecho o carrgamento
                carregamento.style.display = 'none';
                limparCorpo_tabela();
                $.each(response.dados, function (index, valores) {
                    $('#error-message').text(valores).show();
                    console.log('Outro status:', valores);

                });

            }
        },
        error: function (error) {
            console.error('Erro na requisição:', error);
        }
    });

}

//gera o option para a apresentar a busca
async function optionAcoes() {

    const dadosLista = await carrregarDados();

    const listaOpcoes = dadosLista[0]['status_tratativa'];

    console.log('minha lista de opcoes');
    console.warn(listaOpcoes);
}
async function toAssemble(dadosTabela) {

    console.info('DADOS ENVIADO PARA A INHA TABELA E ENVIAR OS DADOS');

    const table = document.getElementById('table-relatorio');

    const dadosLista = await carrregarDados();

    const listaOpcoes = dadosLista[0]['status_tratativa'];


    //limpo o corpo da tabela para não duplicar os dados.

    limparCorpo_tabela();

    // const tbody = document.getElementById('corpo-tabela-relatorio');
    // tbody.innerHTML = '';


    $.each(dadosTabela, function (index, valores) {
        console.log(valores['n_nro']);

        const valoresFormatado = formatarValores(valores['valor']);
        const diasFormatado = formaTdias(valores['vencimento']);

        let textoStatus = listaOpcoes[valores['cod_status']];

        // const diasFormatado = formaTdias(valores['vencimento']);
        const idValor = valores['id'] || valores['n_nro'] || '';

        console.log(idValor);
        console.log('meu valor');

        let novaLinha = `
            <tr>
                <td>${valores['cliente']}</td>
                <td>POS PAGO</td>
                <td>nao</td>
                <td>${valores['n_nro']}</td>
                <td>${valores['vencimento']}</td>
                <td>${valoresFormatado}</td>
                <td>${valores['doc_ger']}</td>
                <td${diasFormatado > 0 ? " class='celula-destaque'" : ''}>${diasFormatado}</td>
                <td>${valores['vendedor']}</td>
                <td>${valores['descricao_mov']}</td>
                <td>${textoStatus}</td>
                <td>${valores['descricao_acao']}</td>
                <td class="acoes">
                 <div class="d-flex flex-row bd-highlight mb-3">
                            <div class="p-2 bd-highlight">
                             <button type="button" class="btn" data-valor="${idValor}" data-toggle="modal" data-target="#ExemploModalCentralizado"><img src="img/balao-de-fala.png" width="14" height="14" class="icon"></img></button>
                        </div> 
                        
                          <div class="p-2 bd-highlight">
                    <button type="button" class="btn" data-valor="${idValor}" data-bs-toggle="modal" data-bs-target="#modal-adicionar">
                        <img src="img/mais.png" width="14" height="14" class="icon">
                    </button>
                </div>
                          <div class="p-2 bd-highlight">
                             <button type="button" class="btn" data-valor="${idValor}" data-toggle="modal" data-target="#ExemploModalCentralizado"><img src="img/relogio.png" width="14" height="14" class="icon"></img></button>
                        </div>

                        </div>
                            
                    </td>
            </tr>`;
        $("#corpo-tabela-relatorio").append(novaLinha);
    });

}

//LINHA DOS BOTOES 
//   <td>
//                     <div class="d-flex flex-row bd-highlight mb-3">
//                         <div class="p-2 bd-highlight">
//                             <button type="button" class="btn btn-primary" data-valor="${idValor}" data-toggle="modal" data-target="#ExemploModalCentralizado">Editar</button>
//                         </div>
//                         <div class="p-2 bd-highlight">
//                             <button type="button" class="btn btn-danger" data-valor="${idValor}" data-toggle="modal" data-target="#ExemploModalCentralizado">Deletar</button>
//                         </div>
//                     </div>
//                 </td>

// dadosTabela.forEach(element => {

//     console.log(element);

// });

function formatarValores(dado) {
    let valorFormatado = parseFloat(dado).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });

    return valorFormatado;
}


function formaTdias(diasFormatados) {

    const rawDate = diasFormatados;

    const [year, month, day] = rawDate.split("-");
    const invertedDate = `${year}-${month}-${day}`;
    const dataCriada = new Date(invertedDate);
    const dataAtual = new Date();
    const diferencaEmMilissegundos = dataAtual - dataCriada;
    let diferencaEmDias = Math.floor(diferencaEmMilissegundos / (1000 * 60 * 60 * 24));
    // console.log('MEU DIAS EM ATRASO');
    // console.info(diferencaEmDias);

    return diferencaEmDias
}

function formaTdiasPesquisa(diasFormatados) {

    const rawDate = diasFormatados;

    const [year, month, day] = rawDate.split("-");
    const invertedDate = `${day}/${month}/${year}`;


    return invertedDate
}

function limparCorpo_tabela() {

    const tbody = document.getElementById('corpo-tabela-relatorio');
    tbody.innerHTML = '';
}

console.log('carregando tratativa.js');