const App_consultas = {

    tipo: 'n_cliente',
    tctrid: '',
    tctraut: '',
    tipo_busca_c: '',
    dados: JSON.parse(localStorage.getItem('dados_clientes')) || [], //LISTA COM O RETORNO
    selecionados: [],
    url_consulta : 'https://beta.proscore.com.br/prodecision/api/listar_consultas.php',
    url_consulta_beta : '/prodecision/api/listar_consultas.php',
    url_doc : 'https://beta.proscore.com.br/prodecision/api/exibir_dados_consulta.php',
    url_upload : "https://beta.proscore.com.br/prodecision/api/upload_documentacao.php",
    url_agentes : "https://beta.proscore.com.br/prodecision/api/listar_agentes_comite_consulta.php",
    url_agentes_insert : "https://beta.proscore.com.br/prodecision/api/inserir_analise_agente_comite.php",
    url_agentes_selecionar : "https://beta.proscore.com.br/prodecision/api/selecionar_agentes_comite_consulta.php",
    url_agentes_comite_acoes : "https://beta.proscore.com.br/prodecision/api/selecionar_agentes_comite_cnsid.php",
    url_lista_documentos : "https://beta.proscore.com.br/prodecision/api/listar_documentacao.php",
    url_atribuir : "https://beta.proscore.com.br/prodecision/api/atribuir_consulta_a_mim.php",
    url_listar_alcadas : "https://beta.proscore.com.br/prodecision/api/verifica_alcadas_contrato.php",
    url_acoes_consutlas : "https://beta.proscore.com.br/prodecision/api/executar_acao_consulta.php",
    url_lista_status : "https://beta.proscore.com.br/prodecision/api/listar_acoes_consulta.php",
    url_lista_status_all : "https://beta.proscore.com.br/prodecision/api/listar_acoes_consulta_all.php",
    url_lista_comite_chat : "https://beta.proscore.com.br/prodecision/api/listar_consultas_comite_chat.php",
    tipo_button: 'radio-todos',
    tipoSelection: 'radio-selecionado',
    nivel_maximo: 3,
    tipo_selecionados_: 1,
    tipo_acao: {
        'btn-incluir': 2,
        'btn-desfazer': 1,
        'btn-remover': 4,
        'btn-save-limite': 5
    },
    tipo_acao_radio_todos: 3
}

console.log('SAINDO PARA CRIAR AS TABELAS VIA JS NA CONSULTAS FEITAS');


var lista = [
     {
    'NOME': 'Melissa Penteado (Bord)',
     },{
    'NOME': 'Anderson Oliveira (Compliance)',
     },
     {
    'NOME': 'Diego Paulista (Integridade)',
     }

    ]


var dados = [
    {
        LOGS: '06/08/2026',
        CONSULTA: 'CONSULTA DE INTEGRIDADE PF',
        DOCUMENTO: '6767267372163127',
        SCORE: 900,
        DECISION: 'MUITO ALTO',
        STATUS: 4
    },
    {
        LOGS: '07/08/2026',
        CONSULTA: 'CONSULTA DE INTEGRIDADE PJ',
        DOCUMENTO: '1234567890123456',
        SCORE: 820,
        DECISION: 'ALTO',
        STATUS: 3
    },
    {
        LOGS: '08/08/2026',
        CONSULTA: 'CONSULTA DE RISCO',
        DOCUMENTO: '9876543210987654',
        SCORE: 650,
        DECISION: 'MEDIO',
          STATUS: 2
    },
    {
        LOGS: '09/08/2026',
        CONSULTA: 'CONSULTA DE CRÉDITO',
        DOCUMENTO: '4567891234567890',
        SCORE: 430,
        DECISION: 'BAIXO',
        STATUS: '1'
    }
];


var status_ = [
    {
        LOGS: '09/08/2026',
        USUARIO: '417039-ANDERSON',
        SETOR: 'Compilance',
        STATUS: 'APROVADO',
        JUSTIFICATIVA: 'BAIXO RISCO'
    } , {
        LOGS: '10/08/2026',
        USUARIO: '417039-ANDERSON',
        SETOR: 'Compilance',
        STATUS: 'RECUSADO',
        JUSTIFICATIVA: 'BAIXO RISCO'
    }  ,{
        LOGS: '09/08/2026',
        USUARIO: '417039-ANDERSON',
        SETOR: 'Compilance',
        STATUS: 'CANCELADO',
        JUSTIFICATIVA: 'BAIXO RISCO'
    } , {
        LOGS: '09/08/2026',
        USUARIO: '417039-ANDERSON',
        SETOR: 'Compilance',
        STATUS: 'EM ANALISE',
        JUSTIFICATIVA: 'BAIXO RISCO'
    }, {
        LOGS: '09/08/2026',
        USUARIO: '417039-ANDERSON',
        SETOR: 'Compilance',
        STATUS: 'APROVADO',
        JUSTIFICATIVA: 'BAIXO RISCO'
    }
]

   if (typeof $ !== 'undefined') {
            //  create_table_consultas("#lista_consulta",dados);
                    // create_table_consultas("#lista_acao", dados);
                    // create_table_consultas("#lista_comite", dados);
                    // create_table_consultas("#lista_status", status_);
                    lista_consultas_acao();
                    // lista_status_all();
        } else {
            /* fallback: aguarda o jQuery do view_menu carregar */
            window.addEventListener('load', function() {
                if (typeof $ !== 'undefined') {
                    lista_consultas_acao();
                    // lista_status_all();
                    // create_table_consultas("#lista_consulta",dados);
                    // create_table_consultas("#lista_acao", dados);
                    // create_table_consultas("#lista_comite", dados);
                    // create_table_consultas("#lista_status", status_);
                }
            });
        }

//  create_table_consultas(dados);

// new DataTable('#listaConsulta', {
//     data: dados,
//     columns: [
//         { data: 'LOGS', title: 'LOGS' },
//         { data: 'CONSULTA', title: 'CONSULTA' },
//         { data: 'DOCUMENTO', title: 'DOCUMENTO' },
//         { data: 'SCORE', title: 'SCORE' },
//         { data: 'DECISION', title: 'DECISION' },
//         { data: 'DECISION', title: 'DECISION' },
//         { data: 'DECISION', title: 'DECISION' },
        
//     ]
// });


