<?php require_once('partial/cabecalho.php') ?>

<head>
    <link href="css/proDecision/style_v1_consultas.css?v= <?= time(); ?>" rel="stylesheet">
</head>

<body>
    <div class="container-fluid p-0 d-flex"> 
        <div class="bg-light flex-fill">

            <!-- hamburguer mobile -->
            <div class="p-2 d-md-none d-flex text-white bg-white">
                <a href="#" class="text-white" data-bs-toggle="offcanvas" data-bs-target="#bdSidebar">
                    <i class="fa-solid fa-bars"></i>
                </a>
            </div>

            <?php require_once 'partial/menuLateral.php'; ?>

            <div class="pg-content-wrapper">

                <!-- ============================================================
                     LINHA 1 — MÉTRICAS + FILTRO DE DATA
                ============================================================ -->
                <div class="pg-metrics-row">
                 <div class="container" style="max-width: 900px;">
							<!-- Barra Principal (Filtro e Botão) -->
							<!-- 								
								
								<div class="flex-grow-1 me-3">
									<input 
									type="text" 
									class="form-control input-filtro" 
									placeholder="Filtrar consultas"
									aria-label="Filtrar consultas"
									>
								</div>

							
								<button 
									class="btn btn-mostrar-filtros d-flex align-items-center gap-2" 
									type="button" 
									data-bs-toggle="collapse" 
									data-bs-target="#painelFiltros" 
									aria-expanded="false" 
									aria-controls="painelFiltros"
								>
									<i class="bi bi-funnel"></i>
									<span>Mostrar Filtros</span>
									<i class="bi bi-chevron-down"></i>
								</button>

								</div> -->

								<!-- Conteúdo dos Filtros -->
								<div class="collapse mt-3" id="painelFiltros">
								<div class="card card-body painel-filtros-expandido p-4 shadow-sm">
									
									<div class="row g-3">
									<!-- Campo 1 -->
									<div class="col-md-4">
										<label class="form-label small fw-bold text-secondary">Período</label>
										<input type="date" class="form-control form-control-sm">
									</div>
									
									<!-- Campo 2 -->
									<div class="col-md-4">
										<label class="form-label small fw-bold text-secondary">Status</label>
										<select class="form-select form-control-sm">
										<option selected>Todos</option>
										<option>Ativo</option>
										<option>Pendente</option>
										<option>Cancelado</option>
										</select>
									</div><!-- class="col-md-4"> -->
									<!-- Campo 3 -->
									<div class="col-md-4">
										<label class="form-label small fw-bold text-secondary">Registro</label>
										<input type="text" class="form-control form-control-sm" placeholder="Buscar por nome...">
									</div>
									</div>

									<!-- Botões de Ação do Painel Interno -->
									<div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
									<button type="button" class="btn btn-sm btn-link text-secondary text-decoration-none">Limpar Filtros</button>
									<button type="button" class="btn btn-sm text-white" style="background-color: #78909c;">Filtrar</button>
									</div>

								</div>
								</div>

							</div>

                </div><!-- /pg-metrics-row -->

                <!-- ============================================================
                     LINHA 2 — TABELA DE JOBS  (largura total, fora do metrics-row)
                ============================================================ -->
                <div class="pg-table-section">
                    <div id="" class="teste">
                        <form class="col-sm-12">
                           <table id="lista_consulta"lass="text-center tableJobs"
                                style="font-size:10px; width:100%; border-radius:0;">
                                <thead>
                                    <tr>
                                        <th>#id</th>
                                        <th>Log</th>
                                        <th>Consulta</th>
                                        <th>Registro</th>
                                        <th>Score</th>
                                        <th>Decision</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                    </div><!-- /teste -->
                </div><!-- /pg-table-section -->

              

            </div><!-- /pg-content-wrapper -->
        </div><!-- /flex-fill -->
    </div><!-- /container-fluid -->


    <!-- ================================================================
         MODAIS 
    ================================================================ -->

	<!-- MODAL PARA ABRIR O AÇÃO -->
<div class="modal fade" id="modal_action" data-bs-backdrop="true" tabindex="-1" aria-labelledby="acao_ModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="acao_ModalLabel">Ações</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Mantido o ID original aqui -->
        <div id="lista-checkboxes" class="list-group"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <!-- <button type="button" class="btn btn-primary">Aprovar</button> -->
      </div>
    </div>
  </div>
</div>

