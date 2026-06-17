  // ── CONFIGURAÇÃO ──────────────────────────────────────────────

  //   const {
  //       forwardRef
  //   } = require("react");

  // Mude só API_URL se o backend mudar de lugar
  const API_URL = '../index.php';

  const ICONS = {
      historico: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><polyline points="12 8 12 12 14 14"/><circle cx="12" cy="12" r="3" fill="none"/></svg>`,
      nova: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>`,
      detalhes: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`,
  };



  function listArchivesMonth() {
      const anoAtual = new Date().getFullYear().toString();
      const mesAtual = (new Date().getMonth() + 1).toString();

      //pega o que foi aplicado dentro do filtro de selecionar
      $('#range, #range_ate').on('change', function () {

          const dataInicio = $('#range').val();
          const dataFim = $('#range_ate').val();

          console.log('DATAS:: SELECIONADAS');
          console.log(dataInicio, dataFim);

          // Verifica se as duas datas foram preenchidas
          if (dataInicio && dataFim) {

              // Exemplo formato esperado: 19/05/2026
              const partesInicio = formaTdiasPesquisa(dataInicio);
              const partesFim = formaTdiasPesquisa(dataFim);

              list_dados(null, partesInicio, partesFim);
          }
      });

      var mesAno = `${mesAtual}/${anoAtual}`;
      list_dados(mesAno, null, null);
  }

  // cod_status numérico vindo do banco → CSS e texto
  // vem da varialve cod_status
  // 1=Pendente, 2=Em andamento, 4=Sem retorno, 3=Resolvido, 5=Cancelado

  const STATUS_CLASS = {
      1: 'st-pendente',
      2: 'st-andamento',
      4: 'st-semretorno',
      3: 'st-resolvido',
      5: 'st-cancelado'
  };


  const STATUS_LABEL = {
      1: 'Pendente',
      2: 'Em andamento',
      4: 'Sem retorno',
      3: 'Resolvido',
      5: 'Cancelado'
  };

  const STATUS_LABEL_HISTORICO = {
      1: 'Pendente',
      2: 'Em andamento',
      3: 'Sem retorno',
      4: 'Resolvido',
      5: 'Cancelado'
  };

  // emoji por tipo de contato
  const TIPO_ICON_ID = {
      1: '💬',
      2: '📧',
      3: '📞',
      4: '🤝',
      5: '📱'
  };
  // emoji por tipo de contato
  const TIPO_ICON = {
      'WhatsApp': '💬',
      'E-mail': '📧',
      'Ligação': '📞',
      'Visita': '🤝',
      'SMS': '📱'
  };



  // ── ESTADO GLOBAL ─────────────────────────────────────────────
  const App = {
      dados: [], // todos os registros da API
      dadosFiltrados: [], // subconjunto após filtros
      paginaAtual: 1,
      porPagina: 50,
      linhaSelecionada: null,
      drawerAberto: false,
      abaAtiva: 'tratativa',
      historicos: {}, // cache por n_nro
  };



  //vou pegar o click para atualizar a pagina com o dados novos
  $('#btn-limpar').click(function () {
      clearFiltro();
      listArchivesMonth();
  });


  // ── INICIALIZAÇÃO ─────────────────────────────────────────────
  $(document).ready(function () {
      preencherDataHoje();
      listContato();
      list_action();
      listArchivesMonth();
      buscar_Info_Responsavel_Logado();

      // fetchAcoes(); // ← DESCOMENTAR quando backend implementar GET ?acao=listAcoes (Select de ações para o dropdown de próxima ação)
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

  // ALTERADO PARA PEGAR O CLICK E FAZER AS VALIDADOCOES DENTRO
  $(document).on('click', '#btn-salvar-tratativa', function () {

      const erros = [
          $('#d-tipo-contato').val(),
          $('#d-status-tratativa').val(),
          $('#d-proxima-acao').val()
      ];

      const infoErros = validarCamposTratativa(erros);

      const descricao = $('#d-descricao').val().trim();
      if (!descricao) {
          toast('Preencha a descrição da tratativa.', 'error');
          return;
      }
      // TUDO VALIDADO LIBERA PARA O BOTÃO PARA FAZER O INSERT
      if (infoErros && descricao.length > 1) {

          salvarTratativa();
      }


  });

  function preencherDataHoje() {
      const hoje = new Date().toISOString().split('T')[0];
      const [ano, mes] = hoje.split('-');
      $('#f-data-ini').val(`${ano}-${mes}-01`);
      $('#f-data-fim').val(hoje);
      $('#d-data-tratativa').val(hoje);
      const agora = new Date();
      $('#d-hora-tratativa').val(
          `${String(agora.getHours()).padStart(2,'0')}:${String(agora.getMinutes()).padStart(2,'0')}`
      );
  }

  // ── BUSCAR AÇÕES DO BANCO ─────────────────────────────────────────────
  // TODO: descomentar fetchAcoes() no ready quando o backend implementar:
  // GET ?acao=listAcoes
  // Retorna: { sucesso: true, dados: [ { id: 1, descricao: "Aguardar retorno" }, ... ] }
  //
  // function fetchAcoes() {
  //     $.ajax({
  //         url: API_URL,
  //         type: 'GET',
  //         data: { acao: 'listAcoes' },
  //         dataType: 'json',
  //         success: function(resp) {
  //             const sel = $('#d-proxima-acao');
  //             sel.empty().append('<option value="">Selecione...</option>');
  //             if (resp.sucesso && Array.isArray(resp.dados)) {
  //                 resp.dados.forEach(a => {
  //                     sel.append(`<option value="${a.id}">${a.descricao}</option>`);
  //                 });
  //             }
  //         },
  //         error: function() {
  //             toast('Não foi possível carregar as ações.', 'error');
  //         }
  //     });
  // }

  // ── FETCH DE DADOS ────────────────────────────────────────────
  // GET ?rota=index&acao=listRelatorio
  // Retorna: { sucesso: true, dados: [...] }
  function list_dados(mes = null, tdataInicio = null, tdataFim = null) {
      mostrarLoading();

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
          headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Content-Type': 'application/json',
              'Accept': 'application/json'
          },
          data: {
              convertidoJson
          },
          dataType: 'json',
          success: function (resp) {
              if (resp.sucesso && Array.isArray(resp.dados)) {
                  App.dados = resp.dados;
                  App.dadosFiltrados = [...resp.dados];
                  renderTabela();
              } else {
                  clearFiltro();
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

  function corrigirEncoding(texto) {
      if (!texto) return '';
      try {
          // Converte a string quebrada em uma sequência de bytes (Windows-1252/ISO-8859-1)
          const bytes = Uint8Array.from(texto, c => c.charCodeAt(0));
          // Decodifica corretamente essa sequência usando UTF-8
          return new TextDecoder('utf-8').decode(bytes);
      } catch (e) {
          // Se falhar por algum motivo, retorna o texto original para não quebrar a tela
          return texto;
      }
  }

  // ── RENDERIZAÇÃO ──────────────────────────────────────────────
  function renderTabela() {
      atualizarCardsResumo();
      const total = App.dadosFiltrados.length;
      const inicio = (App.paginaAtual - 1) * App.porPagina;
      const fim = Math.min(inicio + App.porPagina, total);
      const pagina = App.dadosFiltrados.slice(inicio, fim);

      $('#contagem').text(total === 0 ? 'Nenhum registro.' : `Exibindo ${inicio + 1}–${fim} de ${total} registros`);

      const tbody = $('#corpo-tabela');
      tbody.empty();

      if (pagina.length === 0) {
          tbody.html(`<tr><td colspan="13"><div class="empty-state"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg><p>Nenhum registro encontrado</p><small>Ajuste os filtros e tente novamente</small></div></td></tr>`);
      } else {
          pagina.forEach(r => {
              const dias = calcularDias(r.vencimento);
              const ativo = App.linhaSelecionada === String(r.n_nro) ? 'ativo' : '';
              const bClass = STATUS_CLASS[r.cod_status] || 'st-pendente';
              const bLabel = STATUS_LABEL[r.cod_status] || r.cod_status;
              const newPerfil = r.perfilcobtipo;
              const info_prePago = r.crcprepago;


              const perfilCorrigido = newPerfil == 'P?S-PAGO' ? newPerfil.replace(/\?/g, "Ó") : corrigirEncoding(newPerfil);
              const [infos, compraDeCreditos] = info_prePago ? ['PRÉ-PAGO', 'SIM'] : ['PÓS-PAGO', 'NÃO'];
              const concatDados =
                  tbody.append(`
                        <tr class="${ativo}" data-id="${r.n_nro}" onclick="selecionarLinha('${r.n_nro}')">
                            <td title="${r.cliente}"><strong>${r.cliente}</strong></td>
                            <td>${infos || '—'}</td>
                            <td>${compraDeCreditos || '—'}</td>
                            <td class="nnro-mono">${r.n_nro}</td>
                            <td>${formatarData(r.vencimento)}</td>
                            <td class="valor-mono">${formatarValor(r.valor)}</td>
                            <td>${r.doc_ger || '—'}</td>
                            <td class="${dias > 0 ? 'dias-atraso' : ''}">${dias}d</td>
                            <td title="${r.vendedor || ''}">${r.vendedor || '—'}</td>
                          
                            <td class="col-ocultavel" title="${r.status  || ''} '-' ${r.ultima_consulta}" 
                              style="color:var(--muted);font-size:11px">
                              ${r.status  || ''} '-' ${r.ultima_consulta} 
                              </td>
                            <td><span class="badge-st ${bClass}">${bLabel}</span></td>
                            <td class="col-ocultavel" style="font-size:11px;color:var(--muted)">${r.descricao_acao || '—'}</td>
                            <td>
                                <div class="acoes-cell">
                                    <button class="btn-acao" title="Histórico"    onclick="event.stopPropagation();selecionarLinha('${r.n_nro}','historico')">${ICONS.historico}</button>
                                    <button class="btn-acao" title="Nova tratativa" onclick="event.stopPropagation();selecionarLinha('${r.n_nro}','tratativa')">${ICONS.nova}</button>
                                    <button class="btn-acao" title="Detalhes"     onclick="event.stopPropagation();selecionarLinha('${r.n_nro}','detalhes')">${ICONS.detalhes}</button>
                                </div>
                            </td>
                        </tr>`);
          });
      }
      renderPaginacao(total);
  }


  function renderPaginacao(total) {
      const totalPag = Math.max(1, Math.ceil(total / App.porPagina));
      const atual = App.paginaAtual;
      $('#pag-info').text(`${total} registros · Página ${atual} de ${totalPag}`);
      const btns = $('#pg-btns');
      btns.empty();
      btns.append(`<button class="pg-btn" ${atual===1?'disabled':''} onclick="irPagina(${atual-1})">‹</button>`);
      const range = [];
      for (let i = Math.max(1, atual - 2); i <= Math.min(totalPag, atual + 2); i++) range.push(i);
      if (range[0] > 1) {
          btns.append(`<button class="pg-btn" onclick="irPagina(1)">1</button>`);
          if (range[0] > 2) btns.append(`<span style="padding:0 4px;font-size:12px;color:var(--muted);line-height:28px">…</span>`);
      }
      range.forEach(p => btns.append(`<button class="pg-btn ${p===atual?'active':''}" onclick="irPagina(${p})">${p}</button>`));
      if (range[range.length - 1] < totalPag) {
          if (range[range.length - 1] < totalPag - 1) btns.append(`<span style="padding:0 4px;font-size:12px;color:var(--muted);line-height:28px">…</span>`);
          btns.append(`<button class="pg-btn" onclick="irPagina(${totalPag})">${totalPag}</button>`);
      }
      btns.append(`<button class="pg-btn" ${atual===totalPag?'disabled':''} onclick="irPagina(${atual+1})">›</button>`);
  }

  function irPagina(p) {
      App.paginaAtual = p;
      renderTabela();
      $('#table-wrap').scrollTop(0);
  }


  // ── DRAWER ────────────────────────────────────────────────────
  function selecionarLinha(id, aba) {
      App.linhaSelecionada = String(id);
      const reg = App.dados.find(r => String(r.n_nro) === String(id));
      if (!reg) return;

      $('#drawer-cliente-nome').text(reg.cliente);
      $('#drawer-parcela-info').text(`Parcela ${reg.n_nro} · Venc. ${formatarData(reg.vencimento)} · ${formatarValor(reg.valor)}`);

      const dias = calcularDias(reg.vencimento);
      $('#det-cliente').text(reg.cliente);
      $('#det-perfil').text(reg.perfil || '—');
      $('#det-compra').text(reg.compra_credito || '—');
      $('#det-nnro').text(reg.n_nro);
      $('#det-venc').text(formatarData(reg.vencimento));
      $('#det-valor').text(formatarValor(reg.valor));
      $('#det-docger').text(reg.doc_ger || '—');
      $('#det-dias').text(`${dias} dias`);
      $('#det-vendedor').text(reg.vendedor || '—');
      $('#det-status').text(STATUS_LABEL[reg.cod_status] || reg.cod_status || '—');
      $('#det-ultima').text(reg.descricao_mov || '—');
      $('#det-proxacao').text(reg.descricao_acao || '—');

      carregarHistorico(id);

      if (!App.drawerAberto) {
          $('#drawer').addClass('aberto');
          $('#main-layout').addClass('drawer-open');
          App.drawerAberto = true;
      }

      mudarAba(aba || App.abaAtiva);
      $('#corpo-tabela tr').removeClass('ativo');
      $(`#corpo-tabela tr[data-id="${id}"]`).addClass('ativo');
  }

  function fecharDrawer() {
      $('#drawer').removeClass('aberto');
      $('#main-layout').removeClass('drawer-open');
      $('#corpo-tabela tr').removeClass('ativo');
      App.drawerAberto = false;
      App.linhaSelecionada = null;
  }

  function mudarAba(tab) {
      App.abaAtiva = tab;
      $('.drawer-tab').removeClass('ativo');
      $(`.drawer-tab[data-tab="${tab}"]`).addClass('ativo');
      $('.tab-pane').removeClass('ativo');
      $(`#tab-${tab}`).addClass('ativo');
  }


  // ── HISTÓRICO ─────────────────────────────────────────────────
  // GET ?acao=getHistorico&parcela_id={n_nro}
  // Retorna: { sucesso: true, dados: [...] } — ordenado do mais recente
  function carregarHistorico(id) {
      if (App.historicos[id] !== undefined) {
          renderHistorico(id);
          return;
      }
      $('#historico-lista').html('<div style="text-align:center;padding:30px;color:var(--muted);font-size:12px"><span class="spinner"></span> Carregando...</div>');

      const convertDados = {
          acao: 'getHistorico',
          numeroCobranca: id,
          tctrid: $('#d-id').val(),
      }

      const convertidoJson = JSON.stringify(convertDados);

      $.ajax({
          url: 'api/getHistorico',
          type: 'GET',
          data: {
              convertidoJson
          },
          dataType: 'json',
          success: function (resp) {

              App.historicos[id] = resp.sucesso ? (resp.dados || []) : [];
              renderHistorico(id);
          },
          error: function () {
              App.historicos[id] = [];
              renderHistorico(id);
              toast('Não foi possível carregar o histórico.', 'error');
          }
      });
  }

  function renderHistorico(id) {


      const hist = App.historicos[id] || [];

      const lista = $('#historico-lista');
      lista.empty();
      if (hist.length === 0) {
          lista.html('<div style="text-align:center;padding:40px;color:var(--muted);font-size:12px">Nenhuma tratativa registrada para esta parcela.</div>');
          return;
      }
      hist.forEach(h => {
          const bClass = STATUS_CLASS[h.cod_acao] || 'st-pendente';
          const bLabel = STATUS_LABEL[h.status] || h.status;
          const icon = TIPO_ICON[h.tipo] || '📋';

          let dataFormatada = h.ultima_consulta;

          if (!h.ultima_consulta) {
              // Corrige formatos sem o 'T' (ex: "2026-05-21 14:30:00") que quebram em alguns navegadores
              const dataTratada = String(h.ultima_consulta).replace(' ', 'T');
              const dataObjeto = new Date(dataTratada);

              // Verifica se a conversão gerou uma data válida
              if (!isNaN(dataObjeto.getTime())) {
                  const formatador = new Intl.DateTimeFormat('pt-BR', {
                      day: '2-digit',
                      month: '2-digit',
                      year: 'numeric',
                      hour: '2-digit',
                      minute: '2-digit',
                      second: '2-digit'
                  });
                  dataFormatada = formatador.format(dataObjeto);
              }
          }

          lista.append(`
                    <div class="hist-item">
                        <div class="hist-meta">
                            <span class="hist-data">Data/Hora:${dataFormatada}</span>
                            <span class="badge-st ${bClass}" style="font-size:10px">${bLabel} </span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:3px">
                            <span class="hist-tipo">${icon} - ${h.descricao_acao || h.proxima_acao}</span>
                            <span class="hist-resp">${h.res || h.responsavel}</span>
                        </div>
                        <div class="hist-desc">${h.descricao_mov || h.descricao}</div>
                        ${h.proxima_acao ? `<div style="font-size:11px;color:var(--azul);margin-top:4px">→ ${h.proxima_acao}</div>` : ''}
                    </div>`);
      });

  }

  //   $('#btnSalvar').on('click', function () {

  //       const btn = $(this);

  //       btn.prop('disabled', true);

  //       const validar = validarCamposTratativa(
  //           $('#d-tipo-contato').val(),
  //           $('#d-status-tratativa').val(),
  //           $('#d-proxima-acao').val()
  //       );

  //       if (!validar) {
  //           btn.prop('disabled', false);
  //           return;
  //       }

  //       salvarTratativa();
  //   });


  // ── SALVAR TRATATIVA ──────────────────────────────────────────
  // POST ?acao=salvarTratativa
  // Body: parcela_id, tipo, data, hora, responsavel, status, descricao, proxima_acao
  // Retorna: { sucesso: true }
  function salvarTratativa() {
      const id = App.linhaSelecionada;
      if (!id) return;


      const payload = {
          acao: 'salvarTratativa',
          parcela_id: id,
          tipo_trativa: $('#d-tipo-contato').val(),
          numeroCobranca: id,
          //   data: $('#d-data-tratativa').val(),
          //   hora: $('#d-hora-tratativa').val(),
          tctrid: $("#d-responsavel-id").val(),
          status_tratativa: $('#d-status-tratativa').val(),
          descricao: $('#d-descricao').val().trim(),
          tipo_acoes: $('#d-proxima-acao').val(),
      };

      const payloadtext = {
          acao: 'salvarTratativa',
          parcela_id: id,
          tipo_trativa: $("#d-tipo-contato option:selected").text(),
          numeroCobranca: id,
          tctrid: $("#d-responsavel-id").val(),
          status_tratativa: $("#d-status-tratativa option:selected").text(),
          descricao: $('#d-descricao').val().trim(),
          tipo_acoes: $("#d-proxima-acao option:selected").text(),
      };


      const playloadJson = JSON.stringify(payload);

      $('#btn-salvar-tratativa').prop('disabled', true).text('Salvando...');

      $.ajax({
          url: '/api/insertTrativa',
          type: 'POST',
          data: playloadJson,
          dataType: 'json',
          success: function (resp) {
              if (resp.sucesso) {
                  _atualizarEstadoLocal(id, payloadtext);
                  toast('Tratativa salva com sucesso!', 'success');
              } else {

                  //   toast('Erro ao salvar: ' + (resp.dados.error[0] || 'tente novamente'), 'error');
                  var erros = JSON.stringify(resp.dados);

                  toast('Erro ao salvar: ' + (erros || 'tente novamente'), 'error');
              }
          },
          error: function () {
              toast('Falha de conexão ao salvar. Tente novamente.', 'error');
          },
          complete: function () {
              $('#btn-salvar-tratativa').prop('disabled', false).html(`
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg> Salvar tratativa`);
          }
      });
  }

  function validarCamposTratativa(
      tipo,
      data,
      status
  ) {
      const [tipoContato, statusTratativa, proximaAcao] = dados;


      if (!tipoContato || tipoContato == 0) {
          toast('Selecione o tipo de contato.', 'error');
          $('#btn-salvar-tratativa').prop('disabled', false);
          return false; // Para a função aqui
      }

      if (!statusTratativa || statusTratativa == 0) {
          toast('Selecione o status da tratativa.', 'error');
          $('#btn-salvar-tratativa').prop('disabled', false);
          return false; // Para a função aqui
      }

      if (!proximaAcao || proximaAcao == 0) {

          toast('Selecione a próxima ação.', 'error');
          $('#btn-salvar-tratativa').prop('disabled', false);
          return false; // Para a função aqui
      }

      return true; // Se passar por todos, os dados estão válidos
  }




  //BUSCO DADOS PRA GERACAO DE OPTION TIPO CONTATO
  function listContato() {

      $.ajax({
          url: '/api/listaTipoContato',
          type: 'GET',
          dataType: 'json', // Define que você espera JSON de retorno
          headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Content-Type': 'application/json',
              'Accept': 'application/json'
          },

          success: function (resp) {

              if (resp.status == 2) {

                  set_up_contact(resp.dados)
              } else {
                  console.log('Outro status:', resp.message);
              }
          },
          error: function (error) {
              console.error('Erro na requisição:', error);
          }
      });

  } //BUSCO DADOS PRA GERACAO DE OPTION TIPO CONTATO
  function list_action() {

      $.ajax({
          url: '/api/listAcoes',
          type: 'GET',
          dataType: 'json', // Define que você espera JSON de retorno
          headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Content-Type': 'application/json',
              'Accept': 'application/json'
          },


          success: function (resp) {
              console.log('Resposta da api', resp);
              if (resp.status == 2) {

                  set_up_action(resp.dados)
              } else {
                  console.log('Outro status:', resp.message);
              }
          },
          error: function (error) {
              console.error('Erro na requisição:', error);
          }
      });

  }

  function buscar_Info_Responsavel_Logado() {

      $.ajax({
          url: '/api/listName',
          type: 'GET',
          dataType: 'json', // Define que você espera JSON de retorno
          headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Content-Type': 'application/json',
              'Accept': 'application/json'
          },
          data: {
              contrato: $('#d-id').val()
          },
          success: function (resp) {
              console.log('Resposta da api com o nome do contrato responvavel logado', resp);
              if (resp.status == 2) {

                  $('#d-responsavel').val(resp.dados);
                  $('#d-responsavel-id').val($('#d-id').val());

              } else {
                  console.log('Outro status:', resp.message);
              }
          },
          error: function (error) {
              console.error('Erro na requisição:', error);
          }
      });

  }


  function validarCamposTratativa(dados) {

      const [tipoContato, statusTratativa, proximaAcao] = dados;


      if (!tipoContato || tipoContato == 0) {
          toast('Selecione o tipo de contato.', 'error');
          $('#btn-salvar-tratativa').prop('disabled', false);
          return false; // Para a função aqui
      }

      if (!statusTratativa || statusTratativa == 0) {
          toast('Selecione o status da tratativa.', 'error');
          $('#btn-salvar-tratativa').prop('disabled', false);
          return false; // Para a função aqui
      }

      if (!proximaAcao || proximaAcao == 0) {

          toast('Selecione a próxima ação.', 'error');
          $('#btn-salvar-tratativa').prop('disabled', false);
          return false; // Para a função aqui
      }

      return true; // Se passar por todos, os dados estão válidos
  }

  // atualiza memória local após salvar (sem reload da tabela inteira)
  function _atualizarEstadoLocal(id, payloadtext) {

      if (!App.historicos[id]) {
          App.historicos[id] = [];
      }

      // Obter o nome do responsável para apresentacao em tela sem reload
      const nomeResponsavel = $("#d-responsavel").val() || '—';


      App.historicos[id].unshift({
          tipo: payload.tipo_trativa,
          responsavel: nomeResponsavel,
          status: payload.status_tratativa,
          descricao: payload.descricao,
          proxima_acao: payload.tipo_acoes,
          data_cadastro: new Date().toLocaleDateString('pt-BR')
      });


      const reg = App.dados.find(r => String(r.n_nro) === String(id));
      if (reg) {

          reg.cod_status = payload.status_tratativa;
          reg.descricao_mov = `${payload.tipo_trativa}`;

          if (payload.tipo_acoes) {
              reg.descricao_acao = payload.tipo_acoes;
          }
      }

      // Limpa os campos do formulário para o próximo uso
      $('#d-tipo-contato').val('0');
      $('#d-status-tratativa').val('0');
      $('#d-proxima-acao').val('0');
      $('#d-descricao').val('');

      //  Atualiza a parte visual sem dar reload na página
      renderTabela();
      renderHistorico(id);
  }

  //MONTO OPTION A PARTIR DO RESULADO VINDO DA API 
  function set_up_contact(tipo) {

      const tipo_selects = document.getElementById('d-tipo-contato');
      tipo_selects.innerHTML = '';


      const todosOption = document.createElement("option");
      todosOption.value = "0";
      todosOption.text = "Todos";
      tipo_selects.appendChild(todosOption);

      tipo.forEach((items) => {
          const option = document.createElement("option");
          option.value = items.cod_tipo_tratativa;
          option.text = items.tipo_tratativa;
          tipo_selects.appendChild(option);
      });
  }
  //MONTO OPTION A PARTIR DO RESULADO VINDO DA API 
  function set_up_action(tipo) {

      const tipo_selects = document.getElementById('d-proxima-acao');
      tipo_selects.innerHTML = '';


      const todosOption = document.createElement("option");
      todosOption.value = "0";
      todosOption.text = "Todos";
      tipo_selects.appendChild(todosOption);

      tipo.forEach((items) => {
          const option = document.createElement("option");
          option.value = items.cod_acao;
          option.text = items.acao_descricao;
          tipo_selects.appendChild(option);
      });
  }


  // ── EXPORTAR CSV ──────────────────────────────────────────────
  // Exporta todos os registros filtrados (não só a página atual)
  // Separador: ; | Encoding: UTF-8 BOM (compatível Excel BR)
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
      const rangeEl = $('#range')[0];
      const rangeAteEl = $('#range_ate')[0];

      if (rangeEl && rangeEl._flatpickr) {
          rangeEl._flatpickr.clear();
      }
      if (rangeAteEl && rangeAteEl._flatpickr) {
          rangeAteEl._flatpickr.clear();
      }
  }

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

  function atualizarCardsResumo() {
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