function create_table_consultas(idTabela, dados) {

console.log(idTabela);
var colunas = []; 

if (idTabela == '#lista_status'){

  colunas = [ 
            {data: 'log', defaultContent: '-',
            data: null,
                orderable: false,
                render: function (data,type,row,metea){
                    var texto = data.log;  
                    return quebrarTexto(texto,type,row,metea)
                }
            },
            { data: 'cnsid' , defaultContent: '-'}, 
            {data: 'consulta', defaultContent: '-'}, 
            { data: 'documento', defaultContent: '-'},
            {data: 'score', defaultContent: '-' ,
                 data: null,
            orderable: false,
            render: function (data, type, row, meta) {
                return switch_pontuacao(data, type, row, meta);
            }
        
            },
            {data: 'decisao', defaultContent:'-',
                data: null,
                orderable: false,
                    render: function (data, type, row, meta) {
                return switch_color(data, type, row, meta);
            }
            },
            { data: 'status' , defaultContent: '-',
                data: null,
                orderable: false,
                  render: function (data, type, row) {
            return render_status(data, type, row);
        }
            },
           
        ]
}
  else if (idTabela == '#lista_consulta' || idTabela == '#lista_acao' ) {
 colunas = [
    //   {
    //       data: null,
    //       orderable: false,
    //       render: function (data, type, row, meta) {
    //       return checkbox_tabela(data, type, row, meta);

    //   }
    //   },
        { data: 'cnsid', defaultContent: '-' },
        { data: 'data_consulta', defaultContent: '-' ,
             data: null,
            orderable: false,
            render: function (data,type,row,metea){
                 var texto = data.data_consulta;  
                return quebrarTexto(texto,type,row,metea)
            } 
        },
      
        { data: 'consulta', defaultContent: '-'},
        { data: 'documento', defaultContent: '-',
            data: null,
            orderable: false,
            render: function (data, type, row, meta) {
                return render_doc_consulta(data, type, row, meta);
            }
         },
        { data: 'score', defaultContent: '-',
            data: null,
            orderable: false,
            render: function (data, type, row, meta) {
                return switch_pontuacao(data, type, row, meta);
            }
        }
    ];

  
      colunas.push({
            data: null,
            orderable: false,
            render: function (data, type, row, meta) {
                return switch_color(data, type, row, meta);
            }
        });
    

    //  última coluna de ações 
    colunas.push({
        data: null,
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
            return renderActions(data, type, row);
        }
    });
} 
else if (idTabela == '#lista_comite_chat') {
    console.log('lista para minha tabela de chat')
   colunas = [
        { data: 'cnsid', defaultContent: '-' },
        // { data: 'data_consulta', defaultContent: '-' },
        { data: 'consulta', defaultContent: '-' },
        { data: 'documento', defaultContent: '-' },
        { data: 'status', defaultContent: '-' },
        { data: 'justificativa', defaultContent: '-' },
        { data: 'setor', defaultContent: '-' },
        { data: 'score', defaultContent: '-',
            data: null,
            orderable: false,
            render: function (data, type, row, meta) {
                return switch_pontuacao(data, type, row, meta);
            }
        },
        { data: null,
                    render: function(data, type, row) {

                        return `
                            <button 
                                type="button"
                                class="btn btn-primary btn-mensagens btn-mostrar-filtros"
                                data-cnsid="${row.cnsid}">
                                Ver mensagens
                            </button>`; },
        },
    
        
    ];

}
else if (idTabela == "#lista_comite"){

    colunas = [
      { data: null,
         orderable: false,
            render: function (data, type, row, meta) {
            return checkbox_tabela(data, type, row, meta);

      }
      },
       { data: 'data_consulta', defaultContent: '-',
         data: null,
            orderable: false,
            render: function (data,type,row,metea){
                 var texto = data.data_consulta;  
                return quebrarTexto(texto,type,row,metea)
            } 

        },
        // { data: 'cnsid', defaultContent: '-' },
        { data: 'consulta', defaultContent: '-' },
        { data: 'documento', defaultContent: '-' },
        { data: 'score', defaultContent: '-',
            data: null,
            orderable: false,
            render: function (data, type, row, meta) {
                return switch_pontuacao(data, type, row, meta);
            }
        }
    ];

  
    //   colunas.push({
    //         data: null,
    //         orderable: false,
    //         render: function (data, type, row, meta) {
    //             return switch_color(data, type, row, meta);
    //         }
    //     });
    

    // //  última coluna de ações 
    // colunas.push({
    //     data: null,
    //     orderable: false,
    //     searchable: false,
    //     render: function (data, type, row) {
    //         return renderActions(data, type, row);
    //     }
    // });
    colunas.push({
        data: null,
        orderable: false,
        searchable: false,
        render: function (data, type, row) {

            return parseInt(row.score)  > 500
                ? 'aprovado'
                : 'em análise';
        }
    });
}else if (idTabela == "#lista_status_msg"){

  colunas = [ 
            {data: 'log', defaultContent: '-'}, 
            {data: 'usuario', defaultContent: '-' },
            { data: 'setor', defaultContent: '-'},
            { data: 'status' , defaultContent: '-',
                data:null,
                orderable: false,
                serverSide: false,
                render: function(data,type,row){
                     return switch_status_consulta(data, type, row);
                } 
            },
            {data: 'justificativa', defaultContent:'-'},]
     
            $("#modal_status_msg").modal('show');
    // console.log('estou chegando aqui');
}
    console.log('para montar' , dados);




    const tabelaAtual = $(idTabela).DataTable({
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
                // buttons: [
                //     'excel',
                //     // {
                //     //     text: 'Marcar todos',
                //     //     action: function () {

                //     //         tabela_indicadores.rows().nodes().to$().find('input[type="checkbox"]').each(function () {

                //     //             console.log($(this)).val();
                //     //             App.selecionados.push($(this).val());
                //     //         });

                //     //         console.log(App.selecionados);
                //     //     }
                //     // }
                // ]
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
        data: dados ? dados : [],
        columns: colunas,
        drawCallback: function (settings) {
            let api = this.api();
            api.rows().every(function () {
            //     let row = this.data();
            //     let cell = $(this.node()).find('td').eq(4); /// coluna limite de dados

            //     cell.text("Carregando!...");
            //     // $('#process_id').val(row.processo_id);

            //     get_limit_contrato(row.rdeljactr).then(dados => {
            //         const actionCell = $(this.node()).find('td').eq(5);
            //         const isEmptyLimit = dados == null || dados === '' || dados === 'VAZIO' || dados === '-';

            //         if (isEmptyLimit) {
            //             cell.text('-');
            //             actionCell.find('.acoes-cell').hide();
            //             return;
            //         }

            //         cell.html(dados);
            //         actionCell.find('.acoes-cell').show();

            //     }).catch(err => {
            //         console.error(err);
            //         cell.text('-');
            //         $(this.node()).find('td').eq(5).find('.acoes-cell').hide();
            //     });

            });

        }
    });

     if(idTabela == '#lista_consultas'){
        idTabela.columns([2]).search('Search Keyword').draw();

     } 
    
    if (idTabela == '#lista_comite') {
        App_consultas.selecionados = [];

        $(idTabela).off('change', 'input.select-checkbox').on('change', 'input.select-checkbox', function () {
            const rowData = tabelaAtual.row($(this).closest('tr')).data();
            const idSelecionado = rowData && rowData.cnsid ? rowData.cnsid : $(this).val();
            const jaSelecionado = App_consultas.selecionados.includes(idSelecionado);

            if (this.checked && !jaSelecionado) {
                App_consultas.selecionados.push(idSelecionado);
            }

            if (!this.checked && jaSelecionado) {
                App_consultas.selecionados = App_consultas.selecionados.filter(item => item !== idSelecionado);
            }

            console.log('Selecionados:', App_consultas.selecionados);
        });

        const selecionados = tabelaAtual.rows().nodes().to$().find('input[type="checkbox"]:checked').map(function () {
            return $(this).val();
        }).get();

        // if (selecionados.length === 0) {
        //     alert('Por favor, selecione a caixa para prosseguir.');
        //     return;
        // }

        App_consultas.selecionados = selecionados;
    }
}

  function checkbox_tabela(data, type, row, meta) {
       return `
        <input type="checkbox" class="select-checkbox" value="${row.cnsid}">
        <input type="hidden" name="dd" value="${row.cnsid}">
    `;
    }

function switch_color(data, type, row, meta) {
    const decisao = row && row.decisao ? row.decisao : null;



    switch (decisao) {
        // case 'MUITO ALTO':
        //     return `<span class="statusC muito">${decisao}</span>`;
        // case 'ALTO':
        //     return `<span class="statusC alto">${decisao}</span>`;
        // case 'MODERADO':
        //     return `<span class="statusC moderado">${decisao}</span>`;
        // case 'BAIXO':
        //     return `<span class="statusC baixo">${decisao}</span>`;
        // default:
        //     return `<span class="statusC processamento">${decisao}</span>`; 
        case 'CADASTRO COMPLETO':
            return `<span class="statusC baixo">${decisao}</span>`;
         case 'CREDITO APROVADO':
            return `<span class="statusC baixo">${decisao}</span>`;
        case 'APROVADO':
            return `<span class="statusC alto">${decisao}</span>`;
        case 'CREDITO NEGADO - RECEITA':
            return `<span class="statusC muito">${decisao}</span>`;
        case 'CREDITO NEGADO - RECEITA':
            return `<span class="statusC alto">${decisao}</span>`;
        default:
            return `<span class="statusC processamento">${decisao}</span>`;
    }
}

