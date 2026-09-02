<?php require_once('partial/cabecalho.php') ?>

<head>
    <link href="css/proDecision/style_v1_consultas.css?v= <?= time(); ?>" rel="stylesheet">
</head>

<body>
    <div class="container-fluid p-0 d-flex">
        <div class="bg-light flex-fill">
          <?php require_once 'partial/menuLateral.php'; ?>
           <div class="pg-content-wrapper">
                       
                <div class="pg-table-section">
                    
                    <div id="tabela_consulta">
                        <form>
                            <table id="lista_acao" class="text-center tableJobs" style="font-size:10px; width:100%; border-radius:0;">
                                <thead>
                                    <tr class="list-jobs">
                                        <th>#id</th>
                                        <th>Log</th>
                                        <th>Consulta</th>
                                        <th>CPF/CNPJ</th>
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
    </div>
          <div class="container">
                <div class="row justify-content-md-center">
                   
                    <div class="col-md-auto">
                                        
                    <form id="atribuir_amim">
                        <input type="hidden" name="cns" id="id_cns" />
                        <input type="hidden" name="arquivo_nome" id="arquivo_nome" />
                        <button class="btn btn-primary btn-padrao" type="submit">Atribuir a mim </button>
                    </form>
                </div>
                    
                </div>
          </div>
          <div class="pg-table-section">
       <div class="container"> 
    <BR>
    <div class="row"> 
        <div class="col order-last"> 
            <!-- Vinculado ao id_alcada 5 -->
            <button type="button" id="btnAlcada_5" class="btn btn-warning btn-padrao" disabled>Cancelar Análise</button> 
        </div> 
        <div class="col"> 
            <!-- Vinculado ao id_alcada 1 ou similar (Analisar) -->
            <button type="button" id="btnAlcada_1" class="btn btn-secondary btn-padrao" disabled>Analisar</button> 
        </div> 
        <div class="col order-first"> 
            <!-- Vinculado ao id_alcada 2 -->
            <button type="button" id="btnAlcada_2" class="btn btn-success btn-padrao" disabled>Aprovar Análise</button> 
        </div> 
        <div class="col order-first"> 
            <!-- Vinculado ao id_alcada 3 -->
            <button type="button" id="btnAlcada_3" class="btn btn-danger btn-padrao" disabled>Recusar Análise</button> 
        </div> 
    </div> 
</div>
        </div>
        
    </div><!-- /container-fluid -->


<!-- MODAL GENÉRICO PARA AÇÕES -->
<div class="modal fade" id="aprovarModal" tabindex="-1" aria-labelledby="aprovarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <!-- Adicionado id="modalTitulo" -->
        <h1 class="modal-title fs-5" id="modalTitulo">Justificativa</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Adicionado id e classes do Bootstrap para ficar bonito -->
        <textarea id="modalJustificativa" class="form-control" rows="4" placeholder="Digite a justificativa aqui..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <!-- Adicionado id="btnConfirmarAcao" -->
        <button type="button" id="btnConfirmarAcao" class="btn">Confirmar</button>
      </div>
    </div>
  </div>
</div>


	<?php require_once('partial/footer.php') ?>
    <!-- <script src="../../../Scripts/ProDecisionConsultas.js?v=<?= time(); ?>"></script> -->
    <script src="Scripts/ProDecisionConsultas.js?v=<?= time(); ?>"></script>