  const API_URL = '../index.php';

  // Escopo global para gerenciar cache local dos registros e agilizar downloads
  const App = {
      dadosLocais: []
  };

  $(document).ready(function () {
      // Inicializa as datas padrão (mês corrente)
      const hoje = new Date().toISOString().split('T')[0];
      const [ano, mes] = hoje.split('-');
      $('#f-data-ini').val(`${ano}-${mes}-01`);
      $('#f-data-fim').val(hoje);

      fetchDadosSuspensao();

      $('#btn-filtrar').on('click', fetchDadosSuspensao);
      $('#btn-csv').on('click', exportarCSVSuspensao);
  });

  //   <a href="relatzsusp.chp?tctrid=417039&amp;
  // tctraut=2715a97d639da7ac69c219aa5269c040&amp;&amp;
  // tdiaini=01&amp;
  // tmesini=05&amp;tanoini=2026
  // &amp;tdiafim=21&amp;tmesfim=05&amp;tanofim=2026&amp;tordby=tordctr" class="lkpr">Passíveis de Suspensão</a>
  function fetchDadosSuspensao() {
      $('#corpo-tabela-susp').html('<tr class="loading-row"><td colspan="11"><span class="spinner"></span>Executando cruzamento de dados...</td></tr>');

      const convertDados = {
          "acao": "listSuspensao",
          "tdataInial": $('#f-data-ini').val(),
          "tdfim": $('#f-data-fim').val(),
          "ordenacao": $('#f-ordenacao').val(),

      }

      const convertidoJson = JSON.stringify(convertDados);

      $.ajax({
          url: '/api/listSuspesao',
          type: 'GET',
          data: {
              convertidoJson
          },
          dataType: 'json',
          success: function (resp) {
              if (resp.sucesso && Array.isArray(resp.dados)) {
                  App.dadosLocais = resp.dados; // Alimenta cache local
                  renderTabelaSuspensao(resp.dados);
              } else {
                  App.dadosLocais = [];
                  $('#corpo-tabela-susp').html(`<tr><td colspan="11" class="text-center text-danger padding: 40px;">${resp.dados.msg || 'Erro ao processar listagem.'}</td></tr>`);
              }
          },
          error: function () {
              App.dadosLocais = [];
              $('#corpo-tabela-susp').html(`<tr><td colspan="11" class="text-center text-danger padding: 40px;">Erro crítico de comunicação com o servidor.</td></tr>`);
          }
      });
  }

  function renderTabelaSuspensao(dados) {
      const tbody = $('#corpo-tabela-susp').empty();
      $('#contagem-susp').text(`${dados.length} clientes elegíveis para suspensão localizados.`);

      let qtdGeral = 0;
      let somaGeral = 0;
      let somaSuspensosSim = 0;
      let somaSuspensosNao = 0;

      if (dados.length === 0) {
          tbody.html('<tr><td colspan="11" class="text-center" style="padding:40px; color:var(--muted)">Nenhum cliente atende aos critérios de suspensão no período informado.</td></tr>');
          return;
      }

      dados.forEach(r => {
          const valorNum = parseFloat(r.valor || 0);
          qtdGeral++;
          somaGeral += valorNum;

          // Mapeamento idêntico ao tratamento de cores do arquivo legado
          const isSuspenso = String(r.suspenso).toUpperCase() === 'SIM' || String(r.suspenso).toUpperCase() === 'S';
          if (isSuspenso) {
              somaSuspensosSim += valorNum;
          } else {
              somaSuspensosNao += valorNum;
          }

          const badgeClass = isSuspenso ? 'st-suspenso-sim' : 'st-suspenso-nao';
          const badgeLabel = isSuspenso ? 'SIM' : 'NÃO';

          // Formatação de telefone vinda do fnccmpaux legado
          let fFone = r.telefone || '—';
          if (r.telefone && r.telefone.length >= 11) {
              fFone = `(${r.telefone.substring(0,3)})${r.telefone.substring(3,7)}-${r.telefone.substring(7,11)}`;
          }

          tbody.append(`
                    <tr>
                        <td title="${r.cliente}"><strong>${r.cliente}</strong></td>
                        <td>${r.perfilcobtipo || '—'}</td>
                        <td>${r.crcprepago === 't' || r.crcprepago === true ? 'SIM' : 'NÃO'}</td>
                        <td class="nnro-mono">${r.n_nro}</td>
                        <td>${formatarData(r.vencimento)}</td>
                        <td class="valor-mono">${formatarValor(valorNum)}</td>
                        <td>${r.doc_ger || '—'}</td>
                        <td><span class="badge-st ${badgeClass}">${badgeLabel}</span></td>
                        <td title="Contato: ${r.contato_financeiro || '—'}">${fFone}</td>
                        <td title="${r.vendedor || ''}">${r.vendedor || '—'}</td>
                        <td>
                            <button class="btn-acao" title="Tratar Inadimplência" onclick="window.location='?rota=index&busca=${encodeURIComponent(r.cliente)}'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            </button>
                        </td>
                    </tr>
                `);
      });

      // Atualiza os contadores de rodapé calculados em tempo real
      $('#res-qtd').text(qtdGeral);
      $('#res-total').text(formatarValor(somaGeral));
      $('#res-ativos').text(formatarValor(somaSuspensosSim));
      $('#res-inativos').text(formatarValor(somaSuspensosNao));
  }

  function exportarCSVSuspensao() {
      if (!App.dadosLocais || App.dadosLocais.length === 0) {
          alert('Não existem dados disponíveis para exportar no momento.');
          return;
      }

      // Cabeçalho idêntico ao mapa estrutural da view
      const colunas = [
          'Cliente', 'Perfil', 'Pre-pago', 'N.Nro', 'Vencimento',
          'Valor', 'Doc. Ger.', 'Ja Suspenso?', 'Contato Financeiro', 'Telefone', 'Vendedor(es)'
      ];

      // BOM para o Excel decodificar caracteres latinos e acentos nativamente
      let csvContent = '\uFEFF';
      csvContent += colunas.join(';') + '\n';

      App.dadosLocais.forEach(r => {
          const isSuspenso = String(r.suspenso).toUpperCase() === 'SIM' || String(r.suspenso).toUpperCase() === 'S';
          const isPrepago = r.crcprepago === 't' || r.crcprepago === true;

          const linha = [
              `"${(r.cliente || '').replace(/"/g, '""')}"`,
              `"${r.perfilcobtipo || '—'}"`,
              `"${isPrepago ? 'SIM' : 'NÃO'}"`,
              `"${r.n_nro || ''}"`,
              `"${r.vencimento || ''}"`,
              `"${parseFloat(r.valor || 0).toFixed(2).replace('.', ',')}"`, // Formato numérico amigável para somatórios no Excel
              `"${r.doc_ger || '—'}"`,
              `"${isSuspenso ? 'SIM' : 'NÃO'}"`,
              `"${(r.contato_financeiro || '—').replace(/"/g, '""')}"`,
              `"${r.telefone || '—'}"`,
              `"${(r.vendedor || '—').replace(/"/g, '""')}"`
          ];
          csvContent += linha.join(';') + '\n';
      });

      const blob = new Blob([csvContent], {
          type: 'text/csv;charset=utf-8;'
      });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      const dataCorrente = new Date().toLocaleDateString('pt-BR').replace(/\//g, '-');

      link.href = url;
      link.download = `clientes-passiveis-suspensao_${dataCorrente}.csv`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
  }

  function formatarValor(v) {
      return v.toLocaleString('pt-BR', {
          style: 'currency',
          currency: 'BRL'
      });
  }

  function formatarData(d) {
      if (!d) return '—';
      const [y, m, dd] = d.split('-');
      return `${dd}/${m}/${y}`;
  }