function switch_status_consulta(data, type, row, meta) {
    var decisao = row && row.status ? row.status : null;

    const de = decisao.toString().toLowerCase();

     switch (de) {
      
        case 'aprovar':
            return `<span class="statusC baixo">${de}</span>`;
         case 'recusar':
            return `<span class="statusC alto">${de}</span>`;
        case 'cancelado':
            return `<span class="statusC moderado">${de}</span>`; 
        case 'cancelar':
            return `<span class="statusC alto">${de}</span>`;
        case 'em analise':
            return `<span class="statusC muito">${de}</span>`;
        case 'aprovado':
            return `<span class="statusC muito">${de}</span>`;
             case 'reprovar':
            return `<span class="statusC baixo">${de}</span>`;
        default:
            return `<span class="statusC processamento">${de}</span>`;
    }
}


function switch_pontuacao(data, type, row, meta) {
    const pontuacao = row && row.score ? row.score : null;
    
    switch (true) {
        case pontuacao > 500:
            return `<span class="statusC alto">${pontuacao}</span>`;
        case pontuacao > 100:
            return `<span class="statusC muito">${pontuacao}</span>`;
        case pontuacao < 100:
            return `<span class="statusC alto">${pontuacao}</span>`;
        default:
            return `<span class="statusC processamento">${pontuacao}</span>`;
    }
}

function renderActions(data, type, row) {
    const cnsid = row.cnsid; 
    const rowData = encodeURIComponent(JSON.stringify(row));


    // retorno_status = lista_status_cnid(cnsid);
    // // console.log(cnsid);

    

    return `
    <div class="acoes-cell"> 
        <button type="button" 
                class="btn-acao btn-action" 
                title="Ação"
                onclick="navegarParaAcao('${rowData}')">
            <img src="img/icons8-aperto-de-mão-24.png" width="14" height="14" class="icon" alt="Ação">
        </button>
    
    <button type="button" class="btn-comite btn-action" data-tipo-cns-comite="${cnsid}"title="Comite"> 
        <img src="img/icons8-lote-de-atribuir-50.png" width="14" height="14" class="icon" alt="">
    </button> 
    
    <button type="button" class="btn-status btn-action" data-tipo-status="${cnsid}" title="Status"> 
        <img src="img/relogio.png" width="14" height="14" class="icon" alt="">
    </button> 
    
     <button type="button" class="btn-doc-lista btn-action" data-tipo-cns="${cnsid}" title="Listar Documentos enviados"> 
        <img src="img/icons8-baixar-24.png" width="14" height="14" class="icon" alt="">
    </button> 
    <button type="button" class="btn-enviar-documentos btn-action" data-tipo-cns-documento="${cnsid}" title="Enviar Documentos"> 
        <img src="img/icons8-upload-para-nuvem-32.png" width="14" height="14" class="icon" alt="">
    </button> 
    
    <button type="button" class="btn-reconsultar btn-action" data-tipo="" title="Reconsultar"> 
        <img src="img/icons8-laço-oval-48.png" width="14" height="14" class="icon" alt="">
    </button> 

     <button type="button" class="btn-decision btn-action" data-tipo-cns="${cnsid}" title="Decision"> 
        <img src="img/decision_icone.png" width="25" height="25" class="icon" alt="Decision">
    </button> 
</div>
`;

}function render_status(data, type, row) {
    const rowData = encodeURIComponent(JSON.stringify(row));
    return `
    <div class="acoes-cell"> 
      <button type="button" class="btn-status-dados btn-action" data-tipo-status-dados="${rowData}" title="Status"> 
        <img src="img/relogio.png" width="14" height="14" class="icon" alt="">
    </button> 
    </div>`;

}


// $(document).on('click', '.btn-status-dados', function () {
   
// });


function navegarParaAcao(rowDataString) {
    // Decodifica e recupera os dados originais
    const rowData = JSON.parse(decodeURIComponent(rowDataString));
    
    // Salva no sessionStorage (apaga quando fechar a aba) ou localStorage
    sessionStorage.setItem('dadosLinha Selecionada', JSON.stringify(rowData));
    
    // Redireciona para a sua rota de ação
     const url = new URL(window.location.href);
    const rotaAtual = url.searchParams.get('r');

    // if (rotaAtual === 'proDecisionStatus' || window.location.pathname.includes('/proDecisionStatus')) {
    //     lista_status_all();
    // }
    window.location.href = `https://beta.proscore.com.br/prodecision/index.chp?r=proDecisionAcao&tctrid=${App_consultas.tctrid}&tctraut=${App_consultas.tctraut}`;
}
     
function render_doc_consulta(data, type, row) {
    const cnsid = row.cnsid; 
    return `
     <div class="acoes-cell"> 
     <span >${row.documento}</span>
     <button  style="display:flex;flex-direction:column;line-height:1.10;" type="button" class="btn-doc btn-action" data-tipo-cns="${cnsid}"title="Doc"> 
      <img src="img/escrito.png" width="14" height="14" class="icon" alt="">
    </button> </div>`
    ;

}
     

function quebrarTexto(texto, type, row, limite) {
    // console.log("texot reenviado",  texto)
    
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
    const retorno_texto = quebrarTexto(data, App.limite_linhas_p);
    return retorno_texto;
}
// PEGAR O CHECKBOX DO COMITE PARA LIBERAR UM MODAL 
function listar_agentes(lista){
// $(document).ready(function() {
   
    let container = document.getElementById('lista-checkboxes');

    function limparListaAgentes() {
        if (container) {
            container.innerHTML = '';
        }
    }

    $('#ListarComiteModal').on('hidden.bs.modal', function () {
        $('#comiteSwitch').prop('checked', false);
        limparListaAgentes();
    });

    $('#comiteSwitch').on('change', function () {
        if ($(this).is(':checked')) {
            console.log('Switch ATIVADO - Abrindo modal...');
            limparListaAgentes();

            $('#ListarComiteModal').modal('show');

            console.log(lista);
            $.each(lista, function (_, val) {
                console.log(val.nome_agente);

                let div = document.createElement('div');
                // div.className = 'form-check-input me-1';
                div.id = 'agentes';

                let checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'lista_checkbox';
                checkbox.id = 'item_' + val.contrato;
                checkbox.value = val.contrato;
                checkbox.name = 'itens[]';

                let label = document.createElement('label');
                label.htmlFor = 'item_' + val.nome_agente;
                label.textContent = val.nome_agente;

                div.appendChild(checkbox);
                div.appendChild(label);
                container.appendChild(div);
            });
        } else {
            console.log('Switch DESATIVADO');
            $('#ListarComiteModal').modal('hide');
            $('#comiteSwitch').prop('checked', false);
            limparListaAgentes();
        }
    });
// });
}

$(document).ready(function() {
	
    // console.log($("#d-id-tctrid").val());
    App_consultas.tctrid = $("#d-id-tctrid").val();
    App_consultas.tctraut = $("#d-tctraut").val();

    // console.log( App.tctrid)
listar_consultas();

listar_consultas_chat();
listar_agentes_comite_consulta();
});
 
