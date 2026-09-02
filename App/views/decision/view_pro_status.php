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
                <div class="container">
                    <div id="teste" class="row">
                        <form class="col-sm-12">
                           <table id="lista_status"lass="text-center tableJobs"
                                style="font-size:10px; width:100%; border-radius:0;">
                                <thead>
                                    <tr>
                                        <th>log</th>
                                        <th>#id</th>
                                        <th>Documento</th>
                                         <th>Registro</th>
                                         <th>score</th>
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

        
<!-- MODAL PARA ABRIR O STATUS -->
<div class="modal fade" id="modal_status_msg" data-bs-backdrop="true" tabindex="-1" aria-labelledby="status_MsgModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="status_MsgModalLabel">Status</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
       <div class="modal-body">
        
        <div class="pg-table-section">
                    <div id="teste" class="row">
                        <form class="col-sm-12">
                           <table id="lista_status_msg"lass="text-center tableJobs"
                                style="font-size:10px; width:100%; border-radius:0;">
                                <thead>
                                    <tr>
                                        <th>log</th>
                                         <th>Usuário</th>
                                          <th>setor</th>
                                           <th>Status</th>
                                         <th>Justificativa</th>
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
        
	<?php require_once('partial/footer.php') ?>
    <!-- <script src="../../../Scripts/ProDecisionConsultas.js?v=<?= time(); ?>"></script> -->
    <script src="Scripts/ProDecisionConsultas.js?v=<?= time(); ?>"></script>