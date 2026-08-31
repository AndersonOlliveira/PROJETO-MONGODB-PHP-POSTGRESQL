const App = {

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

// ── INICIALIZAÇÃO ─────────────────────────────────────────────
$(document).ready(function () {
    // PEGO O LOGIN E APROVEITO DE FORMA GLOBAL DENTRO DO CODIGO
    App.crt = $('#d-id').val();
    App.tctraut = $('#d-tctraut').val();

    // chamar_auxiliares();

});
console.log('ACESSANDO OS DADOS DA MINHA JS')
//    function lista_aplication_user() {
//         $.ajax({
//             type: 'GET',
//             url: 'progestor.chp?r=users_aplications_data',
//             data: {
//                 tctrid: <?php print $tctrid; ?>,
//                 tctraut: '<?php print $tctraut; ?>'
//             },
//             success: function(data) {
//                 const dados = JSON.parse(data);
//                 if (dados.status == 2) {
//                     $.each(dados.data, function(index, item) {
//                         $('#user-id').text(item.ctrid);
//                         $('#user-name').text(item.ctrapl);
//                     });
//                 }
//             },
//             error: function(xhr) {
//                 console.error('sidebar user data: erro AJAX', xhr.status);
//             }
//         });
//     }
   
   
   /* ─────────────────────────────────────────────
   populateAppsGrid — monta o grid do dropdown
───────────────────────────────────────────── */
    function populateAppsGrid(dados) {
        const grid = document.getElementById('pg-apps-grid');
        if (!grid) return;

        grid.innerHTML = '';

        const tctrid = document.getElementById('tctrid')?.value || '';
        const tctraut = document.getElementById('tctraut')?.value || '';


        // console.warn('dados da minha api!');
        // console.warn(dados);
        // // remove o rnglst (evita duplicidade do toScore)
        const itens = dados.filter(function(item) {
            return item.srvlnk;
        });

        if (itens.length === 0) {
            grid.innerHTML = '<span class="pg-apps-empty">Nenhuma solução disponível</span>';
            return;
        }

        itens.forEach(function(item) {
            const isCurrent = item.srvlnk?.includes('/progestor/');
            const isProZoomy = item.srvlnk?.includes('/proZoomy/proZoomy.php');
            const isProCura = item.srvlnk?.includes('/procura/proCura.php');
            const isManutencao = item.srvlnk?.includes('REGRAS_DE_NEGOCIO');

            // ---------- LINK ----------
            const a = document.createElement('a');
            a.className = 'pg-app-item';
            a.href = '#';

            // ---------- ÍCONE ----------
            const icon = document.createElement('div');
            icon.className = 'pg-app-icon' + (isCurrent ? ' pg-app-current' : '');

            const img = document.createElement('img');
            const logoss = item.srvlnk_logos || '';

            let nomeLogo = 'default';

            if (logoss.includes('/')) {
                nomeLogo = logoss.split('/')[1];
            }

            img.src = `img/${nomeLogo}.png?v=1`;
            img.alt = item.srvobs || '';
            img.onerror = function() {
                this.style.display = 'none';
            };

            // ---------- NOME ----------
            const name = document.createElement('span');
            name.className = 'pg-app-name';
            name.textContent = item.srvobs || '';

            icon.appendChild(img);
            a.appendChild(icon);
            a.appendChild(name);
            grid.appendChild(a);

            // ---------- CLICK ----------
            a.addEventListener('click', function(e) {
                e.preventDefault();

                // fecha dropdown de soluções
                const dropdown = document.querySelector('.pg-apps-dropdown');

                if (dropdown) {
                    dropdown.classList.remove('open');
                }

                // =========================
                // ToSocre → abre modal
                // =========================

                if (isManutencao) {

                    $('#listaManutencao').html('');

                    // monta os links dos mapas
                    if (item.dados_regras && item.dados_regras.length > 0) {
                        item.dados_regras.forEach(function(dados_manutencao) {

                            console.log(dados_manutencao)
                            const urlMapa =
                                `${dados_manutencao.srvlnk}` +
                                `?tctrid=${tctrid}` +
                                `&tctraut=${tctraut}`;

                            const linha = `
                            <tr>
                                <td
                                    class="tdt1"
                                    width="90%"
                                    align="left"
                                    style="padding: 8px 0; border-bottom: 1px solid #ddd;"
                                >
                                    <a
                                        href="${urlMapa}"
                                        class="termoadesao"
                                    >
                                        ${dados_manutencao.title}
                                    </a>
                                </td>
                            </tr>
                        `;

                            $('#listaManutencao').append(linha);

                        });
                    } else {
                        $('#listaManutencao').html(`
                        <tr>
                            <td
                                class="tdt1"
                                width="90%"
                                align="left"
                                style="padding: 8px 0; border-bottom: 1px solid #ddd;"
                            >
                                Nenhum mapa disponível.
                            </td>
                        </tr>
                    `);
                    }

                    // remove backdrop antigo
                    $('.modal-backdrop').remove();

                    // abre modal
                    $('#modalManutecao').modal('show');

                    // ao fechar modal → reabre dropdown
                    $('#modalManutecao')
                        .off('hidden.bs.modal')
                        .on('hidden.bs.modal', function() {
                            if (dropdown) {
                                dropdown.classList.add('open');
                            }
                        });

                    return;
                }


                // =========================
                // PROZOOMY → abre modal
                // =========================
                if (isProZoomy) {
                    $('#listaMapasProZoomy').html('');

                    // monta os links dos mapas
                    if (item.proZommy && item.proZommy.length > 0) {
                        item.proZommy.forEach(function(mapa) {
                            const urlMapa =
                                `/proZoomy/MapaproZoomy.php` +
                                `?contrato=${tctrid}` +
                                `&token=${tctraut}` +
                                `&id_mapa=${mapa.id_mapa}`;

                            const linha = `
                            <tr>
                                <td
                                    class="tdt1"
                                    width="90%"
                                    align="left"
                                    style="padding: 8px 0; border-bottom: 1px solid #ddd;"
                                >
                                    <a
                                        href="${urlMapa}"
                                        target="_blank"
                                        class="termoadesao"
                                    >
                                        ${mapa.descricao}
                                    </a>
                                </td>
                            </tr>
                        `;

                            $('#listaMapasProZoomy').append(linha);
                        });
                    } else {
                        $('#listaMapasProZoomy').html(`
                        <tr>
                            <td
                                class="tdt1"
                                width="90%"
                                align="left"
                                style="padding: 8px 0; border-bottom: 1px solid #ddd;"
                            >
                                Nenhum mapa disponível.
                            </td>
                        </tr>
                    `);
                    }

                    // remove backdrop antigo
                    $('.modal-backdrop').remove();

                    // abre modal
                    $('#modalProZoomy').modal('show');

                    // ao fechar modal → reabre dropdown
                    $('#modalProZoomy')
                        .off('hidden.bs.modal')
                        .on('hidden.bs.modal', function() {
                            if (dropdown) {
                                dropdown.classList.add('open');
                            }
                        });

                    return;
                }

                if (isProCura) {



                    $('#listaMapasProCura').html('');


                    // monta os links proCura
                    if (item.proCura && item.proCura.length > 0) {
                        console.warn(item.proCura);

                        item.proCura.forEach(function(lista) {



                            const urlMapa =
                                `/cns/cns.chp` +
                                `?cns=${lista.rdecnsid}` +
                                `&tacao=EXEC&tctrid=${tctrid}` +
                                `&tctraut=${tctraut}`;

                            const linha = `
                            <tr>
                                <td
                                    class="tdt1"
                                    width="90%"
                                    align="left"
                                    style="padding: 8px 0; border-bottom: 1px solid #ddd;"
                                >
                                    <a
                                        href="${urlMapa}"
                                        target="_blank"
                                        class="termoadesao"
                                    >
                                        ${lista.rdecnsdsc}
                                    </a>
                                </td>
                            </tr>
                        `;


                            $('#listaMapasProCura').append(linha);
                        });
                    } else {
                        $('#listaMapasProCura').html(`
                        <tr>
                            <td
                                class="tdt1"
                                width="90%"
                                align="left"
                                style="padding: 8px 0; border-bottom: 1px solid #ddd;"
                            >
                                Nenhum mapa disponível.
                            </td>
                        </tr>
                    `);
                    }

                    // remove backdrop antigo
                    $('.modal-backdrop').remove();

                    // abre modal
                    $('#modalProCura').modal('show');

                    // ao fechar modal → reabre dropdown
                    $('#modalProCura')
                        .off('hidden.bs.modal')
                        .on('hidden.bs.modal', function() {
                            if (dropdown) {
                                dropdown.classList.add('open');
                            }
                        });

                    return;
                }

                // =========================
                // OUTROS LINKS → navegação normal
                // =========================
                const url =
                    item.srvlnk +
                    `?tctrid=${tctrid}&tctraut=${tctraut}`;

                window.location.href = url;
            });
        });
    }


    /* ─────────────────────────────────────────────
       AJAX autônomo — busca as soluções contratadas
       sem depender do view_menu.php
       ───────────────────────────────────────────── */
    function carregarAppsGrid() {
        const tctrid = document.getElementById('tctrid') ? document.getElementById('tctrid').value : '';
        const tctraut = document.getElementById('tctraut') ? document.getElementById('tctraut').value : '';

        $.ajax({
            type: 'GET',
            url: 'index.chp?r=users_aplications',
            data: {
                tctrid: tctrid,
                tctraut: tctraut
            },
            success: function(data) {
                try {
                    const dados = JSON.parse(data);
                    if (dados.status == 2) {
                        console.log(dados);
                        populateAppsGrid(dados.data);
                    } else {
                        const grid = document.getElementById('pg-apps-grid');
                        if (grid) grid.innerHTML = '<span class="pg-apps-empty">Nenhuma solução disponível</span>';
                    }
                } catch (e) {
                    console.error('pg-apps-grid: erro ao parsear resposta', e);
                }
            },
            error: function(xhr) {
                console.error('pg-apps-grid: erro AJAX', xhr.status);
                const grid = document.getElementById('pg-apps-grid');
                if (grid) grid.innerHTML = '<span class="pg-apps-empty">Erro ao carregar</span>';
            }
        });
    }

    /* ─────────────────────────────────────────────
       Toggle dropdown
       ───────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', function() {
        const trigger = document.getElementById('pg-apps-trigger');
        const dropdown = document.getElementById('pg-apps-dropdown');

        if (trigger && dropdown) {
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('open');
            });

            document.addEventListener('click', function() {
                dropdown.classList.remove('open');
            });

            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        /* dispara o carregamento assim que o jQuery estiver disponível */
        // if (typeof $ !== 'undefined') {
        //     carregarAppsGrid();
        // } else {
        //     /* fallback: aguarda o jQuery do view_menu carregar */
        //     window.addEventListener('load', function() {
        //         if (typeof $ !== 'undefined') carregarAppsGrid();
        //     });
        // }
    });