function listar_consultas(){
 
	let formData = new FormData();
	formData.append('tctrid',  App_consultas.tctrid);
	formData.append('tctraut',  App_consultas.tctraut);

    // return
	$.ajax({
		// url: "/prodecision/api/listar_consultas.php",
		url: App_consultas.url_consulta,
	    type: 'POST',
	    data:  formData,
	    processData: false,
	    contentType: false,
	    async: true,
	    statusCode: {
	    	200: function(response) {
                console.log(response);
                console.log('tenho o retorno da api com os dados');
                create_table_consultas("#lista_consulta",response.registros);
                create_table_consultas("#lista_comite", response.registros);
	    		$.each( response.registros, function( key, value ) {
	    			//value.cnsid
	    			//value.data_consulta
	    			//value.consulta
	    			//value.documento
	    			//value.score
	    			//value.decisao
	    		});
		    },
		    422: function(response) {
		    	$.each( response.responseJSON.errors, function( key, value ) {
		    		//erros
		    	  	console.log(key+value);
				});
		    },
		    401:function(response) {
		      alert(response.responseText);
		    },
		    500: function(response) {
		      alert(response.responseText);
		    } 
		}
	});
}

let dadosAgrupados = {};
function listar_consultas_chat(){
 
	let formData = new FormData();
	formData.append('tctrid',  App_consultas.tctrid);
	formData.append('tctraut',  App_consultas.tctraut);

    // return
	$.ajax({
		// url: "/prodecision/api/listar_consultas.php",
		url: App_consultas.url_lista_comite_chat,
	    type: 'POST',
	    data:  formData,
	    processData: false,
	    contentType: false,
	    async: true,
	    statusCode: {
	    	200: function(response) {
                console.log(response.registros);
                r = response.registros; 
                
                dadosAgrupados = {};

        r.forEach(function(item) {

            const id = item.cnsid;

            if (!dadosAgrupados[id]) {

                dadosAgrupados[id] = {
                    cnsid: item.cnsid,
                    documento: item.documento,
                    consulta: item.consulta,
                    score: item.score,
                    decisao: item.decisao,
                    status: item.status,
                    justificativa: item.justificativa,
                    setor: item.setor,
                    mensagens: []
                };
            }

            dadosAgrupados[id].mensagens.push(item);
        });

        const dadosTabela = Object.values(dadosAgrupados);

        console.log('AGRUPADO:', dadosTabela);
                create_table_consultas("#lista_comite_chat", dadosTabela);
	    		$.each( response.registros, function( key, value ) {
	    			//value.cnsid
	    			//value.data_consulta
	    			//value.consulta
	    			//value.documento
	    			//value.score
	    			//value.decisao
	    		});
		    },
		    422: function(response) {
		    	$.each( response.responseJSON.errors, function( key, value ) {
		    		//erros
		    	  	console.log(key+value);
				});
		    },
		    401:function(response) {
		      alert(response.responseText);
		    },
		    500: function(response) {
		      alert(response.responseText);
		    } 
		}
	});
}
// CRIAR FUNCOES PARA ABIR OS MODAIS DE FORMA AUTOMATICA
  $('#lista_consulta').on('click', '.btn-acao', function() {

     console.log('TENHO O CLICK NO ACAO');

      $("#modal_action").modal('show');
 });

// CRIAR FUNCOES PARA ABIR OS MODAIS DE FORMA AUTOMATICA
  $('#lista_status').on('click', '.btn-status-dados', function() {
    const dadosLinha = JSON.parse(decodeURIComponent($(this).attr('data-tipo-status-dados')));

    var dados_msg = dadosLinha.mensagens;
    console.log('minha mensagens', dados_msg);

    

    create_table_consultas('#lista_status_msg', dados_msg);






    // tipo-status-dados  
    
    // var codCnscomite = $(this).data('tipo-status-dados');
    // console.log(codCnscomite);
    //  console.log('TENHO O CLICK NO ACAO PARA APARECER OS DADOS DE  APROVADO ');

    // const escapeHtml = valor => $('<div>').text(valor == null ? '-' : String(valor)).html();
    // const linhas = Object.keys(dadosLinha).map(chave => `
    //     <tr>
    //         <th>${escapeHtml(chave)}</th>
    //         <td>${escapeHtml(dadosLinha[chave])}</td>
    //     </tr>`).join('');

    // let $modal = $('#modal-dados-linha');
    // if (!$modal.length) {
    //     $('body').append(`
    //         <div class="modal fade" id="modal-dados-linha" tabindex="-1" role="dialog" aria-hidden="true">
    //             <div class="modal-dialog modal-lg" role="document">
    //                 <div class="modal-content">
    //                     <div class="modal-header">
    //                         <h5 class="modal-title">Dados da consulta</h5>
    //                         <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
    //                     </div>
    //                     <div class="modal-body"><table class="table table-sm table-striped mb-0"><tbody></tbody></table></div>
    //                 </div>
    //             </div>
    //         </div>`);
    //     $modal = $('#modal-dados-linha');
    // }

    // $modal.find('tbody').html(linhas || '<tr><td>Nenhum dado encontrado.</td></tr>');
    // $modal.modal('show');

    //   $("#modal_action").modal('show');
 });

 $('#lista_consulta').on('click', '.btn-comite', function() {

     console.log('TENHO O CLICK NO BTN COMITE');
    //   data-tipo-cns-comite
      var codCnscomite = $(this).data('tipo-cns-comite');
      $('#id_comite_motvo').val($(this).data('tipo-cns-comite'));

      
          verificar_comite(codCnscomite);
  });
 $('#lista_consulta').on('click', '.btn-status', function() {

      var cod_status = $(this).data('tipo-status');

     console.log('TENHO O CLICK NO ACAO' , cod_status);

     if (cod_status){
         //chamar listar os status
         lista_status_cnid(cod_status)
     }

      $("#modal_status").modal('show');

      
});


 $('#lista_consulta').on('click', '.btn-doc', function() {

    $("#modal_doc").modal('show');
    console.log('TENHO O CLICK NO ACAO');
    var cns = $(this).data("tipo-cns");
    var iframeUrls = App_consultas.url_doc +
        "?tctrid=" + encodeURIComponent(App_consultas.tctrid) +
        "&tctraut=" + encodeURIComponent(App_consultas.tctraut) +
        "&cnsid=" + encodeURIComponent(cns);

    console.log('URL IFRAME:', iframeUrls);

    var iframe = $('#doc_iframe');

    console.log('iframe encontrado:', iframeUrls.length);

    iframe.attr('src', iframeUrls);
    $('#id_cns_enviado').val(cns);

    console.log('iframe src:', iframe.prop('src'));

 });



 $('#lista_consulta').on('click', '.btn-doc-lista', function() {

    $("#modal_doc_listar").modal('show');
   listar_documentacao();
    // vou chamar a api parara lista os documentos 



 });

 $('#lista_consulta').on('click', '.btn-reconsultar', function() {

    alert('Funcionalidade está desabilitada no momento');
     
      
}); $('#lista_consulta').on('click', '.btn-decision', function() {

    alert('Funcionalidade está desabilitada no momento');
     
      
});

 $('#lista_consulta').on('click', '.btn-enviar-documentos', function() {

     console.log('TENHO O CLICK NO ACAO');
     console.log($(this).data("tipo-cns-documento"));

      $("#id_cns_arquivo").val($(this).data("tipo-cns-documento"));
      $("#modal_reconsultar").modal('show');
     
      
});


