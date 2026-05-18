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
    list_dados();
});


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


function list_dados() {

    $.ajax({
        url: '/api/listRelatorio',
        type: 'GET',
        dataType: 'json', // Define que você espera JSON de retorno
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        success: function (response) {
            console.log('Resposta da api vindo com a lista para criar a tabela dinamica', response);
            if (response.status == 2) {
                console.warn('DADOS criar a tabela dinamica', response.dados);

                toAssemble(response.dados);

            } else {
                console.log('Outro status:', response.message);
            }
        },
        error: function (error) {
            console.error('Erro na requisição:', error);
        }
    });

}
async function toAssemble(dadosTabela) {

    console.info('DADOS ENVIADO PARA A INHA TABELA E ENVIAR OS DADOS');

    const table = document.getElementById('table-relatorio');

    const dadosLista = await carrregarDados();

    const listaOpcoes = dadosLista[0]['status_tratativa'];


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

    console.log('dias enviado', diasFormatados);
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

console.log('carregando tratativa.js');