<!-- MODAL PARA ABRIR O COMITÊ -->
<div class="modal fade" id="modal_comite" data-bs-backdrop="true" tabindex="-1" aria-labelledby="comite_ModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="comite_ModalLabel">Comitê</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="form_comite">
         
          <div class="form-group mb-3"> 
            <label for="justificativa_comite" class="form-label">Informe o motivo</label>
            <textarea class="form-control" id="justificativa_comite" rows="3"></textarea>
            <input type="hidden" id="id_comite_motvo">
          </div>
          <div class="form-group mb-3">
            <label for="selectStatus" class="form-label">Status</label>
            <select class="form-control" id="selectStatus">
              <option>Favorável</option>
              <option>Condicional</option>
              <option>Em análise</option>
            </select>
          </div>
          <button type="submit" class="btn btn-success w-100">Enviar</button> 
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <!-- <button type="button" class="btn btn-primary">Aprovar</button> -->
      </div>
    </div>
  </div>
</div>

<!-- MODAL PARA ABRIR O STATUS -->
<div class="modal fade" id="modal_status" data-bs-backdrop="true" tabindex="-1" aria-labelledby="status_ModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="status_ModalLabel">Status</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
       <div class="modal-body">
		
          <div class="pg-table-section">
                    <div id="teste" class="row">
                        <form class="col-sm-12">
                           <table id="lista_statuss"lass="text-center tableJobs"
                                style="font-size:10px; width:100%; border-radius:0;">
                                <thead>
                                    <tr>
                                        <th>log</th>
                                        <th>setor</th>
                                        <th>status</th>
                                        <!-- <th>data_cadastro</th> -->
                                        <th>Justificativa</th>
                                        <th>usuário</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                    </div><!-- /teste -->
                </div><!-- /pg-table-section -->
	
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>

      </div>
    </div>
  </div>
</div>

<!-- MODAL PARA REALIZAR A RECONSULTA -->
<div class="modal fade" id="modal_reconsultar" data-bs-backdrop="true" tabindex="-1" aria-labelledby="re_ModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="re_ModalLabel">Documentos</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="envio_arquivo">
          <div class="mb-3">
            <label for="formFile" class="form-label">Envie documentos para reconsultar</label>
            <input class="form-control" type="file" id="formFile">
            <input class="form-control" type="hidden" id="id_cns_arquivo">
          </div>
          <button type="submit" class="btn btn-primary">Enviar Arquivo</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <!-- <button type="button" class="btn btn-primary">Aprovar</button> -->
      </div>
    </div>
  </div>
</div>

<!-- MODAL PARA ABRIR O DOC -->
<div class="modal fade" id="modal_doc" data-bs-backdrop="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Documentos</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
         <div class="modal-body">
		
   <p>acessando documentos</p>
		<iframe
    id="doc_iframe"
    width="100%"
    height="500"
    frameborder="0"
    style="border: 1px solid #ccc;">
</iframe>
<a id="click_salvar_pdf" class="btn btn-primary" href="#">
    <i class="fa fa-download"></i> Download PDF
</a>


<!-- <form target="_blank" action="https://beta.proscore.com.br/cns/pdf.php" method="post" id="form_salvar" style="display:none;"> -->
<form target="_blank" action="/cns/pdf.php" method="post" id="form_salvar" style="display:none;">
    <input type="hidden" name="conteudo_salvar" id="conteudo_salvar" />
    <input type="hidden" name="id_cns_enviado" id="id_cns_enviado" />
    <input type="hidden" name="arquivo_nome" id="arquivo_nome" />
</form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>

      </div>
    </div>
  </div>
</div>
</div>



<!-- MODAL PARA ABRIR O DOC LISTAR -->
<div class="modal fade" id="modal_doc_listar" data-bs-backdrop="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Lista de Documentos</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
         <div class="modal-body">
		
          <div class="pg-table-section">
                    <div id="teste" class="row">
                        <form class="col-sm-12">
                           <table id="lista_documentos"lass="text-center tableJobs"
                                style="font-size:10px; width:100%; border-radius:0;">
                                <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>cnsid</th>
                                        <th>ctrid</th>
                                        <th>data_cadastro</th>
                                        <th>descricao</th>
                                        <th>formato_arquivo</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                    </div><!-- /teste -->
                </div><!-- /pg-table-section -->
	
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>

      </div>
    </div>
  </div>
</div>
</div>
</body>

	<?php require_once('partial/footer.php') ?>
        <!-- <script src="../../../Scripts/ProDecisionConsultas.js?v=<?= time(); ?>"></script> -->
    <script src="Scripts/ProDecisionConsultas.js?v=<?= time(); ?>"></script>