$("#click_salvar_pdf").click(function(e) {
    e.preventDefault(); 

    console.log('TENHO O CLICK NO ACAO - GERANDO PDF');
    
    
    var cnsId = $('#id_cns_enviado').val();
    var iframeUrl = App_consultas.url_doc +
        "?tctrid=" + encodeURIComponent(App_consultas.tctrid) +
        "&tctraut=" + encodeURIComponent(App_consultas.tctraut) +
        "&cnsid=" + encodeURIComponent(cnsId);

        console.log('id do cns:', cnsId);

     $("#arquivo_nome").val("proScore_consulta_" + cnsId + ".pdf");
     var conteudoIframe = $("iframe").contents().find("body").html();

    var htmlCodificado = btoa(unescape(encodeURIComponent(conteudoIframe)));

   $("#conteudo_salvar").val(htmlCodificado);
    $("#form_salvar").submit();
});


// PARA PEGAR O CLICKS CHECKOS
$('#aprovar_comite').on('submit', function(event) {
        event.preventDefault();

        console.log('tenho o click para o comite');
        const selecionados = document.querySelectorAll('#lista-checkboxes input[type="checkbox"]:checked');
        const agentes_Marcados = Array.from(selecionados).map(checkbox => checkbox.value);
        console.log(agentes_Marcados);

         if (App_consultas.selecionados.length == 0) {
            alert('Por favor, selecione a as consultas.');
            return;
        }

          App_consultas.selecionados
          associonar_agentes(App_consultas.selecionados,agentes_Marcados)

});

$('#form_comite').on('submit', function(event) {
        event.preventDefault();

        console.log($("#id_comite_motvo").val());
        const valorAtual = selectStatus.value;
        const text = justificativa_comite.value;
        console.log("Valor atual:", valorAtual);

        insert_motivo_agentes(valorAtual,text,$("#id_comite_motvo").val())

});

$('#envio_arquivo').on('submit', function(event) {
        event.preventDefault();
    
    console.log("Dados capturados no submit:", dados);

    var id_cns_arquivo = $("#id_cns_enviado").val();

    var campoArquivo = $('#formFile')[0]; 
    var arquivo = campoArquivo.files[0]; 

    // Pega o arquivo selecionado
    // var arquivo = event.target.files[0];
    
    if (arquivo) {
        var reader = new FileReader();
        
        // Define o que fazer quando a leitura terminar
        reader.onload = function(e) {
            var base64Completo = e.target.result; // String Base64 com o prefixo data:
            upload_documentacao(base64Completo,id_cns_arquivo);
        }
 
        // Lê o arquivo como URL de dados (Base64)
         reader.readAsDataURL(arquivo);
    }
});
 
 
function upload_documentacao(base64Completo,id_cns_arquivo){
 
    let formData = new FormData();

    formData.append('tctrid', App_consultas.tctrid);
    formData.append('tctraut', App_consultas.tctraut);
    formData.append('cnsid', id_cns_arquivo);
    formData.append('descricao', "teste");
    formData.append('base64Completo', base64Completo);

    for (const [chave, valor] of formData.entries()) {
     console.log(`${chave}: ${valor}`);
    }
    
    // return
$.ajax({
url: App_consultas.url_upload,
    type: 'POST',
    data:  formData,
    processData: false,
    contentType: false,
    async: true,
    statusCode: {
     200: function(response) {
     alert(response.mensagem);
    },
    422: function(response) {
     $.each( response.responseJSON.errors, function( key, value ) {
     //erros
        console.log(key+value);
});
    },
    401:function(response) {
      alert(response.responseText);
    },
    500: function(response) {
      alert(response.responseText);
    }
}
});
}


// BUSCAR AGENTES 
function listar_agentes_comite_consulta(){

 
	let formData = new FormData();
	formData.append('tctrid',  App_consultas.tctrid);
	formData.append('tctraut',  App_consultas.tctraut);
 	$.ajax({
		// url: "/prodecision/api/listar_consultas.php",
		url: App_consultas.url_agentes,
	    type: 'POST',
	    data:  formData,
	    processData: false,
	    contentType: false,
	    async: true,
	    statusCode: {
	    	200: function(response) {
                console.log(response);
                console.log('tenho o retorno da api com os dados agentes');
                listar_agentes(response.agentes)
                // create_table_consultas("#lista_consulta",response.agentes);
	    	
		    },
		    422: function(response) {
		    	$.each( response.responseJSON.errors, function( key, value ) {
		    		//erros
		    	  	console.log(key+value);
				});
		    },
		    401:function(response) {
		      alert(response.responseText);
		    },
		    500: function(response) {
		      alert(response.responseText);
		    } 
		}
	});
}

// BUSCAR AGENTES POR CNS 
function listar_agentes_comite_consulta_cns(){
 
	let formData = new FormData();
	formData.append('tctrid',  App_consultas.tctrid);
	formData.append('tctraut',  App_consultas.tctraut);
 
	//filtro
	//formData.append('documento', "34616883807");
	//formData.append('consulta', "CONSULTA PLUGIN 5040,5041,5043 E 5044");
	//formData.append('score', "200");
	//formData.append('decisao', "CADASTRO COMPLETO");
	//formData.append('data_consulta_inicio', "01/08/2026");
	//formData.append('data_consulta_final', "29/08/2026");
 
    // return
	$.ajax({
		// url: "/prodecision/api/listar_consultas.php",
		url: App_consultas.url_agentes,
	    type: 'POST',
	    data:  formData,
	    processData: false,
	    contentType: false,
	    async: true,
	    statusCode: {
	    	200: function(response) {
                console.log(response);
                console.log('tenho o retorno da api com os dados agentes');
                listar_agentes(response.agentes)
                // create_table_consultas("#lista_consulta",response.agentes);
	    	
		    },
		    422: function(response) {
		    	$.each( response.responseJSON.errors, function( key, value ) {
		    		//erros
		    	  	console.log(key+value);
				});
		    },
		    401:function(response) {
		      alert(response.responseText);
		    },
		    500: function(response) {
		      alert(response.responseText);
		    } 
		}
	});
}


// BUSCAR AGENTES 
function insert_motivo_agentes(status,justificativa,cnd_id){
 
	let formData = new FormData();
	formData.append('tctrid',  App_consultas.tctrid);
	formData.append('tctraut',  App_consultas.tctraut);
	formData.append('cnsid', cnd_id);
	formData.append('status', status);
    formData.append('justificativa',  justificativa);
 
//     for (const [chave, valor] of formData.entries()) {
//   console.log(`${chave}: ${valor}`);
// }
    
//     return
	$.ajax({
		// url: "/prodecision/api/listar_consultas.php",
		url: App_consultas.url_agentes_insert,
	    type: 'POST',
	    data:  formData,
	    processData: false,
	    contentType: false,
	    async: true,
	    statusCode: {
	    	200: function(response) {
                console.log(response);
                // console.log('tenho o retorno da api com os dados agentes');
                // listar_agentes(response.agentes)
                // create_table_consultas("#lista_consulta",response.agentes);
	    	
		    },
		    422: function(response) {
		    	$.each( response.responseJSON.errors, function( key, value ) {
		    		//erros
		    	  	console.log(key+value);
				});
		    },
		    401:function(response) {
		      alert(response.responseText);
		    },
		    500: function(response) {
		      alert(response.responseText);
		    } 
		}
	});
}


// ASSOCIAR AGENTES
function associonar_agentes(cns_id,agentes_Marcados){

    var fncoes = ['analisar'];
 
	let formData = new FormData();
	formData.append('tctrid',  App_consultas.tctrid);
	formData.append('tctraut',  App_consultas.tctraut);
	formData.append('cnsid', cns_id);
	formData.append('contratos', agentes_Marcados);
    formData.append('funcoes_contratos', fncoes);
 
//     for (const [chave, valor] of formData.entries()) {
//   console.log(`${chave}: ${valor}`);
// }
    
//     return
	$.ajax({
		// url: "/prodecision/api/listar_consultas.php",
		url: App_consultas.url_agentes_selecionar,
	    type: 'POST',
	    data:  formData,
	    processData: false,
	    contentType: false,
	    async: true,
	    statusCode: {
	    	200: function(response) {
                console.log(response);
                console.log('tenho o retorno da api com os dados agentes');
                alert(response.status);
                                // listar_agentes(response.agentes)
                // create_table_consultas("#lista_consulta",response.agentes);
	    	
		    },
		    422: function(response) {
		    	$.each( response.responseJSON.errors, function( key, value ) {
		    		//erros
		    	  	console.log(key+value);
				});
		    },
		    401:function(response) {
		      alert(response.responseText);
		    },
		    500: function(response) {
		      alert(response.responseText);
		    } 
		}
	});
}

function listar_documentacao(){
 
	let formData = new FormData();
	  formData.append('tctrid', App_consultas.tctrid);
    formData.append('tctraut', App_consultas.tctraut);
	formData.append('cnsid', "581751855");
 
	//filtro
	//formData.append('ctrid', "412866");
	//formData.append('descricao', "teste");
	//formData.append('data_cadastro_inicio', "01/08/2026");
	//formData.append('data_cadastro_final', "31/08/2026");
 
	$.ajax({
		url: App_consultas.url_lista_documentos,
	    type: 'POST',
	    data:  formData,
	    processData: false,
	    contentType: false,
	    async: true,
	    statusCode: {
	    	200: function(response) {
                console.log(response);

                lisArquivos(response.registros);
	    		
		    },
		    422: function(response) {
		    	$.each( response.responseJSON.errors, function( key, value ) {
		    		//erros
		    	  	console.log(key+value);
				});
		    },
		    401:function(response) {
		      alert(response.responseText);
		    },
		    500: function(response) {
		      alert(response.responseText);
		    } 
		}
	});
}



function lisArquivos(dados) {

    console.log(dados);

    $('#lista_documentos tbody').empty();

    $.fn.dataTable.ext.errMode = 'none';

    tabelaArquivos = $('#lista_documentos').DataTable({
        pagingType: $(window).width() < 768 ? "simple" : "simple_numbers",
        language: {
            info: "Mostrando _START_ de _END_ Total _TOTAL_ registros",
            infoEmpty: "Mostrando 0 de 0 Total 0 registros",
            infoFiltered: "(filtered from _MAX_ total registros)",
            lengthMenu: "Rolagens por páginas: _MENU_",
            search: "",
            searchPlaceholder: "Buscar na tabela...",
            paginate: {
                previous: "<i class='fas fa-angle-double-left'></i> ",
                next: " <i class='fas fa-angle-double-right'></i>  ",
            },

        },

        processing: true,
        serverSide: false,
        order: [
            [1, 'asc']
        ],

        select: {
            style: 'multi',
            selector: 'td:first-child .select-checkbox'
        },
        layout: {
            topStart: 'pageLength',
            topEnd: [{
                    buttons: [{

                    }]
                },
                'search'
            ],
            bottomStart: 'info',
            bottomEnd: 'paging',
        },
        scrollX: false,
        scrollCollapse: true,
        paging: true,
        searching: true,
        destroy: true,
        data: dados ? dados : [],

        columns: [
            {
                data: 'id',
            }, {
                data: 'cnsid',
            }, {
                data: 'ctrid',
            }, {
                data: 'formato_arquivo',
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {

                    return renderArquivos(data, type, row);

                }
            },
            {
                data: 'data_cadastro'
            },  {
                data: 'descricao'
            },
        ],
    });
}

function renderArquivos(data, type, row) {

    const tipo = row.tipo == 'diretorio' ? "folder" : "file";
    var infoColuna = ''; // Nome e tipo do arquivo
    var acaoColuna = ''; // Botão de download ou de abrir pasta

    // 1. Estrutura para ARQUIVO SIMPLES
    if (tipo == 'file') {
        infoColuna = `${row.nome_arquivo}/${tipo}`;
        acaoColuna = `<a href="javascript:;" title="Baixar" data-cod="${row.id}" data-name="${row.nome_arquivo}" data-caminho="${row.caminho}" class="btn btn-action btn-download">
                    <i class="fa fa-download"></i>
                  </a>`;
    }

    // 2. Estrutura para PASTA (Folder)
    else if (tipo == 'folder') {
        infoColuna = `${row.nome_arquivo}/${tipo}`;


        const arquivosJson = encodeURIComponent(JSON.stringify(row.arquivos || []));

        //PARA BAIXAR ARQUIVOS
        acaoColuna = ` <a href="javascript:;" title="Ver Arquivos" data-id-pasta="${row.id}" data-arquivos="${arquivosJson}" data-caminho="${row.caminhoPasta}" class="btn-open-folder">
                         <i class="fa fa-folder-open"></i> Ver Conteúdo
                       </a>`;
    }

    // 3. Fallback de Erro
    else {
        infoColuna = `<span class="statusC processamento">Falha em localizar dados</span>`;
        acaoColuna = '';
    }


    return `
    <td>${infoColuna}</td>
    <td class="text-center">
        <div class="status-actions" data-processo="${row.id}"> 
            ${acaoColuna} 
        </div>
    </td>
`;
}

$(document).on('click', '.btn-open-folder', function (e) {
    e.preventDefault();

    var pastaId = $(this).data('id-pasta');
    var arquivosRaw = $(this).data('arquivos');
    var arquivosRawcaminho = $(this).data('caminho');

    var listaArquivos = JSON.parse(decodeURIComponent(arquivosRaw));

    // Limpa a lista antiga dentro do seu modal
    var $corpoModal = $('#modalArquivos #modal-body-lista');
    $corpoModal.empty();

    if (listaArquivos.length == 0) {
        $corpoModal.append('<p>Nenhum arquivo encontrado nesta pasta.</p>');
    } else {
        // Percorre e lista os arquivos usando as propriedades exatas do seu JSON
        $.each(listaArquivos, function (index, arquivo) {
            
            // Define o nome de exibição baseado na descrição ou id
            var nomeExibicao = arquivo.descricao ? arquivo.descricao : 'Arquivo ' + arquivo.id;
            
            // Monta o caminho final para o download combinando a pasta raiz e o id/nome se necessário
            var caminhoDownload = arquivosRawcaminho + '/' + (arquivo.descricao || arquivo.id);

            var itemHtml = `
                <div class="arquivo-modal-item" style="display: flex; justify-content: space-between; margin-bottom: 5px; align-items: center;">
                    <span>
                        <i class="far fa-file-alt"></i> ${nomeExibicao} 
                        <small class="text-muted">(${arquivo.formato_arquivo})</small>
                    </span>
                    <a href="javascript:;" title="Baixar" data-cod="${arquivo.id}" data-name="${caminhoDownload}" class="btn btn-action btn-download">
                        <i class="fa fa-download"></i> Baixar
                    </a>
                </div>
            `;
            $corpoModal.append(itemHtml);
        });
    }
    $('#modalArquivos').modal('show');
});

/// PARA PEGAR O CLICK PARA BAIXAR

$(document).on('click', '.btn-download', function (e) {

    // e.preventDefault();
    // var arquivosRaw = $(this).data('name');
    // window.location.href = "progestor.chp?r=downloadFile&name=" + arquivosRaw;
});

$(document).ready(function(e){

    const url = new URL(window.location.href);
    const rotaAtual = url.searchParams.get('r');

    if (rotaAtual === 'proDecisionStatus' || window.location.pathname.includes('/proDecisionStatus')) {
        lista_status_all();
    }
});


// TRABALHA NA PAGINA DE ACÃO
function lista_consultas_acao(){

    const dadosRecuperados = JSON.parse(sessionStorage.getItem('dadosLinha Selecionada'));
    // console.log(dadosRecuperados.cnsid);


    if(dadosRecuperados){
         // INSIRO NO ATRIBUIR NO FORM 
         $("#id_cns").val(dadosRecuperados.cnsid);
        //PASSAR COMO SE FOR UM ARRAY 
        create_table_consultas("#lista_acao", [dadosRecuperados]);
         }

    console.log('VOU LISTA AS ACOES');
}

//

$('#atribuir_amim').on('submit', function(event) {
        event.preventDefault();

        console.log($(this))
        
        console.log('teve o click vindo aqui {}');
        if (confirm('Deseja Atribuir esta consulta a seu contrato?')) { 
            
            console.log("Usuário aceitou.");
            atribuir_cnid();

          } else {
            console.log("Usuário cancelou.");
        }

});

function atribuir_cnid(){
  
    let formData = new FormData();
	formData.append('tctrid', App_consultas.tctrid);
	formData.append('tctraut', App_consultas.tctraut);
	formData.append('cnsid',  $("#id_cns").val());
	
    $.ajax({
		url: App_consultas.url_atribuir,
	    type: 'POST',
	    data:  formData,
	    processData: false,
	    contentType: false,
	    async: true,
	    statusCode: {
	    	200: function(response) {

                if(!response.errors){

                                // sessionStorage.clear();
            // let dadosSimples = {
            //             tctrid: App_consultas.tctrid,
            //             tctraut: App_consultas.tctraut,
            //             cnsid: $("#id_cns").val()
            //         };
                    
                    
                    listar_atribuicoes();
                    // listar_atribuicoes(formData);

                }
                alert(response.status);

                // lisArquivos(response.registros);
	    		
		    },
		    422: function(response) {
		    	$.each( response.responseJSON.errors, function( key, value ) {
		    		//erros
		    	  	console.log(key+value);
				});
		    },
		    401:function(response) {
		      alert(response.responseText);
		    },
		    500: function(response) {
		      alert(response.responseText);
		    } 
		}
	});
}
 
function listar_atribuicoes(){
    //  console.log('TENHO O ENVIO DO FORMULARIO', formData);
	let formData = new FormData();
	formData.append('tctrid', App_consultas.tctrid);
	formData.append('tctraut', App_consultas.tctraut);
	// formData.append('cnsid',  $("#id_cns").val());
    
     $.ajax({
		url: App_consultas.url_listar_alcadas,
	    type: 'POST',
	    data:  formData,
	    processData: false,
	    contentType: false,
	    async: true,
	    statusCode: {
	    	200: function(response) {
		    if (response.alcadas_do_contrato  && response.alcadas_do_contrato.length > 0)  {
                    
                    //Esconde o botão 
                    $("#atribuir_amim").hide(); 

                    $("[id^='btnAlcada_']").prop('disabled', true);

                    response.alcadas_do_contrato.forEach(function(alcada) {
                        $("#btnAlcada_" + alcada.id_alcada).prop('disabled', false);
                    });
              } else {
                
                    $("[id^='btnAlcada_']").prop('disabled', true); 
                    
                    // Exibe o botão de atribuir novamente
                    $("#btnAtribuir").show(); 
                }
             
             if (response && response.status) {
                 alert(response.status);
             }
            },
		    422: function(response) {
                
		    	$.each( response.responseJSON.errors, function( key, value ) {
		    		//erros
		    	  	console.log(key+value);
				});
		    },
		    401:function(response) {
		      alert(response.responseText);
		    },
		    500: function(response) {
		      alert(response.responseText);
		    } 
		}
	});
}

let acaoAtual = null;

$(document).on('click', "[id^='btnAlcada_']", function() {
    let botaoClicado = $(this).attr('id');
    let campoJustificativa = $("#modalJustificativa");
    let btnConfirmar = $("#btnConfirmarAcao");
    
    
    campoJustificativa.val('');
    
    // Reseta as classes de cor do botão de confirmação
    btnConfirmar.removeClass('btn-success btn-danger btn-warning');

    
    if (botaoClicado === 'btnAlcada_2') { // APROVAR
        acaoAtual = 'aprovar';
        $("#modalTitulo").text('Justificativa para Aprovação');
        btnConfirmar.text('Aprovar').addClass('btn-success');
        
    } else if (botaoClicado === 'btnAlcada_3') { // REPROVAR
        acaoAtual = 'reprovar';
        $("#modalTitulo").text('Justificativa para Reprovação');
        btnConfirmar.text('Reprovar').addClass('btn-danger');
        
    } else if (botaoClicado === 'btnAlcada_5') { // CANCELAR
        acaoAtual = 'cancelar';
        $("#modalTitulo").text('Justificativa para Cancelamento');
        btnConfirmar.text('Cancelar Consulta').addClass('btn-warning');
    }

  
    $("#aprovarModal").modal('show');
});

// Captura o clique no botão de confirmar
$("#btnConfirmarAcao").on('click', function() {
    let justificativa = $("#modalJustificativa").val().trim();
    
    if (justificativa === "") {
        alert("Por favor, preencha a justificativa.");
        return;
    }

    console.log("Executando ação:", acaoAtual, "com a justificativa:", justificativa);
    
    if (acaoAtual == 'aprovar') {
        executaFuncao(acaoAtual,justificativa);
    } else if (acaoAtual == 'reprovar') {
        executaFuncao(acaoAtual,justificativa);
    } else if (acaoAtual == 'cancelar') {
         executaFuncao(acaoAtual,justificativa);
    }

    // Fecha o modal após o envio
    $("#aprovarModal").modal('hide');
});


function executaFuncao(status, justificativa){

	let formData = new FormData();
	formData.append('tctrid', App_consultas.tctrid);
	formData.append('tctraut', App_consultas.tctraut);
	formData.append('status',status);
	formData.append('justificativa', justificativa);
	formData.append('cnsid',  $("#id_cns").val());
    
     $.ajax({
		url: App_consultas.url_acoes_consutlas,
	    type: 'POST',
	    data:  formData,
	    processData: false,
	    contentType: false,
	    async: true,
	    statusCode: {
	    	200: function(response) {
		    if (response.alcadas_do_contrato  && response.alcadas_do_contrato.length > 0)  {
                    
                    //Esconde o botão 
                    $("#atribuir_amim").hide(); 

                    $("[id^='btnAlcada_']").prop('disabled', true);

                    response.alcadas_do_contrato.forEach(function(alcada) {
                        $("#btnAlcada_" + alcada.id_alcada).prop('disabled', false);
                    });
              } else {
                
                    $("[id^='btnAlcada_']").prop('disabled', true); 
                    
                    // Exibe o botão de atribuir novamente
                    $("#btnAtribuir").show(); 
                }
             
             if (response && response.status) {
                 alert(response.status);
             }
            },
		    422: function(response) {
                
		    	$.each( response.responseJSON.errors, function( key, value ) {
		    		//erros
		    	  	console.log(key+value);
				});
		    },
		    401:function(response) {
		      alert(response.responseText);
		    },
		    500: function(response) {
		      alert(response.responseText);
		    } 
		}
	});
}


function lista_status_cnid(cnsid){

	let formData = new FormData();
	formData.append('tctrid', App_consultas.tctrid);
	formData.append('tctraut', App_consultas.tctraut);
	// formData.append('status',status);
	// formData.append('justificativa', justificativa);
	formData.append('cnsid',  cnsid);
//     for (const [chave, valor] of formData.entries()) {
//   console.log(`${chave}: ${valor}`);
// }
    
//     return

     $.ajax({
		url: App_consultas.url_lista_status,
	    type: 'POST',
	    data:  formData,
	    processData: false,
	    contentType: false,
	    async: true,
	    statusCode: {
	    	200: function(response) {
		   
                console.log(response);
                if(response.acoes){
                  listStatus(response.acoes);
                }
            },
		    422: function(response) {
                
		    	$.each( response.responseJSON.errors, function( key, value ) {
		    		//erros
		    	  	console.log(key+value);
				});
		    },
		    401:function(response) {
		      alert(response.responseText);
		    },
		    500: function(response) {
		      alert(response.responseText);
		    } 
		}
	});
}

var dadosAgrupados_acoes = {};

function lista_status_all(){

	let formData = new FormData();
	formData.append('tctrid', App_consultas.tctrid);
	formData.append('tctraut', App_consultas.tctraut);

     $.ajax({
		url: App_consultas.url_lista_status_all,
	    type: 'POST',
	    data:  formData,
	    processData: false,
	    contentType: false,
	    async: true,
	    statusCode: {
	    	200: function(response) {
		   
                console.log(' LISTA COM OS STATUS',response.acoes);

            acoes = response.acoes || [];

                   dadosAgrupados_acoes = {};

        acoes.forEach(function(item) {

            const idCnsid = String(item.cnsid || '').trim();

            if (!dadosAgrupados_acoes[idCnsid]) {

                dadosAgrupados_acoes[idCnsid] = {
                    cnsid: item.cnsid,
                    log: item.log,
                    documento: item.documento,
                    consulta: item.consulta,
                    score: item.score,
                    decisao: item.decisao,
                    status: item.status,
                    justificativa: item.justificativa,
                    setor: item.setor,
                    
                    mensagens: []
                };
            }

            dadosAgrupados_acoes[idCnsid].mensagens.push(item);
        });

        const dadosTabelaAcoes = Object.values(dadosAgrupados_acoes);

        console.log('DADOS AGRUPADOS PARA A TABELA DE AÇÕES:', dadosTabelaAcoes);
                // if(response.acoes){
                 create_table_consultas('#lista_status',dadosTabelaAcoes);
                // }
            },
		    422: function(response) {
                
		    	$.each( response.responseJSON.errors, function( key, value ) {
		    		//erros
		    	  	console.log(key+value);
				});
		    },
		    401:function(response) {
		      alert(response.responseText);
		    },
		    500: function(response) {
		      alert(response.responseText);
		    } 
		}
	});
}

function verificar_comite(cnsid){

	let formData = new FormData();
	formData.append('tctrid', App_consultas.tctrid);
	formData.append('tctraut', App_consultas.tctraut);
	
	formData.append('cnsid',  cnsid);
    // for (const [chave, valor] of formData.entries()) {
    //   console.log(`${chave}: ${valor}`);
    // }
    
    // return

     $.ajax({
		url: App_consultas.url_agentes_comite_acoes,
	    type: 'POST',
	    data:  formData,
	    processData: false,
	    contentType: false,
	    async: true,
	    statusCode: {
	    	200: function(response) {
		   
                console.log(response);
                if(response.data == false){
                  alert('Para acessar e preciso esta vinculado a esta consulta');
                    $("#modal_comite").modal('hide');
                  return
                  
                }
                  $("#modal_comite").modal('show');
            },
		    422: function(response) {
                
		    	$.each( response.responseJSON.errors, function( key, value ) {
		    		//erros
		    	  	console.log(key+value);
				});
		    },
		    401:function(response) {
		      alert(response.responseText);
		    },
		    500: function(response) {
		      alert(response.responseText);
		    } 
		}
	});
}



//monto uma tabela com o hitorico

function listStatus(dados) {

    console.log(dados);

    $('#lista_statuss tbody').empty();

    $.fn.dataTable.ext.errMode = 'none';

    $('#lista_statuss').DataTable({
        pagingType: $(window).width() < 768 ? "simple" : "simple_numbers",
        language: {
            info: "Mostrando _START_ de _END_ Total _TOTAL_ registros",
            infoEmpty: "Mostrando 0 de 0 Total 0 registros",
            infoFiltered: "(filtered from _MAX_ total registros)",
            lengthMenu: "Rolagens por páginas: _MENU_",
            search: "",
            searchPlaceholder: "Buscar na tabela...",
            paginate: {
                previous: "<i class='fas fa-angle-double-left'></i> ",
                next: " <i class='fas fa-angle-double-right'></i>  ",
            },

        },

        processing: true,
        serverSide: false,
        order: [
            [1, 'asc']
        ],

        select: {
            style: 'multi',
            selector: 'td:first-child .select-checkbox'
        },
        layout: {
            topStart: 'pageLength',
            topEnd: [{
                    buttons: [{

                    }]
                },
                'search'
            ],
            bottomStart: 'info',
            bottomEnd: 'paging',
        },
        scrollX: false,
        scrollCollapse: true,
        paging: true,
        searching: true,
        destroy: true,
        data: dados ? dados : [],

        columns: [
            {
                data: 'log',
            }, {
                data: 'setor',
            }, {
                data: 'status',
                 data: null,
                orderable: false,
                  render: function (data, type, row) {
            return switch_status_consulta(data, type, row);
            }

            },
           
             {
                data: 'justificativa',
            },
             
            {
                data: 'usuario'
            }
        ],
    });
}

$('#lista_comite_chat').on('click', '.btn-mensagens', function() {

    const cnsid = $(this).data('cnsid');

    const consulta = dadosAgrupados[cnsid];

    console.log('CONSULTA:', consulta);

    if (!consulta) {
        console.error('Consulta não encontrada');
        return;
    }

        let html = '';
        const mensagens = Array.isArray(consulta.mensagens)
                ? consulta.mensagens
                : [];

        mensagens.forEach(function(item, index) {
                const mensagem = item.mensagem || item.message || item.texto || item.decisao || '-';
                const usuario = item.usuario || item.nome || '';
                const data = item.data_analise || item.data_consultas || item.data_consulta || '';
                const setores = item.setor || item.data_consultas || item.data_consulta || '';
                const justificativa = item.justificativa || '-';
                const status = item.status || '-';



                html += `
                        <div class="container_ ${index % 2 ? 'darker' : ''}">
                                <p>Status:${status}</p>
                                <p>Usuário: ${usuario ? `<small>${usuario}</small>` : ''}</p>
                                <p>Setor: ${setores ? `<small>${setores}</small>` : ''}</p>
                           
                                <p>Justificativa: ${justificativa ? `<small>${justificativa}</small>` : ''}</p>
                                ${data ? `<span class="time-right">${ajustar_data(data)}</span>` : ''}
                        </div>`;
        });

        $('#chat-mensagens')
                .css({
                        'max-height': '400px',
                        'overflow-y': 'auto',
                        'overflow-x': 'hidden'
                })
                .html(html || '<p>Nenhuma mensagem encontrada.</p>');

    $('#chat-documento').text(
        consulta.documento || '-'
    );

    $('#chat-score').text(
        consulta.score || '-'
    );

    $('#modal-chat').modal('show');
});

function ajustar_data(data_enviada){

    const dataString = data_enviada.replace(/(\d{2})\/(\d{2})\/(\d{4}) (\d{2}:\d{2}:\d{2})/, '$3-$2-$1T$4');

    // Substitui o espaço por 'T' para garantir a compatibilidade do formato ISO
    const data = new Date(dataString.replace(" ", "T"));

    // Formata para o padrão brasileiro (DD/MM/AAAA HH:MM:SS)
    const dataFormatada = data.toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
    });

    return dataFormatada;
}